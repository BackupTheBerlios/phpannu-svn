<?php
/**
* @package	copix
* @subpackage users
* @version	$Id: admin.desc.php,v 1.4 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
$list        = & new CopixAction ('AdminUsers', 'getList',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

$valid       = & new CopixAction ('AdminUsers', 'doValid',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$prepareEdit = & new CopixAction ('AdminUsers', 'doPrepareEdit',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$delete      = & new CopixAction ('AdminUsers', 'doDelete',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$create      = & new CopixAction ('AdminUsers', 'doCreate',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$edit        = & new CopixAction ('AdminUsers', 'getEdit',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

$removeGroup = & new CopixAction ('AdminUsers', 'doRemoveGroup',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$selectGroup = & new CopixAction ('AdminUsers', 'getSelectGroup',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$addGroup    = & new CopixAction ('AdminUsers', 'doAddGroup',
                        array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

$default = & $list;
?>
