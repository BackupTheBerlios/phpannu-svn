<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: groupedit.zone.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Shows a given user group.
*/
class ZoneGroupEdit extends CopixZone {
	/**
	* @param $this->params['group']
	*/
	function _createContent (&$toReturn){
	   $tpl = & new CopixTpl ();

      $arTempCapabilities = CopixProfileTools::getCapabilities ();
      $arCapabilities     = array ();
      //make array of current capability
      foreach ($this->params['group']->_capabilities as $path=>$currentCapabilities){
         foreach ($arTempCapabilities as $index=>$capability){
            if (isset($currentCapabilities[$capability->name_ccpb])) {
               $arCapabilities[$index] = $capability;
            }
         }
      }

      //assignation de la liste des profils connus.
      $tpl->assign ('group',                  $this->params['group']);
      $tpl->assign ('arCapabilitiesCaptions', $this->_buildCapabilitiesValues ());
      $tpl->assign ('arCapabilities',         $arCapabilities);
      $tpl->assign ('arCapabilitiesPath',     $arCapabilitiesPath   = CopixProfileTools::getCapabilitiesPath ());

		//appel du template.
		$toReturn = $tpl->fetch ('group.edit.tpl');
		return true;
	}

   /**
   * builds the capabilities captions list
   */
   function _buildCapabilitiesValues (){
      $ar [PROFILE_CCV_NONE]     = CopixI18N::get ('copix:profile.capabilities.none');
      $ar [PROFILE_CCV_SHOW]     = CopixI18N::get ('copix:profile.capabilities.show');
      $ar [PROFILE_CCV_READ]     = CopixI18N::get ('copix:profile.capabilities.read');
      $ar [PROFILE_CCV_WRITE]    = CopixI18N::get ('copix:profile.capabilities.write');
      $ar [PROFILE_CCV_VALID]    = CopixI18N::get ('copix:profile.capabilities.valid');
      $ar [PROFILE_CCV_PUBLISH]  = CopixI18N::get ('copix:profile.capabilities.publish');
      $ar [PROFILE_CCV_MODERATE] = CopixI18N::get ('copix:profile.capabilities.moderate');
      $ar [PROFILE_CCV_ADMIN]    = CopixI18N::get ('copix:profile.capabilities.admin');

      return $ar;
	}
}
?>
