<?php
/**
* @package  copix
* @subpackage project
* @version  $Id: index.php,v 1.11.2.1 2005/05/18 21:15:54 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
* @copyright 2001-2004 Aston S.A.
* @link     http://copix.aston.fr
* @licence  http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
*/

//includes copix files.
//will define constants, paths, relative to copix.
require_once ('../utils/copix/copix.inc.php');

//include copix_project files.
//will mainly define paths relative to the actual project.
//(zones, pages, modules, plugins, ...)
require_once ('../project/project.inc.php');

$copix_config_file = '../project/config/copix.conf.php';


//CopixInstall
if(!file_exists(COPIX_LOG_PATH.'.installed')){
   include(COPIX_PATH.'install.php');
}else{

    //creates the main object, giving it the configuration file to use.
    //will register itself to $GLOBALS['COPIX']['COORD']
    new ProjectCoordination($copix_config_file);

    //now we go, Copix is launched.
    $GLOBALS['COPIX']['COORD']->process();
}
?>