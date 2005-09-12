<?php
/**
* @package  copix
* @subpackage exemple
* @version   $Id: exemple.zone.php,v 1.3 2005/04/09 09:08:38 laurentj Exp $
* @author   Jouanneau Laurent, see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneExemple extends CopixZone {
   function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();
      $tpl->assign ('nom', $this->params['nom']);

      $url = $GLOBALS['COPIX']['COORD']->url;
      $url->delParam('nom');

      $tpl->assign('params', $url->params);
      $tpl->assign('url',$url->scriptName);

      // retour de la fonction :
      $toReturn = $tpl->fetch ('exemple.tpl');
      return true;
   }
}
?>
