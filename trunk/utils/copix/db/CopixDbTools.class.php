<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbTools.class.php,v 1.9 2005/02/22 11:12:32 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
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
 */
class CopixDbTools {
    /**
    * @constructor
    */
   function CopixDbTools(& $connector){
      $this->connector = & $connector;
   }
   
   /**
   * returns the table list
   */
   function getTableList (){
      return $this->_getTableList ();
   }
   
   /**
   * return the field list of a given table
   */
   function getFieldList ($tableName){
      return $this->_getFieldList ($tableName);
   }
}
?>