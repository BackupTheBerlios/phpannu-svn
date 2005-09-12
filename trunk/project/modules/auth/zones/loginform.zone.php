<?php
/**
* @package	copix
* @subpackage auth
* @version	$Id: loginform.zone.php,v 1.5.4.1 2005/07/30 09:52:33 laurentj Exp $
* @author	Croes Gérald, Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneLoginForm extends CopixZone {
    function _createContent (& $toReturn){
        $tpl = & new CopixTpl ();

        $plugAuth  = & $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();
        if ($user->isConnected ()){
            $tpl->assign ('user', $user);
        }else{
            $tpl->assign ('user', null);
            $tpl->assign ('login', isset ($this->params['login']) ? $this->params['login'] : null);
        }

        $tpl->assign ('failed', isset ($this->params['failed']) ? $this->params['failed'] : 0);
        $tpl->assign ('showLostPassword', CopixConfig::get ('auth|enableSendLostPassword'));
        $tpl->assign ('showRememberMe', ($GLOBALS['COPIX']['COORD']->getPlugin ('auth|reconnect') !== null));

        $toReturn = $tpl->fetch ('login.form.tpl');
        return true;
    }
}
?>