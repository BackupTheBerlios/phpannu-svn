<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbResultSet.odbcsqlserver.class.php,v 1.4.4.1 2005/08/17 20:06:54 laurentj Exp $
* @author   Sylvain DACLIN
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 *
 * Couche d'encapsulation des resultset ODBC sql server.
 * @package copix
 * @subpackage copixdb
 */
class CopixDbResultSetODBCSQLServer extends CopixDbResultSet {
   function & _fetch (){
      $res = odbc_fetch_object ($this->_idResult);
      return $res;
   }

   function _free (){
      return odbc_free_result ($this->_idResult);
   }

    function numRows(){
      return odbc_num_rows($this->_idResult);
   }
}
?>
