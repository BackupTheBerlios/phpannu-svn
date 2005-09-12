<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.copixlogo.php,v 1.8 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin smarty type fonction
 * input: type : big   -> the big one
 *               small -> simply made with Copix, http://copix.aston.fr
 *        default is small
 * Examples: {copixlogo}
 * Simply output the made with Copix Logo
 * -------------------------------------------------------------
 */
function smarty_function_copixlogo($params, &$smarty) {
    extract($params);
    if (empty($type) || $type == 'small') {
       return '<!-- made with Copix, http://copix.aston.fr, http://copix.org-->';
    }else{
return '<!-- made with
    ______   ____     ______   _  __      __
   / ____/  /    \   /  __  \ / \ \ \    / /
  / /      |  --  |  |    | | \_/  \ \  / /
 / /       | |  | |  | |_ __/  _    \ \/ / 
 \ \____   |  __| |  | |      | |    \ \/  
  \_____\   \____/   |_|      |_|    / /\  
                                    / /\ \ 
 __________________________________/_/  \_\___
|Open Source Framework for PHP                |
|_____________________________________________|
http://copix.org         http://copix.aston.fr
-->';
    }
}
?>