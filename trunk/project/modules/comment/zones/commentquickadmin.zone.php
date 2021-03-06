<?php
/**
* @package	copix
* @subpackage comment
* @version	$Id: commentquickadmin.zone.php,v 1.1 2005/02/23 10:58:15 graoux Exp $
* @author	Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* shows all comment order by date.
*/
require_once (COPIX_UTILS_PATH.'CopixPager.class.php');

class ZoneCommentQuickAdmin extends CopixZone {
    function _createContent (& $toReturn) {
        $tpl = & new CopixTpl ();

        $dao = & CopixDAOFactory::create ('comment|comment');
        $sp  = & CopixDAOFactory::createSearchConditions ();
        $sp->addItemOrder ('date_cmt', 'desc');
        $sp->addItemOrder ('position_cmt', 'desc');
        $arComments = array();
        $arComments = $dao->findby($sp);
        if (count($arComments)>0) {
            $perPage = intval(CopixConfig::get('comment|quickAdminPerPage'));
            $params  = Array(
               'perPage'    => $perPage,
               'delta'      => 5,
               'recordSet'  => $arComments,
               'template'   => '|pager.tpl'
            );
            $pager = CopixPager::Load($params);
            $tpl->assign ('pager'    , $pager->GetMultipage());
            $tpl->assign ('comments' , $pager->data);
        }

        $toReturn = $lastComments === array () ? '' : $tpl->fetch ('comment.quickadmin.tpl');

        return true;
    }
}
?>
