<?php
/**
* @package   copix
* @subpackage profile
* @version   $Id: CopixGroup.class.php,v 1.21 2005/02/15 10:34:33 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class CopixGroup {
    /**
    * capabilities
    * @var array of CopixCapabilities
    */
    var $_capabilities = array ();

    /**
    * only logins here.
    * @var array of users
    */
    var $_users = array ();

    /**
    * group id
    */
    var $id_cgrp = null;

    /**
    * group name
    * @var string
    */
    var $name_cgrp = null;

    /**
    * group description
    * @var string
    */
    var $description_cgrp = null;

    /**
    * Public group ?
    * @var boolean
    */
    var $all_cgrp = false;

    /**
    * All known users group ?
    * @var boolean
    */
    var $known_cgrp = false;

    /**
    * Group is Super user ?
    * @var boolean
    */
    var $isadmin_cgrp = false;

    /**
    * constructor.
    * @param int $id the group id
    */
    function CopixGroup ($id){
        //look for the capability values for the given group
        $this->id_cgrp = $id;

        if ($id !== null){
            //Loads the group
            $daoGroup   = & CopixDAOFactory::create ('copix:CopixGroup');
            $group      = & $daoGroup->get ($id);
            if ($group === null){
                //check if the group exists
                trigger_error ('Given group does not exists');
            }

            $this->description_cgrp = $group->description_cgrp;
            $this->name_cgrp        = $group->name_cgrp;
            $this->all_cgrp         = $group->all_cgrp;
            $this->known_cgrp       = $group->known_cgrp;
            $this->isadmin_cgrp     = $group->isadmin_cgrp == '1' ? true : false;

            //Loads the capabilities
            $daoCap      = & CopixDAOFactory::create ('copix:CopixGroupCapabilities');
            $sp          = & CopixDAOFactory::createSearchParams ();
            $sp->addCondition ('id_cgrp', '=', $id);

            //load capabilities.
            $capabilities = $daoCap->findBy ($sp);
            foreach ($capabilities as $capability){
                $this->setCapability ($capability->name_ccpt,
                $capability->name_ccpb,
                $capability->value_cgcp);
            }

            //load logins
            $daoUserGroup = & CopixDAOFactory::create ('copix:CopixUserGroup');
            $sp           = & CopixDAOFactory::createSearchParams ();
            $sp->addCondition ('id_cgrp', '=', $id);
            $logins = $daoUserGroup->findBy ($sp);

            //adds the logins in the object
            foreach ($logins as $login){
                $this->addUsers ($login->login_cusr);
            }
        }
    }

    /**
    * récupération de la liste des utilisateurs.
    */
    function getUsers (){
        return $this->_users;
    }

    /*
    * Ajout d'utilisateurs.
    */
    function addUsers ($users){
        if (is_array ($users)){
            foreach ($users as $user){
                if (!in_array ($user, $this->_users)){
                    $this->_users[] = $user;
                }
            }
        }else{
            if (!in_array ($users, $this->_users)){
                $this->_users[] = $users;
            }
        }
    }

    /**
    * suppression d'utilisateurs.
    */
    function removeUser ($userName){
        if (in_array ($userName, $this->_users)){
            unset ($this->_users[array_search ($userName, $this->_users)]);
        }
    }

    /**
    * gets the max value of the element
    */
    function valueOf ($path, $cap) {
        if ($this->isadmin_cgrp) {
            return PROFILE_CCV_ADMIN;
        }
        $currentValue = PROFILE_CCV_NONE;//starts with NONE
        $lastValue    = null;
        $testString   = '';
        $values       = explode ('|', $path);
        $first = true;

        //test all given elements.
        //eg for site|module|something|other
        //   testing site,
        //           site|module,
        //           site|module|something,
        //           site|module|something|other
        foreach ($values as $element){
            if (!$first) {
                $testString .= '|';
            }
            $first = false;
            $testString .= $element;//the test string.

            //If the value is known, and if the value is below (to remeber the maximum value)
            if (isset ($this->_capabilities[$testString][$cap])){
                //Best value has changed.
                if (($this->_capabilities[$testString][$cap] > $currentValue)){
                    $currentValue = $this->_capabilities[$testString][$cap];
                }
                //last defined value
                $lastValue = $this->_capabilities[$testString][$cap];

            }
        }
        return (($lastValue === null) ? $currentValue : $lastValue);
    }

    /**
    * gets the max value in any of the subcapabilities, from the given path
    * @param $basepath the path we're starting our investigation from
    */
    function valueIn ($basePath, $cap){
        if ($this->isadmin_cgrp) {
            return PROFILE_CCV_ADMIN;
        }
        require_once (COPIX_PROFILE_PATH.'CopixProfileServicesFactory.class.php');
        $servicesCapability = & CopixProfileServicesFactory::createCapabilityServices ();
        $currentValue = $this->valueOf ($basePath, $cap);

        foreach ($this->_capabilities as $path=>$infos){
            if ($servicesCapability->checkBelongsTo ($basePath, $path)){
                $value = $this->valueOf ($path, $cap);//gets the value of cap in path

                if ($value > $currentValue){
                    $currentValue = $value;
                }
            }
        }
        return $currentValue;
    }

    /**
    * adds the capability to the list.
    * will replace any value in the list.
    */
    function setCapability ($path, $cap, $value){
        $this->_capabilities[$path][$cap] = $value;
    }

    /**
    * retire une capability du groupe
    */
    function removeCapability ($path, $cap){
        if (isset ($this->_capabilities[$path][$cap])){
            unset ($this->_capabilities[$path][$cap]);
        }
    }

    /**
    * ajoute des capacités en groupe, défini à une valeur donnée.
    */
    function addCapabilities ($paths, $value = PROFILE_CCV_READ){
        foreach ($paths as $path=>$capabilities) {
            foreach ($capabilities as $name=>$noGivenValue){
                $this->setCapability ($path, $name, $value);
            }
        }
    }

    /**
    * gets the list of capabilities
    * associative array key == value.
    */
    function getCapabilities () {
        return $this->_capabilities;
    }
}
?>