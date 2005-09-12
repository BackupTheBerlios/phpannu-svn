<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixConfig.class.php,v 1.26.2.2 2005/08/01 22:17:54 laurentj Exp $
* @author   Croes Gérald, Bertrand Yan
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class CopixGroupConfig {
    /**
    * the group name
    */
    var $group      = null;

    /**
    * the config vars names
    */
    var $_configVars = array ();

    /**
    * constructor.
    */
    function CopixGroupConfig ($name){
        $this->group = $name;
        $this->_load ();
    }

    /**
    * loads the group (will choose xml / database)
    */
    function _load (){
        if ($this->_needsCompilation ()){
            $this->_loadFromXML ();
            $this->_loadFromDatabase ();
            $this->_save ();
        }else{
            $this->_loadFromPHPCache ();
        }
    }

    /**
    * compilation
    */
    function _save (){
        //on sauvegarde n base de données pour ne pas à relire le fichier xml à chaque fois
        $this->_writeInDatabase ();
        $this->_writeInPHPCache ();
    }

    /**
    * loads the vars from the PHP Cache
    */
    function _loadFromPHPCache (){
        require ($this->_getCompiledFileName());
        //$this->_configVars = unserialize (stripslashes ($vars));//we assume vars is the varname of the compiled config variables.
        $this->_configVars = $_load;
    }

    /**
    * Says if the php file needs to be refresh
    * @return boolean
    */
    function _needsCompilation () {
        //force compilation ?
        if ($GLOBALS['COPIX']['CONFIG']->compile_config_forced){
            return true;
        }

        //check if the file exists.
        if (!is_readable ($this->_getCompiledFileName())){
            return true;
        }
        //don't check the compiled file
        if ($GLOBALS['COPIX']['CONFIG']->compile_config_check === false){
            return false;
        }

        //we needs to compile if the xml file is newer than de PHPCache file
        $select = & CopixSelectorFactory::create ($this->group.'module.xml');
        if (!$select->isValid){
            trigger_error ('Invalid config file');
        }
        return filemtime ($select->getPath ($this->group === '|' ? 'project.xml' : 'module.xml')) > filemtime ($this->_getCompiledFileName());
    }

    /**
    * compile to the hard drive.
    */
    function _writeInPHPCache (){
        /**
        $compileString = '<?php $vars=\''.addslashes (serialize ($this->_configVars)).'\'; ?>';
        */
        //we want to use the PHP compilation of the resources.
        $first = true;
        $_resources = '<?php $_load=array (';
        foreach ($this->_configVars as $key=>$elem){
            if (!$first){
                $_resources .= ', ';
            }

            $elemString = 'array (';
            $firstElem = true;
            foreach ($elem as $elemKey=>$elemValue){
                if (!$firstElem){
                    $elemString .= ', ';
                }
                $elemString .= '\''.str_replace ("'", "\\'", $elemKey).'\'=>\''.str_replace ("'", "\\'", $elemValue).'\'';
                $firstElem = false;
            }
            $_resources .= '\''.str_replace ("'", "\\'", $key).'\'=>'.$elemString.')';
            $first = false;
        }
        $_resources .= '); ?>';

        require_once (COPIX_UTILS_PATH . 'CopixFile.class.php');
        $objectWriter = & new CopixFile ();
        $objectWriter->write ($this->_getCompiledFileName (), $_resources);
    }

    /**
    * gets the compiled file name.
    */
    function _getCompiledFileName (){
        return $GLOBALS['COPIX']['CONFIG']->compile_config_dir.str_replace (array ('|', ':'), array ('_M_', '_K_'), $this->group);
    }

    /**
    * Get the configVars from dao group array
    * We will not load values that do not exists in the XML file.
    * We will only load the values of the config variables, not their captions or so.
    * We remind that the database here is just a _Saving_ purpose in case the "temp" directory is deleted.
    * We will test the presence of the CopixDB plugin to store values in the database.
    */
    function _loadFromDatabase () {
        if (! $this->_checkDBConnection ()){
            return;
        }

        $dao = & CopixDAOFactory::create ('copix:CopixConfig');
        $sp  = & CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('group_ccfg','=',$this->group);

        $arVars = $dao->findBy ($sp);
        foreach ($arVars as $vars) {
            if (isset ($this->_configVars[$vars->id_ccfg])){
                $this->_configVars[$vars->id_ccfg]['Value']  = $vars->value_ccfg;
            }
        }
    }

    /**
    * Check if we have a correct DB Connection
    */
    function _checkDBConnection (){
        static $dbOk = null;
        if ($dbOk === null){
            //We also check if we're installing or not
            if (defined ('COPIX_INSTALL')){
                return $dbOk = false;
            }

            if ($GLOBALS['COPIX']['COORD']->getPlugin ('copixdb') === null){
                $dbOk = false;
                return false;
            }
            $ct = CopixDBFactory::getConnection ();
            $dbOk = $ct->isConnected ();
            return $dbOk;
        }
        return $dbOk;
    }

    /**
    * Save config in the database
    */
    function _writeInDatabase () {
        if (! $this->_checkDBConnection ()){
            return;
        }

        $dao = & CopixDAOFactory::create ('copix:CopixConfig');

        foreach ($this->_configVars as $attribute){
            $toInsert               = & CopixDAOFactory::createRecord ('copix:CopixConfig');
            $toInsert->id_ccfg      = $this->group.$attribute['Name'];
            $toInsert->group_ccfg   = $this->group;
            $toInsert->value_ccfg   = $attribute['Value'];

            if ($dao->get ($toInsert->id_ccfg) === false){
                $dao->insert ($toInsert);//did not exists before
            }else{
                $dao->update ($toInsert);//updates the DB values
            }
        }
    }

    /**
    * gets a value from the config file (the current group)
    */
    function get ($id) {
        if (isset ($this->_configVars[$id])){
            return $this->_configVars[$id]['Value'];
        }else{
            trigger_error ('unknow variable '.$id);
        }
    }

    /**
    * check if the given param exists.
    */
    function exists ($id){
        return isset ($this->_configVars[$id]);
    }

    /**
    * gets the list of known params.
    */
    function getParams (){
        return $this->_configVars;
    }

    /**
    * saves the value for id, will compile if different from the actual value.
    */
    function set ($id, $value){
        //if the config var exists only....
        if (isset ($this->_configVars[$id])){
            $this->_configVars[$id]['Value'] = $value;//Sets the value itself.

            //Update the value in the file.
            $this->_configVars[$id]['Value'] = $value;

            //Saves changes in the database
            if ($this->_checkDBConnection ()){
                $dao      = & CopixDAOFactory::create ('copix:CopixConfig');
                $toInsert               = & CopixDAOFactory::createRecord ('copix:CopixConfig');
                $toInsert->id_ccfg      = $id;
                $toInsert->group_ccfg   = $this->group;
                $toInsert->value_ccfg   = $value;

                if ($dao->get ($toInsert->id_ccfg) === false){
                    $dao->insert ($toInsert);//did not exists before
                }else{
                    $dao->update ($toInsert);//updates the DB values
                }
            }

            //Saves changes in the PHP File
            $this->_writeInPHPCache();
        }else{
            trigger_error ('unknow variable '.$id.' not set.');
        }
    }

    /**
    * load from xml
    */
    function _loadFromXML (){
        require_once (COPIX_UTILS_PATH . 'CopixSimpleXml.class.php');
        $configFile = ($this->group === '|' ? 'project.xml' : 'module.xml');

        $select = & CopixSelectorFactory::create ($this->group . $configFile);
        if (!$select->isValid){
            trigger_error ('Invalid config file');
        }

        //checks if the file exists
        $fileName = $select->getPath ($configFile);
        if (! is_readable ($fileName) ){
            return false;
        }

        $this->_configVars = array();
        $parser   = & new CopixSimpleXml();

        if (! ($xml = $parser->parseFile($fileName))){
            $parser->raiseError ();
        }

        if (isset ($xml->PARAMETERS->PARAMETER)){
            foreach (is_array ($xml->PARAMETERS->PARAMETER) ? $xml->PARAMETERS->PARAMETER : array ($xml->PARAMETERS->PARAMETER) as $key=>$child){
                $attributes = $child->attributes ();
                //we stores in a key with the following format module|attributeName
                $this->_configVars[$this->group.$attributes['NAME']] = array ('Name'=>$attributes['NAME'],'Caption'=>$attributes['CAPTIONI18N'],'Default'=>$attributes['DEFAULT'],'Value'=>$attributes['DEFAULT']);
            }
        }
    }
}

/**
* fichier de configuration principal du framework
* definit une classe dont les propriétés representent tout les paramètres
* du framework, avec leurs valeurs par défaut.
* Pour indiquer des valeurs spécifiques, il faut le faire via le fichier
* de configuration copix.conf.php
* @package   copix
* @subpackage core
*/
class CopixConfig {
    /* ========================================= paramètres généraux */

    /**
    * indique si le système d'autorisation des modules est activé
    * @var boolean
    */
    var $checkTrustedModules = false;

    /**
    * liste des modules autorisés
    * 'nom_du_module'=>true/false
    * @var array
    */
    var $trustedModules = array();

    /**
    * indique si on active le gestionnaire d'erreur de Copix
    */
    var $errorHandlerOn = true;

    /**
   * fichier de configuration du gestionnaire d'erreur de Copix
   */
    var $errorHandlerConfigFile='errorhandler.conf.php';

    /**
     * objet de configuration pour le handler des erreurs (instancié par CopixCoordination)
     * @var CopixErrorHandlerConfig
     */
    var $errorHandler = null;

    /* ========================================= infos plugins */

    /**
    * tableau contenant les noms des plugins enregistrés.
    * array( 'nomplugin', 'nomplugin2', 'nomPlugin3', ...)
    * @var array
    */
    var $plugins = array ();

    /**
    * Si la fonctionnalité de cache est autorisée
    * @var boolean
    * @see CopixCache
    */
    var $cacheEnabled = true;

    /**
    * Liste des autorisation de cache.
    * @var array
    * @see CopixCache
    */
    var $cacheTypeEnabled = array ();

    /**
    * Répertoires des différents types de cache.. terminer le nom par un / ou \
    * n'indiquer que le chemin relatif au chemin des caches définit dans la constante COPIX_CACHE_PATH
    * @var array
    * @see CopixCache
    */
    var $cacheTypeDir = array ();

    /**
    * type de cache par défaut
    */
    var $defaultCache = 'Default';
    /**
    * Utilisation d'un système de lock ?
    * @var boolean
    * @see CopixCache
    */
    var $useLockFile = true;

    /**
    * Automatic DAO
    */
    var $compile_dao_dir    = '';
    var $compile_dao_check  = true;
    var $compile_dao_forced = false;

    /* =========================================  internationalisation */

    /**
    * code langage par defaut
    * @var string
    */
    var $default_language = 'fr';

    /**
    * code pays par defaut
    * @var string
    */
    var $default_country  = 'FR';

    /**
    * Indique si l'on vérifie la compilation des ressources ou non
    */
    var $compile_resource_check = true;

    /**
    * Indique si l'on force la compilation des ressources ou non.
    */
    var $compile_resource_forced = false;

    /**
    * repertoire où sont stockés les ressources compilées
    * @var string
    */
    var $compile_resource_dir = '';

    /* =========================================  paramétrages du moteur de template */

    /**
    * chemin d'un sous repertoire à partir du repertoire de template
    * sous forme de liste de repertoire
    * est déstiné à être modifié dynamiquement par les plugins (d'internationalisation par exemple)
    */
    var $tpl_sub_dirs = array();

    /**
    * chemin vers les templates
    * @var string
    */
    var $template_dir   = '';

    /**
    * indique si mode debuggage
    * @var boolean
    */
    var $debugging      = false;

    /**
    * indique si le moteur de template doit verifier la compilation (smarty)
    * @var boolean
    */
    var $compile_check  = true;

    /**
    * indique si il faut toujours recompiler
    * @var boolean
    */
    var $force_compile  = false;

    /**
    * indique si il faut mettre en cache le resultat du template
    * @var int
    */
    var $caching        = 0;

    /**
    * chemin vers le repertoire de cache pour le moteur de template
    * @var string
    */
    var $cache_dir      = '';

    /**
    * chemin vers les templates compilés (smarty)
    * @var string
    */
    var $compile_dir    = '';

    /**
    * Doit on utiliser des sous répertoires pour la compilation des templates.
    * (Smarty uniquement)
    */
    var $use_sub_dirs   = false;

    /**
    * nom du fichier template principal
    * @var string
    */
    var $mainTemplate = 'main.ptpl';

    var $config_dir = './configs';

    var $compile_config_dir='';

    //---------------------------------------------------------------
    //---------------------------------------------------------------
    //--End of config file.
    var $configGroups = array ();

    //----------------------------------------------------------------
    // EXPERIMENTAL, support des urls significatifs
    var $significant_url_mode = 'none'; // "none" (inactif) ou "prepend" (actif)
    var $significant_url_prependIIS_path_key = '__COPIX_SIGNIFICANT_URL__';
    var $stripslashes_prependIIS_path_key    = true;

    /**
    * enregistrement d'un plugin
    * @param string  $name   nom du plugin
    * @param string $conf   nom d'un fichier de configuration alternatif. si chaine vide = celui par défaut
    */
    function registerPlugin($name, $conf=null){
       $this->plugins[$name] = $conf;
    }

    /**
    * Singleton.
    */
    function & instance (){
        static $me = false;
        if ($me === false){
            $me = new CopixConfig ();
        }
        return $me;
    }

    /**
    * gets the value of a parameter
    * @param id - string [module]|name
    * @return string
    */
    function get ($id) {
        if (($pos = strpos ($id, '|')) === false){
            $id    = CopixContext::get ().'|'.$id;
            $group = CopixContext::get ().'|';
        }else{
            $group = substr ($id, 0, $pos).'|';
        }
        //echo "GROUPE : $group <br />";
        //echo $id;
        /*
        $select = CopixSelectorFactory::create ($id);
        if (!$select->isValid) {
        trigger_error ('Invalid selector'.$id, E_USER_ERROR);
        }
        */
        $me    = & CopixConfig::instance ();
        $group = & $me->_getGroupConfig ($group);

        return $group->get ($id);
    }

    /**
    * check if the given param exists.
    */
    function exists ($id){
        if (($pos = strpos ($id, '|')) === false){
            $id    = CopixContext::get ().'|'.$id;
            $group = CopixContext::get ().'|';
        }else{
            $group = substr ($id, 0, $pos).'|';
        }
        $me    = & CopixConfig::instance ();
        $group = & $me->_getGroupConfig ($group);
        return $group->exists ($id);
    }

    /**
    * gets all parameters
    * @param group - string [module]
    * @return array
    */
    function getParams ($groupName) {
        //Is the module name valid ? or is it the project we wants to get the parameters of ?
        if (!($groupName === null ||  CopixModule::isValid ($groupName))){
            return array ();
        }
        $me    = & CopixConfig::instance ();
        $group = & $me->_getGroupConfig ($groupName.'|');
        return $group->getParams ();
    }

    /**
    * sets the value of a parameter
    */
    function set ($id, $value){
        if (($pos = strpos ($id, '|')) === false){
            $id    = CopixContext::get ().'|'.$id;
            $group = CopixContext::get ().'|';
        }else{
            $group = substr ($id, 0, $pos).'|';
        }
        $me    = & CopixConfig::instance ();
        $group = & $me->_getGroupConfig ($group);
        return $group->set ($id, $value);
    }

    /**
    * gets a CopixGroupConfig. Handle single instance to avoid multiple loadings.
    * @param $kind - the kind of group we wants to load (moduleName, copix:, plugin:name, ..., ...)
    * @return CopixConfigGroup
    */
    function & _getGroupConfig ($kind){
        if (isset ($this->_configGroup[$kind])){
            return $this->_configGroup[$kind];
        }else{
            $this->_configGroup[$kind] = & new CopixGroupConfig ($kind);
            return $this->_configGroup[$kind];
        }
    }

    /**
    * Gets the real path of a given path
    */
    function getRealPath($path){
        return realpath ($path);

        // Check if path begins with "/". => use === with strpos
        /*if (strpos($path,"/") === 0) {
            return $path;
        } else {
            // Strip slashes and convert to arrays.
            $currentDir = preg_split("/\//",dirname($_SERVER['PATH_TRANSLATED']));
            $newDir     = preg_split('/\//',$path);
            // Drop one directory from the array for each ".."; add one otherwise.
            //if windows drop the last element of currentDir ???
            if (strtoupper(substr(PHP_OS, 0,3) == 'WIN')) {
                array_pop($currentDir);
            }
            foreach ($newDir as $dir) {
                if ($dir == ".."){
                    array_pop($currentDir);
                }elseif ($dir != "."){
                    //test for "." which represents current dir (do nothing in that case)
                    array_push($currentDir,$dir);
                }
            }
            // Return the slashes.
            $path = implode ($currentDir, "/");
        }
        return $path;*/
    }


    /**
    * Gets the operating system of the server
    * @return string name of the operating System
    */
    function getOSName (){
    	static $osString = false;
    	if ($osString === false){
    		$osString = substr (PHP_OS, 0, (($pos = strpos (PHP_OS, ' ')) === false) ? strlen (PHP_OS) : $pos);
    	}
    	return $osString;
    }

    /**
    * Says if the OS is windows or not
    * @return boolean
    */
    function osIsWindows (){
    	static $checked   = false;
    	static $isWindows = false;
    	if (!$checked){
    		$isWindows = (strtoupper (substr(CopixConfig::getOsName (), 0, 3)) === 'WIN');
    		$checked = true;
    	}
    	return $isWindows;
    }
}
?>