<?php
/**
* @package   copix
* @subpackage profile
* @version   $Id: CopixProfileServicesFactory.class.php,v 1.11 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class CopixProfileServicesFactory {
   /**
   * creates a GroupServices object
   * @return ServicesCopixGroup
   * @access public
   */
   function & createGroupServices () {
      require_once (COPIX_PROFILE_PATH.'CopixGroup.services.class.php');
      $object = & new ServicesCopixGroup ();
      return $object;
   }

   /**
   * creates a ServicesCopixCapability object
   * @return ServicesCopixCapability
   * @access public
   */
   function createCapabilityServices () {
      require_once (COPIX_PROFILE_PATH.'CopixCapability.services.class.php');
      $object = & new ServicesCopixCapability ();
      return $object;
   }
}
?>