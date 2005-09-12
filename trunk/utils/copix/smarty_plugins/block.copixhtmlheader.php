<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: block.copixhtmlheader.php,v 1.2 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

 
/**
 * Smarty {copixhtmlheader}{/copixhtmlheader} block plugin
 *
 * Type:     block function<br>
 * Name:     copixhtmlheader<br>
 * Purpose:  Enables the template designer to add things that should be placed
 *   in the header of the HTML content.
 *   Basically, it's an alias to CopixHtmlHeader::XXX where XXX will be the given kind<br>
 * @param array
 * <pre>
 * Params:   kind: string (jsLink, cssLink, style, others, jsCode)
 * </pre>
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content re-formatted
 */
function smarty_block_copixhtmlheader($params, $content, &$smarty)
{
    if (is_null($content)) {
        return;
    }
   
    //If no kind was given, using others as a default
    if (!isset ($params['kind'])){
       $params['kind'] = 'others';
    }
   
    //Checking if the given kind is valid
    if (!in_array ($params['kind'], array ('jsLink', 'cssLink', 'style', 'others', 'jsCode'))){
       $smarty->_trigger_fatal_error ("[plugin copixhtmlheader] unknow kind ".$params['kind'].", only jsLink, cssLink, style, others, jsCode are available");
    }
   
    $funcName = 'add'.$params['kind'];
    CopixHTMLHeader::$funcName ($content);
    return '';
}
?>