<?php
/**
* @package   copix
* @subpackage generaltools
* @version   $Id: CopixModule.class.php,v 1.16.2.1 2005/05/18 21:17:47 laurentj Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class CopixModule {

   /**
   * Delete php cache and empty database
   */
   function reset (){
      $cacheFile = CopixModule::_getCompiledFileName();
      if (is_file($cacheFile)) {
         unlink($cacheFile);
      }
      if ($GLOBALS['COPIX']['COORD']->getPlugin ('copixdb') !== null){
            $dao = CopixDAOFactory::create ('copix:CopixModule');
            $dao->deleteAll ();
      }
   }

    /**
    * gets the module list
    * @param boolean $restrictedList true if we only wants the installed modules.
    * @return array
    */
    function getList ($restrictedList = true){
        $toReturn = array ();

        if ($restrictedList === false) {
           $dir      = opendir (COPIX_MODULE_PATH);
           while (false !== ($file = readdir($dir))) {
               $complete = COPIX_MODULE_PATH . $file;
               if (CopixModule::isValid ($file)){
                  $toReturn[] = $file;
               }
           }
           closedir ($dir);
           clearstatcache();
        }else{
           $cacheFile = CopixModule::_getCompiledFileName();
           if (is_readable($cacheFile)) {
               include($cacheFile);
               $toReturn = $arModules;
           }else{
               CopixModule::_loadPHPCacheFromDatabase();
               if (is_readable($cacheFile)) {
                  include($cacheFile);
                  $toReturn = $arModules;
               }
           }
        }
        return $toReturn;
    }

    /**
    * Install all module in array if its possible
    * @params array $arModules
    * @return array
    */
    function install ($arModules) {
        $arError = array();
        foreach ($arModules as $module){
            CopixModule::_installOneModule ($module,$arModules,$arError);
        }
        require_once (COPIX_EVENTS_PATH.'CopixListenerFactory.class.php');
        CopixListenerFactory::clearCompiledFile ();
        return $arError;
    }

    /**
    * delete all module in array if its possible
    * @params array $arToDeleteModules
    * @return array
    */
    function delete ($arToDeleteModules) {
        $arError           = array();
        $arInstalledModule = CopixModule::getList();
        foreach ($arToDeleteModules as $toDelete){
            CopixModule::_deleteOneModule ($toDelete, $arToDeleteModules, $arInstalledModule, $arError);
        }
        require_once (COPIX_EVENTS_PATH.'CopixListenerFactory.class.php');
        CopixListenerFactory::clearCompiledFile ();
        return $arError;
    }

    /**
    * Install one module and its dependency if he could
    * @params array $arModule array of all module which could be install
    * @params string $module module to install
    * @params array $arError array of errors
    * @return boolean
    */
    function _installOneModule ($module,$arModules, & $arError) {
        $install           = true;
        $arInstalledModule = CopixModule::getList();

        //get the module config
        $toInstall         = CopixModule::getInformations($module);
        if (!(in_array($toInstall->name, $arInstalledModule))) {
            //check if the installation required another module installation
            if (count($toInstall->dependencies) > 0) {
                foreach ($toInstall->dependencies as $dependency){
                    if (!(in_array($dependency, $arInstalledModule))) {
                        if (in_array($dependency,$arModules)) {
                            if (!CopixModule::_installOneModule($dependency,$arModules,$arError)) {
                                //dependency can't be installed
                                $install = false;
                            }
                        }else{
                            //dependency is not install and is not in install array
                            $install = false;
                        }
                        if (!$install) {
                            $arError[] = CopixI18N::get ('copix:copixmodule.error.unableToInstall').' '.$toInstall->name.', '.CopixI18N::get ('copix:copixmodule.error.missingDependencies').' '.$dependency;
                        }
                    }
                }
            }
            if ($install) {
                $scriptFile = CopixModule::_getInstallFile($toInstall->name);
                if ($scriptFile) {
                    $ct = CopixDBFactory::getConnection () ;
                    $ct->doSQLScript($scriptFile);
                }else{
               }
                CopixModule::_addModuleInDatabase($toInstall->name);
                CopixModule::_addModuleInPHPCache($toInstall->name);
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    /**
    * compile to the hard drive.
    */
    function _addModuleInPHPCache ($moduleName){
         include (CopixModule::_getCompiledFileName());
         $arModules[] = $moduleName;//we assume arModules is the array of the compiled installed modules.
         CopixModule::_writeInPHPCache ($arModules);
    }

    /**
    * Load php file from database if database exists
    */
    function _loadPHPCacheFromDatabase () {
         if ($GLOBALS['COPIX']['COORD']->getPlugin ('copixdb') !== null){
            $dao = CopixDAOFactory::create ('copix:CopixModule');
            $arTemp = $dao->findAll();
            $arModules = array();
            foreach ($arTemp as $module){
               $arModules[] = $module->name_cpm;
            }
            CopixModule::_writeInPHPCache ($arModules);
         }
    }

    /**
    * add to database
    */
    function _addModuleInDatabase ($moduleName){
         //insert in database if we can
          if ($GLOBALS['COPIX']['COORD']->getPlugin ('copixdb') !== null){
             $dao    = CopixDAOFactory::create ('copix:CopixModule');
             $record = CopixDAOFactory::createRecord ('copix:CopixModule');
             $record->name_cpm = $moduleName;
             $dao->insert ($record);
          }
    }

    /**
    * compile to the hard drive.
    */
    function _deleteModuleInPHPCache ($moduleName){
         include (CopixModule::_getCompiledFileName());
         foreach ($arModules as $key=>$name){
            if ($name == $moduleName) {
               unset($arModules[$key]);
            }
         }
         CopixModule::_writeInPHPCache ($arModules);
    }

    /**
    * add to database
    */
    function _deleteModuleInDatabase ($moduleName){
         //insert in database if we can
          if ($GLOBALS['COPIX']['COORD']->getPlugin ('copixdb') !== null){
             $dao    = CopixDAOFactory::create ('copix:CopixModule');
             $dao->delete ($moduleName);
          }
    }

    /**
    * Delete one module if it's possible
    * @params array $arToDeleteModules array of all module which could be deleted
    * @params array $arInstalledModule array of all installed modules
    * @params string $module module to delete
    */
    function _deleteOneModule ($moduleName,$arToDeleteModules,$arInstalledModule, & $arError) {
        //check if there is a module which need the module to delete
        if (in_array($moduleName, $arInstalledModule)) {
            $delete      = true;
            foreach ($arInstalledModule as $installedModule){
                 $toCheck = CopixModule::getInformations($installedModule);

                 foreach ((array)$toCheck->dependencies as $dependency){
                     if ($dependency == $moduleName) {
                         if (in_array($installedModule, $arToDeleteModules)) { /* replace $installedModule->name_imd  par $installed_module*/
                             if (!CopixModule::_deleteOneModule ($installedModule, $arToDeleteModules, $arInstalledModule, $arError)) {  /* replace $installedModule->name_imd  par $installed_module*/
                                 //one module need the deleted module so we can't delete it ....
                                 $delete          = false;
                                 $arError[]       = CopixI18N::get ('copix:copixmodule.error.unableToDelete').' '.$moduleName.', '.CopixI18N::get ('copix:copixmodule.error.moduleIsNeededBy').' '.$toCheck->name;
                             }
                         }else{
                             //one module need the deleted module so we can't delete it ....
                             $delete          = false;
                             //die('kouik2 : '. implode($arToDeleteModules,' || ') . ' > ' .$installedModule );
                             $arError[]       = CopixI18N::get ('copix:copixmodule.error.unableToDelete').' '.$moduleName.', '.CopixI18N::get ('copix:copixmodule.error.moduleIsNeededBy').' '.$toCheck->name;
                         }
                     }
                 }
            }
            if ($delete) {
                $scriptFile = CopixModule::_getDeleteFile($moduleName);
                if ($scriptFile) {
                  $ct = CopixDBFactory::getConnection () ;
                  $ct->doSQLScript($scriptFile);
                }
                CopixModule::_deleteModuleInDatabase($moduleName);
                CopixModule::_deleteModuleInPHPCache($moduleName);
            }
            return $delete;
        }else{
            return true;
        }
    }
    /**
    *   Return the dependance Tree
    *    @param : string $moduleName : moduleName to use
    *    @param : string $todo : action (add // remove)
    *    @param : array $arModules : modules to remove/add
    */
    function _getDependenciesArray($moduleName, $toDo, $arModules){
         if ($toDo == "add") {
            $toCheck = CopixModule::getInformations($moduleName);
            foreach($toCheck->dependencies as $dependency){
               if( ! in_array($dependency, CopixModule::getList(true)) && ! in_array($dependency, $arModules) ) {
                     $arModules[] = $dependency;
                     $arModules = CopixModule::_getDependenciesArray($dependency,$toDo,$arModules);
               }
            }
         } elseif ($toDo == "remove") {
            $strResult ='';
            foreach(CopixModule::getList(true) as $installedModule){
               $toCheck = CopixModule::getInformations($installedModule);
               foreach((array)$toCheck->dependencies as $dependency){
                  if($dependency == $moduleName && !in_array($toCheck->name, $arModules)) {
                     $arModules[] = $toCheck->name;
                     $arModules = CopixModule::_getDependenciesArray($toCheck->name, $toDo, $arModules);
                  }
               }
            }
         }
         return $arModules;
    }

    /**
    * _getInstallFile
    *
    * Return  install.DBType.sql file for the modulePath
    * @params string $modulePath
    * @return scriptFile
    * @access private
    */
    function _getInstallFile ($modulePath) {
        // find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
        $pluginDB   = & $GLOBALS['COPIX']['COORD']->getPlugin ('CopixDb');
        $typeDB     = $pluginDB->config->profils['Select']->driver;

        // Search each module install file
        $scriptName ='install.'.$typeDB.'.sql';

        $SQLScriptFile = COPIX_MODULE_PATH . $modulePath . '/' . COPIX_INSTALL_DIR . 'scripts/' . $scriptName; // chemin et nom du fichier de script d'install
        if (file_exists($SQLScriptFile)) {
            return $SQLScriptFile;
        }else{
            return null;
        }
    }

    /**
    * _getDeleteFile
    *
    * Return  delete.DBType.sql file for the modulePath
    * @params string $modulePath
    * @return scriptFile
    * @access private
    */
    function _getDeleteFile ($modulePath) {
        // find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
        $pluginDB   = & $GLOBALS['COPIX']['COORD']->getPlugin ('CopixDb');
        $typeDB = $pluginDB->config->profils['Select']->driver;

        // Search each module install file
        $scriptName ='delete.'.$typeDB.'.sql';

        $SQLScriptFile = COPIX_MODULE_PATH . $modulePath . '/' . COPIX_INSTALL_DIR . 'scripts/' . $scriptName; // chemin et nom du fichier de script d'install
        if (file_exists($SQLScriptFile)) {
            return $SQLScriptFile;
        }else{
            return null;
        }
    }

    /**
    * Make a php file which contain array with installed modules
    */
    function _writeInPHPCache ($arModules) {
        $compiled = '<?php $arModules = array (';
        $first = true;
        foreach ($arModules as $name){
            if (!$first){
                $compiled .= ',';
            }
            $first = false;
            $compiled .= "'$name'";
        }
        $compiled .= '); ?>';

        require_once (COPIX_UTILS_PATH .'CopixFile.class.php');
        $objectWriter = & new CopixFile ();
        $objectWriter->write (CopixModule::_getCompiledFileName(), $compiled);
    }

    /**
    * gets the compiled file name.
    */
    function _getCompiledFileName (){
        return COPIX_CACHE_PATH.'copixmodule.php';
    }

    /**
    * gets the module info
    * @return object module informations
    */
    function getInformations ($moduleName){
        if (! CopixModule::isValid ($moduleName)){
            return null;
        }

        $toReturn = null;
        require_once (COPIX_UTILS_PATH.'CopixSimpleXml.class.php');
        $xmlParser = & new CopixSimpleXML ();
        $fileName = COPIX_MODULE_PATH.$moduleName.'/module.xml';

        if (!($parsedFile = $xmlParser->parseFile ($fileName))){
            $xmlParser->raiseError ();
        }

        if (isset($parsedFile->GENERAL)) {
            $defaultAttr    = $parsedFile->GENERAL->DEFAULT->__attributes;
            $toReturn->name = $defaultAttr['NAME'];

            CopixContext::push($toReturn->name);
            $toReturn->description     = CopixI18N::get($defaultAttr['DESCRIPTIONI18N']);
            $toReturn->longDescription = isset($defaultAttr['LONGDESCRIPTIONI18N']) ? CopixI18N::get($defaultAttr['LONGDESCRIPTIONI18N']) : '';
            CopixContext::pop();

            $toReturn->dependencies = array();
            if (isset($parsedFile->DEPENDENCIES)) {
                if (is_array ($parsedFile->DEPENDENCIES->DEPENDENCY)){
                    foreach ($parsedFile->DEPENDENCIES->DEPENDENCY as $dependency){
                        $attributes = $dependency->attributes ();
                        $toReturn->dependencies[] = $attributes['NAME'];
                    }
                }else{
                    $attributes = $parsedFile->DEPENDENCIES->DEPENDENCY->__attributes;
                    $toReturn->dependencies[] = $attributes['NAME'];
                }
            }
        }

        return $toReturn;
    }

    /**
    * gets the parameters for a given module
    * @return array
    */
    function getParameters ($moduleName){
        if (CopixModule::isValid($moduleName)){
            return CopixConfig::getParams($moduleName);
        }
        return array ();
    }

    /**
    * Check if the module has a correct name
    * Check (if trusted module is on) if the module name belongs to the trusted module list
    * Check if there is a module.xml file
    * Handles a cache as it is called very very very often
    */
    function isValid ($moduleName){
        static $okNames = array ();
        if (isset ($okNames[$moduleName])){
            return $okNames[$moduleName];
        }
        return $okNames[$moduleName] = CopixModule::_isValid ($moduleName);
    }

    /**
    * check if the module has a correct name
    * Check (if trusted module is on) if the module name belongs to the trusted module list
    * Check if there is a module.xml file
    */
    function _isValid ($moduleName){
        //Is the module name ok ?
        $safeModuleName = str_replace (array ('.', ';', '/', '\\', '>', '-', '[', ']', '(', ')', ' ', '&', '|'), '', $moduleName);
        if ($safeModuleName !== $moduleName){
            return false;
        }
        if (strlen (trim ($moduleName)) === 0){
            return false;
        }

        //double test on the directories to avoid bugs on the windows platform.
        $path = COPIX_MODULE_PATH.$moduleName;
        if (! (is_dir ($path) && !is_file ($path))){
            return false;
        }
        //Can we read the module.xml file ?
        if (!is_readable ($path.'/module.xml')){
            return false;
        }

        //check for the trusted module.
        $config = $GLOBALS['COPIX']['CONFIG'];
        if (($config->checkTrustedModules === true) && (!in_array ($moduleName, $config->trustedModules))){
            return false;
        }
        return true;
    }
}
?>
