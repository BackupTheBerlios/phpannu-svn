<?php
/**
* @package    copix
* @subpackage generaltools
* @version    $Id: CopixUrl.class.php,v 1.28.2.3 2005/08/19 08:39:53 laurentj Exp $
* @author    Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Objet url permettant de manipuler facilement une url
* @package    copix
* @subpackage core
*
* http://monsite.com/chemin/index.php/sous/che/min?param1=valeur1&param2=valeur2
*   scriptname = /chemin/index.php
*   pathinfo = /sous/che/min
*   params = array('param1'=>'valeur1', 'param2'=>'valeur2');
*/
class CopixUrl {
    /**
    * nom du script
    * @var string
    */
    var $scriptName;

    /**
    * paramètres de l'url
    * @var array
    */
    var $params;

    /**
    * info path, partie du path situé aprés le nom du script dans le path
    * @var string
    */
    var $pathInfo = '';

    /**
    * initialise l'objet
    * @param    string    $scriptname    nom du script
    * @param    array    $params    parametres
    */
    function CopixUrl ($scriptname='', $params=array (), $pathInfo=''){
        $this->params      = $params;
        $this->scriptName  = $scriptname;
        $this->pathInfo    = $pathInfo;
    }

    /**
    * ajoute ou redefini un paramètre url
    * @param    string    $name    nom du paramètre
    * @param    string    $value    valeur du paramètre
    */
    function set ($name, $value){
        $this->params[$name] = $value;
    }

    /**
    * supprime un paramètre
    * @param    string    $name    nom du paramètre
    */
    function delParam ($name){
        if (isset($this->params[$name]))
        unset ($this->params[$name]);
    }

    /**
    * Clear parameters
    */
    function clear (){
        $this->params = array ();
    }

    /**
    * get current Url
    */
    function getCurrentUrl () {
        static $url = false;
        if ($url === false){
           $currentUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?';
           foreach ($_GET as $key=>$elem){
              $currentUrl .= $key.'='.$elem.'&';
           }
           $url = $currentUrl;
        }
        return $url;
    }

    /**
    * construit l'url
    * @param    boolean    $forhtml indique si l'url est destiné à être intégré dans du code HTML/XML ou non
    * @return    string    l'url formée
    */
    function getUrl ($forhtml = false){
        $plugin = $GLOBALS['COPIX']['COORD']->getPlugin ('significanturl');
        if ($plugin !== null){
            $urlobj = $plugin->transformUrl($this); // on modifie une copie , pas cette instance même, sinon l'instance n'est plus réutilisable.
            if (count ($urlobj->params)>0){
                $url = $urlobj->scriptName.$urlobj->pathInfo.'?'.$urlobj->collapseParams ($forhtml);
            }else{
                $url = $urlobj->scriptName.$urlobj->pathInfo;
            }
        }else{
            if (count ($this->params) > 0){
                $url = $this->scriptName.$this->pathInfo.'?'.$this->collapseParams ($forhtml);
            }else{
                $url = $this->scriptName.$this->pathInfo;
            }
        }
        return $url;
    }

    /**
    * Transforms
    * @param boolean $forhtml if the output must be HTML/XML compliant
    * @return string
    */
    function collapseParams ($forhtml = false) {
        return $this->_collapseParams($this->params, $forhtml);
    }

    //============================== other methods that can be called without instanciation

    /**
    * Adds parameters to the given url
    * @param string $url
    * @param array $params
    */
    function appendToUrl ($url, $params = array (), $fromHtml = false){
        if ((($pos = strpos ( $url, '?')) !== false) && ($pos !== (strlen ($url)-1))){
            return $url . ($fromHtml ? '&amp;' : '&').CopixUrl::_collapseParams ($params, $fromHtml);
        }else{
            return $url . '?'.CopixUrl::_collapseParams ($params, $fromHtml);
        }
    }

    /**
    * Parse the given url into an associative array with [key = paramName]=>[value = paramValue}
    * @param string $url the url to parse
    * @param boolean $fromHtml if the url comes from an HTML formatted string
    * @param boolean $includeGETVars if we should include the GET vars in our string parsing
    * @return assocative array
    * @access public
    */
    function parse ($url) {
        //deleting parameters we don't need. We could avoid this.....
        if (($pos = strpos ($url, '?')) !== false){
            $url  = substr ($url, 0, $pos);
        }

        switch ($GLOBALS['COPIX']['CONFIG']->significant_url_mode){
            case 'none':
               //$vars = array ();
               if (isset($_REQUEST['module']) && $_REQUEST['module'] === '_'){
                   $_REQUEST['module'] = '';
               }
               return $_REQUEST;
               break;
            case 'prependIIS':
                  if (isset ($_GET[$GLOBALS['COPIX']['CONFIG']->significant_url_prependIIS_path_key])){
                      $url = $_GET[$GLOBALS['COPIX']['CONFIG']->significant_url_prependIIS_path_key];
                      $url = $GLOBALS['COPIX']['CONFIG']->stripslashes_prependIIS_path_key === true ? stripslashes($url) : $url;

                 }
            case 'prepend':
               $vars = CopixUrl::_parsePrepend ($url);
               break;

            default:
               trigger_error ('Unknown significant url handler', E_USER_ERROR);
        }
        return array_merge ($_REQUEST, $vars);
    }

    /**
    * Parse the given url that should be like index.php/modulename/desc/action as a default
    * @param string $url the given url
    * @param boolean $fromHtml if the given url
    * @return associative array with url params
    * @access private
    */
    function _parsePrepend ($url){
        //We don't want the first slash in the string
        if (strpos ($url, '/') === 0){
            $url = substr ($url, 1);
        }

        //We unescape spaces (we replaced spaces with - and - with -- before)
        //We only unescape the path part of the url, not the parameters
        $url = strtr ($url, array ('--'=>'-', '-'=>' '));

        //exploding the url with slashes
        $urlX = explode ('/', $url);
        if (((($countUrl = count ($urlX)) === 1) && ($urlX[0] === ''))){
            //no parameter
            return array ();
        }

        $module = $urlX[0];
        if ($module === '_'){
            $module = null;
        }

        //there is a handler.
        if (CopixUrl::_existsModuleHandler ($module)){
            $significantUrlHandler = CopixUrl::_createModuleHandler ($module);
            if (($vars = $significantUrlHandler->parse ($urlX, 'prepend')) !== false) {
                return $vars;
            }
        }

        //there is no handler.
        if ($countUrl >= 2){
            $desc = $urlX[1];
            if ($desc == '_'){
                $desc = 'default';
            }
        }else{
            $desc = 'default';
        }

        if ($countUrl >= 3){
            $action = $urlX[2];
            if ($action == '_'){
                $action = 'default';
            }
        }else{
            $action = 'default';
        }

        return array ('module'=>$module, 'desc'=>$desc, 'action'=>$action);
    }

    /**
    * Gets the url string from parameters
    * @param string $dest the module|desc|action description
    * @param array $params associative array with the parameters
    */
    function get ($dest = null, $params = array (), $html = false, $scriptName = null) {
        if ($dest === null){
            return $GLOBALS['COPIX']['CONFIG']->significant_url_basepath;
        }

        switch ($GLOBALS['COPIX']['CONFIG']->significant_url_mode){
            case 'none':
               return CopixUrl::_getNone ($dest, $params, $html, $scriptName);

            case 'prependIIS':
            case 'prepend':
               return CopixUrl::_getPrepend ($dest, $params, $html, $scriptName);

            default:
            trigger_error ('Unknown significant url handler', E_USER_ERROR);
        }
    }

    /**
    * gets the normally formatted URL
    * @param string  $dest the module|dest|action string
    * @param array   $params an associative array with the parameters
    * @param boolean $htmlif the string has to be for html
    * @return string the url
    */
    function _getNone ($dest, $params = array (), $html = false, $scriptName = null){
        if ($scriptName === null){
            $scriptName = $GLOBALS['COPIX']['CONFIG']->significant_url_script_name;
        }
        return $scriptName.'?'.CopixUrl::_collapseParams (array_merge (CopixUrl::_getDest ($dest), $params), $html);
    }

    /**
    * gets the prepended URL
    * @param string  $dest the module|dest|action string
    * @param array   $params an associative array with the parameters
    * @param boolean $htmlif the string has to be for html
    * @return string the prepended url
    */
    function _getPrepend ($dest, $params = array (), $html = false, $scriptName = null){
        if ($scriptName === null){
            $scriptName = $GLOBALS['COPIX']['CONFIG']->significant_url_script_name;
        }
        $dest = CopixUrl::_getDest ($dest);

        $urlObject = false;
        if (CopixUrl::_existsModuleHandler ($dest['module'])){
            $significantUrlHandler = CopixUrl::_createModuleHandler ($dest['module']);
            $urlObject = $significantUrlHandler->get ($dest, $params, 'prepend');
        }

        if ($urlObject === false){
            $urlObject->path   = $dest;
            $urlObject->vars = $params;
        }

        foreach ((array) $urlObject->path as $key=>$value){
            $urlObject->path[$key] = urlencode (strtr ($value, array ('-'=>'--', ' ' =>'-')));
        }

        $toReturn = $GLOBALS['COPIX']['CONFIG']->significant_url_basepath.$scriptName.'/'.implode ('/', $urlObject->path);

        if (isset ($urlObject->vars) && count ($params) > 0){
           $toReturn .= '?'.CopixUrl::_collapseParams ($urlObject->vars, $html);
        }
        
        return $toReturn;
    }

    /**
    * collapse parameters to generate an url parameters string
    * @param array $params array of parameters
    * @param boolean $html if the string has to be html compliant (&amp; for &)
    * @return string the url
    * @access private
    */
    function _collapseParams ($params, $html = false) {
        $url = '';
        if (count ($params)>0){
            foreach ($params as $k=>$v){
                if ($url == ''){
                    $url = $k.'='.$v;
                }else{
                    $url .= ($html ? '&amp;' : '&').$k.'='.$v;
                }
            }
        }
        return $url;
    }

    /**
    * Says if there is a significant url handler for the given module
    * @param string $module the module name we wants to check the existance of a handler
    * @return boolean true => there is a specific handler false => there is no specific handler
    */
    function _existsModuleHandler ($module){
        return is_readable (CopixUrl::_getModuleHandlerFileName ($module));
    }

    /**
    * Creates a significant url handler
    */
    function _createModuleHandler ($module){
        require_once (CopixUrl::_getModuleHandlerFileName($module));
        $className = $module.'SignificantUrl';
        return new $className ();
    }

    /**
    * Gets the significant url handler filename for the given module
    * @param string $module the module name. null if we wants the project
    */
    function _getModuleHandlerFileName ($module){
        if ($module === null) {
            return  COPIX_PROJECT_PATH.COPIX_CLASSES_DIR.'project.significanturl.class.php';
        } else {
            return COPIX_MODULE_PATH.strtolower($module).'/'.COPIX_CLASSES_DIR.strtolower ($module).'.significanturl.class.php';
        }
    }

    /**
    * gets the module/desc/action parameters from the destination string.
    *   dest is described as modules|desc|action where module & desc are optionnal.
    * @param string $dest the destination to parse
    * @see function.copixurl.php
    * @return assocative array where keys are module, desc and action
    */
    function _getDest ($dest){
        $tabUrl    = explode ('|', $dest);
        $urlParams = array ();
        switch (count ($tabUrl)){
            case 1:
            $urlParams = array ('module'=>CopixContext::get (), 'desc'=>'default', 'action'=>$tabUrl[0]);
            break;

            case 2:
            $urlParams = array ('module'=>CopixContext::get (), 'desc'=>$tabUrl[0], 'action'=>$tabUrl[1]);
            break;

            case 3:
            $urlParams = array ('module'=>$tabUrl[0], 'desc'=>$tabUrl[1], 'action'=>$tabUrl[2]);
            break;

            default :
            $urlParams = array ();
        }

        if ($urlParams['module'] == '' || $urlParams['module'] == null){
            $urlParams['module'] = '_';
        }
        if ($urlParams['desc'] == '' || $urlParams['desc'] == null){
            $urlParams['desc'] = 'default';
        }
        if ($urlParams['action'] == '' || $urlParams['action'] == null){
            $urlParams['action'] = 'default';
        }

        return $urlParams;
    }

    /**
    * Makes a destination string from an associative array
    * @param array $array an associative array
    * @return string the dest string module|desc|action
    */
    function _makeDest ($array) {
        $string = '';
        if (isset ($array['module'])){
            $string = $array['module'];
        }
        $string .= '|';
        if (isset ($array['dest'])){
            $string .= $array['dest'];
        }
        $string .= '|';
        if (isset ($array['action'])){
            $string .= $array['action'];
        }
        return $string;
    }
}
?>
