<?php
/**
* @package   copix
* @subpackage project
* @version   $Id: default.actiongroup.php,v 1.6 2005/02/15 07:59:50 gcroes Exp $
* @author   Croes G�rald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class ActionGroupDefault extends CopixActionGroup {
   function ActionGroupDefault (){
      parent::CopixActionGroup ();
   }

   /**
   * Pr�pare les donn�es pour la page d'accueil.
   */
   function getIndex () {
      //cr�ation de l'objet.
      $tpl = & new CopixTpl ();

      $tpl->assign ('MAIN', $GLOBALS['COPIX']['COORD']->includeStatic ('welcome|welcome.htm'));
      $tpl->assign ('TITLE_PAGE', CopixI18N::get ('copix:common.version').': '.COPIX_VERSION);

      //retour de la fonction.
      return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
   }
}
?>