<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: profilelist.zone.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* show the list of the known profiles.
*/
class ZoneProfileList extends CopixZone {
	/**
	* Attends un objet de type textpage en paramètre.
	*/
	function _createContent (&$toReturn){
	   $tpl = & new CopixTpl ();

      //assignation de la liste des profils connus.
      $profile = & new DAOCopixProfile ();
      $tpl->assign ('profiles', $profile->getUnloadedList ());

		//appel du template.
		$toReturn = $tpl->fetch ('profile|profile.adminlist.tpl');
		return true;
	}
}
?>
