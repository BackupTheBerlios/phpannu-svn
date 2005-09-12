<?php
/**
* @package  copix
* @subpackage exemple
* @version   $Id: exemple.actiongroup.php,v 1.4 2005/04/15 21:18:50 laurentj Exp $
* @author   Jouanneau Laurent, see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * handle the exemple
 */
class ActionGroupExemple extends CopixActionGroup {

   /**
   * Gets the news list.
   */
   function getExemple () {
        $tpl = & new CopixTpl ();

        $main = '<p>'.CopixI18N::get ('exemple.defaultPageMessage').'</p>'
           . '<p><a href="'.CopixUrl::get('hello').'">'
           .CopixI18N::get ('exemple.clickHere').'</a> '.CopixI18N::get ('exemple.toSeeWelcome').'</p>';

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('exemple.title'));
        $tpl->assign ('MAIN',$main);

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

   /**
   * Gets a hello the world
   */
   function getHelloworld (){
    $tpl = & new CopixTpl ();

    $nom = isset ($this->vars['nom']) ? $this->vars['nom'] : 'World';

    $tpl->assign ('TITLE_PAGE', CopixI18N::get ('exemple.welcome.message'));
    $tpl->assign ('MAIN', $GLOBALS['COPIX']['COORD']->processZone ('exemple', array('nom'=>$nom)));
    return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }
}
?>