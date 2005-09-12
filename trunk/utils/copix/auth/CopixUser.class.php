<?php
/**
* @package   copix
* @subpackage auth
* @version   $Id: CopixUser.class.php,v 1.11.4.2 2005/08/17 20:06:53 laurentj Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* This is the base class for authentification process
* You should extend this class to fit your needs.
* login and password are reserved words
*/
class CopixUser {
    /**
    * the login that was used to establish the connection.
    * @var string
    */
    var $login = null;

    /**
    * Is the user connected ?
    * @access private
    * @var boolean
    */
    var $_isConnected = false;

    /**
    * @constructor
    */
    function CopixUser (){
        $this->_setNotConnected ();
    }

    /**
    * Try to log in
    */
    function login ($name, $password = null){
        if (! $this->_doLogin ($name, $password)){
            $this->logout ();
            return false;
        }
        $this->_isConnected  = true;
        return true;
    }

    /**
    * Logs the user out
    */
    function logout (){
        $this->_setNotConnected();
    }

    /**
    * Defines the classes attributes as not connected
    */
    function _setNotConnected (){
        $this->login = null;
        $this->_isConnected = false;
    }

    /**
    *  Maj des paramètres pour indiquer que l'utilisateur est bien loggé.
    *  Ici, on se charge juste de dire que l'utilisateur est passé par la phase d'authentification.
    *  Rien de plus. (a la limite, on peut gérer des sortes de stats.... durée de connexion et tout ça)
    */
    function _doLogin ($name, $password = null){
        trigger_error (CopixI18N::get('copix:copix.error.class.abstract',get_class($this).'::doLogin'), E_USER_ERROR);
    }

    /**
    * returns the crypted password.
    * @param string $clearPass the password (clear)
    * @return string the crypted password
    */
    function cryptPassword ($clearPass){
        return md5 ($clearPass);
    }

    /**
    * Gets the users list
    * @return array
    */
    function getList (){
        trigger_error (CopixI18N::get ('copix:copix.error.class.abstract',get_class($this).'::getList'), E_USER_ERROR);
    }

    /**
    * Says if the user is connected
    */
    function isConnected (){
        return $this->_isConnected;
    }

    /**
    * Gets the properties of the user object
    */
    function getProperties (){
        require_once (COPIX_AUTH_PATH.'CopixUserField.class.php');
        return array ();
    }

    /**
    * Gets the properties of the object the user can update
    * @return array of CopixUserField
    */
    function getUserUpdateProperties (){
        $toReturn = array ();
        foreach ($this->getProperties() as $key=>$property){
            if ($property->availableInUserUpdate ()){
                $toReturn[$key] = $property;
            }
        }
        return $toReturn;
    }

    /**
    * Gets the properties of the object the user can see
    * @return array of CopixUserField
    */
    function getUserProperties (){
        $toReturn = array ();
        foreach ($this->getProperties() as $key=>$property){
            if ($property->availableInUser ()){
                $toReturn[$key] = $property;
            }
        }
        return $toReturn;
    }

    /**
    * Gets the properties of the object people can see in the user list
    * @return array of CopixUserField
    */
    function getListProperties (){
        $toReturn = array ();
        foreach ($this->getProperties () as $key=>$property){
            if ($property->availableInList ()){
                $toReturn[$key] = $property;
            }
        }
        return $toReturn;
    }

    /**
    * Gets the properties of the object the administrator can update
    * @return array of CopixUserField
    */
    function getAdminProperties (){
        $toReturn = array ();
        foreach ($this->getProperties() as $key=>$property){
            if ($property->availableInAdmin ()){
                $toReturn[$key] = $property;
            }
        }
        return $toReturn;
    }

    /**
    * Create a new user
    */
    function & getNew (){
        $userobjectname = get_class ($this);
        $return = & new $userobjectname ();
        return $return;
    }

    /**
    * Gets the password field
    */
    function getPasswordField (){
        return 'password';
    }

    /**
    * Check if the user is ok
    */
    function check (){
        return true;
    }
}
?>