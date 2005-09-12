<?php
/**
* @package   copix
* @subpackage dbtools
* @version   $Id: CopixDbResultSet.oci8.class.php,v 1.8.4.2 2005/08/17 20:06:54 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 *
 * Couche d'encapsulation des resultset mysql.
 * @package copix
 * @subpackage dbtools
 */
class CopixDBResultSetOci8 extends CopixDBResultSet {
   var $_connector=null;
   var $_fetchCount=-1;

   function & _fetch(){
      $res = false;
      if($this->_fetchCount > -1){ // il faut compter les fetch (pour doLimitQuery);
         if($this->_fetchCount == 0){
            return $res;
         }
         $this->_fetchCount --;
      }

      if(ocifetchinto($this->_idResult, $row, OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS)){
         $res = $this->_getCases ($row);
      }
      return $res;
   }

   function _free (){
      return ocifreestatement ($this->_idResult);

   }

   function fetchTo($offset){
      if($offset > 1){
         for($i=1; $i < $offset-1; $i++){
            if( ! ocifetchinto($this->_idResult, $row, OCI_ASSOC + OCI_RETURN_NULLS+OCI_RETURN_LOBS)){
               break;
            }
         }
      }
   }

   function setFetchCount($count){
      $this->_fetchCount=intval($count);
   }

   /**
   *
   */
   function _getCases ($row) {
      $final = array ();
      foreach ($row as $key=>$name){
         if (($pos = strpos(strtoupper($this->_connector->lastQuery),strtoupper($key)))===false) {
            $final[$key] = $name;
         }else{
            $final[substr($this->_connector->lastQuery,$pos,strlen($key))] = $name;
         }
      }
      return (object) $final;
   }
}
?>