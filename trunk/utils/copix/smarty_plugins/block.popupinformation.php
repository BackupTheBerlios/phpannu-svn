<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: block.popupinformation.php,v 1.10 2005/04/05 15:06:09 gcroes Exp $
* @author   Bertrand Yan
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Smarty {popupinformation}{/popupinformation} block plugin
 *
 * Type:     block function<br>
 * Name:     popupinformation<br>
 * Purpose:  Add div wich display when mouse is over img<br>
 * @param array
 * <pre>
 * Params:   img: string
 * Params:   text: string
 * Params:   divclass: (optional)string, css class
 * Params:   displayimg: (optional, default true)boolean, display img ?
 * Params:   displaytext: (optional, default false) boolean , displaty text after img ?
 * Params:   assign :(optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 * </pre>
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content re-formatted
 */
function smarty_block_popupinformation($params, $content, &$smarty) {
   static $_init = false;
   if (! $_init){
    $jsCode = 'function displayPopupInformation(id) {
                  document.getElementById(id).style.visibility = \'visible\';
               }
               function hidePopupInformation(id) {
                  document.getElementById(id).style.visibility = \'hidden\';
               }
               function toggleDisplayPopupInformation(id) {
                  if (document.getElementById(id).style.visibility==\'hidden\') {
                     document.getElementById(id).style.visibility=\'visible\';
                  }else{
                     document.getElementById(id).style.visibility=\'hidden\';
                  }
                  if (document.getElementById(id).style.display==\'none\') {
                     document.getElementById(id).style.display=\'\';
                  }else{
                     document.getElementById(id).style.display=\'none\';
                  }
                  return false;
               }
               ';
               
    /*$jsCode .= '
      function selfHiddePopupInformation(){
         this.style.visibility = \'hidden\';
      }
      function selfShowPopupInformation(){
          this.style.visibility = \'visible\';
      }';*/
    
    CopixHtmlHeader::addJsCode ($jsCode);
    $_init = true;
   }
   
   if (is_null($content)) {
     return;
   }
   
   if (!isset ($params['text'])){
    $params['text'] = '';
   }
   
   if (!isset ($params['displaytext'])){
    $params['displaytext'] = false;
   }
   
   if (!isset ($params['alternativlink'])){
    $params['alternativlink'] = '#';
   }
   
   if (!isset($params['displayimg'])) {
    $params['displayimg']  = true;
   }
   
   if (!isset ($params['img'])){
    $params['img'] = CopixUrl::get().'img/tools/information.png';
   }
   
   if (!isset ($params['divclass'])){
    $params['divclass'] = 'popupInformation';
   }
   
   if (!isset($params['handler'])) {
    $params['handler']  = 'onmouseover';
   }
   
   $id        = uniqid('popupInformation');

   switch ($params['handler']) {
      case 'onmouseover' :
         $toReturn  = '<div id="div'.$id.'" style="display:inline;" ';
         $toReturn .= 'onmouseover="javascript:displayPopupInformation(\''.$id.'\')" onmouseout="javascript:hidePopupInformation(\''.$id.'\');" >';
         break;
      case 'onclick':
         $toReturn  = '<a id="a'.$id.'" href="'.$params['alternativlink'].'" ';
         $toReturn .= 'onclick="return toggleDisplayPopupInformation(\''.$id.'\')">';
         break;
      
      case 'overEvent' :
         $toReturn  = '<div id="div'.$id.'" style="display:inline;" ';
         $toReturn .= 'onmouseover="javascript:displayPopupInformation(\''.$id.'\')" onmouseout="javascript:hidePopupInformation(\''.$id.'\')" onblur="javascript:hidePopupInformation(\''.$id.'\')">';
         break;
      
      default:
         $toReturn  = '<div id="div'.$id.'" style="display:inline;" ';
         $toReturn .= 'onmouseover="javascript:displayPopupInformation(\''.$id.'\')" onmouseout="javascript:hidePopupInformation(\''.$id.'\');" >';
         break;
   }
   
   $toReturn .= $params['displayimg']  === true ? '<img src="'.$params['img'].'" alt="'.$params['text'].'" />' : '';
   $toReturn .= $params['displaytext'] === true ? $params['text'] : '';
   if ($params['handler']=='onclick') {
      $toReturn .= '</a>';
   }
   $toReturn .= '<div class="'.$params['divclass'].'" id="'.$id.'" style="visibility:hidden;';
   $toReturn .= ($params['handler']!='onclick') ?  '" >' : 'display:none" >';
   $toReturn .= $content;
   $toReturn .= '</div>';
   if ($params['handler']!='onclick') {
      $toReturn .= '</div>';
   }
   
   if (isset ($params['assign'])){
      $this->assign($params['assign'], $toReturn);
      return '';
   }else{
      return $toReturn;
   }
}
?>
