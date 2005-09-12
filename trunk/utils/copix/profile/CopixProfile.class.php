<?php
/**
* @package   copix
* @subpackage profile
* @version   $Id: CopixProfile.class.php,v 1.14 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class CopixProfile {

   /**
   * the groups the user belongs to
   * @var array of CopixGroup
   */
   var $_groups = array ();

   /**
   * The profile itself. Will load matching groups.
   * @param string $login the login we wants to load the profile of
   */
   function CopixProfile ($login) {
      $daoGroup = & CopixDAOFactory::create ('copix:CopixUserGroup');
      $sp       = & CopixDAOFactory::createSearchParams ();

      //specific groups.
      $sp->addCondition ('login_cusr', '=', $login);
      $groups = $daoGroup->findBy ($sp);
      foreach ($groups as $group) {
         $this->_groups[$group->id_cgrp] = & new CopixGroup ($group->id_cgrp);
      }

      //public or known user's groups
      $daoGroup = & CopixDAOFactory::create ('copix:CopixGroup');
      $sp       = & CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('all_cgrp', '=', 1);
      if ($login !== null) {
         $sp->addCondition ('known_cgrp', '=', 1, 'or');
      }

      $groups = $daoGroup->findBy ($sp);
      foreach ($groups as $group) {
         $this->_groups[$group->id_cgrp] = & new CopixGroup ($group->id_cgrp);
      }
   }

   /**
   * do we belongs to this group ?
   * @param string $groupName the group we wants to know if the profile belongs to
   * @return boolean
   */
   function belongsTo ($groupName){
      return isset ($this->_groups[$groupName]);
   }

   /**
   * gets the max value of the capability for the given path. We'll test every
   *   group of the profile.
   * @param $path the path we want to test
   * @param the capability we want to test in the given path
   * @return int the best Capability value founded
   */
   function valueOf ($path, $cap) {
      $currentValue = PROFILE_CCV_NONE;
      foreach ($this->_groups as $group) {
         $groupValue = $group->valueOf ($path, $cap);

         if ($currentValue < $groupValue) {
            $currentValue = $groupValue;
         }
      }
      return $currentValue;
   }

   /**
   * gets the max value in any of the subcapabilities
   * @param string $basePath the path we wants to know cap is in or not
   * @param string $cap the assumed child path
   * @return boolean
   * @access public
   */
   function valueIn ($basePath, $cap){
      $currentValue = PROFILE_CCV_NONE;

      foreach ($this->_groups as $group) {
         $groupValue = $group->valueIn ($basePath, $cap);
         if ($currentValue < $groupValue) {
            $currentValue = $groupValue;
         }
      }
      return $currentValue;
   }

   /**
   * gets the groups the profile belongs to.
   * @return array of CopixGroup
   * @access private
   */
   function getGroups () {
      return $this->_groups;
   }

   /**
   * gets the max value of basePath.$nodePath in any of the subcapabilities of basePath
   * @param $basePath the path we're starting our investigation from
   * @param $nodePath the node we're looking for
   * @return CCV_VALUE the best value founded
   */
/*
   function valueOfIn ($nodePath, $basePath, $stopLookingOnValue = PROFILE_CCV_ADMIN) {
      $currentValue = PROFILE_CCV_NONE;

      //trying all the groups the user belongs to.
      foreach ($this->_groups as $group) {
         $groupValue = $group->valueOfIn ($nodePath, $basePath, $stopLookingOnValue);
         if ($currentValue < $groupValue) {
            //found better, forget the old lower value.
            $currentValue = $groupValue;

            if ($currentValue >= $stopLookingOnValue){
               //we run into the best value we should go....
               //we can stop looking for it.
               return $currentValue;
            }
         }
      }

      return $currentValue;
   }
*/
}
?>