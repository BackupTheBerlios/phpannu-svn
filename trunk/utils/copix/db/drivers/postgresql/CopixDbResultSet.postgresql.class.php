<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbResultSet.postgresql.class.php,v 1.7.4.1 2005/08/17 20:06:54 laurentj Exp $
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
 * @subpackage copixdb
 */
class CopixDBResultSetPostgreSQL extends CopixDBResultSet {
   var $_connector=null;
   function & _fetch(){
      $toReturn =  pg_fetch_object ($this->_idResult);
      return $toReturn;
   }
   function _free (){
      return pg_free_result ($this->_idResult);

   }
    function numRows(){
      return pg_num_rows($this->_idResult);
   }
}
?>
