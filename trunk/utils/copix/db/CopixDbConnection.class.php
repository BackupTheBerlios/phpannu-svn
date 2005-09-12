<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbConnection.class.php,v 1.19.2.2 2005/08/17 20:06:54 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class CopixDBConnection {
    /**
    * indique si les requètes doivent être envoyée sur le debugger
    * @var boolean
    */
    var $_debugQuery = false;

    /**
    * indique si il faut provoquer les exceptions
    * @var boolean
    */
    var $_displayError=true;

    /**
    * the internal connection.
    */
    var $_connection = null;

    /**
    * the profil the connection is using
    * @var CopixDBProfil
    */
    var $profil;

    /**
    * If an error occured
    * @var boolean
    */
    var $hasError = false;

    /**
    * The last error message if any
    * @var string
    */
    var $msgError = '';

    /**
    * Are we using an automatic commit ?
    * @var boolean
    */
    var $autocommit = true;

    var $scriptComment = '/^\s*#/';
    var $scriptReplaceFrom = "\\r\\n";
    var $scriptReplaceBy = " ";
    var $scriptEndOfQuery = '/;\s*$/';

    /**
    * @constructor
    */
    function CopixDBConnection(){
            $GLOBALS['COPIX']['DB']['CONNECTIONS'][] = & $this;
    }

    /**
    * Connects to the DB
    * @param   CopixDbProfil   $profil   profil de connection
    */
    function connect (& $profil){
        $this->profil = & $profil;
        $this->_connection=$this->_connect();

        if($this->_connection === false || $this->_connection === null)
            $this->_doError();

    }

    /**
     * pour tester les paramètres d'un profil (lors d'une installation par exemple)
     */
    function testProfil( &$profil){

        $oldprofil = & $this->profil;
        $oldcnx = $this->_connection;

        $this->profil = & $profil;
        $this->_connection = $this->_connect();

        $this->profil = & $oldprofil;
        if($this->_connection === false || $this->_connection === null){
            $this->_connection = $oldcnx;
            return false;
        }

        $this->_disconnect();
        $this->_connection = $oldcnx;
        return true;

    }


    /**
    * Disconnects from the db
    */
    function disconnect (){
        if($this->_connection !== false && $this->_connection !== null)
        $this->_disconnect ();
    }

    /**
    * Says if the connection has been established
    * @return boolean
    */
    function isConnected (){
        return $this->_connection !== null && $this->_connection !== false;
    }

    /**
    * Launch a SQL Query
    * @param   string   $queryString   the SQL query
    * @return  CopixDbResultSet  if SQL Select.
    *          boolean if update / insert / delete.
    *          False if the query has failed.
    */
    function & doQuery ($queryString){
        //on se souvient de la dernière requete.
        $this->lastQuery = $queryString;
        $this->_debug ($queryString);
        $this->hasError = false;

        $result = @ $this->_doQuery ($queryString);
        if (!$result){
            $this->_doWarning ($queryString);
        }

        return $result;
    }

    /**
    * SQL Select query with a limit clause
    */
    function doLimitQuery($queryString, $offset, $number){
        //on se souvient de la dernière requete.
        $this->lastQuery = $queryString;
        $this->_debug ($queryString);
        $this->hasError = false;

        $result = $this->_doLimitQuery ($queryString, intval($offset), intval($number));
        if (!$result){
            $this->_doWarning ($queryString);
        }

        return $result;
    }

    /**
    * Escape and quotes strings. if null, will only return the text "NULL"
    * @param string $text   string to quote
    * @return string
    */
    function quote($text, $checknull=true){
        if($checknull)
        return (is_null ($text) ? 'NULL' : "'".$this->_quote($text)."'");
        else
        return "'".$this->_quote ($text)."'";
    }

    /**
    * alias de quote
    * @deprecated
    */
    function formatText ($text){
        trigger_error ('deprecated "formatText"', E_USER_WARNING);
        return $this->quote($text);
    }

    /**
    * génère un warning, à partir du message d'erreur renvoyé par la base
    * @param   string   $otherMsg   message additionnel
    * @access private
    * @see CopixDbConnection::_displayError CopixDbConnection::getErrorMessage
    */
    function _doWarning($otherMsg=''){
        if($this->_connection)
            $this->msgError=$this->getErrorMessage();
        if ($otherMsg){
            $this->msgError.=' ('.$otherMsg.')';
        }
        $this->hasError=true;
        if ($this->_displayError){
            trigger_error($this->msgError,E_USER_WARNING);
        }
    }

    /**
    * génère une erreur, à partir du message d'erreur renvoyé par la base
    * @param   string   $otherMsg   message additionnel
    * @access private
    * @see CopixDbConnection::_displayError CopixDbConnection::getErrorMessage
    */
    function _doError($otherMsg=''){
        if($this->_connection)
            $this->msgError = $this->getErrorMessage ();
        if($otherMsg){
            $this->msgError.=' ('.$otherMsg.')';
        }
        $this->hasError = true;
        if($this->_displayError){
            trigger_error($this->msgError, E_USER_ERROR);
        }
    }

    /**
    * affiche un message pour débuggage. Utilisé pour afficher les requètes
    * quand _debugQuery est activé
    * @param   string   message à afficher pour le débuggage
    * @access private
    * @see CopixDbConnection::_debugQuery
    */
    function _debug($msg){
        if ($this->_debugQuery){
            if (isset($GLOBALS['COPIX']['DEBUG'])){
                $GLOBALS['COPIX']['DEBUG']->addInfo($msg, 'CopixDb :');
            }else{
                echo '<br />', $msg, '<br />';
            }
        }
    }

    /**
    * doSQLScript
    *
    * Execute the sql script into the current DB
    *
    * @param string $file Filename of the script to be executed
    * @private
    */
    function doSQLScript ($file) {

        $lines = file($file);
        $cmdSQL = '';
        $nbCmd = 0;
        foreach ((array)$lines as $key=>$line) {
            if ((!preg_match($this->scriptComment,$line))&&(strlen(trim($line))>0)) { // la ligne n'est ni vide ni commentaire
               //$line = str_replace("\\'","''",$line);
               $line = str_replace($this->scriptReplaceFrom, $this->scriptReplaceBy,$line);

                $cmdSQL.=$line;

                if (preg_match($this->scriptEndOfQuery,$line)) {
                    //Si on est à la ligne de fin de la commande on l'execute
                    // On nettoie la commande du ";" de fin et on l'execute
                    $cmdSQL = preg_replace($this->scriptEndOfQuery,'',$cmdSQL);
                    $this->doQuery ($cmdSQL);
                    $nbCmd++;
                    $cmdSQL = '';
                }
            }
        }
        return $nbCmd;
    }


    /**
    * sets the autocommit state
    * @param boolean state the status of autocommit
    */
    function setAutoCommit($state=true){
        $this->autocommit = $state;
        $this->_autoCommitNotify ($this->autocommit);
    }

    /**
    * Notify the changes on autocommit
    * Drivers may overload this
    * @param boolean state the new state of autocommit
    */
    function _autoCommitNotify ($state){
    }

    // ====================== méthodes à surcharger (éventuellement)

    /**
    * renvoi la connection, ou false/null si erreur
    * @abstract
    */
    function _connect (){
        return null;
    }

    /**
    * effectue la deconnection (pas besoin de faire le test sur l'id de connection
    * @abstract
    */
    function _disconnect (){
        return null;
    }

    /**
    * effectue la requete
    * @return CopixDbResultSet/boolean    selon la requete, un recordset/true ou false/null si il y a une erreur
    * @abstract
    */
    function &_doQuery ($queryString){
        trigger_error(CopixI18N::get('copix:copix.error.functionnality','doQuery'),E_USER_WARNING);
        return $return = null;
    }

    /**
    * effectue une requete avec liste de résultats limités
    * @return CopixDbResultSet/boolean    selon la requete, un recordset/true ou false/null si il y a une erreur
    * @abstract
    */
    function &_doLimitQuery ($queryString, $offset, $number){
        trigger_error(CopixI18N::get('copix:copix.error.functionnality','doLimitQuery'),E_USER_WARNING);
        $ret = null;
        return $ret;
    }


    /**
    * @abstract
    */
    function & begin (){
        trigger_error(CopixI18N::get('copix:copix.error.functionnality','begin'),E_USER_WARNING);
        $ret = null;
        return $ret;
    }

    /**
    * @abstract
    */
    function & commit (){
        trigger_error(CopixI18N::get('copix:copix.error.functionnality','commit'),E_USER_WARNING);
        $ret = null;
        return $ret;
    }

    /**
    * @abstract
    */
    function & rollBack (){
        trigger_error(CopixI18N::get('copix:copix.error.functionnality','rollBack'),E_USER_WARNING);
        $ret = null;
        return $ret;
    }

    /**
    * @abstract
    */
    function getErrorMessage(){
        return '';
    }

    /**
    * @abstract
    */
    function getErrorCode(){
        return '';
    }

    /**
    * renvoi une chaine avec les caractères spéciaux échappés
    * à surcharger pour tenir compte des fonctions propres à la base (mysql_escape_string etC...)
    * @abstract
    * @access private
    */
    function _quote($text){
        return addslashes($text);
    }

    /**
    * @abstract
    */
    function affectedRows($ressource = null){
        trigger_error(CopixI18N::get('copix:copix.error.functionnality','affectedRows'),E_USER_WARNING);
    }

    /**
    * @abstract
    */
    function lastId($fromSequence=''){
        trigger_error(CopixI18N::get('copix:copix.error.functionnality','lastId'),E_USER_WARNING);
    }

    /**
    * Renvoi le type de jointure à utiliser dans les requêtes sql.
    * @return string le type de jointure (ORACLE ou MYSQL).
    */
    function joinType () {
        if ($this->profil->driver == 'oci8') {
            return 'ORACLE';
        }else{
            return 'MYSQL';
        }
    }
}
?>