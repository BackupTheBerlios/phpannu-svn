<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.commentthis.php,v 1.1 2005/02/23 10:52:50 graoux Exp $
* @author   Bertrand Yan
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     commentthis
 * Version:  1
 * Date:     Feb 20025
 * Author:   Bertrand Yan
 * input: type :
 * Examples: {commentthis}
 * params : type, string type or module wich you want to comment
 *        : id, string id of the item you want to add comment
 *        : displaytype (optionnal default : list), type you want to display comment
 *          >link : make a link with nbComment caption
 *          >list : display all comment
 *          >form : display form to add comment
 *        : dest (optionnal), destination link (default getList in comment module)
 *        : back (optionnal), url back if dest is getList in comment module
 * -------------------------------------------------------------
 */
function smarty_function_commentthis($params, &$smarty) {
    extract($params);

    if (empty ($displaytype)){
       $displaytype = 'list';
    }
    if (empty ($type)){
       $smarty->trigger_error('commentthis: missing type parameter');
    }
    if (empty ($id)){
       $smarty->trigger_error('commentthis: missing id parameter');
    }
    if (empty ($dest)){
       $dest = CopixUrl::get('comment||getList', array('type'=>$type, 'id'=>$id, 'back'=>$back));
    }
    
    $dao = & CopixDAOFactory::create('comment|Comment');
    $services = & CopixClassesFactory::create ('comment|commentservices');
    $services->enableComment ($id, $type);
    
    switch ($displaytype) {
      case 'link':
         $nbComment = $dao->getNbComment ($id, $type);
         $toReturn  = '<a href='.$dest.'>'.$nbComment.' ';
         $toReturn .= ($nbComment > 1) ? CopixI18N::get('comment|comment.messages.comments') : CopixI18N::get('comment|comment.messages.comment');
         $toReturn .= '</a>';
         break;
      case 'form':
         $back = CopixUrl::getCurrentUrl ();
         CopixActionGroup::process ('comment|comment::doPrepareAdd', array ('type'=>$type, 'id'=>$id, 'back'=>$back));
         $toEdit   = CopixActionGroup::process ('comment|comment::_getSessionComment');
         $toReturn = CopixZone::process ('comment|AddComment', array ('toEdit'=>$toEdit));
         break;
      case 'list':
      default:
         $toReturn  = CopixZone::process ('comment|CommentList', array ('type'=>$type, 'id'=>$id));
         break;
    }

    return $toReturn;
}
?>
