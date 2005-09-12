<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: modifier.datei18n.php,v 1.3 2005/02/18 12:34:06 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin smarty type modifier
 * Purpose:  format a date given by its timestamp (YYYMMDD) to a date according
 *   to the current languages settings
 * if an incorrect date is given, returns the string without any modification
 * Input: YYYYMMDD
 * Output: (french) DD/MM/YYYY, (english) MM/DD/YYYY
 * Example:  {$date|datei18n}
 * @return string
 */
function smarty_modifier_datei18n($string) {
   return (($date = CopixI18N::timestampToDate ($string)) !== false) ? $date : $string;
}
?>