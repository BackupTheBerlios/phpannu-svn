<?php
/**
* @package	copix
* @subpackage genericTools
* @version	$Id: confirmdialog.zone.php,v 1.4 2005/02/09 08:27:43 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneConfirmDialog extends CopixZone {
   function _createContent (& $toReturn){
      $tpl = & new CopixTpl ();
      $tpl->assign ('title', isset ($this->params['title']) ? $this->params['title'] : CopixI18N::get ('messages.titlePage.confirm'));

      if (isset ($this->params['message'])){
         $tpl->assign ('message', $this->params['message']);
      }
      
      $tpl->assign ('confirm', $this->params['confirm']);
      $tpl->assign ('cancel', $this->params['cancel']);
      $toReturn = $tpl->fetch ('genericTools|confirm.tpl');
      
      return true;
   }
}
?>
