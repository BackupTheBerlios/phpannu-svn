<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbTools.sqlserver.class.php,v 1.4 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Bertrand Yan
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * classe d'outils pour gérer une base de données
 * @package copix
 * @subpackage copixdb
 * @todo  à revoir totalement
 */
class CopixDBToolsSQLServer {
   function CopixDBToolsSQLServer(&$connector){
      parent::CopixDBTools($connector);
   }
   /**
   * retourne la liste des tables
   * @return   array    $tab[] = $nomDeTable
   */
   function _getTableList (){
      $results = array ();
      $rs = $this->connector->doQuery ('select name from sysobjects where type = \'U\' order by name');
      while ($line = $rs->fetchObject ()){
         $results[] = $line->name;
      }
      return $results;
   }
   /**
   * récupère la liste des champs pour une base donnée.
   * @todo
   * @return   array    $tab[NomDuChamp] = obj avec prop (tye, length, lengthVar, notnull)
   */
   function _getFieldList ($tableName){
      $results = array ();

      $sql_get_fields  = 'SELECT DISTINCT ';
      $sql_get_fields .= "syscolumns.name as Field, systypes.name as type, syscolumns.length as length, syscolumns.isnullable as isnull";
      $sql_get_fields .= " FROM sysobjects,syscolumns,systypes WHERE ";
      $sql_get_fields .= " sysobjects.id = syscolumns.id AND syscolumns.xtype=systypes.xusertype AND ";
      $sql_get_fields .= " syscolumns.xtype = systypes.xtype AND sysobjects.name='" . $tableName ."'";
      $rs = $this->connector->doQuery ($sql_get_fields);

      while ($result_line = $rs->fetchObject())
      {
          $p_result_line->type      = $result_line->type;
          $p_result_line->is_index  = 0;
          $p_result_line->is_auto_increment = 0;

          if( ereg("identity" , $p_result_line->type ) )  {
             $p_result_line->is_auto_increment = 1;
          }

          $p_result_line->length    = $result_line->length;
          $p_result_line->notnull   = (!$result_line->isnull);

          $results[$result_line->Field] = $p_result_line;
      }

      $rs = $this->dbQuery("exec sp_pkeys '".$tableName."'");
      while ($get_primary_key = $rs->fetchObject())
      {
         $keysArray = array_keys($results);
         foreach($keysArray as $key_var)
         {
            if($key_var == $get_primary_key->COLUMN_NAME){
               $results[$key_var] -> is_index = 1;
            }
         }
      }
      return $results;
   }
}
?>
