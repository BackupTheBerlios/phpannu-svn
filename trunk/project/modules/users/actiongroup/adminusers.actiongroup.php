<?php
/**
* @package	copix
* @subpackage users
* @version	$Id: adminusers.actiongroup.php,v 1.9 2005/04/01 14:20:23 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ActionGroupAdminUsers extends CopixActionGroup {
    /**
    * Gets the users list
    * @param string $this->vars['pattern'] le pattern de recherche des utilisateurs.
    */
    function getList () {
        $tpl = & new CopixTpl ();

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('users.title.admin'));
        $tpl->assign ('MAIN', CopixZone::process ('UsersList', array ('pattern'=>isset ($this->vars['pattern']) ? $this->vars['pattern'] : '')));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
    * Validate the user's informations.
    */
    function doValid (){
        if (!$toValid = $this->_getSession()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('users.error.unableToGetObject'),
            'back'=>CopixUrl::get('users|admin|')));
        }
        $pluginAuth = & $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $authUser   = $pluginAuth->getUser ();

        //demande de mettre l'objet à jour en fonction des valeurs saisies dans le
        //formulaire.
        $this->_validFromForm($toValid);
        $existingGroups = array ();

        //vérifie si l'utilisateur n'existe pas déja.
        if ($toValid->is__new) {
            if ($authUser->get ($toValid->login)) {
                $this->_setSession($toValid);
                return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('users|admin|edit', array('e'=>'1', 'exist'=>'1')));
            }
        }else{
            $userProfile    = & new CopixProfile ($toValid->login);
            $existingGroups = $userProfile->getGroups ();
        }

        if (($errors = $this->_check ($toValid)) !== true){
            $this->_setSession ($toValid);
            return CopixActionGroup::process ('users|AdminUsers::getEdit', array ('e'=>1, 'errors'=>$errors));
        }else{
            //modif ou création selon le cas.
            if ($toValid->is__new === false){
                //Tentative de mise à jour des informations
                $authUser->updateInformations ($toValid->login, $toValid);

                //Mise à jour du mot de passe si demandé.
                if ($toValid->__newPassword) {
                    $authUser->updatePassword ($toValid->login, $toValid->password);
                }

                //on suprime la liste des groupes auquel il appartenait avant
                $daoUserGroup  = & CopixDAOFactory::create ('copix:CopixUserGroup');
                $daoUserGroup->removeUser ($toValid->login);
            }else{
                $authUser->createUser ($toValid);
            }

            //on rajoute maintenant les utilisateurs du groupe.
            $daoUserGroup = & CopixDAOFactory::create ('copix:CopixUserGroup');
            $groupUser = & CopixDAOFactory::createRecord ('copix:CopixUserGroup');
            $groupUser->login_cusr = $toValid->login;
            foreach ((array)$toValid->__groups as $group) {
                $groupUser->id_cgrp = $group->id_cgrp;
                $daoUserGroup->insert ($groupUser);
            }

            //retour sur la page de liste.
            return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('users|admin|', array('pattern'=>$toValid->login)));
        }
    }

    /**
    * gets the edit page for the user.
    */
    function getEdit (){
        if (!$toEdit = $this->_getSession()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('users.error.unableToGetObject'),
            'back'=>CopixUrl::get('users|admin|')));
        }
        
        //no errors ?
        if (!isset ($this->vars['errors'])){
            $this->vars['errors'] = array ();
        }

        $tpl = & new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', ($toEdit->is__new === false) ? CopixI18N::get ('users.title.update') : CopixI18N::get ('users.title.create'));
        $tpl->assign ('MAIN', CopixZone::process ('UserEdit',
        array ('toEdit'=>$toEdit,
               'e'=>isset ($this->vars['e']),
               'exist'=>isset ($this->vars['exist']), 
               'errors'=>$this->vars['errors']
               )));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
    * prepare a new user to be edited.
    */
    function doCreate (){
        //gets te current user.
        $userPlug  = & $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $user      = & $userPlug->getUser ();
        $EUser     = & $user->getNew ();

        $EUser->is__new = true;
        $EUser->__groups = array ();
        $EUser->__newPassword = true;
        $this->_setSession ($EUser);

        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('users|admin|edit'));
    }

    /**
    * prepare the news to edit.
    * check if we were given the news id to edit, then try to get it.
    */
    function doPrepareEdit (){
        if (!isset ($this->vars['login'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('users.error.missingParameters'),
            'back'=>CopixUrl::get('users|admin|')));
        }

        $userPlugin = $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $user       = $userPlugin->getUser ();

        if (!$toEdit = $user->get ($this->vars['login'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('users.error.unableToGetObject'),
            'back'=>CopixUrl::get('users|admin|')));
        }

        //ajout des groupes de l'utilsiateur en session.
        $userProfile = new CopixProfile ($toEdit->login);
        $toEdit->__groups = $userProfile->getGroups ();

        $toEdit->is__new = false;
        $this->_setSession ($toEdit);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('users|admin|edit'));
    }

    /**
    * Cancel the edition...... empty the session data
    */
    function doCancelEdit (){
        $this->_setSession(null);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('users|admin|'));
    }

    /**
    * validation temporaire des éléments saisis.
    */
    function doValidEdit (){
        return $this->doValid ();
        
        $toEdit = $this->_getSession ();
        $this->_validFromForm  ($toEdit);
        $this->_setSession ($toEdit);

        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('users|admin|edit', array ('kind'=>$this->vars['kind'])));
    }

    /**
    * updates informations on a single news object from the vars.
    * le formulaire.
    * @access: private.
    */
    function _validFromForm (& $toUpdate){
        $auth = $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $userObject = & $auth->getUser ();
        
        foreach ($userObject->getAdminProperties () as $name=>$value){
            if (isset ($this->vars[$name])){
                if ($name == 'password'){
                    if (strlen (trim ($this->vars['password']))){
                        $toUpdate->$name = $this->vars[$name];
                        $toUpdate->__newPassword  = true;
                        $toUpdate->check_password = $this->vars['check_password'];
                    }
                }else{
                   $toUpdate->$name = $this->vars[$name];
                }
            }
        }
    }

    /**
    * Deletes a user
    */
    function doDelete (){
        $daogroup  = & CopixDAOFactory::create ('copix:copixusergroup');

        //supression de l'utilisateur de tous les groupes
        $daogroup->removeUser ($this->vars['login']);

        $userPlugin = $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
        $userObject = $userPlugin->getUser ();
        
        $userObject->delete ($this->vars['login']);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('users|admin|'));
    }

    /**
    * Removes a group from a user
    */
    function doRemoveGroup () {
        /*
        $toEdit = $this->_getSession ();
        unset ($toEdit->__groups[$this->vars['id_cgrp']]);
        $this->_validFromForm($toEdit);
        $this->_setSession($toEdit);
        */

        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('users|admin|edit'));
    }

    /**
    * adds a group to the user
    * @param int $this->vars[id_cgrp] the group id
    * @access public
    * /
    function doAddGroup () {
        $toEdit = $this->_getSession ();
        $toEdit->__groups[$this->vars['id_cgrp']] = & new CopixGroup ($this->vars['id_cgrp']);
        $this->_setSession($toEdit);

        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('users|admin|edit'));
    }

    /**
    * @access public
    */
    function getSelectGroup (){
        $toEdit = $this->_getSession ();
        $this->_validFromForm($toEdit);
        $this->_setSession($toEdit);
        $tpl = & new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('users.title.groupSelect'));
        $tpl->assign ('MAIN', CopixZone::process ('profile|SelectGroup',
        array ('select'=>CopixUrl::get('users|admin|addGroup'),
        'back'=>CopixUrl::get('users|admin|edit'))));
        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
    * gets the current edited user.
    * @access: private.
    */
    function _getSession () {
        return isset ($_SESSION['MODULE_USERS_EDITED']) ? unserialize ($_SESSION['MODULE_USERS_EDITED']) : null;
    }

    /**
    * sets the current edited user.
    * @access: private.
    */
    function _setSession ($toSet) {
        $_SESSION['MODULE_USERS_EDITED'] = $toSet !== null ? serialize($toSet) : null;
    }

    /**
    * Says if the user is OK
    */
    function _check ($user){
        if (($result = $user->check ()) !== true){
            return false;
        }
        if ($user->__newPassword === true){
            if ((strlen (trim ($user->password)) <= 0) || ($user->password != $user->check_password)){
                return array (CopixI18N::get ('copix:auth.message.passwordDoNotMatch'));
            }
        }
        return true;
    }
}
?>