<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.errormsg.php,v 1.7 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type fonction
 * Purpose:  automated error message.
 *
 * Input:    message = (required if you want to display a message) the error message
 *           class = (optional) class css to use for the paragraph
 *           assign = (optional) text to display, default is address
 *
 * Examples: {errormsg message="Please give an adress"}
 *           {errormsg message="Please give an adress" class="redText"}
 *           {errormsg message=$Message assign=$errorMessage}
 */
function smarty_function_errormsg($params, &$this) {
   extract($params);

   if ($message === null || strlen (trim ($message)) == 0){
      //if message isNull or empty, nothing to do.
      $output = '';
   } else {
      //process the output
      $output  = '<p';
      if (isset ($class)){
         $output .= ' class="'.$class.'"';
      }else{
         $output .= ' style="color: #FF2222;font-weight:bold;"';
      }
      $output.= '>'.$message.'</p>';
   }

   //check if we asked to assign the output to a variable.
   if (!empty($assign)) {
      $this->assign($assign, $output);
   } else {
      //did not ask that, returns the output
      return $output;
   }
}
?>