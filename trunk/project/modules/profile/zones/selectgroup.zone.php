<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: selectgroup.zone.php,v 1.4 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZoneSelectGroup extends CopixZone {
	function _createContent (&$toReturn) {
      //Création du sous template.
      $tpl = & new CopixTpl ();

      $daoGroups = & CopixDAOFactory::create ('copix:CopixGroup');
      $tpl->assign ('groups',      $daoGroups->findAll ());
      $tpl->assign ('select',      $this->params['select']);
      $tpl->assign ('back',        $this->params['back']);

      //fetch & end
      $toReturn = $tpl->fetch ('group.select.tpl');
      return true;
	}
}
?>