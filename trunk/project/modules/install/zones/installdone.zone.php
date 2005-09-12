<?php
/**
* @package  copix
* @subpackage admin
* @version  $Id: installdone.zone.php,v 1.1 2005/04/11 21:32:24 laurentj Exp $
* @author   Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * zone installdone, affiche le résultat de l'installation.
 */
class ZoneInstallDone extends CopixZone {
    function _createContent (&$toReturn) {
        $tpl = & new CopixTpl ();
        if ($this->params['report'] != null) {
            $tpl->assign('report'   ,   $this->params['report']);
        }else{
            $tpl->assign('report'   ,   false);
        }
        $tpl->assign('newInstall'   ,   $this->params['newInstall']);
        // retour de la fonction :
      $toReturn = $tpl->fetch ('install.done.tpl');
        return true;
    }
}
?>