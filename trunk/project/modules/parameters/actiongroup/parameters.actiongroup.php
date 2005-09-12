<?php
/**
* @package	copix
* @subpackage parameters
* @version	$Id: parameters.actiongroup.php,v 1.4 2005/02/09 08:29:09 gcroes Exp $
* @author	Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Administration of the parameters
*/
class ActionGroupParameters extends CopixActionGroup {
   /**
   * gets the default screen.
   */
   function getParameters () {
      $tpl = & new CopixTpl ();
      $tpl->assign ('TITLE_BAR', CopixI18N::get ('params.title'));

      $choiceModule = isset($this->vars['choiceModule']) ? $this->vars['choiceModule'] : false;
      $editParam    = isset($this->vars['editParam'])    ? $this->vars['editParam']    : false;

      $tpl->assign ('TITLE_PAGE', CopixI18N::get ('params.titlePage.admin'));
      $tpl->assign ('MAIN', CopixZone::process ('ShowParams', array ('choiceModule'=>$choiceModule,'editParam'=>$editParam)));

      return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
   }

   /**
   * apply updates
   */
   function doValid () {
      if (isset($this->vars['idFirst']) && isset ($this->vars['idSecond']) && isset($this->vars['value']) && CopixConfig::exists ($this->vars['idFirst'].'|'.$this->vars['idSecond'])){
          $this->vars['id'] = $this->vars['idFirst'].'|'.$this->vars['idSecond'];
          CopixConfig::set ($this->vars['id'], $this->vars['value']);
      }
      return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('parameters||', array ('choiceModule'=>$this->vars['choiceModule'])));
   }

   /**
   * Simple selection of a module.
   * (to avoid anoying message while going back or asking for a refresh)
   */
   function doSelectModule () {
      return new CopixActionReturn (COPIX_AR_REDIRECT, 
        CopixUrl::get ('parameters||', 
           isset ($this->vars['choiceModule']) ? array ('choiceModule'=>$this->vars['choiceModule']) : array ()));
   }
}
?>