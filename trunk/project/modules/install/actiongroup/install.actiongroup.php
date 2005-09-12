<?php
/**
* @package  copix
* @subpackage install
* @version  $Id: install.actiongroup.php,v 1.2.2.1 2005/05/18 21:15:12 laurentj Exp $
* @author   Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/*
* ActionGroup de gestion du process d'installation de la base
*
* Présente le form de connection à la base de donnée
* Exécute l'installation et redirige sur l'accueil du site
*/
class ActionGroupInstall extends CopixActionGroup {
    /**
    * Définit le chemin vers le fichier de def de la connexion à la base : profils.definition.xml
    */
    function ActionGroupInstall() {
        define('XML_COPIXDB_PROFIL',COPIX_PLUGINS_PATH . 'copixdb/profils.definition.xml');
    }

    /**
    * Gets the install form to configure DB connection
    */
    function getInstallConf () {
        $tpl = & new CopixTpl ();

        // show the DB connection form
        $tpl->assign ('TITLE_BAR', CopixI18N::get ('install.title.install'));
        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('install.title.install'));
        $tpl->assign ('MAIN', CopixZone::process ('install|install', array ('databaseNotOk'=>isset ($this->vars['databaseNotOk']))));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
    *   Create the connection file
    */
    function doValidConnection () {
        if(!isset($this->vars['configurerbd']) ||$this->vars['configurerbd'] != 'oui'){
            return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('install|install|installReport'));
        }

        $tpl     = & new CopixTpl ();
        $service = & CopixClassesFactory::Create ('InstallService');

        $service->setConn(htmlentities($this->vars['user_db']),
                          htmlentities ($this->vars['pass_db']),
                          htmlentities ($this->vars['host_db']),
                          htmlentities ($this->vars['db_db']),
                          htmlentities ($this->vars['type_db']));

        if($service->verifyConnection($this->vars['user_db'],
                          $this->vars['pass_db'],
                          $this->vars['host_db'],
                          $this->vars['db_db'],
                          $this->vars['type_db'])){
            return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('install|install|prepareInstall'));
        }else{
            return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('install|install|install', array('databaseNotOk'=>true)));
        }
    }

    /**
    * Prepare installation
    */
    function doPrepareInstall () {
        $service = & CopixClassesFactory::Create ('InstallService');
        if (! $service->checkDatabase ()){
            return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('install|install|install', array ('databaseNotOk'=>1)));
        }
        $service->prepareInstall ();

        // desactivation du choix du type d'installation qui ne fonctionne pas encore
        //return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('install|install|getInstallKind'));

        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('install|install|defaultInstall'));
    }

    /**
    *   Use the new connection file to connect to database and execute SQL create file
    */
    function doValidInstall () {
        $tpl     = & new CopixTpl ();
        $service = & CopixClassesFactory::Create ('InstallService');
        $report  = $service->installAll();
        $this->_setSessionInstallReport ($report);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('install|install|installReport'));
    }


    /**
    * Display installation report
    */
    function getInstallReport () {
        $tpl     = & new CopixTpl ();
        $report = $this->_getSessionInstallReport ();
        //if install is ok we rename install.php
        //unset($_SESSION['installProcess']);
        if (count($report) == 0) {
            touch (COPIX_LOG_PATH.'.installed');
        }

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('install.title.install'));
        $tpl->assign ('MAIN', CopixZone::process ('install|installdone',array('report'=>$report, 'newInstall'=>true)));
        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }


    /**
    * Get all installation kind
    */
    function getInstallKind () {
        if (!isset ($this->vars['installType'])){
            $tpl = & new CopixTpl ();
            $tpl->assign ('TITLE_BAR', CopixI18N::get ('install.title.installkind'));
            $tpl->assign ('TITLE_PAGE', CopixI18N::get ('install.title.installkind'));
            $tpl->assign ('MAIN', CopixZone::process ('install|installKind'));
            return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
        }else{
            if ($this->vars['installType'] == 'custom'){
                return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('install|install|customisedInstall'));
            }
            return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get ('install|install|defaultInstall'));
        }
    }

    /**
    * Get all installable module
    */
    function getCustomisedInstall () {
        $tpl = & new CopixTpl ();

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('install.title.customisedInstall'));
        $tpl->assign ('MAIN', CopixZone::process ('install|customisedInstall',array('newInstall'=>file_exists('install.php'))));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }


    /**
    * Install some modules given in parameters
    */
    function doInstallModules () {
        $service = & CopixClassesFactory::Create ('InstallService');
        $arModules   = $service->getModules ();

        $arToInstall = array ();
        $arToDelete  = array ();
        $report      = array ();

        //install un module si coché, et desinstall si décoché
        foreach ($arModules as $module){
            if (in_array($module->name,(array)$this->vars['arModules'])) {
                $arToInstall[] = $module->name;
            }elseif ($module->isInstalled === true){
                $arToDelete[]  = $module->name;
            }
        }

        $report = CopixModule::install ($arToInstall);
        $report = array_merge($report, CopixModule::delete ($arToDelete));
        $this->_setSessionInstallReport ($report);
        return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('install|install|installReport'));
    }

    /**
    * Install module given in parameter
    */
    function doInstallModule () {

        $service = & CopixClassesFactory::Create ('InstallService');
        $action = $this->vars['todo'];
        $tpl = & new CopixTpl();
        $toAdd = array();
        $toDelete = array();

        if($action=='add') {
           $toAdd = CopixModule::_getDependenciesArray($this->vars['moduleName'], $action, array());
           $toAdd[] = $this->vars['moduleName'];
           $this->_setSessionInstallModules($toAdd);
        } elseif ($action=='remove'){
           $toDelete = CopixModule::_getDependenciesArray($this->vars['moduleName'], $action, array());
           $toDelete[] = $this->vars['moduleName'];
           $this->_setSessionInstallModules($toDelete);
        }

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('install.title.manageModules'));
        $tpl->assign ('MAIN', CopixZone::process ('install|installconfirm', array('toAdd'=>$toAdd,'toDelete'=>$toDelete)));
        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
    * install or deinstall modules after confirmation
    */
    function doInstallModulesWithDependencies() {
       if($this->vars['todo'] == 'add') {
         CopixModule::install( $this->_getSessionInstallModules() );
       } elseif($this->vars['todo'] == 'remove') {
          CopixModule::delete( $this->_getSessionInstallModules() );
       }
       return new CopixActionReturn (COPIX_AR_REDIRECT, CopixUrl::get('install|install|manageModules'));
    }

    /**
    * get install screen
    */
    function getAdmin () {
        $tpl = & new CopixTpl ();

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('install.title.admin'));
        $tpl->assign ('MAIN', CopixZone::process ('install|admin'));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
    * get screen to add or delete modules
    */
    function getManageModules () {
        $tpl = & new CopixTpl ();

        $tpl->assign ('TITLE_PAGE', CopixI18N::get ('install.title.manageModules'));
        $tpl->assign ('MAIN', CopixZone::process ('install|customisedInstall'));

        return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
    }

    /**
    * Set the home page of the web site
    * @param $this->vars['id'] the page id for the home page.
    */
    function setHomePage () {
        if (!isset ($this->vars['id'])) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
                      array ('message'=>CopixI18N::get('error|error.specifyid'),
                      'back'=>'index.php?module=install&desc=install'));
        }
        CopixConfig::set ('|homePage',CopixUrl::get('cms|default|get', array('id'=>$this->vars['id'], 'online'=>'true')));
        return new CopixActionReturn(COPIX_AR_REDIRECT, CopixUrl::get('install|install|getAdmin'));
    }

    /**
    * gets the current report.
    * @access: private.
    */
    function _getSessionInstallReport () {
        return isset ($_SESSION['MODULE_ADMIN_INSTALL_REPORT']) ? unserialize ($_SESSION['MODULE_ADMIN_INSTALL_REPORT']) : null;
    }

    /**
    * sets the current report.
    * @access: private.
    */
    function _setSessionInstallReport ($toSet){
        $_SESSION['MODULE_ADMIN_INSTALL_REPORT'] = $toSet !== null ? serialize($toSet) : null;
    }

    /**
    * gets the modules to be install/deinstall.
    * @access: private.
    */
    function _getSessionInstallModules () {
        return isset ($_SESSION['MODULE_ADMIN_INSTALL_MODULES']) ? unserialize ($_SESSION['MODULE_ADMIN_INSTALL_MODULES']) : null;
    }

    /**
    * sets the modules to be install/deinstall.
    * @access: private.
    */
    function _setSessionInstallModules ($toSet){
        $_SESSION['MODULE_ADMIN_INSTALL_MODULES'] = $toSet !== null ? serialize($toSet) : null;
    }
}
?>