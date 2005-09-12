<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: grouplist.zone.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Shows the list of known user groups
*/
class ZoneGroupList extends CopixZone {
	function _createContent (&$toReturn){
	   $tpl = & new CopixTpl ();

      //getting the group list from the database.
      $daoGroups = & CopixDAOFactory::create ('copix:CopixGroup');
      $tpl->assign ('groups', $daoGroups->findAll ());

      $toReturn = $tpl->fetch ('profile.adminlist.tpl');
		return true;
	}
}
?>
