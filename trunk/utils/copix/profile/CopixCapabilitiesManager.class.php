<?php
/**
* @package   copix
* @subpackage profile
* @version   $Id: CopixCapabilitiesManager.class.php,v 1.11 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class CopixCapabilitiesManager {

   function getList (){
      $dao = & CopixDAOFactory::create('copix:CopixCapability');
      $sp  = & CopixDAOFactory::createSearchParams ();
      $sp->orderBy ('name_ccpb');

      return $dao->findBy ($sp);
   }

}
?>