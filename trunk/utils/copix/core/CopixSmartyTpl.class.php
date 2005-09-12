<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixSmartyTpl.class.php,v 1.10 2005/04/05 15:58:01 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* include smarty class
*/
require_once (COPIX_SMARTY_PATH.'Smarty.class.php');

/**
* Surcharge l'objet Smarty pour un paramètrage plus aisé
* Les paramètres se trouvant dans l'objet de configuration de Copix
* @package   copix
* @subpackage core
* @see CopixConfig
*/
class CopixSmartyTpl extends Smarty {
    /**
    * A single variable to know wehter we are using fetch or display
    */
    var $_displayMethod = null;

    /**
    * Initialize the tplEngine with the right parameters.
    */
    function CopixSmartyTpl (){
        $this->template_dir  = $GLOBALS['COPIX']['CONFIG']->template_dir;
        $this->compile_dir   = $GLOBALS['COPIX']['CONFIG']->compile_dir;
        $this->config_dir    = $GLOBALS['COPIX']['CONFIG']->config_dir;
        $this->debugging     = $GLOBALS['COPIX']['CONFIG']->debugging;
        $this->compile_check = $GLOBALS['COPIX']['CONFIG']->compile_check;
        $this->force_compile = $GLOBALS['COPIX']['CONFIG']->force_compile;
        $this->caching       = $GLOBALS['COPIX']['CONFIG']->caching;
        $this->use_sub_dirs  = $GLOBALS['COPIX']['CONFIG']->use_sub_dirs;
        $this->cache_dir     = $GLOBALS['COPIX']['CONFIG']->cache_dir;
        $this->plugins_dir   = array ('plugins', COPIX_PATH.'smarty_plugins');
    }

    /**
    * Display, we just set a _displayMethod value before proceding.
    */
    function display ($tplName){
        $this->_displayMethod = 'display';
        echo parent::fetch ($tplName);
    }

    /**
    * Fetch, we just set a _displayMethod value before proceding.
    */
    function fetch ($tplName){
        $this->_displayMethod = 'fetch';
        return parent::fetch ($tplName);
    }
}
?>