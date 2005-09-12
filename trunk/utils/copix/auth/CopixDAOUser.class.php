<?php
/**
* @package   copix
* @subpackage auth
* @version   $Id: CopixDAOUser.class.php,v 1.2 2005/02/18 12:34:06 gcroes Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @ignore
*/
require_once (COPIX_AUTH_PATH.'CopixUser.class.php');

/**
* This is the base class for authentification process using a DAO
* You should extend this class to fit your needs.
* @package   copix
* @subpackage auth
* @see ProjectUser.class.php
*/
class CopixDAOUser extends CopixUser {
    /**
    * The DAO we should use
    * @access private
    * @var string
    */
    var $_dao  = 'copix:copixuser';

    /**
    * the password field
    * @access private
    * @var string
    */
    var $_passwordField = 'password_cusr';
    
    /**
    * the login field
    * @access private
    * @var string
    */
    var $_loginField = 'login_cusr';

    /**
     * Constructor
     */
    function CopixDAOUser (){
        parent::CopixUser ();
    }

    /**
     * Says if the login is successfull
     * If successfull loads the users properties
     * @param string  $name   login
     * @param string  $password   mot de passe
     * @return    boolean indique si authentification ok ou pas
     */
    function _doLogin ($name, $password=null){
        $dao    = CopixDAOFActory::create($this->_dao);
        if ($record = $dao->get ($name)){
            if ($record->{$this->_passwordField} == $this->cryptPassword ($password)){
                $this->_loadParams ($record);
                return true;
            }
        }else{
            return false;
        }
    }

    /**
    * Charge les paramètres sur l'objet utilisateur, a partir de la requête
    * envoyée.
    */
    function _loadParams ($objInfos){
        //parcour des champs, mise à jour de l'utilisateur.
        foreach (get_object_vars($objInfos) as $field=>$value) {
            $this->$field = $value;
        }
    }
    
    /**
    * gets the user
    */
    function get ($login){
        $dao = & CopixDAOFactory::create ($this->_dao);
        return $dao->get ($login);
    }

    /**
     * Récupération de la liste des utilisateurs.
     * @return    array   liste des utilisateurs
     */
    function getList (){
        $dao = & CopixDAOFactory::create ($this->_dao);
        return $dao->fetchAll ();
    }
    
    /**
    * gets the user list
    * @param string $pattern the pattern we're looking for (does not contains any special char)
    * @return array
    */
    function findByLogin ($pattern){
        $dao = & CopixDAOFactory::create ($this->_dao);
        $sp = & CopixDAOFactory::createSearchParams ();
        $sp->addCondition ($this->_loginField, 'like', '%'.$pattern.'%');
        return $dao->findBy ($sp);
    }

    /**
    * Updates the users password
    * @param string $login the login
    * @param string $newPassword the new password (clear)
    * @return boolean
    */
    function updatePassword ($login, $newPassword){
        $dao = CopixDAOFactory::create ($this->_dao);
        if ($record = $dao->get ($login)){
            $record->{$this->_passwordField} = $this->cryptPassword ($newPassword);
            return $dao->update ($record);
        }
        return false;
    }

    /**
    * Updates the users informations
    * @param string $login the login
    * @param array $informations the informations to update
    * @return boolean
    */
    function updateInformations ($login, $informations){
        $dao = CopixDAOFactory::create ($this->_dao);
        $record = $dao->get ($login);
        
        foreach ($informations as $name=>$value){
           $record->$name = $value;
        }

        return $dao->update ($record);
    }

    /**
    * Check if the login and password match
    * @param string $login the login
    * @param string $password the password (clear)
    */
    function checkPassword ($login, $password) {
        $dao    = CopixDAOFActory::create($this->_dao);
        if ($record = $dao->get ($name)){
            return $record->{$this->_passwordField} == $this->cryptPassword ($password);
        }
        return false;
    }
}
?>