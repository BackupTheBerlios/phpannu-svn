<?php
/**
* @package   copix
* @subpackage project
* @version   $Id: default.desc.php,v 1.8 2005/04/06 21:04:09 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/*
$default = & new CopixActionRedirect (CopixConfig::get ('|homePage'));
*/
      
$default = & new CopixAction ('default','getIndex');
$affichenews   = & new CopixAction ('News', 'getIndex');

$toutespers = &new CopixAction ('pers', 'getListeToutesPers');
$detailpers = &new CopixAction ('pers', 'afficherDetail');
$supprpers = &new CopixAction ('pers', 'supprimer');
$ajouterpers = &new CopixActionZone  ('perssaisie');
$sauverpers = &new CopixAction  ('pers', 'sauver');
?>