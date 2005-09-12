<?php
/**
* @package   copix
* @subpackage auth
* @version   $Id: copixgroupcapabilities.dao.class.php,v 1.11 2005/03/07 13:18:44 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class DAOCopixGroupCapabilities {
   function removeGroup ($id){
      $query = 'delete from copixgroupcapabilities where id_cgrp='.$id;
      $ct    = & CopixDBFactory::getConnection ($this->_connectionName);
      $ct->doQuery ($query);
   }

   function removePath ($path){
      $ct    = & CopixDBFactory::getConnection ($this->_connectionName);
      $query = 'delete from copixgroupcapabilities where name_ccpt = '.$ct->quote ($path);
      $ct->doQuery ($query);
   }

   function movePath ($path, $newPath){
      $ct    = & CopixDBFactory::getConnection ($this->_connectionName);
      $query = 'update copixgroupcapabilities set name_ccpt = '.$ct->quote ($newPath).' where name_ccpt='.$ct->quote ($path);
      $ct->doQuery ($query);
   }
}
?>