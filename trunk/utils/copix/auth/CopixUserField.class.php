<?php
/**
* @package   copix
* @subpackage auth
* @version   $Id: CopixUserField.class.php,v 1.2 2005/03/21 09:49:22 gcroes Exp $
* @author   Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class CopixUserField {
    /**
    * the i18n key for the caption of the field
    * @var string
    */
    var $captioni18n;

    /**
    * the field name (in the user object)
    */
    var $fieldName;

    /**
    * the field type
    * one of int, string
    * @var string
    */
    var $fieldType;

    /**
    * where the property is available
    * @var array
    */
    var $available;

    /**
    * @constructor
    * @access public
    */
    function CopixUserField ($fieldName, $captioni18n, $fieldType, $available){
        $this->captioni18n = $captioni18n;
        $this->fieldName   = $fieldName;
        $this->fieldType   = $fieldType;
        $this->available   = $available;
    }
    
    /**
    * index of the availability array for the list
    * @return int
    */
    function forList (){
        return 0;
    }
    
    /**
    * index of the availability array for the admin
    * @return int
    */
    function forAdmin (){
        return 1;
    }
    
    /**
    * index of the availability array for the user (show)
    * @return int
    */
    function forUser (){
        return 2;
    }
    
    /**
    * index of the availability array for the user (update)
    * @return int
    */
    function forUserUpdate (){
        return 3;
    }
    
    /**
    * if the field should appear in the users list
    * @return boolean
    */
    function availableInList (){
        return $this->available[$this->forList()];
    }

    /**
    * if the field should appear in the user admin screen
    * @return boolean
    */
    function availableInAdmin (){
        return $this->available[$this->forAdmin ()];        
    }

    /**
    * If the field should appear on the personnal user information
    * @return boolean
    */
    function availableInUser (){
        return $this->available[$this->forUser ()];
    }

    /**
    * If the field can be update by its user
    * @return boolean
    */
    function availableInUserUpdate (){
        return $this->available[$this->forUserUpdate ()];
    }
}
?>