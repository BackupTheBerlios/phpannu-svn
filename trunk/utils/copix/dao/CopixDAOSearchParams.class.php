<?php
/**
* @package   copix
* @subpackage copixdao
* @version   $Id: CopixDAOSearchParams.class.php,v 1.16 2005/03/07 13:18:44 gcroes Exp $
* @author   Croes Gérald , Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* permet d'effectuer des recherches dans un DAO précis, en indiquant les critères
* Voir la méthode findBy des objets DAO générés
* @deprecated Utiliser plutôt CopixDAOSearchConditions
* @see CopixDAOSearchConditions
*/

/**
* structure stockant les paramètres d'une condition
*/
class CopixDAOSearchParamsCondition {
    /**
    * the parent group if any
    */
    var $parent = null;

    /**
    * the conditions in this group
    */
    var $conditions = array ();

    /**
    * the sub groups
    */
    var $group = array ();

    /**
    * the kind of group (AND/OR)
    */
    var $kind;

    function CopixDAOSearchParamsCondition (& $parent, $kind){
        if (get_class ($parent) == strtolower ('copixdaosearchparamscondition')){
            $this->parent = & $parent;
        }
        $this->kind   = $kind;
    }
}

/**
* gestion des critères de recherche
*/

class CopixDAOSearchParams {
    /**
    * a DAOSearchParamsCondition objet to describe the coditions we need to apply
    */
    var $condition;

    /**
    * the orders we wants the list to be
    */
    var $order = array ();

    /**
    * the field list we wants to get.
    */
    var $fields = array ();

    /**
    * the condition we actually are browsing
    */
    var $_currentCondition;

    function CopixDAOSearchParams ($type = 'AND'){
        $this->condition = & new CopixDAOSearchParamsCondition ($this, $type);
        $this->_currentCondition = & $this->condition;
    }

    /**
    * sets the order by condition
    */
    function orderBy (){
        $args = func_get_args();
        foreach ($args as $arg){
            if (is_array ($arg)) {
                $this->order[$arg[0]] = $arg[1];
            }else{
                if ((strtolower ($arg) != 'asc') && (strtolower ($arg) != 'desc')) {
                    $this->order[$arg] = 'ASC';
                }
            }
        }
    }

    /**
    * says if the condition is empty
    */
    function isEmpty (){
        return (count ($this->condition->group) == 0) &&
        (count ($this->condition->conditions) == 0) &&
        (count ($this->order) == 0);
    }

    /**
    * starts a condition group
    */
    function startGroup ($kind = 'AND'){
        //adds a condition
        $this->_currentCondition->group[] = & new CopixDAOSearchParamsCondition ($this->_currentCondition, $kind);
        $this->_currentCondition = & $this->_currentCondition->group[count ($this->_currentCondition->group)-1];
    }

    /**
    * ends a condition group
    */
    function endGroup (){
        if ($this->_currentCondition->parent !== null){
            $this->_currentCondition = & $this->_currentCondition->parent;
        }
    }

    /**
    * adds a condition..... no more no less
    */
    function addCondition ($field_id, $condition, $value, $kind = 'and'){
        $this->_currentCondition->conditions[] = array ('field_id'=>$field_id, 'value'=>$value, 'condition'=>$condition, 'kind'=>$kind);
    }

    /**
    * explain in SQL (only the where part of the query)
    * @param array $fields  array of elements 'name'=>array(0=>'fieldname' , 1=>'type', 2=>'table')
    * @return string  where clause
    */
    //function explainSQL ($fields, $fieldsType, & $ct, $tableName){
    function explainSQL ($fields, & $ct){

        // on recoit les données sur le nouveau format demandé par CopixSearchConditions
        // on le transforme en ancien format

        $fieldsNames=array();
        $fieldsType=array();
        $tableName=each($fields);
        $tableName=$tableName[1][2];

        foreach($fields as $n=>$f){
            if($tableName == $f[2]){
                $fieldsNames[$n]=$f[0];
                $fieldsType[$n]=$f[1];
            }
        }


        // génération de la clause where
        $sql = $this->_explainSQLCondition ($this->condition, false, $fieldsNames, $fieldsType, $ct, $tableName);
        $desc  = false;
        $order = array ();
        $firstOrder = true;
        $orderSQL = '';
        foreach ($this->order as $name=>$direction){
            if (! $firstOrder) {
                $orderSQL .= ', ';
            }
            $firstOrder = false;
            $orderSQL .= $fields[$name][0].' '.$direction;
        }
        if(strlen ($orderSQL) > 0) {
            if(trim($sql) == ''){
              $sql=' 1=1 ';
            }
            $sql .= ' ORDER BY '.$orderSQL;
        }
        return $sql;
    }

    /**
    * explain in SQL a single level of ConditionGroup
    */
    function _explainSQLCondition ($condition, $explainKind, & $fields, & $fieldsType, & $ct, $tableName){

        $r = ' ';

        //direct conditions for the group
        $first = true;
        foreach ($condition->conditions as $conditionDescription){
            if (!$first){
                if (! (is_array ($conditionDescription['value']) && !count ($conditionDescription['value']))){
                    $r .= ' '.$conditionDescription['kind'].' ';
                }
            }
            $first = false;

            if($fields[$conditionDescription['field_id']] == $conditionDescription['field_id']){
              // fieldname and propertie name = same name ?
              $prefix = $tableName.'.'.$fields[$conditionDescription['field_id']];
            }else{
                $prefix = $conditionDescription['field_id'];
            }

            $prefixNoCondition = $prefix;
            $prefix.=' '.$conditionDescription['condition'].' ';

            if (!is_array ($conditionDescription['value'])){
                if ((($preparedValue = $this->_prepareValue($conditionDescription['value'],$fieldsType[$conditionDescription['field_id']], $ct)) === 'NULL') && ($conditionDescription['condition'] == '=')){
                   $r .= $prefixNoCondition.' IS '.$preparedValue;
                } else {
                    $r .= $prefix.$preparedValue;
                }
            }else{
                if (count ($conditionDescription['value'])){
                    $r .= ' ( ';
                    $firstCV = true;
                    foreach ($conditionDescription['value'] as $conditionValue){
                        if (!$firstCV){
                            $r .= ' or ';
                        }
                        if ((($preparedValue = $this->_prepareValue($conditionValue,$fieldsType[$conditionDescription['field_id']], $ct)) === 'NULL') && ($conditionDescription['condition'] == '=')){
                           $r .= $prefixNoCondition.' IS '.$preparedValue;
                        }else{
                            $r .= $prefix.$preparedValue;
                        }
                        $firstCV = false;
                    }
                    $r .= ' ) ';
                }
            }
        }

        //sub conditions
        foreach ($condition->group as $conditionDetail){
            $r .= $this->_explainSQLCondition ($conditionDetail, !$first, $fields, $fieldsType, $ct, $tableName);
            $first = false;
        }

        //adds parenthesis around the sql if needed (non empty)
        if (strlen (trim ($r)) > 0){
            //         if ($explainKind){
            //            $r .= $condition->kind . ' ';
            //         }
            $r = ($explainKind ? ' '.$condition->kind.' ' : '') .'('.$r.')';
        }

        return $r;
    }
    /**
    * explain the condition as it will be
    */
    function explain (){
        $r  = 'select ';

        $r .= count ($this->fields) > 0 ? implode (', ', $this->fields) : ' all ';
        $r .= $this->_explainCondition ($this->condition, false);

        if (count ($this->order) > 0){
            return $r.' order by '.implode (', ', $this->order);
        }

        return $r;
    }

    /**
    * explain a single condition.
    */
    function _explainCondition ($condition, $explainKind){
        $r = ' ';
        if ($explainKind){
            $r .= $condition->kind . ' ';
        }
        $r .= '(';

        //direct conditions for the group
        $first = true;
        foreach ($condition->conditions as $conditionDescription){
            if (!$first){
                $r .= ' '.$conditionDescription['kind'].' ';
            }
            $first = false;
            $r .= $conditionDescription['field_id']. ' '.$conditionDescription['condition'].' '.$conditionDescription['value'];
        }

        //sub conditions
        $first = true;
        foreach ($condition->group as $conditionDetail){
            $r .= $this->_explainCondition ($conditionDetail, !$first);
            $first = false;
        }

        $r .= ')';
        return $r;
    }

    function _prepareValue($value, $fieldType, &$ct){
        switch(strtolower($fieldType)){
            case 'int':
            case 'integer':
            case 'autoincrement':
            $value = $value === null ? 'NULL' : intval($value);
            break;
            case 'double':
            case 'float':
            $value = $value === null ? 'NULL' : doubleval($value);
            break;
            case 'numeric'://usefull for bigint and stuff
            case 'bigautoincrement':
            if (is_numeric ($value)){
                return $value === null ? 'NULL' : $value;//was numeric, we can sends it as is
            }else{
                return $value === null ? 'NULL' : intval ($value);//not a numeric, nevermind, casting it
            }
            break;
            default:
            $value = $ct->quote ($value);
        }
        return $value;
    }
}
?>