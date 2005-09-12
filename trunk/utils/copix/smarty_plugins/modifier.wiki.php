<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: modifier.wiki.php,v 1.7 2005/04/12 06:31:25 laurentj Exp $
* @author   Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type modifier
 * Purpose:  convert a formated wiki text to html text
 * Input:
 * Example:  {$text|wiki}  {$text|wiki:"myModule|mywiki"}
 * @param string  $string  string to convert
 * @param string  $config_file_selector   config to use with wiki renderer
 * @return string
 */
function smarty_modifier_wiki($string, $config_file_selector = ''){
    require_once(COPIX_UTILS_PATH.'CopixWikiRenderer.lib.php');
    if($config_file_selector == '' )
      $wiki= new CopixWikiRenderer();
    else
      $wiki= new CopixWikiRenderer($config_file_selector);
    return $wiki->render($string);
}
?>