<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: profile.actiongroup.php,v 1.8 2005/02/22 15:36:16 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Page concernant la manipulation de pages text
*/
class ActionGroupProfile extends CopixActionGroup {
    function getList (){
        $tpl     = & new CopixTpl ();

        //assignation des différents éléments d'erreur.
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('profile.title.list'));
        $tpl->assign ('MAIN', CopixZone::process ('ProfileList'));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }
    /**
   * Création d'un nouveau profil.
   */
    function doCreate (){
        $this->_setSessionData (new CopixGroup (null));
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|edit'));
    }

    /**
   * Modification d'un profil existant.
   */
    function doPrepareEdit (){
        $this->_setSessionData (new CopixGroup ($this->vars['id']));
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|edit'));
    }

    /**
   * Page de modification d'un groupe
   */
    function getEdit (){
        $tpl   = & new CopixTpl ();
        $group = $this->_getSessionData();

        $tpl->assign ('TITLE_PAGE', $group->name_cgrp !== null ? CopixI18N::get ('profile.title.updateGroup', $group->name_cgrp) : CopixI18N::get ('profile.title.createGroup'));
        $tpl->assign ('MAIN', $GLOBALS['COPIX']['COORD']->processZone ('GroupEdit', array ('group'=>$group)));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
   * Gets the users list
   */
    function getUserList () {
        $tpl   = & new CopixTpl ();
        $group = $this->_getSessionData();

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('profile.title.addUser'));
        $tpl->assign ('MAIN', CopixZone::process ('GroupUserList', array ('group'=>$group)));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
   * récupère la liste des capacités pour l
   */
    function getCapabilitiesList (){
        $tpl = & new CopixTpl ();
        $group = $this->_getSessionData();
        if (! isset ($this->vars['capability'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('profile.error.noGivenCapability'),
            'back'=>'index.php'));
        }

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('profile.title.addCapability'));
        $tpl->assign ('MAIN', CopixZone::process ('GroupCapabilitiesList', array ('group'=>$group, 'capability'=>$this->vars['capability'])));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
   * récupère la liste des capacités pour l
   */
    function getCapabilitiesKind (){
        $tpl = & new CopixTpl ();
        $group = $this->_getSessionData();

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('profile.title.capabilitiesKind'));
        $tpl->assign ('MAIN', CopixZone::process ('CapabilitiesKind', array ('group'=>$group)));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
   * ajoute un utilisateur.
   */
    function doAddUser (){
        $profile = $this->_getSessionData ();
        $profile->addUsers ((array) $this->vars['selectedUsers']);
        $this->_setSessionData ($profile);

        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|edit'));
    }

    /**
   * ajout de capacités.
   */
    function doAddCapabilities (){
        $profile = $this->_getSessionData ();
        $arAllCapabilitiesPath  = CopixProfileTools::getCapabilitiesPath ();
        $capabilitiesPath = array ();
        //adds capabilities to the paths..
        foreach ($arAllCapabilitiesPath as $path=>$capabilityPath){
            $capabilities = CopixProfileTools::getCapabilitiesForPath ($path);
            foreach ($capabilities as $capability){
                if ($capability->name_ccpb == $this->vars['capability']) {
                    $capabilitiesPath[$path] = $capabilityPath;
                }
            }
        }
        foreach ($capabilitiesPath as $path=>$capabilityPath){
            //echo 'test de', $capabilityPath->name_ccpt, '<br />';
            //echo 'valeur', $this->vars[urlencode ($capabilityPath->name_ccpt)];
            if ($this->vars[urlencode ($capabilityPath->name_ccpt)] != '') {
                $profile->setCapability ($path, $this->vars['capability'], $this->vars[urlencode ($capabilityPath->name_ccpt)]);
            }else{
                $profile->removeCapability ($path, $this->vars['capability']);
            }
        }
        //$profile->addCapabilities ((array) $this->vars['selectedCapabilities']);
        $this->_setSessionData ($profile);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|edit'));
    }

    /**
   * enlève une capacité du groupe
   */
    function doRemoveCapability (){
        $this->doValidFromPost ();

        $profile = $this->_getSessionData ();
        $profile->removeCapability ($this->vars['path'], $this->vars['cap']);
        $this->_setSessionData ($profile);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|edit'));
    }

    function doRemoveUser (){
        $this->doValidFromPost ();

        $profile = $this->_getSessionData ();
        $profile->removeUser ($this->vars['user']);
        $this->_setSessionData ($profile);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|edit'));
    }

    function doValid (){
        $this->doValidFromPost ();
        $group = $this->_getSessionData();

        require_once (COPIX_PROFILE_PATH.'CopixGroup.services.class.php');
        $servicesGroup = & new ServicesCopixGroup ();

        if ($group->id_cgrp === null){
            $servicesGroup->insert ($group);
        }else{
            $servicesGroup->update ($group);
        }
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|'));
    }

    /**
   * valid posted informations to the group in session
   */
    function doValidFromPost (){
        $group = $this->_getSessionData();

        //general informations.
        $group->name_cgrp        = $this->vars['name_cgrp'];
        $group->description_cgrp = $this->vars['description_cgrp'];
        $group->isadmin_cgrp     = isset ($this->vars['isadmin_cgrp']);

        //checking the capabilities values.
        foreach ($group->getCapabilities () as $path=>$couple){
            foreach ($couple as $cap=>$value){
                if (isset ($this->vars['capabilities'][$path][$cap])){
                    $group->setCapability ($path, $cap, $this->vars['capabilities'][$path][$cap]);
                }
            }
        }

        $group->all_cgrp   = isset ($this->vars['all_cgrp']);
        $group->known_cgrp = isset ($this->vars['known_cgrp']);

        $this->_setSessionData($group);

        if (isset ($this->vars['then'])){
            return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|'.$this->vars['then']));
        }
    }

    /**
   * removes a capacity from the list.
   */
    function doRemove () {
        if (!isset ($this->vars['confirm'])){
            return CopixActionGroup::process ('genericTools|messages::getConfirm',
            array ('confirm'=>CopixUrl::get ('profile|admin|remove',array('id'=>$this->vars['id'],'confirm'=>1)),
            'cancel'=>CopixUrl::get ('profile|admin|'),
            'message'=>CopixI18N::get ('profile.messages.confirmDelete'))
            );
        }

        require_once (COPIX_PROFILE_PATH.'CopixGroup.services.class.php');
        $servicesGroup = & new ServicesCopixGroup ();
        $servicesGroup->delete ($this->vars['id']);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('profile|admin|'));
    }

    /**
   * Defines the profile in the session variable
   */
    function _setSessionData ($data){
        $_SESSION['EDITED_PROFILE'] = serialize ($data);
    }

    /**
   * gets the current edited profile from the session.
   */
    function _getSessionData (){
        if (! isset ($_SESSION['EDITED_PROFILE'])){
            return null;
        }
        $toReturn = unserialize ($_SESSION['EDITED_PROFILE']);
        if (is_object ($toReturn)){
            return $toReturn;
        }
    }

}
?>