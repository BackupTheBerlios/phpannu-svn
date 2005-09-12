<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbConnection.odbcsqlserver.class.php,v 1.7.2.2 2005/08/17 20:06:54 laurentj Exp $
* @author   Sylvain DACLIN
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class CopixDbConnectionODBCSQLServer extends CopixDbConnection {
    var $scriptComment = '/^\s*#/';
    var $scriptReplaceFrom = "\\r\\n";
    var $scriptReplaceBy = " ";
    var $scriptEndOfQuery = '/\s*GO\s*$/i';

   function _connect (){
      // Define max field length for ODBC to 50Ko
      ini_set('odbc.defaultlrl','65536');
      $funcconnect= ($this->profil->persistent? 'odbc_pconnect':'odbc_connect');
      if ($cnx = @$funcconnect ($this->profil->host,$this->profil->user,$this->profil->password))
         return $cnx;
      else
         return false;
   }

   function _disconnect (){
      odbc_close ($this->_connection);
   }

   /**
    * begin
    * Overhide COPIXDBConnection WARNING
    */
   function begin () {
      odbc_exec($this->_connection,'BEGIN TRAN');
   }

   /**
    * commit
    * Overhide COPIXDBConnection WARNING
    */
   function commit (){
      odbc_exec($this->_connection,'COMMIT TRAN');
   }

   /**
    * rollback
    * Overhide COPIXDBConnection WARNING
    */
   function rollBack (){
      odbc_exec($this->_connection, 'ROLLBACK TRAN');
   }

   function &_doQuery ($queryString){
      if ($qI =  odbc_exec ($this->_connection,$queryString)){
         $rs = & new CopixDbResultSetODBCSQLServer ($qI);
      }else{
         $rs = false;
      }
      return $rs;
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
       return '';
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
