<?php
/**
* @package	copix
* @subpackage auth
* @version	$Id: CopixADUser.class.php,v 1.1 2005/03/21 09:49:22 gcroes Exp $
* @author	Croes Gérald,see copix.aston.fr for other contributors.
* @copyright 2001-2004 Aston S.A.
* @link		http://copix.aston.fr
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @ignore
*/
require_once (COPIX_AUTH_PATH.'CopixUser.class.php');

/**
* This is the base class for authentification process in an Active Directory
* You should extend this class to fit your needs.
* In this baseclass, we try to mimic the posixAccount class
* @package	copix
* @subpackage auth
* @see ProjectUser.class.php
*/
class CopixADUser extends CopixUser {
    /**
    * the properties list we'll remember.
    * @var array
    */
    var $_fieldPropList = array ('uid'=>'login', 'cn'=>'name',
    'sn'=>'firstname');

    /**
    * The login field (in the directory)
    * @var string
    */
    var $loginField    = 'uid';

    /**
    * The password field (in the directory)
    * @var string
    */
    var $passwordField = 'userPassword';
    
    /**
    * @constructor
    */
    function CopixADUser (){
        parent::CopixUser ();
    }

    /**
    * Maj des paramètres pour indiquer que l'utilisateur est bien loggé.
    * Ici, on se charge juste de dire que l'utilisateur est passé par la phase d'authentification.
    * Rien de plus. (a la limite, on peut gérer des sortes de stats.... durée de connexion et tout ça)
    * @param string $name login
    * @param string $password mot de passe
    * @return boolean indique si authentification ok ou pas
    */
    function _doLogin ($name, $password=null){
        if (! ($ct = CopixLDAPFactory::getConnection ())){
            return false;
        }
        $rs = $ct->doQuery ('(cn='.$name.')');
        if (! ($result = $rs->fetch ())){
            return false;
        }

        if ($result->{$this->passwordField} === $this->cryptPassword ($password)){
            $this->_loadParams ($result);
            return true;
        }
        return false;
    }

    /**
    * loads the parameters
    */
    function _loadParams ($objInfos) {
        //parcour des champs, mise à jour de l'utilisateur.
        foreach ($this->_fieldPropList as $field=>$userField) {
            $field = $field;
            $this->$userField = $objInfos->$field;
        }
    }

    /**
    * Gets the user's list
    * @return    array   user's list
    */
    function getList (){
        $userobjectname = get_class ($this);

        $toReturn = array ();
        $ct = CopixLDAPFactory::getConnection ();
        $rs = $ct->doQuery ('(cn=*)');
        while ($r = $rs->fetch ()){
            $pu = new $userobjectname ();
            foreach ($this->_fieldPropList as $field=>$userField) {
                $pu->$userField = $r->$field;
            }
            $toReturn[] = $pu;
        }
        return $toReturn;
    }

    /**
    * gets the user
    */
    function get ($login){
        if (! ($ct = CopixLDAPFactory::getConnection ())){
            return false;
        }
        $rs = $ct->doQuery ('(uid='.$name.')');
        if (! ($result = $rs->fetch ())){
            return false;
        }
        return $result;
    }

    /**
    * find users by their login
    * @param string $pattern the pattern we're looking for (does not contains special chars)
    */
    function findByLogin ($pattern){
        $userobjectname = get_class ($this);

        $toReturn = array ();
        $ct = CopixLDAPFactory::getConnection ();
        $rs = $ct->doQuery ('(cn=*'.$pattern.')');

        while ($r = $rs->fetch ()){
            $pu = new $userobjectname ();
            foreach ($this->_fieldPropList as $field=>$userField) {
                $pu->$userField = $r->$field;
            }
            $toReturn[] = $pu;
        }
        return $toReturn;
    }

    /**
    * Updates the users password
    * @param string $login the login
    * @param string $newPassword the new password (clear)
    * @return boolean
    */
    function updatePassword ($login, $newPassword){
        $ct = CopixDBFactory::getConnection ();
        $query = 'update '.$this->userTable.' set '.$this->passwordfield.'='.$ct->quote ($this->cryptPassword($newPassword)).' where '.$this->loginField.'='.$ct->quote ($login);
        return $ct->doQuery ($query);
    }

    /**
    * Updates the users informations
    * @param string $login the login
    * @param array $informations the informations to update
    * @return boolean
    */
    function updateInformations ($login, $informations){
        $ct = & CopixDBFactory::getConnection ();
        //Creating hash table to get field names from properties
        foreach ($this->fieldPropList as $key=>$value){
            $hashProperties[$value] = $key;
        }

        $toUpdate = array ();
        foreach ($informations as $name=>$value){
            if (isset ($hashProperties[$name])){
                $toUpdate[$hashProperties[$name]] = $ct->quote ($value);
            }
        }

        if (count ($toUpdate)){
           $dbw = CopixDBFactory::getDbWidget ();
           return $dbw->doUpdate ($this->userTable, $toUpdate, $condition);
        }
        return false;
    }

    /**
    * Check if the login and password match
    * @param string $login the login
    * @param string $password the password (clear)
    */
    function checkPassword ($login, $password) {
        $ct = CopixDBFactory::getConnection ();
        $dbw = CopixDbFactory::getDbWidget  ();
        $query = 'select '.$this->passwordfield.' from '.$this->userTable.' where '.$this->loginField.'='.$ct->quote ($login);
        if ($r = $dbw->fetchFirst ($request)){
            return $r->{$this->passwordField} == $this->cryptPassword ($password);
        }
        return false;
    }
}
?>