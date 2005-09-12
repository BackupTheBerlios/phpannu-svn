<?php
/**
* @package   copix
* @subpackage copixdao
* @version   $Id: CopixDAODefinitionV0.class.php,v 1.14 2005/04/27 20:06:11 laurentj Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Analyseur des fichiers XML de définition DAO, de la syntaxe daodefinition version 0 (alpha)
* @deprecated
*/

class CopixDAODefinitionV0 {
    /**
    * the field list.
    */
    var $_properties = array ();
    /**
    * the table name
    */
    var $_tableName;

    /**
    * the connection name to use.
    * null if you wants to use the default connection
    */
    var $_connectionName = null;


    function CopixDAODefinitionV0(& $compiler){
        $this->_compiler= & $compiler;
    }


    /**
    * adds a field to the list.
    */
    function addField ($field){
        $this->_properties[$field->name] = $field;
    }

    function setTableName ($tableName){
        $this->_tableName = $tableName;
    }

    function getTableName (){
        return $this->_tableName;
    }

    function getFields (){
        return $this->_properties;
    }

    /**
    * loads an XML file if given.
    */
    function loadFrom( & $parsedFile){

        if (!isset ($parsedFile->GENERAL->TABLE)){
            $this->_compiler->doDefError('copix:dao.error.definitionfile.table.missing');
        }
        $tableName = $parsedFile->GENERAL->TABLE->attributes ();
        if (!isset ($tableName['NAME'])){
            $this->_compiler->doDefError('copix:dao.error.definitionfile.table.name');
        }
        $this->setTableName ($tableName['NAME']);

        if (isset ($parsedFile->GENERAL->CONNECTION)){
            $connection = $parsedFile->GENERAL->CONNECTION->attributes ();
            if (isset ($connection['NAME'])){
                $this->_connectionName = $connection ['NAME'];
            }
        }

        //adds the fields
        if(isset ($parsedFile->FIELDS) && isset ($parsedFile->FIELDS->FIELD)){
            if (is_array ($parsedFile->FIELDS->FIELD)){
                foreach ($parsedFile->FIELDS->FIELD as $field){
                    $attributes = $field->attributes ();
                    $this->addField (new CopixPropertyForDAOV0 ($attributes, $this));
                }
            }else{
                $this->addField (new CopixPropertyForDAOV0 ($parsedFile->FIELDS->FIELD->attributes, $this));
            }
        }
    }
}

class CopixPropertyForDAOV0 {
    /**
    * the name of the property of the object
    */
    var $name;

    /**
    * the name of the field in table
    */
    var $fieldName;

    /**
    * give the regular expression that needs to be matched against.
    */
    var $regExp;
    /**
    * says if the field is required.
    */
    var $required;
    /**
    * The i18n key for the caption of the element.
    */
    var $captionI18N;
    /**
    * Is it a string ?
    */
    var $isString;
    /**
    * Says if it's a primary key.
    */
    var $isPK;
    /**
    * Say if it's a foreign key
    */
    var $isFK;

    /**
    * Update motif for the SQL update query
    */
    var $updateMotif='%s';
    /**
    * Insert motif for the SQL update query
    */
    var $insertMotif='%s';
    /**
    * Select motif for the SQL update query
    */
    var $selectMotif='%s';

    /**
    * the maxlength of the string
    * @var int
    */
    var $maxlength = null;

    /**
    * The sequence name for auto ID.
    */
    var $sequenceName='';

    /**
    * constructor.
    */
    function CopixPropertyForDAOV0 ($params, &$def){
        if (!isset ($params['NAME'])){
            $this->_compiler->doDefError('copix:dao.error.definitionfile.missing.attr',array('name', 'field'));
        }
        $this->name       = $params['NAME'];
        $this->fieldName  = isset ($params['FIELDNAME']) ? $params['FIELDNAME'] : $this->name;

        $this->regExp     = isset ($params['REGEXP']) ? $params['REGEXP'] : null;
        $this->required   = isset ($params['REQUIRED']) ? $this->_getBool ($params['REQUIRED']) : false;

        $this->maxlength  = isset ($params['MAXLENGTH']) ? ($params['MAXLENGTH']) : null;

        $this->captionI18N = isset ($params['CAPTIONI18N']) ? $params['CAPTIONI18N'] : null;
        $this->caption     = isset ($params['CAPTION']) ? $params['CAPTION'] : null;
        if ($this->caption == null && $this->captionI18N == null){
            $this->_compiler->doDefError('copix:dao.error.definitionfile.properties.caption.missing',$this->name);
        }

        $this->isPK       = isset ($params['PK']) ? $this->_getBool ($params['PK']): false;
        if (!isset ($params['TYPE'])){
            $this->_compiler->doDefError('copix:dao.error.definitionfile.missing.attr',array('type', 'field'));
        }
        $this->needsQuotes = $this->_typeNeedsQuotes ($params['TYPE']);
        if (!in_array (strtolower ($params['TYPE']), array ('autoincrement', 'bigautoincrement', 'int',
                                    'integer', 'varchar', 'string', 'varchardate', 'date', 'numeric', 'double', 'float'))){
           $this->_compiler->doDefError('copix:dao.error.definitionfile.wrong.attr', array($params['TYPE'], $this->fieldName));
        }
        $this->type = $params['TYPE'];

        if(($this->type == 'autoincrement' || $this->type == 'bigautoincrement')&& isset ($params['SEQUENCE'])){
            $this->sequenceName = $params['SEQUENCE'];
        }

        $this->fkTable  = isset ($params['FKTABLE']) ? $params['FKTABLE'] : null;
        $this->fkFields = isset ($params['FKFIELDS']) &&  $this->fkTable !== null ? explode (';', $params['FKFIELDS']) : array ();
        $this->isFK     =  $this->fkTable !== null;

        if(!$this->isPK && !$this->isFK){ // on ignore ces propriétés sur les champs PK
        $this->updateMotif = isset($params['UPDATEMOTIF']) ? $params['UPDATEMOTIF'] :'%s';
        $this->insertMotif = isset($params['INSERTMOTIF']) ? $params['INSERTMOTIF'] :'%s';
        $this->selectMotif = isset($params['SELECTMOTIF']) ? $params['SELECTMOTIF'] :'%s';
        }
    }

    /**
    * just a quick way to retriveve boolean values from a string.
    *  will accept yes, true, 1 as "true" values
    *  the rest will be considered as false values.
    * @return boolean true / false
    */
    function _getBool ($value){
        return in_array (trim ($value), array ('true', '1', 'yes'));
    }

    /**
    * says if the data type needs to be quoted while being SQL processed
    */
    function _typeNeedsQuotes ($typeName){
        return in_array (trim ($typeName), array ('string', 'date', 'varchardate'));
    }
}
?>