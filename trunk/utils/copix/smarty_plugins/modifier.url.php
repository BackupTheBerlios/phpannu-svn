<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: modifier.url.php,v 1.2 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

 
/**
 * Plugin smarty type modifier
 * Purpose:  format an url (a href)
 * Input: caption|href caption is optionnal
 * Output: <a href="url">caption or url</a>
 * Example:  {$url|url}
 * @return string
 */
function smarty_modifier_url ($string) {
   $exploded = explode ('|', $string);
   if (count ($exploded) > 1){
      return '<a href="'.$exploded[1].'">'.$exploded[0].'</a>';
   }else{
      return '<a href="'.$string.'">'.$string.'</a>';
   }
}
?>