<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbResultSet.sqlserver.class.php,v 1.4.4.1 2005/08/17 20:06:54 laurentj Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 *
 * Couche d'encapsulation des resultset sql server.
 * @package copix
 * @subpackage copixdb
 */
class CopixDbResultSetSQLServer extends CopixDbResultSet {
   function & _fetch (){
      $res = mssql_fetch_object ($this->_idResult);
      return $res;
   }
   function _free (){
      return mssql_free_result ($this->_idResult);
   }

    function numRows(){
      return mssql_num_rows($this->_idResult);
   }
}
?>