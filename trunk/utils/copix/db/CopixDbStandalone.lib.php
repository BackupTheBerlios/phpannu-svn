<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbStandalone.lib.php,v 1.9.4.2 2005/08/17 20:06:54 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 *
 * Include à utiliser dans un script n'utilisant pas copix, mais voulant utiliser
 * CopixDb (avec les paramètres définis dans Copix)
 * (par exemple les scripts pour cron..)
 *
 * Il faut que les constantes COPIX_PATH et COPIX_PROJECT_PATH soient déjà définies
 *
 * Appeler CopixDbFactorySA au lieu de CopixDbFactory (le reste, c'est pareil)
 *
 * ex : $ct= CopixDbFactorySA::getConnection();
 *
 */

//define('COPIX_PATH',realpath(dirname(__FILE__).'/../').'/');
//define ('COPIX_PROJECT_PATH', realpath(dirname(__FILE__).'/../../project/').'/');//project is obviously in its own directory
if(!defined('COPIX_DB_PATH'))
   define('COPIX_DB_PATH',COPIX_PATH.'db/');

require COPIX_PROJECT_PATH.'plugins/copixdb/copixdb.plugin.conf.php';

$GLOBALS['STANDALONE_COPIXDBCONF']= new PluginConfigCopixDB();

class CopixDbFactorySA {
   /**
   * Récupération d'une connection.
   * @static
   * @param string  $named  nom du profil de connection définie dans CopixDb.plugin.conf.php
   * @return CopixDbConnection  objet de connection vers la base de donnée
   */
   function & getConnection ($named = null){
      if ($named == null){
         return CopixDbFactorySA::getConnection (CopixDbFactorySA::getDefaultConnectionName ());
      }
      $profil = & CopixDbFactorySA::_getProfil ($named);

      //peut être partagé ?
      if ($profil->shared){
         $foundedConnection = & CopixDbFactorySA::_findConnection ($named);
         if ($foundedConnection === null){
            $foundedConnection = & CopixDbFactorySA::_createConnection ($named);
         }
         return $foundedConnection;
      }else{
         //Ne peut pas être partagé.
         return CopixDbFactorySA::_createConnection ($named);
      }
   }

   /**
   * récupération d'une connection par défaut.
   * @static
   * @return    string  nom de la connection par défaut
   */
   function getDefaultConnectionName (){
      return $GLOBALS['STANDALONE_COPIXDBCONF']->default;
   }

   function & getDbWidget($connectionName=null){
       require_once (COPIX_DB_PATH . 'CopixDbWidget.class.php');
       return $return = & new CopixDbWidget(CopixDBFactorySA::getConnection($connectionName));
   }

   /**
   * creation d'une connection sans se connecter
   */
   function & getConnector ($connectionName = null) {
      if ($connectionName == null){
         return CopixDBFactorySA::getConnector (CopixDBFactorySA::getDefaultConnectionName ());
      }
      $profil = & CopixDBFactorySA::_getProfil ($connectionName);

      //pas de vérification sur l'éventuel partage de l'élément.
      $connector = & CopixDBFactorySA::_createConnector ($connectionName);
      return $connector;
   }

   function & getTools($connectionName=null){
      require_once (COPIX_DB_PATH . 'CopixDbTools.class.php');
      return $return = & new CopixDbTools(CopixDBFactorySA::getConnection($connectionName));
   }

   /* ======================================================================
   *  private
   */

   /**
   * récupération d'un profil de connection à une base de données.
   * @access private
   * @param string  $named  nom du profil de connection
   * @return    CopixDbProfil   profil de connection
   */
   function & _getProfil ($named){
      return $GLOBALS['STANDALONE_COPIXDBCONF']->profils[$named];
   }

   /**
   * Récupération de la connection dans le pool de connection, à partir du nom du profil.
   * @access private
   * @param string  $named  nom du profil de connection
   * @return CopixDbConnection  l'objet de connection
   */
   function & _findConnection ($profilName){
      $profil = & CopixDbFactorySA::_getProfil ($profilName);

      if ($profil->shared){
         //connection partagée, on peut retourner celle qui existe.
         if (isset ($GLOBALS['COPIX']['DB'][$profilName])){
            return $GLOBALS['COPIX']['DB'][$profilName];
         }else{
            $ret = null;
            return $ret;

         }
      }else{
         //la connection n'est pas partagée, quoi qu'il arrive, on ne
         // peut pas retourner une connection existante.
         //(On fera confiance au pool de PHP pour cette gestion)
         $ret = null;
         return $ret;
      }
   }

   /**
   * création d'une connection.
   * @access private
   * @param string  $named  nom du profil de connection
   * @return CopixDbConnection  l'objet de connection
   */
   function &_createConnection ($profilName){
      $profil = & CopixDbFactorySA::_getProfil ($profilName);

      require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbConnection.'.$profil->driver.'.class.php');
      require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbResultSet.'.$profil->driver.'.class.php');
      $class='CopixDbConnection'.$profil->driver;
       //Création de l'objet
      $obj = & new $class ();
      if ($profil->shared){
         $GLOBALS['COPIX']['DB'][$profilName] = & $obj;
      }else{
         $GLOBALS['COPIX']['DB'][$profilName][] = & $obj;
      }
      $obj->connect ($profil);
      return $obj;
   }

   /**
   * création d'une connection.
   * @access private
   * @param string  $named  nom du profil de connection
   * @return CopixDbConnection  l'objet de connection
   */
   function & _createConnector ($profilName){
      $profil = & CopixDBFactorySA::_getProfil ($profilName);
      require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbConnection.'.$profil->driver.'.class.php');
      require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbResultSet.'.$profil->driver.'.class.php');
      $class = 'CopixDbConnection'.$profil->driver;

      //Création de l'objet
      $obj = & new $class ();
      $obj->profil = $profil;
      //$obj->connect ($profil);
      return $obj;
   }
}
?>