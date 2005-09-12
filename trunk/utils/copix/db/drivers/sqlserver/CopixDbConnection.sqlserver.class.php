<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbConnection.sqlserver.class.php,v 1.8.2.2 2005/08/17 20:06:54 laurentj Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class CopixDbConnectionSQLServer extends CopixDbConnection {
    var $scriptComment = '/^\s*#/';
    var $scriptReplaceFrom = "\\r\\n";
    var $scriptReplaceBy = " ";
    var $scriptEndOfQuery = '/\s*GO\s*$/i';

   function _connect (){
     // Define max field length for fields
     ini_set('mssql.textsize',65536);
     ini_set('mssql.textlimit',65536);
     $funcconnect = ($this->profil->persistent? 'mssql_pconnect':'mssql_connect');
     if($cnx = @$funcconnect ($this->profil->host, $this->profil->user, $this->profil->password)){
         if(mssql_select_db ($this->profil->dbname, $cnx))
            return $cnx;
         else
            return false;
      }else
         return false;
   }

   function _disconnect (){
      mssql_close ($this->_connection);
   }

   function _commit (){
      mssql_query('COMMIT TRAN', $this->_connection);
   }

   function _rollBack (){
      mssql_query('ROLLBACK TRAN', $this->_connection);
   }

   function &_doQuery ($queryString){
      if ($qI =  mssql_query ($queryString, $this->_connection)){
         $ret = & new CopixDbResultSetSQLServer ($qI);
      }else{
         $ret = false;
      }
      return $ret;
   }

   function formatText ($text){
      if (is_null ($text)){
         return 'null';
      }else{
         return $this->_quoteValue (str_replace("'", "''", $text));
      }
   }

   function lastId($fieldName, $tableName){
      $rs = $this->doQuery ('SELECT MAX('.$fieldName.') as ID FROM '.$tableName);
      if (($rs !== null) && $r = $rs->fetch ()){
         return $r->ID;
      }
      return 0;
   }

   function getErrorMessage(){
       return mssql_get_last_message ($this->_connection);
   }

   function getErrorCode(){
      return  '';
   }

   function _quote($text){
      return str_replace("'","''",$text);
   }

   function _unquote ($text){
      return str_replace("''","'",$text);
   }

}
?>
