<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: profileuserlist.zone.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* show the list of the known profiles.
*/
class ZoneProfileUserList extends CopixZone {
	/**
	* Attends un objet de type textpage en paramètre.
	*/
	function _createContent (&$toReturn){
	   $tpl = & new CopixTpl ();
	   
	   //récupère la liste des utilisateurs.
	   $auth  = & $GLOBALS['COPIX']['COORD']->getPlugin ('Auth');
	   $user  = $auth->getUser ();
	   $users = $user->getList ();

	   //enlève les utilisateurs qui seraient déja dans le profil, pour ne pas surcharger.
	   foreach ($users as $key=>$user_tmp){
         if (in_array ($user_tmp->login, $this->params['group']->getUsers ())){
            unset ($users[$key]);
         }
	   }
      $tpl->assign ('users', $users);

		//appel du template.
		$toReturn = $tpl->fetch ('user.list.tpl');
		return true;
	}
}
?>
