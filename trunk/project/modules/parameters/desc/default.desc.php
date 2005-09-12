<?php
/**
* @package	copix
* @subpackage parameters
* @version	$Id: default.desc.php,v 1.4 2005/02/09 08:29:09 gcroes Exp $
* @author	Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

$parameters   = & new CopixAction ('Parameters', 'getParameters',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$valid        = & new CopixAction ('Parameters', 'doValid',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$selectModule = & new CopixAction ('Parameters', 'doSelectModule',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

$default    = & $parameters;
?>
