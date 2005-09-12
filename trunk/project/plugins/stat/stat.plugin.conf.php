<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: stat.plugin.conf.php,v 1.6 2005/02/09 08:31:18 gcroes Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class PluginConfigStat {
   /**
   * The table name in the database.
   */
   var $tableName;
   /**
   * Associative array INFO=>FIELDNAME
   */
   var $fields;

   function PluginConfigStat (){
      $this->tableName = 'STATS';
      $this->fields = array ('COUNT'=>'COUNT_STAT',
                             'URL'=>'URL_STAT');
   }
}
?>
