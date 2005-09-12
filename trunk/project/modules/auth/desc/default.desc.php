<?php
/**
* @package	copix
* @subpackage auth
* @version	$Id: default.desc.php,v 1.4 2005/02/17 09:40:41 gcroes Exp $
* @author	Croes G�rald, Julien Mercier, Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

$in            = & new CopixAction ('Login', 'doLogin');//ask to log in
$out           = & new CopixAction ('Login', 'doLogout');//ask to be logged out

$login         = & new CopixAction ('Login', 'getLoginForm');

$default       = & $login;
?>