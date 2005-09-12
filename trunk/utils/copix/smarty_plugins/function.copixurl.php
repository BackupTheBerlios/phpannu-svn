<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.copixurl.php,v 1.14 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin smarty type fonction
* Purpose:  generation of a copixed url
*
* Input:    dest=module|desc|action
*           complete syntax will be:
*           desc|action for current module, desc and action
*           [action or |action] default desc, action
*           [|desc|action] project, desc and action
*           [||action] action in the project
*           [module||action] action in the default desc for the module
*           [|||] the only syntax for the current page
*
*           * = any extra params will be used to generate the url
*
*/
function smarty_function_copixurl($params, &$this) {

    if(isset($params['notxml'])){
      $isxml = ($params['notxml']=='true'?false:true);
      unset($params['notxml']);
    }else{
       $isxml = true;
    }

    $assign = '';
    if(isset($params['assign'])){
      $assign = $params['assign'];
      unset($params['assign']);
    }

    if (!isset ($params['dest']) && !isset ($params['appendFrom'])){
       $toReturn = CopixUrl::get (null,array(),$isxml);
    }
/*
    $tabUrl = explode ('|', $params['dest']);
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
*/

    //checking parameters
    /*
    $urlParams = array ();
    if ($module != ''){
    $urlParams['module'] = $module;
    }
    if ($desc != ''){
    $urlParams['desc'] = $desc;
    }
    if ($action != ''){
    $urlParams['action'] = $action;
    }
    */

    if (isset ($params['appendFrom'])){
       $appendFrom = $params['appendFrom'];
       unset ($params['appendFrom']);
       $toReturn = CopixUrl::appendToUrl ($appendFrom, $params, $isxml);
    }
    if (isset ($params['dest'])){
        $dest = $params['dest'];
        unset ($params['dest']);
        $toReturn = CopixUrl::get ($dest, $params,$isxml);
    }
    
    if (strlen($assign)>0){
        $this->assign($assign, $toReturn);
        return '';
    }else{
        return $toReturn;
    }
}
?>