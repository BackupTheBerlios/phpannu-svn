<?php
/**
* @package	copix
* @subpackage admin
* @version	$Id: installkind.zone.php,v 1.1 2005/04/11 21:32:24 laurentj Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * Ask wich install to perform
 */
class ZoneInstallKind extends CopixZone {
	function _createContent (&$toReturn) {
		$tpl = & new CopixTpl ();
      $toReturn = $tpl->fetch ('install.kind.tpl');
	}
}
?>