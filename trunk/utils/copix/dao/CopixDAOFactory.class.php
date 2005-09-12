<?php
/**
* @package   copix
* @subpackage copixdao
* @version   $Id: CopixDAOFactory.class.php,v 1.11.4.2 2005/08/17 20:06:53 laurentj Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Factory to create automatic DAO.
*/
class CopixDAOFactory {
    /**
    * singleton.
    */
    function & instance (){
        static $instance = false;
        if ($instance === false){
            $instance = new CopixDAOFactory ();
        }
        return $instance;
    }

    /**
    * creates a DAO from its Id.
    * If no dao is founded, try to compile a DAO from the user definitions.
    */
    function & create ($DAOid){
        $instance = & CopixDAOFactory::instance ();
        $DAOid    = $instance->_fullQualifier ($DAOid);
        $instance->fileInclude ($DAOid);
        require_once ($instance->_getCompiledPath ($DAOid));
        $className = $instance->getDAOName ($DAOid);
        $obj = & new $className ();
        return $obj;
    }

    /**
    * Creates a DAO from its ID. Handles a singleton of the DAO.
    */
    function & instanceOf ($DAOid) {
        $instance = & CopixDAOFactory::instance ();
        $DAOid = $instance->_fullQualifier ($DAOid);

        if (! isset ($instance->_daoSingleton[$DAOid])){
            $instance->_daoSingleton[$DAOid] = & $instance->create ($DAOid);
        }
        return $instance->_daoSingleton[$DAOid];
    }

    /**
    * creates a record object
    */
    function & createRecord ($DAOid){
        $instance = & CopixDAOFactory::instance ();
        $DAOid = $instance->_fullQualifier ($DAOid);

        $instance->fileInclude ($DAOid);

        $className = $instance->getDAORecordName ($DAOid);
        $obj = & new $className ();
        return $obj;
    }

    /**
    * includes the compiled
    */
    function fileInclude ($DAOid){
        $instance = & CopixDAOFactory::instance ();
        $DAOid = $instance->_fullQualifier ($DAOid);

        //si oui, compilation et retour.
        if ($instance->_needsCompilation ($DAOid)){
            require_once (COPIX_DAO_PATH.'CopixDAOCompiler.class.php');
            $compiler = & new CopixDAOCompiler ();
            $compiler->compile ($DAOid);
        }
        require_once ($instance->_getCompiledPath ($DAOid));
    }

    /**
    * @deprecated use createSearchConditions instead, better api
    */
    function & createSearchParams ($kind = 'AND'){
        require_once (COPIX_DAO_PATH.'CopixDAOSearchParams.class.php');
        $obj = & new CopixDAOSearchParams ($kind);
        return $obj;
    }

    function & createSearchConditions ($kind = 'AND'){
        require_once (COPIX_DAO_PATH.'CopixDAOSearchConditions.class.php');
        $obj = & new CopixDAOSearchConditions ($kind);
        return $obj;
    }

    /**
    * gets the expected DAO compiled path.
    * have to be called with the full qualifier.
    */
    function _getCompiledPath ($DAOid){
       return $GLOBALS['COPIX']['CONFIG']->compile_dao_dir.strtolower (str_replace (array ('|', ':'), array ('_mod-', '_res-'), $DAOid)).'.dao.class.php';
    }

    /**
    * gets the expected DAO users element files path.
    */
    function _getUsersFilesPath ($DAOid){
        $selector = & CopixSelectorFactory::create ($DAOid);
        if (!$selector->isValid){
            return array ();
        }
        $fileName = strtolower($selector->fileName);
        //array (dao.class.php, .dao.definition.xml)
        return array ($selector->getPath (COPIX_CLASSES_DIR).$fileName.'.dao.class.php',
                      $selector->getPath (COPIX_RESOURCES_DIR).$fileName.'.dao.definition.xml');
    }

    /**
    * the function says wether or not we need to compile the dao.
    */
    function _needsCompilation ($DAOid){
        if ($GLOBALS['COPIX']['CONFIG']->compile_dao_forced){
            return true;
        }
        //regarde s'il existe la classe compilée.
        $compiledPath = $this->_getCompiledPath ($DAOid);
        if (!is_readable($compiledPath)){
            //compiled file does not exists.....
            return true;
        }

        //do we want to check if the compiled file is up to date ?
        if ($GLOBALS['COPIX']['CONFIG']->compile_dao_check){
            $compiledTime = filemtime ($compiledPath);
            $usersPath = $this->_getUsersFilesPath ($DAOid);

            foreach ($usersPath as $name){
                //checks the file age.
                if (is_readable ($name)){
                    //not readable, we may consider it is not needed.
                    if ($compiledTime < filemtime ($name)){
                        //the file time is greater than the compiled file time.
                        //we need to refresh.
                        return true;
                    }
                }
            }
        }
        //nothing matched.... the file appears to be up to date.
        return false;
    }

    /**
    * gets the name of the class from its DAOId
    */
    function getDAOName ($DAOid){
        $selector = & CopixSelectorFactory::create ($DAOid);
        if ($selector->isValid){
            return 'CompiledDAO'.$selector->fileName;
        }
        trigger_error (CopixI18N::get('copix:dao.error.selector.invalid',$DAOId),E_USER_ERROR);
    }

    /**
    * gets the fully qualified selector
    */
    function _fullQualifier ($DAOid){
        $selector = & CopixSelectorFactory::create ($DAOid);
        return $selector->getSelector ();
    }

    /**
    * gets the name of the final DAORecord
    */
    function getDAORecordName ($DAOid){
        $selector = & CopixSelectorFactory::create ($DAOid);
        if ($selector->isValid){
            return 'CompiledDAORecord'.$selector->fileName;
        }
        trigger_error (CopixI18N::get('copix:dao.error.selector.invalid',$DAOId),E_USER_ERROR);
    }
}
?>