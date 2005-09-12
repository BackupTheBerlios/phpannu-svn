<?php
/**
* @package	copix
* @subpackage parameters
* @version	$Id: showparams.zone.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Bertrand Yan see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Display parameters of the given  module
*/
class ZoneShowParams extends CopixZone {
   function _createContent (&$toReturn){
      $tpl     = & new CopixTpl ();

      if (($this->params['choiceModule'] !== false) && CopixModule::isValid ($this->params['choiceModule'])) {
         $tpl->assign('paramsList', $this->_getParams ($this->params['choiceModule']));
      }else{
          $tpl->assign ('paramsList', $this->_getParams (null));
      }

      $tpl->assign('moduleList'  , $this->_getModuleWithParams());
      $tpl->assign('choiceModule', $this->params['choiceModule']);
      $tpl->assign('editParam'   , $this->params['editParam']);

      $toReturn = $tpl->fetch ('parameters.tpl');
      return true;
   }

   /**
   * gets module list, we only get modules with parameters
   */
   function _getModuleWithParams (){
       $toReturn = array ();
       foreach (CopixModule::getList() as $moduleName){
           if (count (CopixConfig::getParams ($moduleName)) > 0) {
               $informations = CopixModule::getInformations($moduleName);
               $toReturn[$moduleName] = $informations->description;
           }
       }
       return $toReturn;
   }

   /**
   * gets the parameters list
   */
   function _getParams ($module){
      $toReturn = array ();
      CopixContext::push ($module);
      foreach (CopixConfig::getParams ($module) as $params){
          $params['Caption'] = CopixI18N::get ($params['Caption']);
          $toReturn[] = $params;
      }
      CopixContext::pop ();
      return $toReturn;
   }
}
?>