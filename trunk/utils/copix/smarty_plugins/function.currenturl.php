<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.currenturl.php,v 1.1 2005/02/22 11:10:23 graoux Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type fonction
 * Purpose:  get the current url.
 *
 * Input:   assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *
 * Examples:
 */
function smarty_function_currenturl($params, &$this) {
   
   $assign = CopixUrl::getCurrentUrl ();

   if (isset ($params['assign'])){
      $this->assign($params['assign'], $assign);
      return '';
   }else{
       return $assign;
   }
}
?>
