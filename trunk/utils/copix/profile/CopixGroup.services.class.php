<?php
/**
* @package   copix
* @subpackage profile
* @version   $Id: CopixGroup.services.class.php,v 1.13 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Services for the CopixGroup object.
* will handle load, saves
*/
class ServicesCopixGroup {
   /**
   * Will be called to saves the group into the database.
   * @param CopixGroup $group the group we wants to save (wether existing or not)
   * @return void
   */
   function save ($group) {
      if ($group->id_cgrp !== null){
         $this->update ($group);
      }else{
         $this->insert ($group);
      }
   }

   /**
   * updates an existing profile in the database.
   * @param CopixGroup group the group to save
   * @return void
   */
   function update ($group) {
      //clear users and capabilities first.
      $this->_removeUsers ($group->id_cgrp);
      $this->_removeCapabilities ($group->id_cgrp);

      //capabilities and users.
      $this->_saveCapabilities ($group);
      $this->_saveUsers ($group);

      //general informations.
      $daoGroup = & CopixDAOFactory::create ('copix:CopixGroup');
      $record   = & CopixDAOFactory::createRecord ('copix:CopixGroup');

      $record->id_cgrp   = $group->id_cgrp;
      $record->name_cgrp = $group->name_cgrp;
      $record->description_cgrp = $group->description_cgrp;
      $record->all_cgrp    = $group->all_cgrp;
      $record->known_cgrp  = $group->known_cgrp;
      $record->isadmin_cgrp  = $group->isadmin_cgrp;

      $daoGroup->update ($record);
   }

   /**
   * inserts a new profile in the database.
   * @param CopixGroup toSave the group to save
   * @return void
   */
   function insert ($toSave) {
      $daoGroup     = & CopixDAOFactory::create ('copix:CopixGroup');

      //insert the group itself.
      $group            = & CopixDAOFactory::createRecord ('copix:CopixGroup');
      $group->id_cgrp   = $this->_genId ();
      $group->name_cgrp = $toSave->name_cgrp;
      $group->description_cgrp = $toSave->description_cgrp;
      $group->all_cgrp    = $toSave->all_cgrp;
      $group->known_cgrp  = $toSave->known_cgrp;
      $group->isadmin_cgrp  = $toSave->isadmin_cgrp;

      $daoGroup->insert ($group);

      $toSave->id_cgrp = $group->id_cgrp;

      $this->_saveCapabilities ($toSave);
      $this->_saveUsers ($toSave);
   }

   /**
   * deletes an existing profile
   * @param int id the group id
   * @return void
   * @access public
   */
   function delete ($id) {
      $this->_removeCapabilities ($id);
      $this->_removeUsers ($id);

      $daoGroup  = & CopixDAOFactory::create ('copix:CopixGroup');
      $daoGroup->delete ($id);
   }

   /**
   * removes users from a given group
   * @param int $id the group id
   * @access private
   */
   function _removeUsers ($id) {
      $daoUserGroup  = & CopixDAOFactory::create ('copix:CopixUserGroup');
      $daoUserGroup->removeGroup ($id);
   }

   /**
   * removes all the capabilities from a given group
   * @param int id the group id
   * @access private
   */
   function _removeCapabilities ($id) {
      $daoGroupCap  = & CopixDAOFactory::create ('copix:CopixGroupCapabilities');
      $daoGroupCap->removeGroup ($id);
   }

   /**
   * generation of a random id that will be used as a group id
   * @return int
   */
   function _genId (){
      return date ('YmdHis').rand (10, 99);
   }

   /**
   * saves the capabilities of the group
   * @param CopixGroup $toSave the group we wants to save the capabilities of
   * @return void
   * @access private
   */
   function _saveCapabilities ($toSave){
      $daoGroupCap     = & CopixDAOFactory::create ('copix:CopixGroupCapabilities');
      $groupCapability = & CopixDAOFactory::createRecord ('copix:CopixGroupCapabilities');

      $groupCapability->id_cgrp = $toSave->id_cgrp;
      foreach ($toSave->getCapabilities () as $path=>$couple) {
         foreach ($couple as $name=>$value) {
            $groupCapability->name_ccpb  = $name;
            $groupCapability->value_cgcp = $value;
            $groupCapability->name_ccpt  = $path;

            $daoGroupCap->insert ($groupCapability);
         }
      }
   }

   /**
   * saves the users of the group
   * @param CopixGroup $toSave the group we wants to save the users of
   * @return void
   * @access private
   */
   function _saveUsers ($toSave){
      $daoUserGroup = & CopixDAOFactory::create ('copix:CopixUserGroup');
      $groupUser = & CopixDAOFactory::createRecord ('copix:CopixUserGroup');

      $groupUser->id_cgrp = $toSave->id_cgrp;
      foreach ($toSave->getUsers () as $login) {
         $groupUser->login_cusr = $login;
         $daoUserGroup->insert ($groupUser);
      }
   }
}
?>