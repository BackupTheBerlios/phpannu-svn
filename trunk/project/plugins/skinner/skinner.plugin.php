<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: skinner.plugin.php,v 1.5 2005/05/04 11:08:00 laurentj Exp $
* @author Bertrand yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class PluginSkinner extends CopixPlugin {
   function beforeProcess(&$action){
      $GLOBALS['COPIX']['CONFIG']->mainTemplate = CopixConfig::get ('|mainTemplate');
   }
}
