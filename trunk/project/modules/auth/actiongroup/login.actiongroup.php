<?php
/**
* @package	copix
* @subpackage auth
* @version	$Id: login.actiongroup.php,v 1.12.2.1 2005/05/06 08:59:29 laurentj Exp $
* @author	Croes Gérald, Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ActionGroupLogin extends CopixActionGroup {
    /**
    * Try to log in
    * @param string $this->vars['login'] the login
    * @param string $this->vars['password'] the password
    * @return Object CopixActionReturn
    */
    function doLogin (){
        $plugAuth = & $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $user     = & $plugAuth->getUser();
        $user->login ($this->vars['login'], $this->vars['password']);

        if ((intval (CopixConfig::get ('auth|enableAfterLoginOverride')) == 1) && isset($this->vars['auth_url_return']) && !empty ($this->vars['auth_url_return'])){
            $url_return = $this->vars['auth_url_return'];
        }else{
            $url_return = CopixConfig::get('auth|afterLogin');
        }

        //check if the url return is correct.
        if ((strpos ($url_return, 'http://') === false)){
            $url_return = CopixUrl::get ().$url_return;
        }

        if (!$user->isConnected ()){
            sleep (intval(CopixConfig::get ('auth|intervalBetweenFailedLogin')));
            return CopixActionGroup::process ('auth|Login::getLoginForm', array ('login'=>$this->vars['login'], 'failed'=>1));
        }
        return new CopixActionReturn (COPIX_AR_REDIRECT, $url_return);
    }

    /**
    * Logs out
    * @return Object CopixActionReturn
    */
    function doLogout (){
        $plugAuth = & $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $user     = & $plugAuth->getUser();
        $user->logout ();

        if ((intval (CopixConfig::get ('auth|enableAfterLogoutOverride')) == 1) && isset($this->vars['auth_url_return']) && !empty ($this->vars['auth_url_return'])){
            $url_return = $this->vars['auth_url_return'];
        }else{
            $url_return = CopixConfig::get('auth|afterLogout');
        }
        //check if the url return is correct.
        if ((strpos ($url_return, 'http://') === false)){
            $url_return = CopixUrl::get ().$url_return;
        }

        return new CopixActionReturn (COPIX_AR_REDIRECT, $url_return);
    }

    /**
    * Shows the login form
    * @return Object CopixActionReturn
    */
    function getLoginForm() {
        $tpl = & new CopixTpl ();
        $login = isset($this->vars['login'])?$this->vars['login']:'';
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('auth.titlePage.login'));
        $tpl->assign ('MAIN', CopixZone::process ('auth|loginForm', array ('login'=>$login, 'failed'=>isset ($this->vars['failed']))));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }
}
?>