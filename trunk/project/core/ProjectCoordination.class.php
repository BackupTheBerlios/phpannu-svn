<?php
/**
* @package   copix
* @subpackage project
* @version   $Id: ProjectCoordination.class.php,v 1.10 2005/04/05 22:14:32 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class ProjectCoordination extends CopixCoordination {
   function _processStandard (&$tplObject){
      $tplVars = $tplObject->getTemplateVars ();
      if (! isset ($tplVars['TITLE_PAGE'])){
         $tplVars['TITLE_PAGE'] = CopixConfig::get ('|titlePage');
         $tplObject->assign ('TITLE_PAGE', $tplVars['TITLE_PAGE']);
      }

      if (! isset ($tplVars['TITLE_BAR'])){
         $tplVars['TITLE_BAR'] = str_replace ('{$TITLE_PAGE}', $tplVars['TITLE_PAGE'], CopixConfig::get ('|titleBar'));
         $tplObject->assign ('TITLE_BAR', $tplVars['TITLE_BAR'] );
      }

      //if (!defined('COPIX_INSTALL')){
           //$tplObject->assign ('LOGIN_FORM', CopixZone::process ('auth|LoginForm'));
           //$tplObject->assign ('MENU', CopixZone::process ('menu_2|MenuFront'));
      //}
      if (! isset ($tplVars['MENU'])){
          $tplVars['MENU'] = '';
          $tplObject->assign ('MENU', '');
      }
   }
}
?>
