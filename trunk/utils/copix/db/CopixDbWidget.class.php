<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbWidget.class.php,v 1.11.4.1 2005/08/17 20:06:54 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
*
* @package copix
* @subpackage copixdb
*/
class CopixDBWidget {
    /**
    * The CopixDBConnection object
    * @var CopixDBConnection
    * @see  CopixDBConnection
    */
    var $connector;

    /**
    * Constructor
    */
    function CopixDbWidget (& $connection){
        $this->connector = & $connection;
    }

    /**
    * Inserts a record into the given table
    * @param string $table the name of the table.
    * @param array $fieldToInsert an associative array with key=name of the field and value = value of the field.
    *   The values must be correct (eg quoted for string values)
    * @return object CopixDBResultSet (false if failed)
    * @see CopixQueryWidget
    */
    function & doInsert ($table, $fieldToInsert){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->connector->doQuery (CopixQueryWidget::sqlInsert ($this->connector->profil->schema.$table, $fieldToInsert));
    }

    /**
    * Select fields from records of a table matching given conditions, by a specific order
    * @param string $table the name of the table.
    * @param array $what field names we wants to get
    * @param array $condition associative array with key=name of the field and value = value of the field.
    *   The values must be correct (eg quoted for string values)
    * @param boolean $useOr if we're using "or" (true) or "and" (false) for the given conditions.
    * @param array/string    $order      field names the results will be ordered by
    * @param array    $orderDesc  Ascending (false) or not (true)
    * @return object CopixDBResultSet (false if failed)
    * @see CopixQueryWidget
    */
    function & doSelect ($table, $what, $condition, $useOr=false, $order = null, $orderDesc = false){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->connector->doQuery (CopixQueryWidget::sqlSelect ($this->connector->profil->schema.$table, $what, $condition, $useOr, $order, $orderDesc));
    }

    /**
    * Select fields fromthe first record of a table matching given conditions, by a specific order
    * @param string $table the name of the table.
    * @param array $what field names we wants to get
    * @param array $condition associative array with key=name of the field and value = value of the field.
    *   The values must be correct (eg quoted for string values)
    * @param boolean $useOr if we're using "or" (true) or "and" (false) for the given conditions.
    * @param array/string    $order      field names the results will be ordered by
    * @param array    $orderDesc  Ascending (false) or not (true)
    * @return object (false if failed)
    * @see CopixQueryWidget
    */
    function & doSelectFetchFirst ($table, $what, $condition, $useOr=false, $order = null, $orderDesc = false){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->fetchFirst (CopixQueryWidget::sqlSelect ($this->connector->profil->schema.$table, $what, $condition, $useOr, $order, $orderDesc), $this->connector);
    }

    /**
    *
    */
    function & doSelectFetchFirstUsing ($table, $what, $condition, $className, $useOr=false, $order = null, $orderDesc = false){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->fetchFirstUsing (CopixQueryWidget::sqlSelect ($this->connector->profil->schema.$table, $what, $condition, $useOr, $order, $orderDesc), $className, $this->connector);
    }

    /**
    *
    */
    function & doSelectFetchFirstInto ($table, $what, $condition, & $object, $useOr=false, $order = null, $orderDesc = false){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->fetchFirstInto (CopixQueryWidget::sqlSelect ($this->connector->profil->schema.$table, $what, $condition, $useOr, $order, $orderDesc), $object, $this->connector);
    }

    /**
    *
    */
    function doSelectFetchAll ($table, $what, $condition, $useOr=false, $order = null, $orderDesc = false){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->fetchAll (CopixQueryWidget::sqlSelect ($this->connector->profil->schema.$table, $what, $condition, $useOr, $order, $orderDesc), $this->connector);
    }

    /**
    *
    */
    function doSelectFetchAllUsing ($table, $what, $condition, $className, $useOr=false, $order = null, $orderDesc = false){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->fetchAllUsing (CopixQueryWidget::sqlSelect ($this->connector->profil->schema.$table, $what, $condition, $useOr, $order, $orderDesc), $className, $this->connector);
    }

    /**
    *
    */
    function & doDelete ($table, $condition, $useOr=false){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->connector->doQuery (CopixQueryWidget::sqlDelete ($this->connector->profil->schema.$table, $condition));
    }

    function & doUpdate ($table, $toSet, $condition, $useOr=false){
        require_once (COPIX_DB_PATH.'CopixQueryWidget.class.php');
        return $this->connector->doQuery (CopixQueryWidget::sqlUpdate ($this->connector->profil->schema.$table, $toSet, $condition));
    }


    /**
    * Effectue une requête, renvoi une ligne de resultat sous forme d'objet et libere les ressources.
    * @param   string   $query   requète SQL
    * @return  object  objet contenant les champs  sous forme de propriétés, de la ligne sélectionnée
    */
    function & fetchFirst($query){
        $rs     = & $this->connector->doQuery ($query);
        $result = & $rs->fetch ();

        $rs->free();
        return $result;
    }

    function & fetchFirstUsing ($query, $className){
        $rs     = & $this->connector->doQuery  ($query);
        $result = & $rs->fetchUsing ($className);

        $rs->free();
        return $result;
    }

    /**
    * Effectue une requête, et met à jour les propriétes de l'objet passé en paramètre
    * @param   string   $query   requète SQL
    * @return  object  objet contenant les champs  sous forme de propriétés, de la ligne sélectionnée
    */
    function fetchFirstInto ($query, &$object){
        $rs     = & $this->connector->doQuery   ($query);
        $result = & $rs->fetchInto ($object);

        $rs->free();
        return $result;
    }

    /**
    * Récupère tout les enregistrements d'un select dans un tableau (d'objets)
    * @param   string   $query   requète SQL
    * @return  array    tableau d'objets
    */
    function fetchAll($query, $limitOffset=null, $limitCount=null){
        $rs = & $this->_getResultSetFromQuery ($query, $limitOffset, $limitCount);
        $result = array();
        if ($rs){
            while($res = & $rs->fetch ()){
                $result[] = & $res;
            }
        }
        return $result;
    }

    /**
    * Récupère tout les enregistrements d'un select dans un tableau (d'objets)
    * @param   string   $query   requète SQL
    * @return  array    tableau d'objets
    */
    function fetchAllUsing($query, $className, $limitOffset=null, $limitCount=null){
        $rs = & $this->_getResultSetFromQuery ($query, $limitOffset, $limitCount);
        $result = array();
        if ($rs){
            while($res = & $rs->fetchUsing ($className)){
                $result[] = & $res;
            }
        }
        return $result;
    }

    /**
    * Executes a query and get its result set.
    */
    function & _getResultSetFromQuery ($query, $limitOffset = null, $limitCount = null){
        if ($limitOffset !== null && $limitCount !== null){
            $res = & $this->connector->doLimitQuery($query, $limitOffset, $limitCount);
        }else{
            $res = & $this->connector->doQuery($query);
        }
        return $res;
    }

    /**
    * fetch the records into DAORecords
    */
    function & fetchAllRecords ($query, $recordName, $limitOffset=null, $limitCount=null){
        $rs = & $this->_getResultSetFromQuery ($query, $limitOffset, $limitCount);
        $result = array();
        if ($rs){
            while($res = & $rs->fetchRecord ($recordName)){
                $result[] = & $res;
            }
        }
        return $result;
    }

    /**
    * fetch the first record into a DAO record
    */
    function & fetchFirstRecord ($query, $recordName){
        $rs     = & $this->connector->doQuery  ($query);
        $result = & $rs->fetchRecord ($recordName);
        $rs->free();
        return $result;
    }

    /**
    * prepare un ensemble de valeur de manière à étre incluse dans une
    * requète SQL
    * EXPERIMENTAL !!!
    * @param   object   $object         objet dont les propriétés vont être incluse dans la requète
    * @param   CopixObjectDbProperties    $objectProperties objet de propriétes des champs de l'objet
    * @param   array    $propertiesList   liste de propriété à inclure ou exclure dans la preparation
    * @param   boolean  $excludePropertiesList  indique si la liste des propriétés $propertiesList est à inclure ou exclure
    * @return  array    liste des propriétés avec leurs valeurs.
    */
    function prepareValues(& $object, & $objectProperties, $propertiesList=null, $excludePropertiesList=true ){
        $propTypes=$objectProperties->fieldTypeList;
        $mapping = array_flip ($objectProperties->fieldPropList);
        $properties = get_object_vars($object);
        $fields = array();
        foreach ($properties as $propname=>$value){
            if (!isset($mapping[$propname])) // est ce que cette propriété est référencée dans le mapping ?
            continue;
            if ($excludePropertiesList){
                if (is_array ($propertiesList) && in_array ($propname, $propertiesList)) // est ce une propriété exclue temporairement ?
                continue;
            }else{
                if(!is_array($propertiesList) )
                continue;
                if(! in_array($propname, $propertiesList)) // est ce une propriété qu'il faut inclure ?
                continue;
            }
            $fieldname=$mapping[$propname];

            if(isset($propTypes[$fieldname])){
                switch($propTypes[$fieldname] & 0xFF){
                    case COPIXDB_TYPE_INTEGER :
                    $value = intval($value);
                    break;
                    case COPIXDB_TYPE_FLOAT :
                    $value = doubleval($value);
                    break;
                    case COPIXDB_TYPE_BOOLEAN :
                    switch($propTypes[$fieldname]){
                        case COPIXDB_TYPE_BOOLEAN_01:
                        $value = ($value ? 1 : 0);
                        break;
                        case COPIXDB_TYPE_BOOLEAN_YN:
                        $value = ($value ? '\'Y\'' : '\'N\'');
                        break;
                        case COPIXDB_TYPE_BOOLEAN_BOOL:
                        $value = ($value ? 'true' : 'false');
                        break;
                        case COPIXDB_TYPE_BOOLEAN_STR:
                        $value = ($value ? '\'1\'' : '\'\'');
                        break;
                    }
                    break;
                    default:
                    $value=$this->connector->quote($value);
                }
            }else
            $value=$this->connector->quote($value);

            if(isset($objectProperties->appliedFunctionToIUList[$fieldname]))
            $value= sprintf($objectProperties->appliedFunctionToIUList[$fieldname],$value);

            $fields[$fieldname]=$value;
        }
        return $fields;
    }

    function getFieldListForSelect(& $objectProperties, $prefix=''){
        $list=array();
        if($prefix!='') $prefix.='.';

        foreach($objectProperties->fieldPropList as $field=>$property){
            if($field == $property)
            $list[]=$prefix.$field;
            else
            $list[]=$prefix.$field.' as '.$property;

        }
        return $list;
    }




    //==========================================================================
    //deprecated
    //==========================================================================

    /**
    * construction de la chaine d'instruction sql d'INSERTION.
    * Ajout automatique de slashs devant les caractères spéciaux.
    *
    * @param   string $tableName   le nom de la table ou l'on insère les infos.
    * @param array   $fieldsToInsert   tableau associatif de la forme Tab[NomDuChamp]=Value avec les champs à ajouter.
    * @return string La chaine d'instruction sql.
    * @deprecated
    */
    function sqlInsert ($tableName, $fieldsToInsert){
        trigger_error ('CopixDbWidget::sqlInsert obsolete', E_USER_NOTICE);
        return CopixQueryWidget::sqlInsert($tableName, $fieldsToInsert);
    }

    /**
    * création de la chaine sql de SUPPRESSION.
    * ajout automatique des slashs devant les caractères spéciaux.
    *
    * @param   string   $tableName Le nom de la table d'ou l'on supprime les infos.
    * @param   array   $condition Tableau associatif contenant les conditions de suppressions.De la forme Tab[NomDuChamp]=Value.
    * @return string   la chaine d'instruction sql.
    * @deprecated
    */
    function sqlDelete ($tableName, $condition){
        trigger_error ('CopixDbWidget::sqlDelete obsolete', E_USER_NOTICE);
        return CopixQueryWidget::sqlDelete($tableName, $condition);
    }
    /**
    * création de la chaine sql de SELECTION.
    *
    * Ajout automatique des slashs devant les caractères spéciaux.
    * @param   string   $tableName    le nom de la table sur laquelle effectuer la sélection.
    * @param array   $what    tableau indicé contenant la liste des champs à sélectionner.
    * @param array   $condition   tableau associatif des conditions de sélection. De la forme Tab[NomDuChamp]=Value
    * @return  string   la chaine sql.
    * @deprecated
    */
    function sqlSelect ($tableName, $what, $condition = null, $order = null, $orderDesc = false){
        trigger_error ('CopixDbWidget::sqlSelect obsolete', E_USER_NOTICE);
        return CopixQueryWidget::sqlSelect($tableName, $what, $condition, $order, $orderDesc);
    }

}
?>