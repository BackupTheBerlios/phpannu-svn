<?php
/**
* @package   copix
* @subpackage profile
* @version   $Id: CopixUserProfile.class.php,v 1.17 2005/04/21 19:44:49 laurentj Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

require_once (COPIX_PROFILE_PATH.'CopixProfile.class.php');

/**
* the current user's profile.
*/
class CopixUserProfile {
   /**
   * The user's profile
   * @var object CopixProfile
   */
   var $profile = null;

   /**
   * singleton.
   * @return CopixUserProfile
   */
   function & instance () {
      static $me = false;
      if ($me === false) {
         $user = $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
         if ($user === null){
            trigger_error (CopixI18N::get ('copix:copix.error.plugin.unregister', 'auth|auth'),E_USER_ERROR);
          }
          $user = $user->getUser ();

          if ((intval (CopixConfig::get ('profile|keepProfileInSession')) === 1) && isset ($_SESSION['PLUGIN_SESSION_PROFILE']) && ($_SESSION['PLUGIN_SESSION_PROFILE']->user == $user->login)){
              $me = $_SESSION['PLUGIN_SESSION_PROFILE']->profile;
          }else{
            $me   = new CopixUserProfile ($user->login);
            if ((intval (CopixConfig::get ('profile|keepProfileInSession')) === 1)){
               $_SESSION['PLUGIN_SESSION_PROFILE']->profile = $me;
               $_SESSION['PLUGIN_SESSION_PROFILE']->user = $user->login;
            }
          }
      }
      return $me;
   }

   /**
   * gets the user login
   * @return string the user login.
   */
   function getLogin (){
      $userPlugin = & $GLOBALS['COPIX']['COORD']->getPlugin ('auth|auth');
      $user = $userPlugin->getUser ();
      return $user->login;
   }

   /**
   * returns max value in any of the subcapabilities
   * @param string basePath the path we wants to know the capability value of _from_
   *     eg we will check _all_ the subpath of basePath
   * @param string cap the capability id we wants to test
   * @return int the value
   * @access public
   */
   function valueIn ($basePath, $cap){
      $me = & CopixUserProfile::instance ();
      return $me->profile->valueIn ($basePath, $cap);
   }

   /**
   * gets the maximum value on a given path for the user.
   * @param string path the exact path we want to test
   * @param string cap the capability id we want to test
   * @return int the max value founded
   * @access public
   */
   function valueOf ($path, $cap){
      $me = & CopixUserProfile::instance ();
      return $me->profile->valueOf ($path, $cap);
   }

   /**
   * says if the user belongs to a given group
   * @param string $group the group id
   * @return boolean
   * @access public
   */
   function belongsTo ($group) {
      $me = & CopixUserProfile::instance ();
      return $me->profile->belongsTo ($group);
   }

   /**
   * gets the groups the user belongs to.
   * @access public
   * @return array of CopixGroups
   */
   function getGroups (){
      $me = & CopixUserProfile::instance ();
      return $me->profile->getGroups ();
   }

   /**
   * constructor. Will load the given profile.
   * @param string login the login of the user
   */
   function CopixUserProfile ($login) {
      $this->profile = & new CopixProfile ($login);
   }
}
?>