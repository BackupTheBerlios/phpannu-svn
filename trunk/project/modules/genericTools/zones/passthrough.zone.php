<?php
/**
* @package	copix
* @subpackage genericTools
* @version	$Id: passthrough.zone.php,v 1.4 2005/02/09 08:27:43 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ZonePassThrough extends CopixZone {
   /**
   * Zone that passes all its parameters to the given template
   * @param: * abstract
   * @param: template string the template name where the datas will be assigned to.
   */
   function _createContent (&$toReturn){
      //we wants to go back to the current context.
      $context = CopixContext::pop ();
      
      $tpl = & new CopixTpl ();
      //assign the template variables
      foreach ($this->params as $var=>$value){
         if ($var !== 'template'){
            $tpl->assign ($var, $value);
         }
      }
      $toReturn = $tpl->fetch ($this->params['template']);

      //then we wants to bring back the context we poped
      CopixContext::push ($context);
      return true;
   }
}
?>