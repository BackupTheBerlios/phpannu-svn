<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.favicon.php,v 1.2 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Plugin smarty type fonction
* Purpose: adds a favicon to the page (in the HTML Header)
*
* Input:    src = the image source
* Examples:
* {favicon src='./img/copix/favicon.ico' }
*/
function smarty_function_favicon ($params, &$this) {
    extract ($params);

    //are there any values given ?
    if (empty ($src)) {
        $this->_trigger_fatal_error("[plugin favicon] parameter 'src' cannot be empty");
        return;
    }
    CopixHTMLHeader::addFavIcon ($src);
}
?>