<?php
/**
* @package  copix
* @subpackage profile
* @version  $Id: profile.plugin.conf.php,v 1.6.2.1 2005/07/30 09:52:33 laurentj Exp $
* @author   Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

define ('PROFILE_CCV_NONE',     0);
define ('PROFILE_CCV_SHOW',     10);
define ('PROFILE_CCV_READ',     20);
define ('PROFILE_CCV_WRITE',    30);
define ('PROFILE_CCV_VALID',    40);
define ('PROFILE_CCV_PUBLISH',  50);
define ('PROFILE_CCV_MODERATE', 60);
define ('PROFILE_CCV_ADMIN',    70);

class PluginConfigProfile {
    var $noRightsExecParams = null;
    function PluginConfigProfile (){
       $this->noRightsExecParams = & new CopixAction ('auth|Login', 'getLoginForm');
    }
}
?>