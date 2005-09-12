<?php
/**
* @package   copix
* @subpackage copixdao
* @version   $Id: CopixDAOCompiler.class.php,v 1.14 2005/04/05 15:06:08 gcroes Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
require_once (COPIX_UTILS_PATH .'CopixFile.class.php');
require_once (COPIX_DAO_PATH.'CopixDAOSearchConditions.class.php');

/**
* The compiler for the DAO classes.
* @see CopixDAODefinitionV1 CopixDAODefinitionV0
* @see CopixDAOGeneratorV1 CopixDAOGeneratorV0
*/
class CopixDAOCompiler {
    /**
    * the current DAO id.
    */
    var $_DAOid ='';

    /**
    * the base name of dao object.
    */
    var $_baseName= '';

    /**
    *
    */
    var $_shortFileName='';

    /**
    * compile the given class id.
    */
    function compile ($DAOid) {

        // on verifie que le plugin copixdb est bien inclus : on en a besoin pour la génération de code
        $plugin = & $GLOBALS['COPIX']['COORD']->getPlugin ('copixdb');
        if($plugin === null){
            trigger_error (CopixI18N::get('copix:dao.error.plugin.notfound' , $this->_shortFileName ), E_USER_ERROR);
        }

        // recuperation du chemin et nom de fichier de definition xml de la dao
        $this->_DAOid = $DAOid;

        $selector = & CopixSelectorFactory::create ($this->_DAOid);
        if (!$selector->isValid){
            trigger_error (CopixI18N::get('copix:dao.error.selector.invalid', $this->_DAOid), E_USER_ERROR);
        }

        $this->_baseName = $selector->fileName;
        $this->_shortFileName = strtolower ($selector->fileName.'.dao.definition.xml');
        $fileName = $selector->getPath(COPIX_RESOURCES_DIR).$this->_shortFileName;

        if (! is_readable ($fileName) ){
            trigger_error (CopixI18N::get('copix:dao.error.definitionfile.unknow' , $fileName ), E_USER_ERROR);
        }

        // chargement du fichier XML
        require_once (COPIX_UTILS_PATH.'CopixSimpleXml.class.php');
        $xmlParser  = & new CopixSimpleXML ();
        if (!($parsedFile = $xmlParser->parseFile ($fileName))){
            $xmlParser->raiseError ();
        }

        // chargement de l'analyseur de définition et du générateur de code, adéquate à la version de la dao
        $attr = $parsedFile->attributes();
        $version=1;
        if(isset($attr['VERSION'])){
            $version=intval($attr['VERSION']);
        }

        if($version == 1) {
            require_once (COPIX_DAO_PATH.'CopixDAODefinitionV1.class.php');
            require_once (COPIX_DAO_PATH.'CopixDAOGeneratorV1.class.php');
            $userDefinition =& new CopixDAODefinitionV1 ($this);
            $generator = & new CopixDAOGeneratorV1($this);
        }else{
            require_once (COPIX_DAO_PATH.'CopixDAODefinitionV0.class.php');
            require_once (COPIX_DAO_PATH.'CopixDAOGeneratorV0.class.php');
            $userDefinition =& new CopixDAODefinitionV0 ($this);
            $generator = & new CopixDAOGeneratorV0($this);
        }

        // analyse de la définition
        $userDefinition->loadFrom($parsedFile);

        // inclusion des classes "surchargeant" les futures classes générées
        $DAOPath = $selector->getPath (COPIX_CLASSES_DIR).strtolower ($selector->fileName.'.dao.class.php');
        if (is_readable ( $DAOPath)){
            require_once ($DAOPath);
            $generator->setUserDAOPath($DAOPath);

            // eventuelle surcharge de la classe DAO
            $className = $this->_DAOClassName ();
            if (class_exists ($className)){
                $generator->setUserDAO(new $className ());
            }

            // eventuelle surcharge de la classe du record DAO
            $className = $this->_DAORecordClassName ();
            if (class_exists ($className)){
                $generator->setUserDAORecord(new $className ());
            }
        }

        $generator->setUserDefinition($userDefinition);

        // génération des classes PHP correspondant à la définition de la DAO
        $compiled = '<?php ';
        $compiled .= $generator->compileDAORecordClass ();
        $compiled .= $generator->compileDAO ();
        $compiled .="\n?>";
        $objectWriter = & new CopixFile ();
        $objectWriter->write (CopixDAOFactory::_getCompiledPath ($DAOid), $compiled);
    }

    /**
    * gets the single class name.
    */
    function _DAORecordClassName (){
        return 'DAORecord'.$this->_baseName;
    }

    /**
    * gets the DAO classname.
    */
    function _DAOClassName (){
        return 'DAO'.$this->_baseName;
    }


    function doDefError($messageI18N, $arg1=null){
        $arg=array($this->_shortFileName);
        if(is_array($arg1)){
            $arg=array_merge($arg, $arg1);
        }else{
            $arg[]=$arg1;
        }
        trigger_error (CopixI18N::get($messageI18N,$arg),E_USER_ERROR);
    }

    function doGenError($messageI18N, $arg1=null){
        $arg=array($this->_shortFileName);
        if(is_array($arg1)){
            $arg=array_merge($arg, $arg1);
        }else{
            $arg[]=$arg1;
        }
        trigger_error (CopixI18N::get($messageI18N, $arg),E_USER_ERROR);
    }
}
?>
