<?php
/**
* @package	copix
* @subpackage admin
* @version	$Id: installconfirm.zone.php,v 1.1 2005/04/11 21:32:24 laurentj Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * zone installfront, formulaire pour renseigner la connexion à la base de donnée.
 */
class ZoneInstallConfirm extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();
		$toAdd = $this->params['toAdd'];
		$toDelete = $this->params['toDelete'];
		
      foreach($toAdd as $key=>$elem) {
         $toAdd[$key] = CopixModule::getInformations($elem);
      }

      foreach($toDelete as $key=>$elem) {
         $toDelete[$key] = CopixModule::getInformations($elem);
      }
      
      $tpl->assign('toAdd',$toAdd);
      $tpl->assign('toDelete',$toDelete);
      $toReturn = $tpl->fetch ('install.confirm.tpl');
		return true;
	}
}
?>