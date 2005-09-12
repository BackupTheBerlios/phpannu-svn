<?php
/**
* @package	copix
* @subpackage genericTools
* @version	$Id: messages.actiongroup.php,v 1.4.4.1 2005/08/08 22:12:19 laurentj Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ActionGroupMessages extends CopixActionGroup {
    /**
   * Display an error message
   */
    function & getError () {
        $tpl = & new CopixTpl ();

        $tpl->assign ('TITLE_PAGE', isset ($this->vars['TITLE_PAGE']) ? $this->vars['TITLE_PAGE'] : CopixI18N::get ('messages.titlePage.error'));
        $tpl->assign ('MAIN', CopixZone::process ('PassThrough', array    ('message'=>$this->vars['message'],
        'back'=>isset ($this->vars['back']) ? $this->vars['back'] : null,
        'template'=>'messages.error.tpl')
        ));

        return $return = & new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }
    /**
   * Display an error message
   */
    function & getConfirm () {
        $tpl = & new CopixTpl ();
        $tpl->assign ('TITLE_PAGE', isset ($this->vars['TITLE_PAGE']) ? $this->vars['TITLE_PAGE'] : CopixI18N::get ('messages.titlePage.confirm'));
        $tpl->assign ('MAIN', CopixZone::process ('PassThrough', array    ('title'=>$this->vars['title'],
        'message'=>$this->vars['message'],
        'confirm'=>$this->vars['confirm'],
        'cancel'=>$this->vars['cancel'],
        'template'=>'confirm.tpl')
        ));
        return $return = & new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }
}
?>