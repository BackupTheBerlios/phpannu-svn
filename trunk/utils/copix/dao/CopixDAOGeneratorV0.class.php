<?php
/**
* @package   copix
* @subpackage copixdao
* @version   $Id: CopixDAOGeneratorV0.class.php,v 1.30.2.2 2005/08/17 21:06:10 laurentj Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

require_once (COPIX_DAO_PATH.'CopixDAOGenerator.class.php');

/**
* Générateur d'une classe PHP correspondant à un objet DAO définit dans une fichier xml
* de définition version 0 (beta).
* @deprecated utiliser la version 1 des DAO
* @see CopixDAODefinitionV0
* @see CopixDAOGeneratorV1
*/
class CopixDAOGeneratorV0 extends CopixDAOGenerator{
    /**
    * compile the single class
    */
    function compileDAORecordClass (){
        $result = '';

        if ($this->_userDAORecord !== null){
           $result .= '  require_once (\''.$this->_userDAOPath.'\');'."\n";
        }

        $result  .= "\nclass ".$this->_compiledDAORecordClassName." { \n";

        //--Vars
        $classVars=array();
        $syncUserVarsNeeded = false;
        $classMethods =array();

        if ($this->_userDAORecord !== null){
            $classMethods = (array) get_class_methods ($this->_DAORecordClassName);

            $result .= ' var $_userDAORecord=null;'."\n";

            //DAORecord user's fields
            //adds definition for every user's DAO properties.
            $result .= '//Vars defined in User\s DAORecord'."\n";
            $classVars = (array) get_class_vars ($this->_DAORecordClassName);
            foreach ($classVars as $name=>$default){
                $syncUserVarsNeeded = true;
                $result .= ' var $'.$name.' = null;'."\n";
            }
            $result .= '//-- end of user\'s Record vars'."\n";
        }

        //DAORecord fields (not in user's DAO)
        //building the tab for the required properties.
        $usingFields = array ();
        $classVarsList= array_keys($classVars);
        foreach ($this->_userDefinition->getFields () as $id=>$field){
            if (!in_array ($field->name, $classVarsList)){
                $usingFields[$id] = $field;
            }
        }
        //declaration of properties.
        $result .= $this->_writeFieldsInfoWith ('name', ' var $', ' = null;'."\n", '', $usingFields);

        if ($this->_userDAORecord !== null){
            //--constructor.
            $result .= ' function '.$this->_compiledDAORecordClassName.' () {'."\n";
            $result .= '  $this->_userDAORecord = & new '.$this->_DAORecordClassName.';'."\n";
            $result .= '  $this->_userDAORecord->_compiled = & $this;'."\n";
            if ($syncUserVarsNeeded){
                $result .= '  $this->_synchronizeFromUserDAORecordProperties ();'."\n";
            }
            $result .= ' }'."\n";

            //--Waking up, to keep _compiled
            $result .= ' function __wakeup () {'."\n";
            $result .= '  $this->_userDAORecord->_compiled = & $this;'."\n";
            $result .= " }\n";

            //--mapping for every user's DAORecord function.
            foreach ($classMethods as $name){
                $result .= ' function '.$name.' () {'."\n";
                if ($syncUserVarsNeeded){
                    $result .= '   $this->_synchronizeToUserDAORecordProperties ();'."\n";
                }
                $result .= '   $args = func_get_args();'."\n";
                $result .= '   $toReturn = call_user_func_array(array(&$this->_userDAORecord, \''.$name.'\'), $args);'."\n";
                if ($syncUserVarsNeeded){
                    $result .= '   $this->_synchronizeFromUserDAORecordProperties ();'."\n";
                }
                $result .= '   return $toReturn;'."\n";
                $result .= ' }'."\n";
            }

            //--Synchronization functions
            //check if we need a sync process with the user's DAO
            if ($syncUserVarsNeeded) {
                $result .= ' function _synchronizeFromUserDAORecordProperties (){'."\n";
                foreach ($classVars as $name=>$defaultValue) {
                    $result .= '  $this->'.$name.' = $this->_userDAORecord->'.$name.';'."\n";
                }
                $result .= ' }'."\n";

                $result .= ' function _synchronizeToUserDAORecordProperties (){'."\n";
                foreach ($classVars as $name=>$defaultValue) {
                    $result .= '  $this->_userDAORecord->'.$name.' = $this->'.$name.';'."\n";
                }
                $result .= ' }'."\n";
            }
        }

        //InitFromDBObject
        $methodName = in_array ('initFromDBObject', $classMethods) ? '_compiled_initFromDBObject' : 'initFromDBObject';
        $result .= ' function '.$methodName.' (& $dbRecord){'."\n";
        foreach ($this->_userDefinition->getFields () as $field){
            $result .= ' $this->'.$field->name.'=$dbRecord->'.$field->name.";\n";
        }
        $result .= ' }'."\n";

        //--method check.
        $methodName = in_array ('check', $classMethods) ? '_compiled_check' : 'check';
        $result .= ' function '.$methodName.' (){'."\n";
        $result .= '  require_once (COPIX_CORE_PATH . \'CopixErrorObject.class.php\');'."\n";
        $result .= '  $errorObject = new CopixErrorObject ();'."\n";
        foreach ($this->_userDefinition->_properties as $id=>$field){
            //if required, add the test.
            if ($field->required && $field->type != 'autoincrement'  &&  $field->type != 'bigautoincrement'){
                $result .= '  if (strlen ($this->'.$field->name.') <= 0){'."\n";
                $result .= '    $errorObject->addError (\''.$field->name.'\', CopixI18N::get (\'copix:dao.errors.required\',';
                if ($field->captionI18N !== null){
                    $result.= 'CopixI18N::get (\''.$field->captionI18N.'\')';
                }else{
                    $result.= '\''.str_replace("'","\'",$field->caption).'\'';
                }
                $result .= "));\n  }\n";
            }

            //if a regexp is given, check it....
            if ($field->regExp !== null){
                $result .= '  if (strlen ($this->'.$field->name.') > 0){'."\n";
                $result .= '   if (preg_match (\''.$field->regExp.'\', $this->'.$field->name.') === 0){'."\n";
                $result .= '      $errorObject->addError (\''.$field->name.'\', CopixI18N::get (\'copix:dao.errors.format\',';
                if ($field->captionI18N !== null){
                    $result.= 'CopixI18N::get (\''.$field->captionI18N.'\')';
                }else{
                    $result.= '\''.str_replace("'","\'",$field->caption).'\'';
                }
                $result .=  "));\n  }\n";
                $result .= '  }'."\n";
            }

            //if a maxlength is given
            if ($field->maxlength !== null && !in_array ($field->type, array ('date', 'varchardate'))){
                $result .= '  if (strlen ($this->'.$field->name.') > '.$field->maxlength.'){'."\n";
                $result .= '      $errorObject->addError (\''.$field->name.'\', CopixI18N::get (\'copix:dao.errors.sizeLimit\',array(';
                if ($field->captionI18N !== null){
                    $result.= 'CopixI18N::get (\''.$field->captionI18N.'\')';
                }else{
                    $result.= '\''.str_replace("'","\'",$field->caption).'\'';
                }
                $result .= ', '.$field->maxlength;
                $result .=  ")));\n";
                $result .= '  }'."\n";
            }

            //if int or numeric, will check if it is really a numeric.
            if (in_array ($field->type, array ('numeric', 'int', 'integer'))){
                $result .= '  if (strlen ($this->'.$field->name.') > 0){'."\n";
                $result .= '   if (! is_numeric ($this->'.$field->name.')){'."\n";
                $result .= '      $errorObject->addError (\''.$field->name.'\', CopixI18N::get (\'copix:dao.errors.numeric\',';
                if ($field->captionI18N !== null){
                    $result.= 'CopixI18N::get (\''.$field->captionI18N.'\')';
                }else{
                    $result.= '\''.str_replace("'","\'",$field->caption).'\'';
                }
                $result .=  "));\n  }\n";
                $result .= '  }'."\n";
            }

            //if date, will check if the format is correct
            if (in_array ($field->type, array ('date', 'varchardate'))){
                $result .= '     if (CopixI18N::timestampToDate ($this->'.$field->name.') === false){'."\n";
                $result .= '        $errorObject->addError (\''.$field->name.'\', CopixI18N::get (\'copix:dao.errors.date\',';
                if ($field->captionI18N !== null){
                    $result.= 'CopixI18N::get (\''.$field->captionI18N.'\')';
                }else{
                    $result.= '\''.str_replace("'","\'",$field->caption).'\'';
                }
                $result .=  "));\n  }\n";
            }

        }
        $result .= '  return $errorObject->isError () ? $errorObject->asArray () : true;'."\n";
        $result .= " }\n}\n";
        return $result;
    }

    /**
    * compile the DAO class
    */
    function compileDAO (){
        $result = 'require_once (COPIX_DB_PATH . \'CopixDbWidget.class.php\');'."\n";
        if ($this->_userDAO !== null){
           $result .= '  require_once (\''.$this->_userDAOPath.'\');'."\n";
        }

        $result .= "\nclass ".$this->_compiledDAOClassName." { \n";
        $result .='   var $_table=\''.$this->_userDefinition->getTableName().'\';'."\n";

        if ($this->_userDefinition->_connectionName ==''){
           $result .='   var $_connectionName=null;'."\n";
        }else{
           $result .='   var $_connectionName=\''.$this->_userDefinition->_connectionName.'\';'."\n";
        }

        if ($this->_userDAO !== null){
            $result .= ' var $_userDAO;'."\n";

            //Base elements for the DAO
            //adds definition for every user's DAO properties.
            $result .= '//Vars defined in User\s DAO'."\n";
            $syncUserVarsNeeded = false;
            foreach (get_class_vars ($this->_DAOClassName) as $name=>$default){
                $syncUserVarsNeeded = true;
                $result .= ' var $'.$name.';'."\n";
            }
            $result .= "//--\n";

            $result .= ' function '.$this->_compiledDAOClassName.' () {'."\n";
            $result .= '  $this->_userDAO = & new '.$this->_DAOClassName.';'."\n";
            $result .= '  $this->_userDAO->_compiled = & $this;'."\n";
            if ($syncUserVarsNeeded){
                $result .= '  $this->_synchronizeFromUserDAOProperties ();'."\n";
            }
            $result .= " }\n";

            //--Waking up, to keep _compiled
            $result .= ' function __wakeup () {'."\n";
            $result .= '  $this->_userDAO->_compiled = & $this;'."\n";
            $result .= " }\n";

            //adds mapping for every user's DAO function.
            foreach ($classMethods = (array) get_class_methods ($this->_DAOClassName) as $name){
                $result .= ' function '.$name.' () {'."\n";
                if ($syncUserVarsNeeded){
                    $result .= '   $this->_synchronizeToUserDAOProperties ();'."\n";
                }
                $result .= '   $args = func_get_args();'."\n";
                $result .= '   $toReturn = call_user_func_array(array(&$this->_userDAO, \''.$name.'\'), $args);'."\n";
                if ($syncUserVarsNeeded){
                    $result .= '   $this->_synchronizeFromUserDAOProperties ();'."\n";
                }
                $result .= '   return $toReturn;'."\n";
                $result .= " }\n";
            }

            //check if we need a sync process with
            if ($syncUserVarsNeeded) {
                $result .= ' function _synchronizeFromUserDAOProperties (){'."\n";
                foreach (get_class_vars ($this->_DAOClassName ) as $name=>$defaultValue) {
                    $result .= '  $this->'.$name.' = $this->_userDAO->'.$name.';'."\n";
                }
                $result .= " }\n";

                $result .= ' function _synchronizeToUserDAOProperties (){'."\n";
                foreach (get_class_vars ($this->_DAOClassName) as $name=>$defaultValue) {
                    $result .= '  $this->_userDAO->'.$name.' = $this->'.$name.';'."\n";
                }
                $result .= " }\n";
            }
        }else{
            $classMethods = array ();
        }

        // prepare some values to generate methods
        // generate part of sql queries about foreign table
        list( $sqlFkTables, $sqlFkCondition, $sqlFkFields) = $this->_buildFKInfosForSelect();
        $connectionName = ($this->_userDefinition->_connectionName ==''?'':'\''. $this->_userDefinition->_connectionName.'\'');

        //Selection, findAll.
        $methodName = in_array ('findall', $classMethods) ? '_compiled_findAll' : 'findAll';
        $result .= ' function '.$methodName.' (){'."\n";
        $result .= '    $query = \'select '.$this->_writeFieldNamesListForSelect ($this->_userDefinition->getTableName ().'.');

        $result .= $sqlFkFields;
        $result .= ' from '.$this->_userDefinition->getTableName ().$sqlFkTables.' '.
        ($sqlFkCondition!=''?' where '.$sqlFkCondition:'').'\';'."\n";
        $result .= '    $dbWidget = & CopixDBFactory::getDbWidget ('.$connectionName.');'."\n";
        $result .= '    return $dbWidget->fetchAllRecords ($query, \''.$this->_compiler->_DAOid.'\');'."\n";
        $result .= ' }'."\n";

        //Selection, get.
        $methodName = in_array ('get', $classMethods) ? '_compiled_get' : 'get';
        $result .= ' function & '.$methodName.' ('.$this->_writeFieldNamesWith ('$', '', ',', $this->_getPKFields ()).'){'."\n";
        $result .= '    $__RESERVED_INTERNAL_COPIX_ct = & CopixDBFactory::getConnection ('.$connectionName.');'."\n"; // obligé pour les $ct->quote
        $result .= '    $query = \'select '
        .$this->_writeFieldNamesListForSelect ($this->_userDefinition->getTableName ().'.')
        .$sqlFkFields.' from '.$this->_userDefinition->getTableName ().$sqlFkTables;

        //condition on the PK
        $sqlCondition = $this->_buildPKCondition('', $this->_userDefinition->getTableName ().'.');

        if($sqlFkCondition != ''){
            $sqlCondition.=($sqlCondition == '' ? '' : ' and ').$sqlFkCondition;
        }

        if($sqlCondition != ''){
            $result.=' where '.$sqlCondition;
        }

        $result .= '\';'."\n";// ends the query
        $result .= '    require_once (COPIX_DB_PATH . \'CopixDbWidget.class.php\');';
        $result .= '    $dbWidget = & new CopixDbWidget ($__RESERVED_INTERNAL_COPIX_ct);'."\n";
        $result .= '    $ret =  & $dbWidget->fetchFirstRecord ($query, \''.$this->_compiler->_DAOid.'\');'."\n";
        $result .= '    return $res;';
        $result .= ' }'."\n";

        //Insertion.
        $methodName = in_array ('insert', $classMethods) ? '_compiled_insert' : 'insert';
        $result .= ' function '.$methodName.' (& $object, $__RESERVED_INTERNAL_COPIX_ct = null){'."\n";
        $result .= '    if ($__RESERVED_INTERNAL_COPIX_ct === null)';
        $result .= '    $__RESERVED_INTERNAL_COPIX_ct = & CopixDBFactory::getConnection ('.$connectionName.');'."\n";

        $pluginDB   = & $GLOBALS['COPIX']['COORD']->getPlugin ('CopixDb');
        $driverName = ($this->_userDefinition->_connectionName != null) ? $pluginDB->config->profils[$this->_userDefinition->_connectionName]->driver : $pluginDB->config->profils[$pluginDB->config->default]->driver;

        $pkai = $this->_getAutoIncrementField();
        if($pkai !== null){
            if (($driverName=='mysql') || ($driverName=='sqlserver')) {
               //$result .= '      $object->'.$pkai->name.'= $__RESERVED_INTERNAL_COPIX_ct->lastId('.$pkai->name.','.$this->_userDefinition->getTableName ().');'."\n";
               $fields= $this->_getFieldsExcludeTypes(array('autoincrement','bigautoincrement'));
            }elseif ($pkai->sequenceName != ''){
               $result .= '     $object->'.$pkai->name.'= $__RESERVED_INTERNAL_COPIX_ct->lastId(\''.$pkai->sequenceName.'\');'."\n";
               $fields = $this->_userDefinition->getFields ();
            }
        }else{
            $fields = $this->_getFieldsExcludeTypes(array ('autoincrement', 'bigautoincrement'));
        }

        $result .= '    $query = \'INSERT INTO '.$this->_userDefinition->getTableName ().' (';
        list($fields, $values)=$this->_prepareValues($fields,'insertMotif', 'object->');

        $result .= implode(',',$fields);
        $result .= ') VALUES (';
        $result .= implode(', ',$values);
        $result .= ')\';'."\n";

        $result .= '   $toReturn = $__RESERVED_INTERNAL_COPIX_ct->doQuery ($query);'."\n";

        /** modif by Yan **/
        //return lastid after inserting for mysql
        if($pkai !== null){
            if (($driverName=='mysql') || ($driverName=='sqlserver')) {
               $result .= '      $object->'.$pkai->name.'= $__RESERVED_INTERNAL_COPIX_ct->lastId(\'';
               if (strlen($pkai->fieldName) > 0) {
                  $result .= $pkai->fieldName;
               }else{
                  $result .= $pkai->name;
               }
               $result .= '\',\''.$this->_userDefinition->getTableName ().'\');'."\n";
            }
        }
        /** end modif **/

        $result .= '   if($toReturn){'."\n";



        $result .= '    return $toReturn;'."\n      }else return false; \n   }\n";//ends insert function

        //mise à jour.
        $methodName = in_array ('update', $classMethods) ? '_compiled_update' : 'update';
        $result .= ' function '.$methodName.' ($object, $__RESERVED_INTERNAL_COPIX_ct = null){'."\n";
        $result .= '    if ($__RESERVED_INTERNAL_COPIX_ct === null)';
        $result .= '    $__RESERVED_INTERNAL_COPIX_ct = & CopixDBFactory::getConnection ('.$connectionName.');'."\n";
        $result .= '    $query = \'UPDATE '.$this->_userDefinition->getTableName ().' SET ';

        list($fields, $values)=$this->_prepareValues($this->_getFieldsExcludePK(),'updateMotif', 'object->');

        $sqlSet='';
        foreach($fields as $k=> $fname){
            $sqlSet.= ', '.$fname. '= '. $values[$k];
        }
        $result.=substr($sqlSet,1);

        //condition on the PK
        $sqlCondition = $this->_buildPKCondition('object->');
        if($sqlCondition!='')
        $result .= ' where '.$sqlCondition;

        $result .= '\';'."\n";

        $result .= '   return $__RESERVED_INTERNAL_COPIX_ct->doQuery ($query);'."\n";
        $result .= ' }'."\n";//ends the update function

        //supression.
        $methodName = in_array ('delete', $classMethods) ? '_compiled_delete' : 'delete';
        $result .= ' function '.$methodName.' ('.$this->_writeFieldNamesWith ('$', '', ',', $this->_getPKFields ()).', $__RESERVED_INTERNAL_COPIX_ct = null){'."\n";
        $result .= '    if ($__RESERVED_INTERNAL_COPIX_ct === null)';
        $result .= '    $__RESERVED_INTERNAL_COPIX_ct = & CopixDBFactory::getConnection ('.$connectionName.');'."\n";
        $result .= '    $query = \'DELETE FROM '.$this->_userDefinition->getTableName ().' where ';
        $result .= $this->_buildPKCondition();
        $result .= '\';'."\n";//ends the query
        $result .= '   return $__RESERVED_INTERNAL_COPIX_ct->doQuery ($query);'."\n";
        $result .= ' }'."\n";//ends delete function

        //recherche.
        $methodName = in_array ('findby', $classMethods) ? '_compiled_findBy' : 'findBy';
        $result .= ' function '.$methodName.' ($searchParams){'."\n";
        $result .= '    $__RESERVED_INTERNAL_COPIX_ct = & CopixDBFactory::getConnection ('.$connectionName.');'."\n";
        $result .= '    $query = \'select '.$this->_writeFieldNamesListForSelect ($this->_userDefinition->getTableName ().'.');
        $result .= $sqlFkFields;
        $result .= ' from '.$this->_userDefinition->getTableName ().$sqlFkTables.' '.($sqlFkCondition!=''?' where '.$sqlFkCondition:'').'\';'."\n";

        //les conditions du By de la méthode findBy.
        $result .= '    if (!$searchParams->isEmpty ()){'."\n";
        $result .= '       $query .= \''.($sqlFkCondition!='' ? ' AND ' : ' WHERE ').'\';'."\n";
        $result .= '    }'."\n";

        //génération des paramètres de la méthode explain
        //$fieldsType        = array();
        $fieldsTranslation = array();

        foreach ($this->_userDefinition->_properties as $name=>$field){
            $fieldsTranslation[]='\''.$field->name.'\'=>array(\''.$field->fieldName.'\', \''.$field->type.'\',\''.$this->_userDefinition->getTableName ().'\',\''.str_replace("'","\\'",$field->selectMotif).'\')';
            /*$fieldsTranslation[]='\''.$field->name.'\'=>\''.$field->fieldName.'\'';
            $fieldsType[]='\''.$field->name.'\'=>\''.$field->type.'\'';*/
        }
        $fieldsTranslation = '         array('.implode(', ',$fieldsTranslation).')';
        //$fieldsType        = '         array('.implode(', ',$fieldsType).')';

        //fin de la requete
        //      $result .= '    $query .= $searchParams->explainSQL ('."\n".$fieldsTranslation.",\n".$fieldsType.','."\n".'             $__RESERVED_INTERNAL_COPIX_ct, \''.$this->_userDefinition->getTableName ().'\');'."\n";
        $result .= '    $query .= $searchParams->explainSQL ('."\n".$fieldsTranslation.",\n".'             $__RESERVED_INTERNAL_COPIX_ct);'."\n";
        $result .= '    require_once (COPIX_DB_PATH . \'CopixDbWidget.class.php\');'."\n";
        $result .= '    $dbWidget = & new CopixDBWidget ($__RESERVED_INTERNAL_COPIX_ct);'."\n";
        $result .= '    return $dbWidget->fetchAllRecords ($query, \''.$this->_compiler->_DAOid.'\');'."\n";
        $result .= ' }'."\n";

        $result .= '}'."\n";//end of class
        return $result;
    }

    /**
    * format field names with start, end and between strings.
    *   will write the field named info.
    *   eg info == name
    *   echo $field->name
    * @param string   $info    property to get from objects in $using
    * @param string   $start   string to add before the info
    * @param string   $end     string to add after the info
    * @param string   $beetween string to add between each info
    * @param array    $using     list of CopixFieldForDAO object. if null, get default fields list
    * @see  CopixFieldForDAO
    */
    function _writeFieldsInfoWith ($info, $start = '', $end='', $beetween = '', $using = null){
        $result = array();
        if ($using === null){
            //if no fields are provided, using _userDefinition's as default.
            $using = $this->_userDefinition->getFields ();
        }

        foreach ($using as $id=>$field){
            $result[] = $start . $field->$info . $end;
        }
        return implode($beetween,$result);;
    }

    /**
    * format field names with start, end and between strings.
    */
    function _writeFieldNamesWith ($start = '', $end='', $beetween = '', $using = null){
        return $this->_writeFieldsInfoWith ('name', $start, $end, $beetween, $using);
    }


    function _writeFieldNamesListForSelect ($start, $using = null){
        $result = array();
        if ($using === null){
            //if no fields are provided, using _userDefinition's as default.
            $using = $this->_userDefinition->getFields ();
        }

        foreach ($using as $id=>$field){
            if($field->selectMotif !=''){
                $str=sprintf($field->selectMotif, $start.$field->fieldName);

                if($field->fieldName != $field->name)
                $result[]= $str.' as '.$field->name;
                else
                $result[]= $str;
            }
        }

        return implode(', ',$result);
    }

    /**
    * replaces field names with
    */
    function _replaceFieldNames ($formatIt, $using = null){
        $result = '';
        if ($using === null){
            //if no fields are provided, using _userDefinition's as default.
            $using = $this->_userDefinition->getFields ();
        }
        foreach ($using as $id=>$field){
            $result .= str_replace ('[FIELDNAME]', $field->name, $formatIt);
        }
        return $result;
    }

    /**
    * get autoincrement PK field
    *
    */
    function _getAutoIncrementField ($using = null){

        if ($using === null){
            //if no fields are provided, using _userDefinition's as default.
            $using = $this->_userDefinition->getFields ();
        }
        $pluginDB   = & $GLOBALS['COPIX']['COORD']->getPlugin ('CopixDb');
        $driverName = ($this->_userDefinition->_connectionName != null) ? $pluginDB->config->profils[$this->_userDefinition->_connectionName]->driver : $pluginDB->config->profils[$pluginDB->config->default]->driver;

        foreach ($using as $id=>$field){
            if ($field->type == 'autoincrement' || $field->type == 'bigautoincrement'){
              if ($driverName=="postgresql") {
                  $field->sequenceName = $this->_userDefinition->_tableName."_".$field->name."_seq";
              }
              return $field;
            }

        }
        return null;
    }


    /**
    * gets fields that belongs to the PK
    */
    function _getPKFields ($using = null){
        $result = array ();
        if ($using === null){
            //if no fields are provided, using _userDefinition's as default.
            $using = $this->_userDefinition->getFields ();
        }
        foreach ($using as $id=>$field){
            if ($field->isPK){
                $result[$field->name] = $field;
            }
        }
        return $result;
    }

    /**
    * gets fields that do not belong to given types
    */
    function _getFieldsExcludeTypes ($types){
        $result = array ();
        $using = $this->_userDefinition->getFields ();
        foreach ($using as $id=>$field){
            if (!in_array ($field->type, $types)){
                $result[$field->name] = $field;
            }
        }
        return $result;
    }

    /**
    * gets fields that isn't primary keys
    */
    function _getFieldsExcludePK (){
        $result = array ();
        $using = $this->_userDefinition->getFields ();
        foreach ($using as $id=>$field){
            if (!$field->isPK){
                $result[$field->name] = $field;
            }
        }
        return $result;
    }

    /**
    * gets the FK infos, returns an array ('FIELDS'=>, 'TABLES', 'CONDITIONS')
    */
    function _getFKInfos (){
        $toReturn = array ('Tables'    =>array (),
        'Fields'    =>array (),
        'Conditions'=>array ());

        foreach ($this->_userDefinition->getFields () as $id=>$field){
            if ($field->fkTable !== null){
                //if not already in the fkTables list, adds the fkTable
                if (! in_array ($field->fkTable, $toReturn['Tables'])){
                    $toReturn['Tables'][] = $field->fkTable;
                    $toReturn['Fields'][$field->fkTable]=array();
                }
                //adds the fields to select for the given fkTable
                //to produce select fkTableName.fieldName
                $toReturn['Fields'][$field->fkTable]       = array_merge ($field->fkFields, $toReturn['Fields'][$field->fkTable]);
                //to produce where fkTableName.fieldName=fieldName
                $toReturn['Conditions'][$field->fkTable][] = $field->fieldName;
            }
        }
        return $toReturn;
    }

    /**
    * build parts of SQL query to do a join with foreign table
    */
    function _buildFKInfosForSelect(){

        //gets the foreign keys.
        $fkInfos      = $this->_getFKInfos ();
        $fkTables     = & $fkInfos['Tables'];
        $fkConditions = & $fkInfos['Conditions'];
        $fkFields     = & $fkInfos['Fields'];

        //creates SQL Strings for the table names
        $sqlTables = implode(', ',$fkTables);
        if($sqlTables != '')
        $sqlTables=', '.$sqlTables;

        //creates SQL strings for the fields to select
        $sqlFields = '';
        foreach ($fkFields as $tableName=>$fields){
            foreach($fields as $field){
                $sqlFields.=', '.$tableName.'.'.$field;
            }
        }

        //creates SQL strings for FK conditions.
        $first = true;
        $sqlCondition = '';
        foreach ($fkConditions as $tableName=>$fields){
            foreach ($fields as $fieldName){
                if (!$first){
                    $sqlCondition .= ' AND ';
                }
                $sqlCondition.= ' '.$tableName.'.'.$fieldName.'='.$this->_userDefinition->getTableName ().'.'.$fieldName.' ';
                $first = false;
            }
        }

        return array($sqlTables, $sqlCondition, $sqlFields);
    }

    /**
    * build where clause
    * @param array $fieldList  list of CopixFieldForDAO objects
    * @param array $prefixFieldName the prefix you wants to have (eg table name) before the field in the where clause
    *    eg where [prefixFieldName][fieldName] = [prefixField][fieldName]
    */
    function _buildPKCondition($prefixfield='', $prefixFieldName=''){

        list ($fields, $values) = $this->_prepareValues($this->_getPKFields (), '', $prefixfield);
        $result='';
        foreach($fields as $name =>$fieldname){
            $values[$name] = $prefixFieldName.$fieldname . '\'.($'.$prefixfield.$name.'===null ? \' IS \' : \' = \').\''. $values[$name];
        }

        return implode(' and ', $values);
    }


    function _prepareValues($fieldList, $motif='', $prefixfield=''){
        $values = $fields = array();
        $first = true;
        foreach ((array)$fieldList as $fieldName=>$field){
            if($motif != '' && $field->$motif == '')
            continue;

            switch(strtolower($field->type)){
                case 'int':
                case 'integer':
                case 'autoincrement':
                $value=' (($'.$prefixfield.$fieldName.' === null) ? \'NULL\' : intval($'.$prefixfield.$fieldName.')) ';
                break;
                case 'double':
                case 'float':
                $value=' (($'.$prefixfield.$fieldName.' === null) ? \'NULL\' : doubleval($'.$prefixfield.$fieldName.')) ';
                break;

                case 'numeric'://usefull for bigint and stuff
                case 'bigautoincrement':
                $value='(($'.$prefixfield.$fieldName.' === null) ? \'NULL\' : (is_numeric ($'.$prefixfield.$fieldName.') ? $'.$prefixfield.$fieldName.' : intval($'.$prefixfield.$fieldName.'))) ';
                break;

                default:
                if($field->required){
                    $value=' $__RESERVED_INTERNAL_COPIX_ct->quote ($'.$prefixfield.$fieldName.',false)';
                }else{
                    $value=' $__RESERVED_INTERNAL_COPIX_ct->quote ($'.$prefixfield.$fieldName.')';
                }
            }

            if($motif != ''){
                $values[$field->name]=sprintf($field->$motif,'\'.'.$value.'.\'');
            } else {
                $values[$field->name]='\'.'.$value.'.\'';
            }

            $fields[$field->name]=$field->fieldName;
        }
        return array($fields, $values);
    }
}
?>