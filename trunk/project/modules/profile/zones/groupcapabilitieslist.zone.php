<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: groupcapabilitieslist.zone.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Show the list of the known capabilities
*/
class ZoneGroupCapabilitiesList extends CopixZone {
	/**
	* Attends un objet de type textpage en paramètre.
	*/
	function _createContent (&$toReturn) {
	   $tpl = & new CopixTpl ();

      $arAllCapabilitiesPath  = CopixProfileTools::getCapabilitiesPath ();
      //$capabilities       = CopixProfileTools::getCapabilities ();
      $capabilitiesPath = array ();
      //value of capabilities
      $values = array ();
      //adds capabilities to the paths.
      foreach ($arAllCapabilitiesPath as $path=>$capabilityPath){
         $capabilities = CopixProfileTools::getCapabilitiesForPath ($path);
         foreach ($capabilities as $capability){
            if ($capability->name_ccpb == $this->params['capability']) {
               $capabilitiesPath[$path] = $capabilityPath;
               $capabilitiesPath[$path]->currentValue = isset ($this->params['group']->_capabilities[$path][$this->params['capability']]) ? $this->params['group']->_capabilities[$path][$this->params['capability']] : null;
               $values = $capability->values_ccpb;
            }
         }
      }
      //Assigning values to the template
      $tpl->assign ('capabilityValues'      , $values);
      $tpl->assign ('arCapabilityPath'      , $capabilitiesPath);
      $tpl->assign ('arCapabilitiesCaptions', $this->_buildCapabilitiesValues ());
		$tpl->assign ('capability'            , $this->params['capability']);
      //appel du template.
		$toReturn = $tpl->fetch ('capability.list.tpl');
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
