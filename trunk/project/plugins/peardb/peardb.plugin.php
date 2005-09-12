<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: peardb.plugin.php,v 1.6 2005/02/09 08:29:09 gcroes Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* plugin gerant la connection à une base de donnée via PEAR::DB
*/
class PluginPearDB extends CopixPlugin {
   /**
     *
    * @param   class   $config      objet de configuration du plugin
     */
   function PluginPearDB($config){
        parent::CopixPlugin($config);

      $GLOBALS['COPIX']['DB']= DB::connect($config->dsn,$config->persistantCnx);
        $GLOBALS['COPIX']['DB']->setFetchMode($config->fetchMode);

   }
   /**
   * traitements à faire apres execution du coordinateur de page
   * @param CopixActionReturn      $actionreturn
   */
   function afterProcess($actionreturn){

        $GLOBALS['COPIX']['DB']->disconnect();
   }

}
?>
