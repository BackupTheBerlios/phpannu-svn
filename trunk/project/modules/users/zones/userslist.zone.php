<?php
/**
* @package	copix
* @subpackage users
* @version	$Id: userslist.zone.php,v 1.7 2005/03/21 09:50:20 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneUsersList extends CopixZone {
    function _createContent (& $toReturn) {
        $auth = $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $userObject = & $auth->getUser ();
        $list = $userObject->findByLogin ($this->params['pattern']);

        $tpl  = & new CopixTpl ();
        $tpl->assign ('arUsers', $list);
        $tpl->assign ('pattern', $this->params['pattern']);
        $tpl->assign ('userProperties', $userObject->getListProperties ());

        $toReturn = $tpl->fetch ('users.list.ptpl');
        return true;
    }
}
?>