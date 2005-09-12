<?php
/**
* @package	copix
* @subpackage htmleditor
* @version	$Id: cmshtmlareatools.actiongroup.php,v 1.4 2005/02/09 08:27:43 gcroes Exp $
* @author	Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ActionGroupCMSHtmlAreaTools extends CopixActionGroup {
   function getSelectPage (){
      $tpl = new CopixTpl ();

      $tpl->assign ('TITLE_PAGE', CopixI18N::get ('htmleditor.title.pageSelect'));
      if (isset($this->vars['popup'])) {
         $tpl->assign ('MAIN', CopixZone::process ('htmleditor|SelectPage', array ('onlyLastVersion'=>1, 'editorName'=>$this->vars['editorName'], 'popup'=>$this->vars['popup'])));
      }else{
         $tpl->assign ('MAIN', CopixZone::process ('htmleditor|SelectPage', array ('onlyLastVersion'=>1, 'editorName'=>$this->vars['editorName'])));
      }
      return new CopixActionReturn (COPIX_AR_DISPLAY_IN, $tpl, '|blank.tpl');
   }
}
?>
