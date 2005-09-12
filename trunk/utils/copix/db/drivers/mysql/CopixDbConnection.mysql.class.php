<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbConnection.mysql.class.php,v 1.13.2.2 2005/08/17 20:06:54 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 *
 * @package copix
 * @subpackage copixdb
 */
class CopixDBConnectionMySQL extends CopixDBConnection {
   var $_fctEscapeString = '';

   function CopixDBConnectionMySQL(){
      parent::CopixDBConnection();
      // fonction d'echappement pour les chaines
      // on essaie de prendre mysql_real_escape_string car tient compte du charset de la base utilisée
      // par contre existe seulement depuis php 4.3.0..
      // on le fait ici, car c'est un test en moins à faire à chaque quote()
      $this->_fctEscapeString= (function_exists('mysql_real_escape_string') ? 'mysql_real_escape_string' : 'mysql_escape_string');
   }

   function _connect (){
      $funcconnect= ($this->profil->persistent? 'mysql_pconnect':'mysql_connect');
      if($cnx = @$funcconnect ($this->profil->host, $this->profil->user, $this->profil->password)){
         if(mysql_select_db ($this->profil->dbname, $cnx))
            return $cnx;
         else
            return false;
      }else{
         $this->msgError = $funcconnect .'() : Access denied';
         return false;
      }
   }

   function _disconnect (){
      return mysql_close ($this->_connection);
   }

   function & _doQuery ($queryString){
      mysql_select_db ($this->profil->dbname, $this->_connection);
      if ($qI = mysql_query ($queryString, $this->_connection)){
         $rs = & new CopixDbResultSetMySQL ($qI);
      }else{
         $rs = false;
      }
      return $rs;
   }

   function & _doLimitQuery ($queryString, $offset, $number){
     $queryString.= ' LIMIT '.$offset.','.$number;
     $result = & $this->_doQuery($queryString);
     return $result;
   }

   /**
   * begin a transaction
   */
   function begin (){
      $this->doQuery ('SET AUTOCOMMIT=0');
      $this->doQuery ('BEGIN');
   }

   /**
   * Commit since the last begin
   */
   function commit (){
      $this->doQuery ('COMMIT');
      $this->doQuery ('SET AUTOCOMMIT=1');
   }

   /**
   * Rollback since the last BEGIN
   */
   function rollBack (){
      $this->doQuery ('ROLLBACK');
      $this->doQuery ('SET AUTOCOMMIT=1');
   }

   /**
   * tell mysql to be autocommit or not
   * @param boolean state the state of the autocommit value
   * @return void
   */
   function _autoCommitNotify ($state){
      $this->doQuery ('SET AUTOCOMMIT='.$state ? '1' : '0');
   }

   function getErrorMessage(){
      return mysql_error($this->_connection);
   }

   function getErrorCode(){
      return  mysql_errno($this->_connection);
   }

   /**
    * renvoi une chaine avec les caractères spéciaux échappés
    * @access private
    */
   function _quote($text){
      $function_name = $this->_fctEscapeString;
      return $function_name ($text);
   }

   function affectedRows($ressource = null){
      return mysql_affected_rows($this->_connection);
   }
   function lastId($fieldName, $tableName){
      //$rs = $this->doQuery ('SELECT LAST_INSERT_ID() as ID');
      $rs = $this->doQuery ('SELECT MAX('.$fieldName.') as ID FROM '.$tableName);
      if (($rs !== null) && $r = $rs->fetch ()){
         return $r->ID;
      }
      return 0;
   }
}
?>
