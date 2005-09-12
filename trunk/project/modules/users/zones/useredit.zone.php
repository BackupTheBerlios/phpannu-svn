<?php
/**
* @package	copix
* @subpackage users
* @version	$Id: useredit.zone.php,v 1.6 2005/04/01 14:20:23 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneUserEdit extends CopixZone {
    function _createContent (& $toReturn) {
        $tpl  = & new CopixTpl ();
        $tpl->assign ('user', $this->params['toEdit']);
        if ($this->params['exist']){
            $this->params['errors'][] = CopixI18N::get ('users.error.loginAlreadyExists');
        }
        $tpl->assign ('errors', $this->params['errors']);

        $tpl->assign ('groups', $this->params['toEdit']->__groups);
        $user = $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $userObject = $user->getUser ();
        $tpl->assign ('userProperties', $userObject->getAdminProperties ());
        $tpl->assign ('passwordfield', $userObject->getPasswordField ());
        
        $toReturn = $tpl->fetch ('user.edit.ptpl');
        return true;
    }
}
?>