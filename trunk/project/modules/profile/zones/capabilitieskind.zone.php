<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: capabilitieskind.zone.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Show the list of the known capabilities kind
*/
class ZoneCapabilitiesKind extends CopixZone {

	function _createContent (&$toReturn) {
	   $tpl = & new CopixTpl ();

      $capabilities       = CopixProfileTools::getCapabilities ();

      //Assigning values to the template
      $tpl->assign ('capabilities', $capabilities);

		//appel du template.
		$toReturn = $tpl->fetch ('capability.kind.tpl');
		return true;
	}
}
?>
