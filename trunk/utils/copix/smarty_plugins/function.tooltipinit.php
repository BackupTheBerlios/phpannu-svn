<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.tooltipinit.php,v 1.8 2005/02/09 08:21:44 gcroes Exp $
* @author   Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type fonction
 * init for tooltip function plugin
 * Part of tooltip package, written by Laurent Jouanneau
 * http://ljouanneau.com/softs/javascript/
 * use :
 *  {tooltipinit}
 *  {tooltipinit path="path_to_tooltip.js"}
 *
 */
function smarty_function_tooltipinit($params, &$smarty){
   static $tooltipinit=0;

   if($tooltipinit == 0 || isset($params['force'])){
      extract($params);

      if(!isset($path))
         $path='js/';
      CopixHTMLHeader::addJSLink($path.'tooltip.js');
      $tooltipinit=1;
      return '<div id="tooltip"></div>';
   }else
      return '';
}


?>