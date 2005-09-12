<?php

/**
* @package   copix
* @subpackage copixdao
* @version   $Id: CopixDAOSearchConditions.class.php,v 1.21.4.1 2005/08/01 22:17:54 laurentj Exp $
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
*/

/**
* structure stockant les paramètres d'une condition
*/
class CopixDAOSearchCondition {
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

    function CopixDAOSearchCondition (& $parent, $kind){
        if (get_class ($parent) == strtolower ('CopixDAOSearchCondition')){
            $this->parent = & $parent;
        }
        $this->kind   = $kind;
    }
}

/**
* gestion des critères de recherche
*/
class CopixDAOSearchConditions {
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

    var $_generatePHP=false;

    function CopixDAOSearchConditions ($kind = 'AND', $generatePHP=false){
        $this->condition = & new CopixDAOSearchCondition ($this, $kind);
        $this->_currentCondition = & $this->condition;
        $this->_generatePHP = $generatePHP;
    }

    function addItemOrder($field_id, $way='ASC'){
        $this->order[$field_id]=$way;
    }

    /**
    * says if the condition is empty
    */
    function isEmpty (){
        return (count ($this->condition->group) == 0) &&
        (count ($this->condition->conditions) == 0) &&
        (count ($this->order) == 0) ;
    }

    /**
    * starts a condition group
    */
    function startGroup ($kind = 'AND'){
        //adds a condition
        $cond= & new CopixDAOSearchCondition ($this->_currentCondition, $kind);
        $this->_currentCondition->group[] = & $cond;
        unset($this->_currentCondition);
        $this->_currentCondition = & $cond->parent->group[count ($cond->parent->group)-1];
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
    function addCondition ($field_id, $condition, $value){
        $this->_currentCondition->conditions[] = array ('field_id'=>$field_id, 'value'=>$value, 'condition'=>$condition, 'php'=>false);
    }

    /**
    * adds a condition..... value is php code
    */
    function addPHPCondition ($field_id, $condition, $value){
        if ($this->_generatePHP){
           $this->_currentCondition->conditions[] = array ('field_id'=>$field_id, 'value'=>$value, 'condition'=>$condition,  'php'=>true);
        }
    }

    /**
    * explain in SQL (only the where part of the query)
    * @param array $fields  array of elements 'name'=>array(0=>'fieldname' , 1=>'type', 2=>'table')
    * @return string  where clause
    */
    function explainSQL ($fields, & $ct){
        $sql = $this->_explainSQLCondition ($this->condition, $fields,$ct, true);

        $order = array ();
        foreach ($this->order as $name => $way){
            if (isset($fields[$name])){
               $order[] = $name.' '.$way;
            }
        }
        if(count ($order) > 0){
            if(trim($sql) =='') {
				$sql.= ' 1=1 ';
			}
            $sql.=' ORDER BY '.implode (', ', $order);
        }
        return $sql;
    }

    /**
    * explain in SQL a single level of ConditionGroup
    * @param array $fields  array of elements 'name'=>array(0=>'fieldname' , 1=>'type', 2=>'table', 3=>'motif')
    * @param array $condition array of associative array representing conditions. 'fieldname'=>array ('fieldId'=>, 'value'=>, 'condition'=>, 'php'=>)
    */
    function _explainSQLCondition ($condition, & $fields, & $ct, $principal=false){
        $r = ' ';

        //direct conditions for the group
        $first = true;
        foreach ($condition->conditions as $condDesc){
            if (!$first){
                $r .= ' '.$condition->kind.' ';
            }
            $first = false;

            $property=$fields[$condDesc['field_id']];

            if(isset($property[2]) && $property[2] != ''){
               $prefix = $property[2].'.'.$property[0];
            }else{
               $prefix = $property[0];
            }

            if(isset($property[3]) && $property[3] != '' &&$property[3] != '%s'){
                $prefix=sprintf($property[3], $prefix);
            }

            $prefixNoCondition = $prefix;
            $prefix.=$condDesc['condition'];

            if($condDesc['php']) {
                if (!is_array ($condDesc['value'])){
                    if ($condDesc['condition'] == '='){//handles equality of "NULL" values.
                       $r .= $prefixNoCondition.'\'.('.$condDesc['value'].'===null ? \' IS \' : \' = \').\''.$this->_preparePHPValue($condDesc['value'],$property[1]);
                    }else{
                        $r .= $prefix.$this->_preparePHPValue($condDesc['value'],$property[1]);
                    }
                }else{
                    $r .= ' ( ';
                    $firstCV = true;
                    foreach ($condDesc['value'] as $conditionValue){
                        if (!$firstCV){
                            $r .= ' or ';
                        }
                        if ($condDesc['condition'] == '='){//handles equality of "NULL" values in the PHP generation.
                           $r .= $prefixNoCondition.'\'.('.$conditionValue.'===null ? \' IS \' : \' = \').\''.$this->_preparePHPValue($conditionValue,$property[1]);
                        } else {
                            $r.=$prefix.$this->_preparePHPValue($conditionValue,$property[1]);
                        }
                        $firstCV = false;
                    }
                    $r .= ' ) ';
                }
            }else{
                if (!is_array ($condDesc['value'])){
                    if ((($preparedValue = $this->_prepareValue($condDesc['value'],$property[1], $ct))
                    === 'NULL')
                    && ($conditionDescription['condition'] == '=')){
                       $r .= $prefixNoCondition.' IS '.$preparedValue;
                    } else {
                        $r .= $prefix.$preparedValue;
                    }
                }else{
                    $r .= ' ( ';
                    $firstCV = true;
                    foreach ($condDesc['value'] as $conditionValue){
                        if (!$firstCV){
                            $r .= ' or ';
                        }
                        if ((($preparedValue = $this->_prepareValue($conditionValue,$property[1], $ct)) === 'NULL') && ($conditionDescription['condition'] == '=')){;
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
            if (!$first){
                $r .= ' '.$condition->kind.' ';
            }
            $r .= $this->_explainSQLCondition ($conditionDetail, $fields, $ct);
            $first=false;
        }

        //adds parenthesis around the sql if needed (non empty)
        if (strlen (trim ($r)) > 0 && !$principal){
            $r = '('.$r.')';
        }
        return $r;
    }

    /**
    * prepare the value ready to be used in a dynamic evaluation
    */
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

    /**
    * prepare a string ready to be included in a PHP script
    * we assume that if the value is "NULL", all things has been take care of
    *   before the call of this method
    * The method generates something like (including quotes) '.some PHP code.'
    *   (we do break "simple quoted strings")
    */
    function _preparePHPValue($value, $fieldType){
        switch(strtolower($fieldType)){
            case 'int':
            case 'integer':
            case 'autoincrement':
            /*            if(is_numeric($value))
            $value=intval($value);
            else
            */
            $value= '\'.('.$value.' === null ? \'NULL\' : intval('.$value.')).\'';
            break;
            case 'double':
            case 'float':
            /*            if(is_numeric($value))
            $value=doubleval($value);
            else
            */
            $value= '\'.('.$value.' === null ? \'NULL\' : doubleval('.$value.')).\'';
            break;
            case 'numeric'://usefull for bigint and stuff
            case 'bigautoincrement':
            if(!is_numeric($value)){
                 $value='\'.('.$value.' === null ? \'NULL\' : (is_numeric ('.$value.') ? '.$value.' : intval('.$value.'))) .\'';
            }
            break;
            default:
            $value ='\'. $__RESERVED_INTERNAL_COPIX_ct->quote ('.$value.').\'';
        }
        return $value;
    }
}
?>