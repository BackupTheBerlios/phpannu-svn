<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixErrorObject.class.php,v 1.7 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* An object to carry Errors
* @package   copix
* @subpackage core
*/
class CopixErrorObject {
    /**
    * Associative array that carries errors.
    * @var array
    * @access private
    */
    var $_errors = array ();

    /**
    * constructor...
    * @param   mixed   $params      liste d'erreurs
    */
    function CopixErrorObject ($params = null) {
        if (is_array($params)){
            $this->addErrors($params);
        }
    }
    /**
    * Sets an error.
    * override the actual error if it already exists.
    * @param mixed   code the code error
    * @param mixed   value the error message
    */
    function addError ($code, $value){
        $this->_errors[$code] =  $value;
    }
    /**
    * add multiple errors.
    * @param array   $toAdd    associative array[code] = error
    * @todo PHP5, THROW.
    */
    function addErrors ($toAdd){
        if (is_array ($toAdd)){
            foreach ($toAdd as $code=>$elem){
                $this->addError ($code, $elem);
            }
        }
    }
    /**
    * gets the error from its code
    * @return string error message
    */
    function getError ($code){
        return isset ($this->_errors[$code]) ? $this->_errors[$code] : null;
    }
    /**
    * says if the error $code actually exists.
    * @param   mixed   $code   code error
    * @return boolean
    */
    function errorExists ($code){
        return isset ($this->_errors[$code]);
    }
    /**
    * says if there are any error in the object
    * @return boolean
    */
    function isError (){
        return count ($this->_errors) > 0;
    }
    /**
    * indique le nombre d'erreurs assignées.
    * @return int
    */
    function countErrors (){
        return count ($this->_errors);
    }
    /**
    * gets the errors as an object, with properties for each error codes
    * If there are numbers for code errors, convert them into _Code
    * @return object
    */
    function asObject (){
        $toReturn = (object) null;
        foreach ($this->_errors as $code=>$value){
            if (!is_integer (substr ($code, 0, 1))){
                $toReturn->$code = $value;
            }else{
                $toReturn->{'_'.$code} = $value;
            }
        }
        return $toReturn;
    }
    /**
    * gets the errors as an array
    * @return array  associative array [code] = message
    */
    function asArray (){
        return $this->_errors;
    }
    /**
    * gets the errors as a single string.
    * @return string error messages
    */
    function asString ($glueString = '<br />'){
        return implode ($glueString, array_values ($this->_errors));
    }
}
?>