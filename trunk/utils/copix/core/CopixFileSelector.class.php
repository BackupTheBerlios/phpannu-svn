<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixFileSelector.class.php,v 1.14.4.2 2005/08/17 21:06:10 laurentj Exp $
* @author   Laurent Jouanneau, Gerald Croes
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
* permet de récupérer un objet selecteur selon le type du selecteur
* un selecteur dans copix permet de spécifier un fichier/composant à utiliser et le module dans
* lequel il se trouve.
* le format d'un selecteur : "type:module|fichier"
*/
class CopixSelectorFactory {
    function & create ($id){
        static $selector = array ();
        //direct id exists ?
        if (isset ($selector[$id])){
            return $selector[$id];
        }

        //checking the full id ?
        if (strpos ($id, '|') === false){
            $fullId = CopixContext::get ().'|'.$id;
        }else{
            $fullId = $id;
        }
        if (isset ($selector[$fullId])){
            return $selector[$fullId];
        }

        if (($colon = strpos ($id, ':')) !== false) {
            switch (substr ($id, 0, $colon)) {
                case 'plugin':
                $selector[$id] = & new CopixPluginFileSelector($id);
                break;

                case 'copix':
                $selector[$id] = & new CopixCopixFileSelector($id);
                break;

                default:
                trigger_error (CopixI18N::get ('copix:copix.error.unknownSelector', $id));
                $ret = null;
                return $ret;
            }
        }else{
            if (strpos($id, '|') === false){
                $id = CopixContext::get ().'|'.$id;
            }
            $selector[$id] = & new CopixModuleFileSelector($id);
        }
        return $selector[$id];
    }
}

/**
* classe de base des selecteurs
*/
class CopixFileSelector {
    var $type      = null;
    var $typeValue = null;
    var $fileName  = null;
    var $isValid   = false;

    /**
    * @abstract
    */
    function getPath ($directory=''){}
    /**
    * @abstract
    */
    function getOverloadedPath ($directory=''){}
    /**
    * @abstract
    */
    function getSelector (){}

    /**
    * @abstract
    */
    function getQualifier (){}
}

/**
* implémente les selecteurs de fichiers/composant de modules
*/
class CopixModuleFileSelector extends CopixFileSelector {

    var $module=null; // si vaut '' ou null = projet

    function CopixModuleFileSelector($selector){
        $this->type = 'module';

        //ok, I don't use regexp here cause it's 0,40 ms slower :-)
        $tab = explode ('|', $selector);
        if (($counted = count ($tab)) > 1){
            $this->module = $tab[0] == '' ? null : $tab[0];
            $this->fileName=$tab[1];
            $this->isValid = true;
        }else if ($counted == 1){
            $this->module = CopixContext::get();
            $this->fileName = $tab[0];
            $this->isValid = true;
        }else{
            $this->isValid = false;
        }

        //we wants to check if the module name is valid, in case we got a non project demand.
        if ($this->isValid && ($this->module !== null)){
            $this->isValid = CopixModule::isValid ($this->module);
        }
    }

    function getPath ($directory=''){
        if ($this->module===null || $this->module=='' ){
            return COPIX_PROJECT_PATH.$directory;
        }else{
            return COPIX_MODULE_PATH.$this->module.'/'.$directory;
        }
    }

    function getOverloadedPath ($directory=''){
        if ($this->module !='' ){
            return COPIX_PROJECT_PATH.$directory.$this->module.'/';
        }else{
            return null;
        }
    }

    function getSelector(){
        return $this->module.'|'.$this->fileName;
    }

    function getQualifier (){
        return $this->module.'|';
    }
}

/**
* implémente les selecteurs de plugins
*/
class CopixPluginFileSelector extends CopixFileSelector {

    /**
    * name of the plugin (ID)
    */
    var $pluginName=null;

    /**
    * Module the plugin belongs to
    */
    var $module=null;

    function CopixPluginFileSelector($selector){
        $this->type='plugin';
        $match=null;
        if(preg_match("/^plugin:([_0-9a-zA-Z-]*)\/(([_0-9a-zA-Z-]*)\|)?(.*)$/",$selector,$match)){
            if($match[2]!=''){
                $this->module=$match[3];
            }
            $this->pluginName=$match[1];
            $this->fileName=$match[4];
            $this->isValid=true;
        }

        //we wants to check if the module name is valid, in case we got a non project demand.
        if ($this->isValid && ($this->module !== null)){
            $this->isValid = CopixModule::isValid ($this->module);
        }
    }

    /**
    * gets the path relative to the selector
    */
    function getPath($directory=''){
        if($this->module===null || $this->module=='' ){
            return COPIX_PLUGINS_PATH.$this->pluginName.'/';
        }else{
            return COPIX_MODULE_PATH.$this->module.'/'.COPIX_PLUGINS_DIR.$this->pluginName.'/';
        }
    }

    /**
    * gets the full qualified selector
    */
    function getSelector(){
        return 'plugin:'.($this->module !== null ? $this->module.'|':'').$this->pluginName;
    }

    /**
    * gets the qualifier (without the element id itself)
    */
    function getQualifier (){
        return 'plugin:'.($this->module !== null ? $this->module.'|' : '');
    }
}

/**
* implémente les selecteurs de fichiers/composant du noyau copix
*/
class CopixCopixFileSelector extends CopixFileSelector {
    function CopixCopixFileSelector($selector){
        $this->type = 'copix';
        if (($pos = strpos ($selector, 'copix:')) === 0){
            $this->fileName = substr ($selector, 6);//we know 'copix:' len is 6.
            $this->isValid  = true;
        }
    }

    function getPath($directory=''){
        return COPIX_PATH.$directory;
    }
    function getSelector(){
        return 'copix:'.$this->fileName;
    }
    function getQualifier () {
        return 'copix:';
    }
}
?>