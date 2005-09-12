<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: stat.plugin.php,v 1.6 2005/02/09 08:31:18 gcroes Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class PluginStat extends CopixPlugin {
   function beforeProcess(&$action){
      $objMetier = & new _DAOPluginStat ();
      $objMetier->tableName = $this->config->tableName;
      $objMetier->fields = $this->config->fields;

      include_once (COPIX_UTILS_PATH.'CopixUtils.lib.php');
      ksort ($_GET);
      $objMetier->add (urlParams ($_GET));
   }
}
class _DAOPluginStat {
   var $tableName;
   var $fields;

   function add ($url){
      $beforeValue = null;
      $ct = CopixDbFactory::getConnection ();
      $rq = $ct->doQuery ('Select '.$this->fields['COUNT'].' from '.$this->tableName.' where '.$this->fields['URL'].'='.$ct->formatText ($url));

      if ($o = $rq->fetchObject ()){
         $beforeValue = $o->{$this->fields['COUNT']};
      }

      if ($beforeValue !== null){
         $beforeValue++;
         $ct->doUpdate ($this->tableName, array ($this->fields['COUNT']=>$beforeValue), array ($this->fields['URL']=>$ct->formatText ($url)));
      }else{
         $beforeValue = 0;
         $ct->doInsert ($this->tableName, array ($this->fields['COUNT']=>$beforeValue, $this->fields['URL']=>$ct->formatText ($url)));
      }
   }
}
?>
