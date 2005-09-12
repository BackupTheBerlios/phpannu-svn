<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.select.php,v 1.7 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type fonction
 * Purpose:  generation of a combo box
 *
 * Input:    name     = (required  name of the select box
 *           id       = (optional) id of SELECT element.
 *           values   = (optional) values to display the values captions will be
 *                        html_escaped, not the ids
 *           selected = (optional) id of the selected element
 *           assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *           emptyValues = id / value for the empty selection
 *           emptyShow   = [true] / false - wether to show or not the "emptyString"
 *           objectMap   = (optional) if given idProperty;captionProperty
 *
 * Examples:
 */
function smarty_function_select($params, &$this) {
   extract($params);
   //input check
   if (empty($name)) {
     $this->_trigger_fatal_error("[plugin select] parameter 'name' cannot be empty");
     return;
   }
   if (!empty ($objectMap)){
      $tab = explode (';', $objectMap);
      if (count ($tab) != 2){
         $this->_trigger_fatal_error("[plugin select] parameter 'objectMap' must looks like idProp;captionProp");
         return;
      }
      $idProp      = $tab[0];
      $captionProp = $tab[1];
   }
   if (empty ($emptyValues)){
      $emptyValues = array (''=>'-----');
   }

   //proceed
   $toReturn  = '<select name="'.$name.'" '.(empty($id)?'':' id="'.$id.'"').'>';
   if ((empty ($emptyShow)) || $emptyShow == true){
      //the "empty" element. If no key is the selected value, then its the one.
      $selectedString = in_array ($selected, array_keys ((array)$values)) ? '' : ' selected="selected" ';
      list ($keyEmpty, $valueEmpty) = each ($emptyValues);
      $toReturn .= '<option value="'.$keyEmpty.'"'.$selectedString.'>'.$valueEmpty.'</option>';
   }
   
   //each of the values.
   if (empty ($objectMap)){
      foreach ((array) $values  as $key=>$caption) {
         $selectedString = ((!empty($selected)) && ($key == $selected)) ? ' selected="selected" ' : '';
         $toReturn .= '<option value="'.$key.'"'.$selectedString.'>' . htmlentities($caption) . '</option>';
      }
   }else{
      //if given an object mapping request.
      foreach ((array) $values  as $object) {
         $selectedString = ((!empty($selected)) && ($object->$idProp == $selected)) ? ' selected="selected" ' : '';
         $toReturn .= '<option value="'.$object->$idProp.'"'.$selectedString.'>' . htmlentities($object->$captionProp) . '</option>';
      }
   }
   $toReturn .= '</select>';

   //check if we asked to assign the output to a variable.
   if (!empty($assign)) {
      $this->assign($assign, $toReturn);
   } else {
      //did not ask that, returns the output
      return $toReturn;
   }
}
?>