<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: copixdb.plugin.php,v 1.9 2005/05/04 11:08:00 laurentj Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class PluginCopixDb extends CopixPlugin {
   function beforeProcess (&$action){
      if ($this->config->showQueryEnabled){
         if (isset ($_GET['showQuery'])){
            switch ($_GET['showQuery']){
               case 'ON':
                  $_SESSION['PLUGIN_COPIXDB_SHOWQUERY'] = 1;
                  break;

               case 'OFF':
                  $_SESSION['PLUGIN_COPIXDB_SHOWQUERY'] = 0;
                  break;
            }
         }
         if (isset ($_SESSION['PLUGIN_COPIXDB_SHOWQUERY']) &&
             $_SESSION['PLUGIN_COPIXDB_SHOWQUERY'] == 1){
            $_GET['showQuery'] = 1;
         }
      }
   }
}
?>
