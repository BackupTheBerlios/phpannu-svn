<?php
/**
* @package   copix
* @subpackage coremodule
* @version   $Id: copixmodule.dao.class.php,v 1.5 2005/02/14 10:37:43 graoux Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class DAOCopixModule {
   function deleteAll (){
      $query = 'delete from copixmodule';
      $ct = CopixDBFactory::getConnection ($this->_connectionName);
      $ct->doQuery ($query);
   }
}
?>
