<?php
/**
* @package   copix
* @subpackage copixdao
* @version   $Id: CopixDAODefinitionV1.class.php,v 1.19 2005/04/27 20:06:11 laurentj Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Analyseur des fichiers XML de définition DAO, de la syntaxe daodefinition version 1 finale
*/
class CopixDAODefinitionV1 {
    /**
    * the properties list.
    * keys = field code name
    * values = CopixPropertyForDAO object
    */
    var $_properties = array ();

    /**
    * all tables with their properties, and their own fields
    * keys = table code name
    * values = array()
    *              'name'=> table code name, 'tablename'=>'real table name', 'JOIN'=>'join type',
    *              'primary'=>'bool', 'fields'=>array(list of field code name)
    */
    var $_tables = array();

    /**
    * primary table code name
    */
    var $_primaryTable = '';

    /**
    * liste des jointures, entre toutes les tables
    * keys = foreign table name
    * values = array('join'=>'type jointure', 'pfield'=>'real field name', 'ffield'=>'real field name');
    */
    var $_joins = array ();

    /**
    * the connection name to use.
    * null if you wants to use the default connection
    */
    var $_connectionName = null;

    var $_methods = array();

    /**
    * Constructor
    * @param CopixDAOCompiler compiler the compiler object
    */
    function CopixDAODefinitionV1(& $compiler){
        $this->_compiler= & $compiler;
    }

    /**
    * loads an XML file if given.
    */
    function loadFrom( & $parsedFile){
        // -- tables
        if(isset ($parsedFile->DATASOURCE) && isset ($parsedFile->DATASOURCE->TABLES) &&
        isset ($parsedFile->DATASOURCE->TABLES->TABLE)){

            if(! is_array ($parsedFile->DATASOURCE->TABLES->TABLE)){
                $this->addTable ($parsedFile->DATASOURCE->TABLES->TABLE->attributes());
            }else{
                foreach($parsedFile->DATASOURCE->TABLES->TABLE as $table){
                    $this->addTable ($table->attributes ());
                }
            }
        }else{
            $this->_compiler->doDefError ('copix:dao.error.definitionfile.table.missing');
        }

        if($this->_primaryTable == '')
        $this->_compiler->doDefError ('copix:dao.error.definitionfile.table.primary.missing');

        uasort($this->_joins, array('CopixDAODefinitionV1','_sortJoins'));

        // -- connection
        if (isset ($parsedFile->DATASOURCE->CONNECTION)){
            $connection = $parsedFile->DATASOURCE->CONNECTION->attributes ();
            if (isset ($connection['NAME'])){
                $this->_connectionName = $connection ['NAME'];
            }
        }

        //adds the properties
        if(isset($parsedFile->PROPERTIES) && isset($parsedFile->PROPERTIES->PROPERTY)){
            if(is_array ($parsedFile->PROPERTIES->PROPERTY)){
                foreach ($parsedFile->PROPERTIES->PROPERTY as $field){
                    $this->addProperty (new CopixPropertyForDAO ($field->attributes(), $this));
                }
            }else{
                $this->addProperty (new CopixPropertyForDAO ($parsedFile->PROPERTIES->PROPERTY->attributes(), $this));
            }
        }else
        $this->_compiler->doDefError ('copix:dao.error.definitionfile.properties.missing');


        // get additionnal methods definition

        if(isset ($parsedFile->METHODS) && isset ($parsedFile->METHODS->METHOD)){
            if(is_array ($parsedFile->METHODS->METHOD)){
                $kcnt= count ($parsedFile->METHODS->METHOD);
                for($k=0; $k < $kcnt; $k++){
                    $this->addMethod (new CopixMethodForDAO ($parsedFile->METHODS->METHOD[$k], $this));
                }
            }else{
                $this->addMethod (new CopixMethodForDAO ($parsedFile->METHODS->METHOD, $this));
            }
        }
    }

    /**
    * adds a field to the list.
    */
    function addProperty ($field){
        $this->_properties[$field->name] = $field;
        $this->_tables[$field->table]['FIELDS'][] = $field->name;

        if($field->fkTable !== null){
            if(! isset ($this->_joins[$field->fkTable]))
            $this->_compiler->doDefError('copix:dao.error.definitionfile.properties.foreign.table.missing', $field->name);
            $this->_joins[$field->fkTable]['pfield']=$field->fieldName;
            $this->_joins[$field->fkTable]['ffield']=$field->fkFieldName;
        }
    }


    function getProperties () {
        return $this->_properties;
    }

    function addTable ($tableinfos){
        if (!isset ($tableinfos['NAME']) || trim ($tableinfos['NAME']) == '' )
        $this->_compiler->doDefError ('copix:dao.error.definitionfile.table.name');

        if(! isset ($tableinfos['TABLENAME']) || $tableinfos['TABLENAME'] == '')
        $tableinfos['TABLENAME'] = $tableinfos['NAME'];

        $tableinfos['FIELDS'] = array ();
        $this->_tables[$tableinfos['NAME']] = $tableinfos;

        if(isset ($tableinfos['PRIMARY']) && $this->_getBool ($tableinfos['PRIMARY'])){
            if($this->_primaryTable != ''){
                $this->_compiler->doDefError ('copix:dao.error.definitionfile.table.primary.duplicate',$this->_primaryTable);
            }

            $this->_primaryTable = $tableinfos['NAME'];
        }else{
            $join = isset ($tableinfos['JOIN']) ? strtolower(trim($tableinfos['JOIN'])) : '';
            if(! in_array ($join, array('left','right','inner',''))){
                $this->_compiler->doDefError ('copix:dao.error.definitionfile.table.join.invalid',$tableinfos['NAME']);
            }

            if ($join == 'inner'){
                $join = '';
            }

            $this->_joins[$tableinfos['NAME']] = array ('join'=>$join, 'pfield'=>'', 'ffield'=>'');
        }

        return true;
    }

    // fonction statique de comparaison pour le tri de la liste des tables
    function _sortJoins($join1, $join2){
        $j1 =$join1['join'];
        $j2 =$join2['join'];

        if($j1 == '' && $j2 !=''){
           return 1;
        }else if($j1 != '' && $j2 ==''){
          return -1;
        }else{
            return 0;
        }

    }

    function getTables(){
        return $this->_tables;
    }

    function addMethod (&$method) {
        if(isset ($this->_methods[$method->name])){
            $this->_compiler->doDefError ('copix:dao.error.definitionfile.method.duplicate',$method->name);
        }
        $this->_methods[$method->name] = $method;
    }
    /**
    * just a quick way to retriveve boolean values from a string.
    *  will accept yes, true, 1 as "true" values
    *  the rest will be considered as false values.
    * @return boolean true / false
    */
    function _getBool ($value) {
        return in_array (trim ($value), array ('true', '1', 'yes'));
    }
}

//--------------------------------------------------------
/**
* objet comportant les données d'une propriété d'un DAO
*/

class CopixPropertyForDAO {
    /**
    * the name of the property of the object
    */
    var $name = '';

    /**
    * the name of the field in table
    */
    var $fieldName = '';

    /**
    * give the regular expression that needs to be matched against.
    * @var string
    */
    var $regExp = null;

    /**
    * says if the field is required.
    * @var boolean
    */
    var $required = false;

    /**
    * The i18n key for the caption of the element.
    * @var string
    */
    var $captionI18N = null;
    /**
    * the caption of the element.
    * @var string
    */
    var $caption = null;

    /**
    * Is it a string ?
    * @var boolean
    */
    var $isString = true;

    /**
    * Says if it's a primary key.
    * @var boolean
    */
    var $isPK = false;

    /**
    * Says if it's a forign key
    * @var boolean
    */
    var $isFK = false;

    var $type;

    var $table=null;
    var $updateMotif='%s';
    var $insertMotif='%s';
    var $selectMotif='%s';

    var $fkTable=null;
    var $fkFieldName=null;
    var $sequenceName='';

    /**
    * the maxlength of the key if given
    * @var int
    */
    var $maxlength = null;

    /**
    * constructor.
    */
    function CopixPropertyForDAO ($params, & $def){
        if (!isset ($params['NAME'])){
            $def->_compiler->doDefError('copix:dao.error.definitionfile.missing.attr', array('name', 'property'));
        }
        $this->name       = $params['NAME'];
        $this->fieldName  = isset ($params['FIELDNAME']) ? $params['FIELDNAME'] : $this->name;
        $this->table      = isset ($params['TABLE']) ? $params['TABLE'] : $def->_primaryTable;

        if(!isset( $def->_tables[$this->table])){
            $def->_compiler->doDefError('copix:dao.error.definitionfile.property.unknow.table', $this->name);
        }

        $this->required   = isset ($params['REQUIRED']) ? $this->_getBool ($params['REQUIRED']) : false;
        $this->maxlength  = isset ($params['MAXLENGTH']) ? ($params['MAXLENGTH']) : null;

        if(isset ($params['REGEXP'])){
            if(trim ($params['REGEXP']) != ''){
                $this->regExp     = $params['REGEXP'];
                //$this->required = true;
            }
        }

        $this->captionI18N = isset ($params['CAPTIONI18N']) ? $params['CAPTIONI18N'] : null;
        $this->caption     = isset ($params['CAPTION']) ? $params['CAPTION'] : null;
        if ($this->caption == null && $this->captionI18N == null){
            //trigger_error (CopixI18N::get('copix:dao.error.definitionfile.missing.attr.caption',$def->_shortFileName),E_USER_ERROR);
            $this->caption=$this->name;
        }

        $this->isPK       = isset ($params['PK']) ? $this->_getBool ($params['PK']): false;
        if (!isset ($params['TYPE'])){
            $def->_compiler->doDefError('copix:dao.error.definitionfile.missing.attr', array('type', 'field'));
        }
        $this->needsQuotes = $this->_typeNeedsQuotes ($params['TYPE']);
        if (!in_array (strtolower ($params['TYPE']), array ('autoincrement', 'bigautoincrement', 'int',
                                    'integer', 'varchar', 'string', 'varchardate', 'date', 'numeric', 'double', 'float'))){
           $def->_compiler->doDefError('copix:dao.error.definitionfile.wrong.attr', array($params['TYPE'], $this->fieldName));
        }
        $this->type = strtolower($params['TYPE']);

        if($this->table == $def->_primaryTable){ // on ignore les champs fktable et fkfieldName pour les propriétés qui n'appartiennent pas à la table principale
        $this->fkTable = isset ($params['FKTABLE']) ? $params['FKTABLE'] : null;
        $this->fkFieldName = isset ($params['FKFIELDNAME']) ? $params['FKFIELDNAME'] : '';
        if($this->fkTable !== null){
            if($this->fkFieldName == ''){
                $def->_compiler->doDefError ('copix:dao.error.definitionfile.property.foreign.field.missing', array($this->name));
            }
        }
        }
        $this->isFK =  $this->fkTable !== null;
        if(($this->type == 'autoincrement' || $this->type == 'bigautoincrement') && isset ($params['SEQUENCE'])){
            $this->sequenceName = $params['SEQUENCE'];
        }

        // on ignore les attributs *motif sur les champs PK et FK
        // (je ne sais plus pourquoi mais il y avait une bonne raison...)
        if(!$this->isPK && !$this->isFK){
            $this->updateMotif= isset($params['UPDATEMOTIF']) ? $params['UPDATEMOTIF'] :'%s';
            $this->insertMotif= isset($params['INSERTMOTIF']) ? $params['INSERTMOTIF'] :'%s';
            $this->selectMotif= isset($params['SELECTMOTIF']) ? $params['SELECTMOTIF'] :'%s';
        }

        // pas de motif update et insert pour les champs des tables externes
        if($this->table != $def->_primaryTable){
            $this->updateMotif = '';
            $this->insertMotif = '';
            $this->required = false;
            $this->ofPrimaryTable = false;
        }else{
            $this->ofPrimaryTable=true;
        }
    }

    /**
    * just a quick way to retriveve boolean values from a string.
    *  will accept yes, true, 1 as "true" values
    *  the rest will be considered as false values.
    * @return boolean true / false
    */
    function _getBool ($value) {
        return in_array (trim ($value), array ('true', '1', 'yes'));
    }

    /**
    * says if the data type needs to be quoted while being SQL processed
    */
    function _typeNeedsQuotes ($typeName) {
        return in_array (trim ($typeName), array ('string', 'date', 'varchardate'));
    }
}




//--------------------------------------------------------
/**
* objet comportant les données d'une propriété d'un DAO
*/
class CopixMethodForDAO{
    var $name;
    var $type;
    var $_searchParams = null;
    var $_parameters   = array();
    var $_limit = null;
    var $_values = array();
    var $_def=null;

    function CopixMethodForDAO (&$method, &$def){
        $this->_def = & $def;
        if (!isset ($method->__attributes['NAME'])){
            $def->_compiler->doDefError ('copix:dao.error.definitionfile.missing.attr', array('name', 'method'));
        }

        $this->name  = $method->__attributes['NAME'];
        $this->type  = isset ($method->__attributes['TYPE']) ? strtolower($method->__attributes['TYPE']) : 'select';

        if (isset ($method->PARAMETERS) && isset ($method->PARAMETERS->PARAMETER)){
            if(!is_array($method->PARAMETERS->PARAMETER)){
                $this->addParameter($method->PARAMETERS->PARAMETER->attributes());
            }else{
                foreach ($method->PARAMETERS->PARAMETER as $param){
                    $this->addParameter($param->attributes ());
                }
            }
        }

        if (isset ($method->CONDITIONS)){
            if(isset ($method->CONDITIONS->__attributes['LOGIC'])){
                $kind = $method->CONDITIONS->__attributes['LOGIC'];
            }else{
                $kind='AND';
            }
            $this->_searchParams = new CopixDAOSearchConditions($kind, true);
            $this->_parseConditions($method,true);
        }else{
            $this->_searchParams = new CopixDAOSearchConditions('AND', true);
        }

        if($this->type == 'update'){
            if(isset($method->VALUES) && isset($method->VALUES->VALUE)){
                if(!is_array($method->VALUES->VALUE)){
                    $this->addValue($method->VALUES->VALUE->attributes());
                }else{
                    foreach ($method->VALUES->VALUE as $val){
                        $this->addValue($val->attributes ());
                    }
                }
            }else{
                $def->_compiler->doDefError('copix:dao.error.definitionfile.method.values.undefine',array($this->name));
            }
        }
        if (isset ($method->ORDER) && isset($method->ORDER->ORDERITEM)){
            if(is_array($method->ORDER->ORDERITEM)){
                foreach($method->ORDER->ORDERITEM as $item){
                    $this->addOrder ($item->attributes());
                }
            }else{
                $this->addOrder ($method->ORDER->ORDERITEM->attributes());
            }
        }

        if (isset($method->LIMIT)){
            if(is_array($method->LIMIT)){
                $def->_compiler->doDefError('copix:dao.error.definitionfile.tag.duplicate', array('limit', $this->name));
            }
            if($this->type == 'select' ||$this->type == 'selectfirst'){
                $attr   = $method->LIMIT->attributes();
                $offset = (isset ($attr['OFFSET']) ? $attr['OFFSET']:null);
                $count  = (isset ($attr['COUNT']) ? $attr['COUNT']:null);

                if( $offset === null){
                    $def->_compiler->doDefError('copix:dao.error.definitionfile.missing.attr',array('offset','limit'));
                }
                if($count === null){
                    $def->_compiler->doDefError('copix:dao.error.definitionfile.missing.attr',array('count','limit'));
                }

                if(substr ($offset,0,1) == '$'){
                    if(in_array (substr ($offset,1),$this->_parameters)){
                        $offset=' intval('.$offset.')';
                    }else{
                        $def->_compiler->doDefError('copix:dao.error.definitionfile.method.limit.parameter.unknow', array($this->name, $offset));
                    }
                }else{
                    if(is_numeric ($offset)){
                        $offset = intval ($offset);
                    }else{
                        $def->_compiler->doDefError('copix:dao.error.definitionfile.method.limit.badvalue', array($this->name, $offset));
                    }
                }

                if(substr ($count,0,1) == '$'){
                    if(in_array (substr ($count,1),$this->_parameters)){
                        $count=' intval('.$count.')';
                    }else{
                        $def->_compiler->doDefError('copix:dao.error.definitionfile.method.limit.parameter.unknow', array($this->name, $count));
                    }
                }else{
                    if(is_numeric($count)){
                        $count=intval($count);
                    }else{
                        $def->_compiler->doDefError('copix:dao.error.definitionfile.method.limit.badvalue', array($this->name, $count));
                    }
                }
                $this->_limit= compact('offset', 'count');

            }else{
                $def->_compiler->doDefError('copix:dao.error.definitionfile.method.limit.forbidden');
            }
        }
    }

    function _parseConditions(&$node, $first=false){
        if (isset ($node->CONDITIONS)){
            if (!$first){
                if (isset ($node->CONDITIONS->__attributes['LOGIC'])){
                    $kind = $node->CONDITIONS->__attributes['LOGIC'];
                }else{
                    $kind = 'AND';
                }
                $this->_searchParams->startGroup ($kind);
            }

            if(! is_array ($node->CONDITIONS)){
                if(isset ($node->CONDITIONS->CONDITION)){
                    $this->addCondition($node->CONDITIONS->CONDITION);
                }
            }else{
                foreach ($node->CONDITIONS as $cond){
                    if (isset ($node->CONDITIONS->CONDITION)){
                        $this->addCondition ($node->CONDITIONS->CONDITION);
                    }
                }
            }

            $this->_parseConditions ($node->CONDITIONS);

            if (!$first) {
                $this->_searchParams->endGroup();
            }
        }
    }

    function addCondition( &$node){
        if(!is_array ($node)){
            $this->_addCondition ($node->attributes());
        }else{
            foreach($node as $param){
                $this->_addCondition ($param->attributes());
            }
        }
    }

    function _addCondition( $attributes){
        $field_id = (isset($attributes['PROPERTY']) ? $attributes['PROPERTY']:'');
        $operator = (isset($attributes['OPERATOR']) ? $attributes['OPERATOR']:'');
        $value    = (isset($attributes['VALUE']) ? $attributes['VALUE']:'');

        // for compatibility with dev version. valueofparam attribute = deprecated
        if(isset($attributes['VALUEOFPARAM'])){
            $value='$'.$attributes['VALUEOFPARAM'];
        }

        if (!isset ($this->_def->_properties[$field_id])){
            $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.property.unknown', array($this->name, $field_id));
        }

        if($this->type=='update'){
            if($this->_def->_properties[$field_id]->table != $this->_def->_primaryTable){
                $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.property.forbidden', array($this->name, $field_id));
            }
        }

        if (substr($value,0,1) == '$'){
            if (in_array (substr ($value,1),$this->_parameters)){
                $this->_searchParams->addPHPCondition ($field_id, $operator, $value);
            }else{
                $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.parameter.unknow', array($this->name, $value));
            }
        }else{
            if(substr($value,0,2) == '\$'){
                $value=substr($value,1);
            }
            $this->_searchParams->addPHPCondition ($field_id, $operator, '\''.str_replace("'","\'",$value).'\'');
        }
    }

    function addParameter($attributes){
        if (!isset ($attributes['NAME'])){
            $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.parameter.unknowname', array($this->name));
        }
        $this->_parameters[]=$attributes['NAME'];
    }

    function addOrder($attr){
        $prop = (isset ($attr['PROPERTY'])?trim($attr['PROPERTY']):'');
        $way  = (isset ($attr['WAY'])?trim($attr['WAY']):'ASC');
        if ($prop != ''){
            if(isset($this->_def->_properties[$prop])){
                $this->_searchParams->addItemOrder($prop, $way);
            }else{
                $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.orderitem.bad', array($prop, $this->name));
            }
        }else{
            $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.orderitem.bad', array($prop, $this->name));
        }
    }

    function addValue($attr){

        $prop = (isset ($attr['PROPERTY'])?trim($attr['PROPERTY']):'');
        $value  = (isset ($attr['VALUE'])?trim($attr['VALUE']):'');
        if ($prop == ''){
            $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.values.property.unknow', array($this->name, $prop));
            return false;
        }

        if(!isset($this->_def->_properties[$prop])){
            $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.values.property.unknow', array($this->name, $prop));
            return false;
        }

        if($this->_def->_properties[$prop]->table != $this->_def->_primaryTable){
            $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.values.property.bad', array($this->name,$prop ));
            return false;
        }
        if($this->_def->_properties[$prop]->isPK){
            $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.values.property.pkforbidden', array($this->name,$prop ));
            return false;
        }

        if (substr($value,0,1) == '$'){
            if (in_array (substr ($value,1),$this->_parameters)){
                $this->_values [$prop]= $this->_searchParams->_preparePHPValue($value, $this->_def->_properties[$prop]->type);
            }else{
                $this->_def->_compiler->doDefError('copix:dao.error.definitionfile.method.values.unknowparameter', array($this->name, $value));
            }
        }else{
            $this->_values [$prop]= $this->_searchParams->_preparePHPValue('\''.str_replace("'","\'",$value).'\'', $this->_def->_properties[$prop]->type);
        }
    }


}
?>