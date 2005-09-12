<?php
/**
* @package   copix
* @subpackage auth
* @version   $Id: CopixDBUser.class.php,v 1.13 2005/04/05 15:06:08 gcroes Exp $
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
* This is the base class for authentification process
* You should extend this class to fit your needs.
* @package   copix
* @subpackage auth
* @see ProjectUser.class.php
*/
class CopixDBUser extends CopixUser {
    /**
    * the base queryString we'll use to connect the user to
    * There are special variables taht will be replaced:
    * [--FIELDS--] replaced by the fields list
    * [--USERTABLE--] replaced by the table where the users are
    * [--LOGINFIELD--] replaced by the login field
    * [--LOGIN--] replaced by the login we try to use to log in
    * [--PASSWORDFIELD--] Where the password is
    * [--PASSWORD--] the given password we're trying to log in with
    * @var string
    */
    var $loginRequest  = 'SELECT [--FIELDS--] from [--USERTABLE--]
                         WHERE  [--LOGINFIELD--] = [--LOGIN--]
                                AND [--PASSWORDFIELD--] = [--PASSWORD--]';
    /**
    * The fields we want to retrieve when logged in
    * @var array
    */
    var $fieldPropList = array ('login_cusr'=>'login', 'email_cusr'=>'email');

    /**
    * The table we wants to use for the login process
    * @var string
    */
    var $userTable     = 'copixuser';

    /**
    * The login field
    * @var string
    */
    var $loginField    = 'login_cusr';
    
    /**
    * the password field
    * @var string
    */
    var $passwordField = 'password_cusr';

    /**
     * Constructor
     */
    function CopixDBUser (){
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
        //Création de la requête utilisateur, par remplacement des différents
        //Champs de paramètres si fournits.
        $request = $this->_getParsedRequest ($this->loginRequest);

        //remplacement de login / password.
        $request = $this->_getParsedRequestLoginPassword ($request, $name, $password);

        $dbw = CopixDbFactory::getDbWidget();
        if( $r = $dbw->fetchFirst ($request)){
            $this->_loadParams ($r);
            return true;
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
        foreach ($this->fieldPropList as $field=>$userField) {
            $this->$userField = $objInfos->$field;
        }
    }

    /**
     * Récupération de la liste des utilisateurs.
     * @return    array   liste des utilisateurs
     */
    function getList (){
        $userobjectname = get_class ($this);
        $toReturn = array ();
        $ct = CopixDBFactory::getDBWidget ();

        $rs = $ct->doSelect ($this->userTable, array_keys($this->fieldPropList), array ());
        while ($r = $rs->fetch ()){
            $pu = new $userobjectname ();
            foreach ($this->fieldPropList as $field=>$userField) {
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
        $userobjectname = get_class ($this);
        $ct = CopixDBFactory::getConnection();
        $query = 'select '.implode (',', array_keys($this->fieldPropList)).' from '.$this->userTable.' where '.$this->loginField.' = '.$ct->quote ($login);
        $rs = $ct->doQuery ($query);
        if ($r = $rs->fetchUsing ($userobjectname)){
            $pu = new $userobjectname ();
            foreach ($this->fieldPropList as $field=>$userField) {
                $pu->$userField = $r->$field;
            }
            return $pu;

        }
        return null;
    }
    
    /**
    * find users by their login
    * @param string $pattern the pattern we're looking for (does not contains special chars)
    */
    function findByLogin ($pattern){
        $userobjectname = get_class ($this);
        $ct = CopixDBFactory::getConnection();
        $query = 'select '.implode (',', array_keys($this->fieldPropList)).' from '.$this->userTable.' where '.$this->loginField.' like '.$ct->quote ('%'.$pattern.'%');
        $rs = $ct->doQuery ($query);
        while ($r = $rs->fetch ()){
            $pu = new $userobjectname ();
            foreach ($this->fieldPropList as $field=>$userField) {
                $pu->$userField = $r->$field;
            }
            $toReturn[] = $pu;
        }
        return $toReturn;
    }

    /**
     * Gets the right parsed query (replace the special patterns with the matching fields)
     * @param string  $request    requête SQL avec les tags à remplacer
     * @return    string  requête finale
     */
    function _getParsedRequest ($request){
        //la liste des champs.
        $fieldString = implode (', ', array_keys ($this->fieldPropList));
        $request = str_replace('[--FIELDS--]', $fieldString, $request);

        //remplacement de la table des users.
        $request  = str_replace ('[--USERTABLE--]', $this->userTable, $request);

        //Remplacement du champ de login.
        $request = str_replace ('[--LOGINFIELD--]', $this->loginField, $request);

        //remplacement du champ password.
        return str_replace ('[--PASSWORDFIELD--]', $this->passwordField, $request);
    }

    /**
     * récupération de la requete avec les logins / password.
     * @param string  $request    requête SQL avec les tags login/password à remplacer
     * @return    string  requête finale
     */
    function _getParsedRequestLoginPassword ($request, $login, $password){
        $ct      = & CopixDbFactory::getConnection ();
        $request = str_replace ('[--LOGIN--]', $ct->quote ($login), $request);
        return   str_replace ('[--PASSWORD--]',  $ct->quote ($this->cryptPassword ($password)), $request);
    }

    /**
    * Updates the users password
    * @param string $login the login
    * @param string $newPassword the new password (clear)
    * @return boolean
    */
    function updatePassword ($login, $newPassword){
        $ct    = CopixDBFactory::getConnection ();
        $query = 'update '.$this->userTable.' set '.$this->passwordField.'='.$ct->quote ($this->cryptPassword($newPassword)).' where '.$this->loginField.'='.$ct->quote ($login);
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
        foreach (get_object_vars ($informations) as $name=>$value){
            if (isset ($hashProperties[$name]) && $name != 'login'){
                $toUpdate[$hashProperties[$name]] = $ct->quote ($value);
            }
        }

        if (count ($toUpdate)){
           $dbw = CopixDBFactory::getDbWidget ();
           return $dbw->doUpdate ($this->userTable, $toUpdate, array ($this->loginField=>$ct->quote ($login)));
        }
        return false;
    }
    
    /**
    * Creates a user.
    */
    function createUser ($informations){
        $ct = & CopixDBFactory::getConnection ();
        //Creating hash table to get field names from properties
        foreach ($this->fieldPropList as $key=>$value){
            $hashProperties[$value] = $key;
        }

        $toInsert = array ();
        foreach (get_object_vars ($informations) as $name=>$value){
            if (isset ($hashProperties[$name])){
                $toInsert[$hashProperties[$name]] = $ct->quote ($value);
            }
        }
        $toInsert[$this->passwordField] = $ct->quote ($this->cryptPassword ($informations->password));

        if (count ($toInsert)){
           $dbw = CopixDBFactory::getDbWidget ();
           return $dbw->doInsert ($this->userTable, $toInsert);
        }
        return false;
    }

    /**
    * Check if the login and password match
    * @param string $login the login
    * @param string $password the password (clear)
    */
    function checkPassword ($login, $password) {
        $ct  = CopixDBFactory::getConnection ();
        $dbw = CopixDbFactory::getDbWidget  ();
        $query = 'select '.$this->passwordfield.' from '.$this->userTable.' where '.$this->loginField.'='.$ct->quote ($login);
        if ($r = $dbw->fetchFirst ($request)){
            return $r->{$this->passwordField} == $this->cryptPassword ($password);
        }
        return false;
    }
    
    /**
    * Deletes a user from the database
    * @param string $login the login we wants to delete
    * @return boolean if the operation succeed
    */
    function delete ($login){
        $ct  = CopixDBFactory::getConnection ();
        $query = 'delete from '.$this->userTable.' where '.$this->loginField.'='.$ct->quote ($login);
        return $ct->doQuery ($query);
    }

    /**
    * Gets the user properties
    * @return array of CopixUserField
    */
    function getProperties (){
        $toReturn = parent::getProperties ();

        $toReturn['login']    = & new CopixUserField ('login', 'copix:auth.userField.login', 'string', 
                                   array (CopixUserField::forList()=>1,
                                          CopixUserField::forAdmin()=>1,
                                          CopixUserField::forUserUpdate()=>0,
                                          CopixUserField::forUser()=>1));

        $toReturn['password'] = & new CopixUserField ('password', 'copix:auth.userField.password', 'string',
                                   array (CopixUserField::forList()=>0,
                                          CopixUserField::forAdmin()=>1,
                                          CopixUserField::forUserUpdate()=>1,
                                          CopixUserField::forUser()=>1));

        $toReturn['email'] = & new CopixUserField ('email', 'copix:auth.userField.email', 'string',
                                   array (CopixUserField::forList()=>1,
                                          CopixUserField::forAdmin()=>1,
                                          CopixUserField::forUserUpdate()=>1,
                                          CopixUserField::forUser()=>1));
        return $toReturn;
    }
}
?>