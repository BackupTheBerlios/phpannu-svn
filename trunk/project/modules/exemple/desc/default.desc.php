<?php
/**
* @package	copix
* @subpackage exemple
* @version   $Id: default.desc.php,v 1.3 2005/03/07 13:23:17 gcroes Exp $
* @author	Jouanneau Laurent, see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
$hello      = & new CopixAction ('Exemple', 'getHelloWorld');
$example    = & new CopixAction ('Exemple', 'getExemple');
$default    = & $example;
?>