<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixActionGroup.class.php,v 1.20.4.4 2005/08/17 20:06:53 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Il implemente les actions liées à une page, et renvoi au coordinateur de module
* des informations concernant la cinematique à suivre aprés action.
* Il faut donc imperativement instancier cette page pour implementer les methodes
* correspondantes à chaque action
*
* @package   copix
* @subpackage core
* @abstract
* @see CopixActionReturn
* @see CopixCoordination
*/
class CopixActionGroup {
    /**
    * Parameters given to the action
    * @var array
    * @access private
    */
    var $vars;

    /**
    * @constructor
    * Register itself in $GLOBALS['COPIX']['ACTIONGROUP']
    */
    function CopixActionGroup (){
//        $GLOBALS['COPIX']['ACTIONGROUP'] = & $this;
    }

    /**
    * CopixZone::process alias
    * @param string $name identifier module|zoneName
    * @param array $params associative array, parameters
    */
    function processZone($name, $params=array ()){
        return CopixZone::process ($name, $params);
    }

    /**
    * extract path information from a string
    * the string is like module|ag|methName
    * if module is not given (ag|methName) then we consider the current context
    */
    function extractPath ($path){
        $extract = explode ('|', $path);
        if (count ($extract) == 1){
            return CopixActionGroup::extractPath (CopixContext::get ().'|'.$path);
        }

        $extractMethod = explode ('::', $extract[1]);
        if (count ($extractMethod) !== 2){
            trigger_error (CopixI18N::get ('copix:copix.error.wrongActionGroupPath', $path), E_USER_ERROR);
        }

        $extracted->module      = $extract[0] === '' ? null : $extract[0];
        $extracted->actiongroup = $extractMethod[0];
        $extracted->method      = $extractMethod[1];

        return $extracted;
    }

    /**
    * Launch an actiongroup method
    * @param string $path identifier 'module|AG::method'
    * @param array $vars parameters
    */
    function & process ($path, $vars=array ()){
        static $instance = array ();
        $toReturn = null;
        $extractedPath = CopixActionGroup::extractPath ($path);
        if ($extractedPath === null){
            trigger_error(CopixI18N::get('copix:copix.error.load.actiongroup',$path), E_USER_ERROR);
            return $toReturn;
        }

        $actionGroupID = $extractedPath->module.'|'.$extractedPath->actiongroup;
        if (! isset ($instance[$actionGroupID])){
            if ($extractedPath->module === null){
                $execPath = COPIX_PROJECT_PATH;
            }else{
                $execPath = COPIX_MODULE_PATH.$extractedPath->module.'/';
            }
            $fileName = $execPath.COPIX_ACTIONGROUP_DIR.strtolower (strtolower ($extractedPath->actiongroup)).'.actiongroup.php';
            if (is_readable ($fileName)){
                require_once ($fileName);
            }else{
                trigger_error(CopixI18N::get('copix:copix.error.load.actiongroup',$path.'-'.$fileName), E_USER_ERROR);
                return $toReturn;
            }

            //Nom des objets/méthodes à utiliser.
            $objName  = 'ActionGroup'.$extractedPath->actiongroup;
            //instance de l'objet, qui s'enregistre dans GLOBALS['COPIX']['ACTIONGROUP']
            $instance[$actionGroupID] = & new $objName ();
        }

        $methName = $extractedPath->method;
        CopixContext::push ($extractedPath->module);
        $instance[$actionGroupID]->vars = & $vars;
        $toReturn =  $instance[$actionGroupID]->$methName ();
        CopixContext::pop ();
        return $toReturn;
    }

    /**
    * Gets the value of a request variable. If not defined, gets its default value.
    * @param string $varName the name of the request variable
    * @param mixed $varDefaultValue the default value of the request variable
    * @return mixed the request variable value
    */
    function getRequest ($varName, $varDefaultValue=null, $emptyIsDefault=false){
       if (array_key_exists ($varName, $this->vars)){
          if ($emptyIsDefault){
	     if (strlen(trim($this->vars[$varName])) == 0){
	        return $varDefaultValue;
	     }else{
	        return $this->vars[$varName];
	     }
	  }else{
	     return $this->vars[$varName];
	  }
       }else{
          return $varDefaultValue;
       }
    }

}
?>