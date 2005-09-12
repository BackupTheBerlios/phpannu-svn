<?php
/**
* @package   copix
* @subpackage copixdao
* @version   $Id: CopixDAOGenerator.class.php,v 1.9 2005/02/17 08:15:24 gcroes Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Base class for DAO generation
* @see CopixDAOGeneratorV1
* @abstract
*/
class CopixDAOGenerator {

    /**
    * The compiler object
    * @var CopixDAOCompiler
    */
    var $_compiler=null;

    /**
    * the user DAO if any.
    * @var Object
    */
    var $_userDAO = null;

    /**
    * the user DAO if any.
    * @var Object
    */
    var $_userDAORecord= null;

    /**
    * the user definition if any.
    */
    var $_userDefinition = null;

    /**
    * The user DAOPath
    * @var string
    */
    var $_userDAOPath=null;
    
    /**
    * The user DAORecord ClassName
    * @var string
    */
    var $_DAORecordClassName = null;
    
    /**
    * the user DAO classname
    * @var string
    */
    var $_DAOClassName=null;
    
    /**
    * The compiled DAORecord classname
    * @var string
    */
    var $_compiledDAORecordClassName = null;
    
    /**
    * The compiled DAO classname
    * @var string
    */
    var $_compiledDAOClassName=null;

    /**
    * constructor
    * @param object $compiler
    */
    function CopixDAOGenerator( & $compiler){
        $this->_compiler= & $compiler;

        $this->_compiledDAOClassName = CopixDAOFactory::getDAOName ($compiler->_DAOid);
        $this->_compiledDAORecordClassName =CopixDAOFactory::getDAORecordName($compiler->_DAOid);
    }

    /**
    * Sets the user DAO 
    * @param object $userDAO
    */
    function setUserDAO (& $userDAO){
        $this->_userDAO = & $userDAO;
        $this->_DAOClassName = get_class($userDAO);
    }

    /**
    * Sets the user DAO record
    * @param object $userDAO
    */
    function setUserDAORecord (& $userDAORecord){
        $this->_userDAORecord = & $userDAORecord;
        $this->_DAORecordClassName = get_class ($userDAORecord);
    }

    /**
    * Sets the user's definition of the DAO
    */
    function setUserDefinition (& $userDefinition){
        $this->_userDefinition = & $userDefinition;
    }

    /**
    * Sets the user DAO Path
    * @param string $userpath
    */
    function setUserDAOPath (& $userpath){
        $this->_userDAOPath = & $userpath;
    }

    /**
    * gets the PHP class definition for the DAO
    * @return string
    */
    function compileDAO () { return '';}
    
    /**
    * gets the PHP class definition for the DAORecord
    * @return string
    */
    function compileDAORecordClass () {return ''; }
}
?>