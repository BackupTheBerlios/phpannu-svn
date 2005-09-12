<?php
/**
* @package	copix
* @subpackage htmleditor
* @version	$Id: selectpage.zone.php,v 1.5 2005/02/14 10:19:42 sdaclin Exp $
* @author	Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Show a select page "dialog"
*/
class ZoneSelectPage extends CopixZone {
    function _createContent (&$toReturn) {
      //Création du sous template.
      $tpl = & new CopixTpl ();

      //require_once (COPIX_MODULE_PATH.'cms/'.COPIX_CLASSES_DIR.'cmsworkflow.class.php');
      require_once (COPIX_MODULE_PATH.'cms/'.COPIX_CLASSES_DIR.'cmspage.services.class.php');

      CopixContext::push('cms');
      $sHeadings = & CopixClassesFactory::instanceOf ('copixheadings|CopixHeadingsServices');
      $headings  = $sHeadings->getTree();

      $cmsPages  = & new ServicesCMSPage ();
      $pages     = $cmsPages->getList ();
      if (isset ($this->params['onlyLastVersion']) && $this->params['onlyLastVersion'] == 1){
         $pages = $this->_filterLastVersion ($pages);
      }
      CopixContext::pop();
      //pagination
      foreach ($pages as $page){
          $arPages[$page->id_head][] = $page;
      }

      $tpl->assign ('arPublished', $arPages);
      $tpl->assign ('arHeadings', $headings);
      
      $tpl->assign ('select'     , $this->params['select']);
      $tpl->assign ('back'       , $this->params['back']);
      $tpl->assign ('popup'      , $this->params['popup']);
      $tpl->assign ('height'     , Copixconfig::get('htmleditor|height'));
      $tpl->assign ('width'      , Copixconfig::get('htmleditor|width'));
      $tpl->assign ('editorType' , CopixConfig::get('htmleditor|type'));
      $tpl->assign ('editorName' , $this->params['editorName']);
      $toReturn = $tpl->fetch ('page.select.ptpl');
      return true;
	}

   /**
   * Filtrage sur les derniers version seulement.
   */
   function _filterLastVersion ($arPublished){
      $toReturn = array ();
      foreach ($arPublished as $key=>$page){
         if (!isset ($toReturn[$page->id_cmsp])){
            //C'est la première version trouvée, on la met.
            $toReturn[$page->id_cmsp] = $page;
         }else if ($toReturn[$page->id_cmsp]->version_cmsp < $page->version_cmsp){
            //C'est une version plus récente, on remplace.
            $toReturn[$page->id_cmsp] = $page;
         }
      }
      return $toReturn;
	}
}
?>