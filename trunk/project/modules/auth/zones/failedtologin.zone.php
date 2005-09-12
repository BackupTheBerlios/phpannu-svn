<?php
/**
* @package	copix
* @subpackage auth
* @version	$Id: failedtologin.zone.php,v 1.1 2005/03/21 09:40:52 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneFailedToLogin extends CopixZone {
    function _createContent (& $toReturn){
        $tpl = & new CopixTpl ();

        $tpl->assign ('login', $this->params['login']);
        $tpl->assign ('enabledSendLogin', CopixConfig::get ('auth|enableSendLostPassword'));

        $toReturn = $tpl->fetch ('login.failed.tpl');
    }
}
?>