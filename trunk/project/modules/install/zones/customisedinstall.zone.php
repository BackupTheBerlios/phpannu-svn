<?php
/**
* @package  copix
* @subpackage admin
* @version  $Id: customisedinstall.zone.php,v 1.2 2005/04/15 21:18:50 laurentj Exp $
* @author   Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * Ask wich modules to install
 */
class ZoneCustomisedInstall extends CopixZone {
    function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();

      $service = & CopixClassesFactory::create ('InstallService');
      if(isset($this->params['newInstall']))
          $tpl->assign('newInstall',$this->params['newInstall']);
      else
          $tpl->assign('newInstall',false);

      $tpl->assign ('arModules',$service->getModules ());
      $toReturn = $tpl->fetch ('install.customised.tpl');
    }
}
?>