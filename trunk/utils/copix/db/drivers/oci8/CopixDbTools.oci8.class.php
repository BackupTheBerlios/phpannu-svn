<?php
/**
* @package   copix
* @subpackage copixdb
* @version   $Id: CopixDbTools.oci8.class.php,v 1.7 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes G�rald, Jouanneau Laurent
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
class CopixDBToolsOci8 {
   function CopixDBToolsOci8(&$connector){
      parent::CopixDBTools($connector);
   }
   /**
   * retourne la liste des tables
   * @todo
   * @return   array    $tab[] = $nomDeTable
   */
   function _getTableList (){
      return null;
   }
   /**
   * r�cup�re la liste des champs pour une base donn�e.
   * @todo
   * @return   array    $tab[NomDuChamp] = obj avec prop (tye, length, lengthVar, notnull)
   */
   function _getFieldList ($tableName){
      return null;
   }
}
?>
