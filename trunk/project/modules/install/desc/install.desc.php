<?php
/**
* @package  copix
* @subpackage admin
* @version  $Id: install.desc.php,v 1.2 2005/05/04 10:11:50 laurentj Exp $
* @author   Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
//Index of the admin panel.
if (defined('COPIX_INSTALL')) {
    $install          = & new CopixAction ('Install', 'getInstallConf');
    $validConnection  = & new CopixAction ('Install', 'doValidConnection');
    $prepareInstall   = & new CopixAction ('Install', 'doPrepareInstall');
    $defaultInstall   = & new CopixAction ('Install', 'doValidInstall');

    $getInstallKind   = & new CopixAction ('Install', 'getInstallKind');
    $customisedInstall= & new CopixAction ('Install', 'getCustomisedInstall');

    $installModules   = & new CopixAction ('Install', 'doInstallModules');
}else{
    /** (dés)installation multiple(s) **/
    $installModules   = & new CopixAction ('Install', 'doInstallModules',array ('profile|profile'=> new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
    /** (dés)installation one shot **/
    $installModule    = & new CopixAction ('Install', 'doInstallModule',array ('profile|profile'=> new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
}

$installReport    = & new CopixAction ('Install', 'getInstallReport');

$getAdmin         = & new CopixAction ('Install', 'getAdmin' ,array ('profile|profile'=> new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$manageModules    = & new CopixAction ('Install', 'getManageModules' ,array ('profile|profile'=> new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$confirmInstall   = & new CopixAction ('Install', 'doInstallModulesWithDependencies' ,array ('profile|profile'=> new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//home page
//$selectHomePage   = & new CopixActionZone('cms|SelectPage', array ('TITLE_PAGE'=>CopixI18N::get ('install.title.homePage'),'Params'=>array ('onlyLastVersion'=>1,'select'=>CopixUrl::get('install|install|setHomePage'), 'back'=>CopixUrl::get('install|install|getAdmin'))) ,array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$setHomePage      = & new CopixAction('Install','setHomePage', array ('profile|profile'=> new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

$default = & $getAdmin;
?>
