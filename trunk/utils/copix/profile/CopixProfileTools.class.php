<?php
/**
* @package   copix
* @subpackage profile
* @version   $Id: CopixProfileTools.class.php,v 1.12 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Serveral services for the CopixProfile API
*/
class CopixProfileTools {
   /**
   * gets the capabilities ( "the values" ) list.
   * Don't forget that capabilities have path and values, and that the users gets
   *   both.
   * @return array CopixCapabiliy
   */
   function getCapabilities (){
      //search
      $dao     = & CopixDAOFactory::create ('copix:CopixCapability');

      //sorting the path
      $tmpSort = array ();
      foreach ($dao->findAll () as $capability) {
         //Btw, we're conerting values_ccpb in an array if needed
         if (strlen (trim ($capability->values_ccpb))) {
            $capability->values_ccpb = explode (';', $capability->values_ccpb);
         }else{
            $capability->values_ccpb = null;
         }
         $tmpSort[str_replace ('|', '.', $capability->name_ccpb)] = $capability;
      }
      ksort ($tmpSort);

      $results = array ();
      foreach ($tmpSort as $name=>$capability) {
         $results [str_replace ('.', '|', $name)] = $capability;
      }

      return $results;
   }

   /**
   * Gets the capabilities path list.
   * @return array of CapabilitiesPath
   * @access public
   */
   function getCapabilitiesPath (){
      //search
      $dao     = & CopixDAOFactory::create ('copix:CopixCapabilityPath');

      //sorting the path
      $tmpSort = array ();
      foreach ($dao->findAll () as $capability) {
         $tmpSort[str_replace ('|', '.', $capability->name_ccpt)] = $capability;
      }
      ksort ($tmpSort);

      $results = array ();
      foreach ($tmpSort as $name=>$capability) {
         $results [str_replace ('.', '|', $name)] = $capability;
      }

      return $results;
   }

   /**
   * gets the capabilities (the values) that can be applied to the given path
   * @param string $path the path we wants to get the capabilities for
   * @access public
   */
   function getCapabilitiesForPath ($path){
      static $capabilities = false;
      if ($capabilities === false){
         $capabilities = CopixProfileTools::getCapabilities ();
      }

      $toReturn = array ();
      foreach ($capabilities as $name=>$capability){
         if (CopixProfileTools::pathBelongsTo ($path, $capability->name_ccpt)){
            $toReturn[] = $capability;
         }
      }

      return $toReturn;

   }

   /**
   * Check if the given childPath belongs to the given motherPath
   * @param string childPath the path we want to test
   * @param string motherPath the assumed mother
   * @return boolean
   * @access public
   */
   function pathBelongsTo ($childPath, $motherPath){
      $mother = explode ('|', $motherPath);
      $child  = explode ('|', $childPath);
      //if less in child, it's not a child
      if (count ($child) < count ($mother)) {
         return false;
      }

      //we have to check the complete path of mother.
      //If all mothers elements are in child, then it's ok.
      foreach ($mother as $key=>$element) {
         if ($child[$key] != $element) {
            return false;//does not match, it's not it's child
         }
      }

      //everything was successful.
      return true;
   }

   /**
   * Creates a capability path.
   *   we'll first check if the capability exist or not
   * @param string $path the capability path we wants to create
   * @param string description the description of the capability
   * @return boolean success or failure.
   * @access public
   */
   function createCapabilityPath ($path, $description, $ct = null) {
      $dao    = & CopixDAOFactory::instanceOf ('copix:CopixCapabilityPath');
      $record = & CopixDAOFactory::createRecord ('copix:CopixCapabilityPath');

      //check if the capability already exists.
      //If so, we won't create it.
      if ($dao->get ($path) !== false){
         return false;
      }

      $record->name_ccpt = $path;
      $record->description_ccpt = $description;

      return $dao->insert ($record, $ct);
   }

   /**
   * Moves all the path that are linked to the given path
   */
   function moveCapabilityPath ($path, $newPath) {
      $dao         = & CopixDAOFactory::create ('copix:CopixCapabilityPath');
      $daoGroupCap = & CopixDAOFactory::create ('copix:CopixGroupCapabilities');

      //gets the moved list.
      $listToMove = CopixProfileTools::getList ($path);
      $listToMove[] = $path;

      //moves the elements.
      foreach ((array) $listToMove as $pathToReplace){
         $pathToCreate = str_replace ($path, $newPath, $pathToReplace);

         //creates the dest cap.
         $oldCap = & $dao->get ($pathToReplace);
         $newCap = & CopixDAOFactory::createRecord ('copix:CopixCapability');

         $newCap->name_ccpt        = str_replace ($path, $newPath, $pathToReplace);
         $newCap->description_ccpt = $oldCap->description_ccpt;

         $dao->insert ($newCap);

         //moves associations.
         $daoGroupCap->movePath ($pathToReplace, $pathToCreate);
         $dao->delete ($pathToReplace);
      }
   }

   /**
   * deletes all the related path.
   */
   function deleteCapabilityPath ($path) {

      $dao  = & CopixDAOFactory::create ('copix:CopixCapabilityPath');
      $daoGroupCapabilities = & CopixDAOFactory::create ('copix:CopixGroupCapabilities');

      //gets the moved list.
      $listToDelete = CopixProfileTools::getList ($path);

      //moves the element childs.
      foreach ((array) $listToDelete as $pathToDelete){
         $daoGroupCapabilities->removePath ($pathToDelete);
         $dao->delete ($pathToDelete);
      }

      //moves the element itself
      $daoGroupCapabilities->removePath ($path);
      $dao->delete ($path);
   }

   /**
   * gets the list of capablities from a base path.
   */
   function getList ($fromPath = null){
      $sp = CopixDAOFactory::createSearchParams ();
      //if given a path.
      if ($fromPath !== null){
         $sp->addCondition ('name_ccpt', 'like', $fromPath.'|%');
      }

      //search
      $dao     = & CopixDAOFactory::create ('copix:CopixCapabilityPath');
      $results = $dao->findBy ($sp);

      //we only wants names
      $toReturn = array ();
      foreach ($results as $cap) {
         $toReturn [] = $cap->name_ccpt;
      }

      //we're gonna put the list in the correct order now
      return $toReturn;
   }

   /**
   * updates the capability path description
   */
   function updateCapabilityPathDescription ($name, $description) {
      $dao    = & CopixDAOFactory::instanceOf ('copix:CopixCapabilityPath');

      //Check if the given capability path exists or not.
      if (($record = $dao->get ($name)) === null){
         //does not exist, cannot update
         return null;
      }

      //updating
      $record->description_ccpt = $description;

      return $dao->update ($record);
   }
}
?>