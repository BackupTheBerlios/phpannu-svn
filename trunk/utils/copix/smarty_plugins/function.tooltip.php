<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.tooltip.php,v 1.7 2005/02/09 08:21:44 gcroes Exp $
* @author   Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type fonction
 * show a tooltip over an HTML element when mouse is over it
 * Part of tooltip package, written by Laurent Jouanneau
 * http://ljouanneau.com/softs/javascript/
 * use :
 *  <htmlelement {tooltip} title="text of tooltip" >...</htmlelement>
 *  <htmlelement {tooltip text="text of tooltip"}>...</htmlelement>
 *  <htmlelement {tooltip text=$the_text }>...</htmlelement>
 *
 *
 */
function smarty_function_tooltip($params, &$smarty)
{
    extract($params);
    $retval =' onmouseover="return tooltip.show(this);" onmouseout="tooltip.hide(this);" ';

    if (!empty($text)) {
      $retval .= ' title="'.htmlspecialchars($text).'"';
    }

	return $retval;
}


?>