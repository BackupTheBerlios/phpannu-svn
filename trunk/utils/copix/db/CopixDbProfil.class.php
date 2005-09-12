<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbProfil.class.php,v 1.10 2005/04/05 12:05:49 gcroes Exp $
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
class CopixDbProfil {
   /**
   * the driver type. You may add your own drivers in /utils/copix/db/drivers/driverName
   * @var string
   */ 
   var $driver;
   
   /**
   * the database name (eg in mySql)
   * @var string
   */
   var $dbname;
   
   /**
   * the host (ip, name or tnsname)
   * @var string
   */
   var $host;
   
   /**
   * the user to connect to the database with
   * @var string
   */
   var $user;
   
   /**
   * the password to connect to the database with
   * @var string
   */
   var $password;
   
   /**
   * if we're using persistent connections.
   * @var boolean
   */
   var $persistent;
   
   /**
   * if we may share the connection in the whole script (eg if called twice, the same object will be given)
   * @var boolean
   */
   var $shared;
   
   /**
   * the default schema
   * @var string
   */
   var $schema;

   /**
   * @constructor
   */
   function CopixDbProfil ($drivername, $databasename, $host, $user, $password,
         $persistent=true, $shared=false,
         $schema =''){
      $this->driver     = $drivername;
      $this->dbname     = $databasename;
      $this->host       = $host;
      $this->user       = $user;
      $this->password   = $password;
      $this->persistent = $persistent;
      $this->shared     = $shared;
      $this->schema     = $schema;
   }
}
?>