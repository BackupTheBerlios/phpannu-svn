<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbFactory.class.php,v 1.14.2.3 2005/08/17 21:06:10 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @ignore
*/
if (!defined ('COPIX_DB_PATH'))
define ('COPIX_DB_PATH', dirname (__FILE__).'/');

require_once (COPIX_DB_PATH . 'CopixDbConnection.class.php');
require_once (COPIX_DB_PATH . 'CopixDbResultSet.class.php');
require_once (COPIX_DB_PATH . 'CopixDbProfil.class.php');

/**
*
* @package copix
* @subpackage copixdb
* @see CopixDbConnection CopixDbResultSet CopixDbProfil
*/
class CopixDBFactory {
    /**
    * Récupération d'une connection.
    * @static
    * @param string  $named  nom du profil de connection définie dans CopixDb.plugin.conf.php
    * @return CopixDbConnection  objet de connection vers la base de donnée
    */
    function & getConnection ($named = null){
        if ($named == null){
            $named = CopixDBFactory::getDefaultConnectionName ();
        }
        $profil = & CopixDBFactory::_getProfil ($named);

        //peut être partagé ?
        if ($profil->shared){
            $foundedConnection = & CopixDBFactory::_findConnection ($named);
            if ($foundedConnection === null){
                $foundedConnection = & CopixDBFactory::_createConnection ($named);
            }
        }else{
            //Ne peut pas être partagé.
            $foundedConnection = &  CopixDBFactory::_createConnection ($named);
        }
        return $foundedConnection;
    }

    /**
    * récupération d'une connection par défaut.
    * @static
    * @return    string  nom de la connection par défaut
    */
    function getDefaultConnectionName (){
        $pluginDB = & CopixDBFactory::_getPluginDB ();
        return $pluginDB->config->default;
    }

    function & getDbWidget($connectionName=null){
        require_once (COPIX_DB_PATH . 'CopixDbWidget.class.php');
        return $return = & new CopixDbWidget(CopixDBFactory::getConnection($connectionName));
    }

    /**
    * creation d'une connection sans se connecter
    */
    function & getConnector ($connectionName = null) {
        if ($connectionName == null){
            $connectionName = CopixDBFactory::getDefaultConnectionName ();
        }
        $profil = & CopixDBFactory::_getProfil ($connectionName);

        //pas de vérification sur l'éventuel partage de l'élément.
        $connector = & CopixDBFactory::_createConnector ($connectionName);
        return $connector;
    }

    /**
    * Récupération des outils de BD
    * @param string Connection name to use
    * @return CopixDBTools
    */
    function & getTools ($connectionName=null){
        require_once (COPIX_DB_PATH . 'CopixDbTools.class.php');
        if ($connectionName == null){
            $connectionName = CopixDBFactory::getDefaultConnectionName ();
        }
        $profil = & CopixDBFactory::_getProfil ($connectionName);

        //pas de vérification sur l'éventuel partage de l'élément.
        $tools = & CopixDBFactory::_createTools ($connectionName);
        return $tools;
    }


    function testConnection ($type, $db, $host, $userDB, $passDB,$schema){
        $profil = new CopixDbProfil($type, $db, $host, $userDB, $passDB,false, true, $schema);

        require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbConnection.'.$profil->driver.'.class.php');
        require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbResultSet.'.$profil->driver.'.class.php');

        $class = 'CopixDbConnection'.$profil->driver;

        //Création de l'objet
        $obj = & new $class ();

        return $obj->testProfil($profil);
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
        $pluginDB = & CopixDBFactory::_getPluginDB ();
        if(isset($pluginDB->config->profils[$named])){
            return $pluginDB->config->profils[$named];
        }else{
            trigger_error(CopixI18N::get('copix:copix.db.error.profil.unknow',$named),E_USER_ERROR);
        }
    }

    function & _getPluginDB (){
        static $pluginDB = false;
        if ($pluginDB === false){
            $pluginDB = $GLOBALS['COPIX']['COORD']->getPlugin ('CopixDb');
            if ($pluginDB === null){
                trigger_error (CopixI18N::get('copix:copix.error.plugin.unregister','CopixDb'), E_USER_ERROR);
            }
        }
        return $pluginDB;
    }

    /**
    * Récupération de la connection dans le pool de connection, à partir du nom du profil.
    * @access private
    * @param string  $named  nom du profil de connection
    * @return CopixDbConnection  l'objet de connection
    */
    function & _findConnection ($profilName){
       if (isset ($GLOBALS['COPIX']['DB'][$profilName])){
          return $GLOBALS['COPIX']['DB'][$profilName];
        }else{
          $r = null;
          return $r;
        }
    }

    /**
    * création d'une connection.
    * @access private
    * @param string  $named  nom du profil de connection
    * @return CopixDbConnection  l'objet de connection
    */
    function & _createConnection ($profilName){
        $profil = & CopixDBFactory::_getProfil ($profilName);

        require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbConnection.'.$profil->driver.'.class.php');
        require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbResultSet.'.$profil->driver.'.class.php');

        $class = 'CopixDbConnection'.$profil->driver;

        //Création de l'objet
        $obj = & new $class ();
        if ($profil->shared){
            $GLOBALS['COPIX']['DB'][$profilName] = & $obj;
        }
        /*else{
        $GLOBALS['COPIX']['DB'][$profilName][] = & $obj;
        }*/

        if ($GLOBALS['COPIX']['COORD']->getPluginConf ('CopixDb', 'showQueryEnabled')
        && (isset ($_GET['showQuery'])) && ($_GET['showQuery'] == '1')){
            $obj->_debugQuery=true;
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
        $profil = & CopixDBFactory::_getProfil ($profilName);
        require_once (COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbConnection.'.$profil->driver.'.class.php');
        require_once (COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbResultSet.'.$profil->driver.'.class.php');
        $class = 'CopixDbConnection'.$profil->driver;

        //Création de l'objet
        $obj = & new $class ();
        $obj->profil = $profil;
        //$obj->connect ($profil);
        return $obj;
    }

    /**
    * Création d'un objet de manipulation sur la base
    * @param string connection name
    * @return CopixDBTools
    */
    function & _createTools ($profilName) {
        $profil = & CopixDBFactory::_getProfil ($profilName);
        require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbTools.'.$profil->driver.'.class.php');
        //require_once(COPIX_DB_PATH.'/drivers/'.$profil->driver.'/CopixDbResultSet.'.$profil->driver.'.class.php');
        $class = 'CopixDbTools'.$profil->driver;

        //Création de l'objet
        $obj = & new $class (CopixDBFactory::getConnection ($profilName));
        $obj->profil = $profil;
        //$obj->connect ($profil);
        return $obj;

    }
}
function _COPIX_DB_SHUTDOWN_FUNCTION (){
    foreach ($GLOBALS['COPIX']['DB']['CONNECTIONS'] as $object){
        if (is_object ($object)){
            $object->disconnect ();
        }
    }
}
?>