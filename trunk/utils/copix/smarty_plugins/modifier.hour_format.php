<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: modifier.hour_format.php,v 1.3 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type modifier
 * Purpose:  format an hour (HHMMSS) string
 * Input:
 * Example:  {$hour|hour_format:"%H:%i:%s"}
 * @param string  $hour  string to convert
 * @param string  $format the format we wants to display the hour with
 * @return string
 */
function smarty_modifier_hour_format($string, $format="%H:%i:%s") {
   $hour    = substr ($string, 0, 2);
   $minute  = substr ($string, 2, 2);
   $seconds = strlen ($string) == 6 ? substr ($string, 4, 2) : 0;

   return str_replace (array ('%H', '%i', '%s'), array ($hour, $minute, $seconds), $format);
}
?>