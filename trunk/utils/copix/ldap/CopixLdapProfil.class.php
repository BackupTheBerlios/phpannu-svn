<?php
/**
* @package	copix
* @subpackage copixdb
* @version	$Id: CopixLdapProfil.class.php,v 1.5 2005/04/05 15:06:09 gcroes Exp $
* @author Bertrand Yan, Croes Gérald
*         see copix.aston.fr for other contributors.
* @copyright 2001-2004 Aston S.A.
* @link		http://copix.aston.fr
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 *
 * @package copix
 * @subpackage copixdb
 */
class CopixLdapProfil {
   var $dn;
   var $host;
   var $user;
   var $password;
   var $shared;

   function CopixLdapProfil ($dnName, $hostName, $userName, $password,$shared=false){
      $this->dn         = $dnName;
      $this->host       = $hostName;
      $this->user       = $userName;
      $this->password   = $password;
      $this->shared     = $shared;;
   }
}
?>