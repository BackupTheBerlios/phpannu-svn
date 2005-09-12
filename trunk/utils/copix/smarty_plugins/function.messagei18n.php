<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.messagei18n.php,v 1.8 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type fonction
 * Purpose:  I18N interface for CopiX.
 *
 * Input:    key      = (required  name of the select box
 *           bundle   = (optional) values to display the values captions will be
 *                        html_escaped, not the ids
 *           lang      = (optional) id of the selected element
 *           assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *
 * Examples:
 */
function smarty_function_messageI18n($params, &$this) {
   extract($params);
   if (empty ($key)){
     $smarty->_trigger_fatal_error("[smarty messagei18n] Missing key parameter");
     return;
   }
   return CopixI18N::get ($key);
}
?>