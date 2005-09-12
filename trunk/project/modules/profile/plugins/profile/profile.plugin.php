<?php
/**
* @package  copix
* @subpackage profile
* @version  $Id: profile.plugin.php,v 1.7 2005/04/15 21:18:50 laurentj Exp $
* @author   Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
if (!defined ('COPIX_PROFILE_PATH')){
   define ('COPIX_PROFILE_PATH', COPIX_PATH.'profile/');
}

require_once (COPIX_PROFILE_PATH.'CopixGroup.class.php');
require_once (COPIX_PROFILE_PATH.'CopixUserProfile.class.php');
require_once (COPIX_PROFILE_PATH.'CopixProfileTools.class.php');
require_once (COPIX_PROFILE_PATH.'CopixProfileServicesFactory.class.php');

/**
* Profile plugin, to handle complex right system
*/
class PluginProfile extends CopixPlugin {
   /**
   * Test if the CopixUserProfile match the required Profile informations (if asked)
   */
   function beforeProcess (& $execParams) {
      if (isset ($execParams->params['profile|profile'])) {
         if (is_a ($execParams->params['profile|profile'], 'CapabilityValueOf')){
            $ok =  CopixUserProfile::valueOf ($execParams->params['profile|profile']->path, $execParams->params['profile|profile']->cap) >= $execParams->params['profile|profile']->value;
         }else if (is_a ($execParams->params['profile|profile'], 'CapabilityValueIn')){
            $ok =  CopixUserProfile::valueIn ($execParams->params['profile|profile']->basePath, $execParams->params['profile|profile']->cap) >= $execParams->params['profile|profile']->value;
         }else{
            $ok = true;
         }
         if (! $ok){
            $execParams = $this->config->noRightsExecParams;
            return false;
         }
         return true;
      }
      return true;
   }
}

/**
* to describe a single path/cap value
*/
class CapabilityValueOf {
   /**
   * the path we'll look for
   * @var string
   */
   var $path = null;

   /**
   * the capability we wants to test
   * @var string
   */
   var $cap;

   /**
   * the value we needs to match
   * @var int
   */
   var $value;

   /**
   * constructor
   * @param path the path we're looking for.
   */
   function CapabilityValueOf ($path, $cap, $value){
      $this->path  = $path;
      $this->cap   = $cap;
      $this->value = $value;
   }
}

/**
* To test cap in all subpath of path, including path.
*/
class CapabilityValueIn {
   /**
   * the path we'll look into
   * @var string
   */
   var $basePath = null;

   /**
   * the capability we wants to test
   * @var string
   */
   var $cap;

   /**
   * the value we needs to match
   * @var int
   */
   var $value;

   /**
   * Constructor
   */
   function CapabilityValueIn ($basePath, $cap, $value){
      $this->basePath = $basePath;
      $this->cap      = $cap;
      $this->value    = $value;
   }
}
?>