<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: print.plugin.conf.php,v 1.6 2005/02/09 08:31:18 gcroes Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class PluginConfigPrint {
   /**
   * Template we're gonna use to print with
   */
   var $_templatePrint;
   /**
   * says the command needed to activate the print plugin.
   * format: _runPrintUrl['name']=Value
   * will activate the print plugin on index.php?name=value
   */
   var $_runPrintUrl;
   function PluginConfigPrint (){
      $this->_templatePrint = 'main.print.tpl';
      $this->_runPrintUrl = array ('toPrint'=>'1');
   }
}
?>
