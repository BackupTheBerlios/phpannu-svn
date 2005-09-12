<?php
/**
* @package   copix
* @subpackage project
* @version   $Id: project.inc.php,v 1.9.2.1 2005/05/18 21:17:47 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/******************************************************************************
* COPIX PATH
* Project, Modules and plugins.
******************************************************************************/


define ('COPIX_PROJECT_PATH', dirname (__FILE__).'/');//project is obviously in its own directory

define ('COPIX_TEMP_PATH',    realpath(dirname(__FILE__).'/../temp/').'/');
define ('COPIX_CACHE_PATH',   COPIX_TEMP_PATH.'cache/');
define ('COPIX_LOG_PATH',     COPIX_TEMP_PATH.'log/');
define ('COPIX_PLUGINS_PATH', COPIX_PROJECT_PATH.'plugins/');
define ('COPIX_MODULE_PATH',  COPIX_PROJECT_PATH.'modules/');

/*
Pour les packageurs, on peut changer par exemple en :
define ('COPIX_TEMP_PATH',    '/var/tmp/copix/defaultapp/');
define ('COPIX_CACHE_PATH',   '/var/cache/copix/defaultapp/');
define ('COPIX_LOG_PATH',     '/var/log/copix/defaultapp/');
define ('COPIX_PLUGINS_PATH', COPIX_PROJECT_PATH.'plugins/');
define ('COPIX_MODULE_PATH' , COPIX_PROJECT_PATH.'modules/');

defaultapp est l'application fournie avec copix. Si il y a plusieurs applications
qui utilisent copix, mettre un repertoire different pour chacune d'entre elles dans leur
project.inc.php respectif.
*/


/******************************************************************************
* URL AND DEFAULT VALUES
******************************************************************************/
// we define what the url should look like.
// here: index.php?action=value_action&module=value_module&desc=value_desc
define ('COPIX_NAME_CODE_MODULE','module');
define ('COPIX_NAME_CODE_ACTION','action');
define ('COPIX_NAME_CODE_DESC',  'desc');

// then we define the default values for thoose params if not specified.
// Note: When no module is being asked for, we execute a project action
// See documentation for further information on modules and project
define ('COPIX_DEFAULT_VALUE_MODULE', null);
define ('COPIX_DEFAULT_VALUE_ACTION', 'default');
define ('COPIX_DEFAULT_VALUE_DESC'  , 'default');


/**
* Replace COPIX_PAGES_DIR
* @since Copix2_RC1
*/
define ('COPIX_ACTIONGROUP_DIR'    , 'actiongroup/');
define ('COPIX_DESC_DIR'     , 'desc/');
define ('COPIX_ZONES_DIR'    , 'zones/');
define ('COPIX_TEMPLATES_DIR', 'templates/');
define ('COPIX_STATIC_DIR'   , 'static/');
define ('COPIX_CLASSES_DIR'  , 'classes/');
define ('COPIX_CORE_DIR'     , 'core/');
define ('COPIX_RESOURCES_DIR', 'resources/');
define ('COPIX_PLUGINS_DIR'    , 'plugins/');
define ('COPIX_EVENTS_DIR', 'events/');
define ('COPIX_INSTALL_DIR', 'install/');

require_once (COPIX_PROJECT_PATH.COPIX_CORE_DIR.'ProjectCoordination.class.php');
?>