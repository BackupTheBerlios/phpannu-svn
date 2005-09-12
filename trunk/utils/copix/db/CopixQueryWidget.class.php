<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixQueryWidget.class.php,v 1.10 2005/02/22 11:12:32 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package copix
* @subpackage copixdb
*/
class CopixQueryWidget {

    /**
    * Génération d'une requete SQL d'INSERTION.
    * les valeurs dans $fieldsToInsert  doivent avoir été préparées auparavant
    * par CopixDbWidget::prepareValues ou préparées à la main (chaines echappées, mis entre quote etc...)
    *
    * @param   string $tableName   le nom de la table ou l'on insère les infos.
    * @param array   $fieldsToInsert   tableau associatif de la forme Tab[NomDuChamp]=Value avec les champs à ajouter.
    * @return string La chaine d'instruction sql.
    */
    function sqlInsert ($tableName, $fieldsToInsert){
        $keys   = implode(',',array_keys($fieldsToInsert));
        foreach ($fieldsToInsert as $key=>$value){
            if ($value === null){
                $fieldsToInsert[$key] = 'NULL';
            }
        }
        $values = implode(',',array_values($fieldsToInsert));

        return 'INSERT INTO '.$tableName.'('.$keys.') VALUES ('.$values.')';
    }

    /**
    * Génération d'une requete SQL de SUPPRESSION.
    * les valeurs dans $condition  doivent avoir été préparées auparavant
    * par CopixDbWidget::prepareValues ou préparées à la main (chaines echappées, mis entre quote etc...)
    *
    * @param   string   $tableName Le nom de la table d'ou l'on supprime les infos.
    * @param   array      $condition Tableau associatif contenant les conditions de suppressions.De la forme Tab[NomDuChamp]=Value.
    * @param boolean  $useOr      indique si il faut utiliser un OR ou un AND entre les élements de la condition
    * @return string   la chaine d'instruction sql.
    */
    function sqlDelete ($tableName, $condition=null, $or=false){
        $first = true;

        $sqlquery = 'DELETE FROM '.$tableName;

        if ($condition == null){
            return $sqlquery;
        }
        return $sqlquery. CopixQueryWidget::_prepareCondition($condition, $or);
    }

    /**
    * Génération d'une requete SQL de  SELECTION.
    * les valeurs dans $condition  doivent avoir été préparées auparavant
    * par CopixDbWidget::prepareValues ou préparées à la main (chaines echappées, mis entre quote etc...)
    *
    * @param   string   $tableName    le nom de la table sur laquelle effectuer la sélection.
    * @param mixed      $what       liste (tableau) des champs à sélectionner, ou chaine
    * @param array      $condition   tableau associatif des conditions de sélection. De la forme Tab[NomDuChamp]=Value
    * @param boolean  $useOr      indique si il faut utiliser un OR ou un AND entre les élements de la condition
    * @param array    $order      liste des elements d'ordre
    * @param array    $orderDesc  indique si l'ordre est descendant (true) ou ascendant (false)
    * @return  string   la chaine sql.
    */
    function sqlSelect($tableName, $what, $condition = null, $useOr=false, $order = null, $orderDesc = false){
        $sqlquery = 'SELECT ';

        //Champs à sélectionner.
        if (is_array ($what)){
            $sqlquery .= implode (',', $what);
        }else{
            $sqlquery .= $what;
        }
        $sqlquery .= ' FROM ' . $tableName;

        $sqlquery .= CopixQueryWidget::_prepareCondition($condition,$useOr);

        if ($order !== null){
            $sqlquery .= ' order by ' . (is_array($order) ? implode (',', $order) : $order) . ($orderDesc ? ' DESC ' : '');
        }
        return $sqlquery;
    }
    /**
    * Génération d'une requete SQL de mise à jour.
    * les valeurs dans $toSet,$condition  doivent avoir été préparées auparavant
    * par CopixDbWidget::prepareValues ou préparées à la main (chaines echappées, mis entre quote etc...)
    *
    * @param string   $tableName   Nom de la table sur laquelle effectuer la mise à jour.
    * @param array      $toSet   Tableau associatif contenant les champs à mettre à jour. De la forme Tab[NomDuChamp]=Value.
    * @param array      $condition   Tableau associatif contenant les conditions de mise à jour. De la forme Tab[NomDuChamp]=Value.
    * @param boolean  $useOr      indique si il faut utiliser un OR ou un AND entre les élements de la condition
    * @return string  La chaine sql.
    */
    function sqlUpdate ($tableName, $toSet, $condition=null, $useOr=false){

        $sqlquery = 'UPDATE '.$tableName.' SET ';
        $first=true;
        //partie mise à jour.
        foreach ($toSet as $Key=>$Elem){
            if (!$first){
                $sqlquery = $sqlquery.', ';
            }
            $first = false;
            $sqlquery = $sqlquery.$Key.'='.$Elem.' ';
        }
        //partie condition.
        $sqlquery = $sqlquery. CopixQueryWidget::_prepareCondition($condition,$useOr);;
        return $sqlquery;
    }


    /**
    * génère une clause WHERE à partir d'un tableau de paramètre
    * les valeurs dans $condition  doivent avoir été préparées auparavant
    * par CopixDbWidget::prepareValues ou préparées à la main (chaines echappées, mis entre quote etc...)
    *
    * @param   array    $condition  paramètres
    * @param   boolean  $or         indique si il s'agit d'un OR ou d'un AND entre les élements de la clause
    * @return  string   chaine clause WHERE
    */
    function _prepareCondition($condition, $or=false){
        $cond=array();
        foreach ((array) $condition as $Key=>$Elem){
            //si la condition comporte plusieurs valeurs, on fait un ou sur ces valeurs.
            if (is_array ($Elem)){
                if (count ($Elem) > 0){
                    foreach ($Elem as $k=>$or_conditions){
                        $Elem[$k] = $Key.'='.$or_conditions;
                    }
                    $cond[]='('.implode( ($or?' AND ':' OR '),$Elem).')';
                }
            }else{
                $cond[]= $Key.'='.$Elem;
            }
        }

        if(count($cond) > 0){
           return ' WHERE '.implode(($or?' OR ':' AND '), $cond);
        }
        return '';
    }
}
?>