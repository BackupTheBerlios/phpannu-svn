<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixQueryWidget.class.php,v 1.10 2005/02/22 11:12:32 gcroes Exp $
* @author   Croes G�rald, Jouanneau Laurent
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
    * G�n�ration d'une requete SQL d'INSERTION.
    * les valeurs dans $fieldsToInsert  doivent avoir �t� pr�par�es auparavant
    * par CopixDbWidget::prepareValues ou pr�par�es � la main (chaines echapp�es, mis entre quote etc...)
    *
    * @param   string $tableName   le nom de la table ou l'on ins�re les infos.
    * @param array   $fieldsToInsert   tableau associatif de la forme Tab[NomDuChamp]=Value avec les champs � ajouter.
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
    * G�n�ration d'une requete SQL de SUPPRESSION.
    * les valeurs dans $condition  doivent avoir �t� pr�par�es auparavant
    * par CopixDbWidget::prepareValues ou pr�par�es � la main (chaines echapp�es, mis entre quote etc...)
    *
    * @param   string   $tableName Le nom de la table d'ou l'on supprime les infos.
    * @param   array      $condition Tableau associatif contenant les conditions de suppressions.De la forme Tab[NomDuChamp]=Value.
    * @param boolean  $useOr      indique si il faut utiliser un OR ou un AND entre les �lements de la condition
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
    * G�n�ration d'une requete SQL de  SELECTION.
    * les valeurs dans $condition  doivent avoir �t� pr�par�es auparavant
    * par CopixDbWidget::prepareValues ou pr�par�es � la main (chaines echapp�es, mis entre quote etc...)
    *
    * @param   string   $tableName    le nom de la table sur laquelle effectuer la s�lection.
    * @param mixed      $what       liste (tableau) des champs � s�lectionner, ou chaine
    * @param array      $condition   tableau associatif des conditions de s�lection. De la forme Tab[NomDuChamp]=Value
    * @param boolean  $useOr      indique si il faut utiliser un OR ou un AND entre les �lements de la condition
    * @param array    $order      liste des elements d'ordre
    * @param array    $orderDesc  indique si l'ordre est descendant (true) ou ascendant (false)
    * @return  string   la chaine sql.
    */
    function sqlSelect($tableName, $what, $condition = null, $useOr=false, $order = null, $orderDesc = false){
        $sqlquery = 'SELECT ';

        //Champs � s�lectionner.
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
    * G�n�ration d'une requete SQL de mise � jour.
    * les valeurs dans $toSet,$condition  doivent avoir �t� pr�par�es auparavant
    * par CopixDbWidget::prepareValues ou pr�par�es � la main (chaines echapp�es, mis entre quote etc...)
    *
    * @param string   $tableName   Nom de la table sur laquelle effectuer la mise � jour.
    * @param array      $toSet   Tableau associatif contenant les champs � mettre � jour. De la forme Tab[NomDuChamp]=Value.
    * @param array      $condition   Tableau associatif contenant les conditions de mise � jour. De la forme Tab[NomDuChamp]=Value.
    * @param boolean  $useOr      indique si il faut utiliser un OR ou un AND entre les �lements de la condition
    * @return string  La chaine sql.
    */
    function sqlUpdate ($tableName, $toSet, $condition=null, $useOr=false){

        $sqlquery = 'UPDATE '.$tableName.' SET ';
        $first=true;
        //partie mise � jour.
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
    * g�n�re une clause WHERE � partir d'un tableau de param�tre
    * les valeurs dans $condition  doivent avoir �t� pr�par�es auparavant
    * par CopixDbWidget::prepareValues ou pr�par�es � la main (chaines echapp�es, mis entre quote etc...)
    *
    * @param   array    $condition  param�tres
    * @param   boolean  $or         indique si il s'agit d'un OR ou d'un AND entre les �lements de la clause
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