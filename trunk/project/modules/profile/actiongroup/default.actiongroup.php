<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: default.actiongroup.php,v 1.4 2005/03/07 13:23:17 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Page concernant la manipulation de pages text
*/
class ActionGroupDefault extends CopixActionGroup {
   function getList (){
	   $tpl     = & new CopixTpl ();

		//assignation des différents éléments d'erreur.
		$tpl->assign ('MAIN', $GLOBALS['COPIX']['COORD']->processZone ('ProfileList'));
	    $tpl->assign ('TITLE_PAGE', CopixI18N::get ('profile.title.list'));

		return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
   }
}
?>
