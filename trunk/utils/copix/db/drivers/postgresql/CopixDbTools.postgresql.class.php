<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbTools.postgresql.class.php,v 1.8 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes G�rald, Jouanneau Laurent, Ferlet Patrice
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * classe d'outils pour g�rer une base de donn�es
 * @package copix
 * @subpackage copixdb
 * @todo  � revoir totalement
 */
class CopixDBToolsPostgreSQL {
   function CopixDBToolsPostgreSQL(&$connector){
      parent::CopixDBTools($connector);
   }
   /**
   * retourne la liste des tables
   * @return   array    $tab[] = $nomDeTable
   */
   function _getTableList (){
      $results = array ();
      $sql = "SELECT tablename FROM pg_tables WHERE tablename NOT LIKE 'pg_%' ORDER BY tablename";
      $rs = $this->doQuery ($sql);
      while ($line = $rs->fetchObject ()){
         $results[] = $line->tablename;
      }
      return $results;
   }
    /**
    * r�cup�re la liste des champs pour une base donn�e.
    * @return    array    $tab[NomDuChamp] = obj avec prop (tye, length, lengthVar, notnull)
    */
    function _getFieldList ($tableName){
        $results = array ();
        $sql_get_fields = "SELECT
        a.attname as Field, t.typname as type, a.attlen as length, a.atttypmod,
        case when a.attnotnull  then 1 else 0 end as notnull,
        a.atthasdef,
        (SELECT adsrc FROM pg_attrdef adef WHERE a.attrelid=adef.adrelid AND a.attnum=adef.adnum) AS adsrc
        FROM
            pg_attribute a,
            pg_class c,
            pg_type t
        WHERE
          c.relname = '{$tableName}' AND a.attnum > 0 AND a.attrelid = c.oid AND a.atttypid = t.oid
        ORDER BY a.attnum";

        $rs = $this->connector->doQuery ($sql_get_fields);
        $toReturn=array();
        //$results = $this->getAll ($sql_get_fields);
        while ($result_line = $rs->fetch ()){
            if(preg_match('/nextval\(\'(.*?)\.'.$tableName.'_'.$result_line->field.'_seq\'::text\)/',
            $result_line->adsrc)){
                $result_line->auto="auto_increment";
            }

            $result_line->notnull = ($result_line->notnull==1)  ? true:false;
            $result_line->type = preg_replace('/(\D*)\d*/','\\1',$result_line->type);
            if($result_line->length<0) $result_line->length=null;
            $toReturn[$result_line->field]=$result_line;
        }

        return $toReturn;
    }
}
?>