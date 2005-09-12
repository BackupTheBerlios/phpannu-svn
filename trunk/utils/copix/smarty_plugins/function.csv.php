<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.csv.php,v 1.3 2005/02/24 17:29:33 sdaclin Exp $
* @author   Daclin Sylvain
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
* Plugin smarty type fonction
* Purpose:  generation of a CSV content file
*
* Input:    values           = (required) array of objects, or array of hash array
*           order            = (optional) if given, the resulting CSV file will be sorted by this fields.
*           separator        = (optional) the separator for the csv file. (default is "," [comma])
*           displayHeaders   = (optional) if we wants to output the headers
* Examples:
* {csv values=$arObjects displayHeaders=false order=$array displayHeaders=false}
*/
function smarty_function_csv($params, &$this) {
    extract ($params);

    //are there any values given ?
    if (empty ($values)) {
        $this->_trigger_fatal_error("[plugin CSV] parameter 'values' cannot be empty");
        return;
    }
    //checking if values is an array
    if (!is_array ($values)) {
        $this->_trigger_fatal_error("[plugin CSV] parameter 'values' must be an array");
        return;
    }
    //checinkg if value is an array of object or an array of array.
    if (count ($values) <= 0){
        $output = '';
    }else{
        $first = $values[0];
        if (is_object ($first)){
            $objectMode = true;
        }elseif (is_array ($first)){
            $objectMode = false;
        }else{
            $this->_trigger_fatal_error ("[plugin CSV] parameter 'values' must be an array of object or an array of array");
        }
    }

    //the separator
    if (!(isset ($separator) && is_string ($separator))) {
        $separator=',';
    }

    //no values ? empty output.
    if (count ($values) <= 0){
        $output = '';
    }else{
        $firstRow = $values[0];
        if (is_object ($firstRow)){
            $objectMode = true;
        }elseif (is_array ($firstRow)){
            $objectMode = false;
        }else{
            $this->_trigger_fatal_error ("[plugin CSV] parameter 'values' must be an array of object or an array of associative array");
        }
    }

    //calculating headers.
    if ($displayHeaders){
        if ($objectMode){
            $headers = get_object_vars ($firstRow);
        }else{
            $headers = array_key ($firstRow);
        }
        $output .= implode ($separator, $headers)."\n";
    }

    //exporting values into csv
    foreach ($values as $rowNumber=>$rowValues) {
        $rowValues = $objectMode ? array_values (get_object_vars ($rowValues)) : array_values ($rowValues);
        $output .= implode ($separator, $rowValues)."\n";
    }
    
    //now sorting elements.
    //TODO.

    //processing output
    if (!empty ($assign)){
        $this->assign($assign, $toReturn);
        return;
    }else{
        return $output;
    }
}
?>