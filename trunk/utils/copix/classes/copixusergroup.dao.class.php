<?php
/**
* @package   copix
* @subpackage auth
* @version   $Id: copixusergroup.dao.class.php,v 1.11 2005/02/14 10:37:43 graoux Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class DAOCopixUserGroup {
   /**
   * removes the given group from all the users
   * @param int $id the group id
   * @return void
   */
   function removeGroup ($id){
      $query = 'delete from copixusergroup where id_cgrp='.$id;
      $ct    = & CopixDBFactory::getConnection ($this->_connectionName);
      $ct->doQuery ($query);
   }

   /**
   * Deletes the given user from every groups
   * @param string $login the user login
   * @return void
   */
   function removeUser ($login) {
      $ct    = & CopixDBFactory::getConnection ($this->_connectionName);
      $query = 'delete from copixusergroup where login_cusr='.$ct->quote ($login);
      $ct->doQuery ($query);
   }
}
?>
