<?php

/**
* @package   copix
* @subpackage plugins
* @version   $Id: sessiontools.plugin.php,v 1.5 2005/02/09 08:31:18 gcroes Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


class PluginSessionTools extends CopixPlugin {
   function beforeProcess(& $copixaction){
      if (isset ($GLOBALS['COPIX']['COORD']->vars['sessionTools'])){
         switch ($GLOBALS['COPIX']['COORD']->vars['sessionTools']){
            case 'destroy':
               session_destroy();
               break;

            case 'show':
               print_r ($_SESSION);
               break;

         }
      }
   }

   function beforeSessionStart (){
   }
}
?>
