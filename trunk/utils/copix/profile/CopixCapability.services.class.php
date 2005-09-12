<?php
/**
* @package   copix
* @subpackage profile
* @version   $Id: CopixCapability.services.class.php,v 1.13 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class ServicesCopixCapability {

   /**
   * Creates a capability
   * @param string $fromPath the path the capability is available from
   * @param string $name the capability id we wants to create
   * @param string $description the capability description
   * @param array $values the capability possibles values
   * @return void
   * @access public
   */
   function create ($fromPath, $name, $description, $values) {
      $dao    = & CopixDAOFactory::create       ('copix:CopixCapability');
      $record = & CopixDAOFactory::createRecord ('copix:CopixCapability');

      $record->name_ccpb        = $name;
      $record->name_ccpt        = $fromPath;
      $record->description_ccpb = $description;
      if (is_array ($values)) {
         $record->values_ccpb = implode (';', $values);
      } else {
         $record->values_ccpb = $values;
      }

      $dao->insert ($record);
   }

   /**
   * Updates a capability, will _not_ update the subcapabilities.
   * If you want to move a capability (eg xxx -> xxY, where you want xxx|YYY to become xxY|YYY), use move instead
   * @param string $fromPath the path the capability is available from
   * @param string $name the capability id we wants to create
   * @param string $description the capability description
   * @param array $values the capability possibles values
   * @see ServicesCopixCapability::move ()
   */
   function update ($fromPath, $name, $description, $values = null) {
      $dao    = & CopixDAOFactory::create       ('copix:CopixCapability');

      //we obviously have to check the path existence before updating
      //the element
      if ($record = $dao->get ($path)){

         $record->name_ccpb        = $fromPath;
         $record->description_ccpb = $description;

         if ($values !== null){//updates only if given
            if (is_array ($values)){
               $record->values_ccpb = implode (';', $values);//if an array has been given,
                //convert the values into a string
            }else{
               $record->values_ccpb = $values;
               //If it's already a string, then it's ok :-)
            }
         }
         $dao->update ($record);
      }
   }

   /**
   * Moves all the path that are linked to the given path
   */
   function move ($path, $newPath) {
      $dao         = & CopixDAOFactory::create ('copix:CopixCapability');
      $daoGroupCap = & CopixDAOFactory::create ('copix:CopixGroupCapabilities');

      //gets the moved list.
      $listToMove = $this->getList ($path);

      //moves the elements.
      foreach ($listToMove as $pathToReplace){
         $pathToCreate = str_replace ($path, $newPath, $pathToReplace);

         //creates the dest cap.
         $oldCap = & $dao->get ($pathToReplace);
         $newCap = & CopixDAOFactory::createRecord ('copix:CopixCapability');

         $newCap->name_ccpb        = str_replace ($path, $newPath, $pathToReplace);
         $newCap->description_ccpb = $oldCap->description_ccpb;
         $newCap->values_ccpb      = $oldCap->values_ccpb;

         $dao->insert ($newCap);

         //moves associations.
         $daoGroupCap->movePath ($pathToReplace, $pathToCreate);
         $dao->delete ($pathToReplace);
      }
   }

   /**
   * deletes all the related path.
   */
   function delete ($path) {
      $dao  = & CopixDAOFactory::create ('copix:CopixCapability');
      $daoGroupCapabilities = & CopixDAOFactory::create ('copix:CopixGroupCapabilities');

      //gets the moved list.
      $listToDelete = $this->getList ($path);

      //moves the elements.
      foreach ((array) $listToDelete as $pathToDelete){
         $daoGroupCapabilities->removePath ($pathToDelete);
         $dao->delete ($pathToDelete);
      }
      $dao->delete ($path);
   }

   /**
   * gets the list of capablities from a base path.
   */
   function getList ($fromPath = null){
      $sp = CopixDAOFactory::createSearchParams ();
      //if given a path.
      if ($fromPath !== null){
         $sp->addCondition ('name_ccpb', 'like', $fromPath.'|%');
      }
      $sp->orderBy ('name_ccpb');

      //search
      $dao     = & CopixDAOFactory::create ('copix:CopixCapability');
      $results = $dao->findBy ($sp);

      //we only wants names
      $toReturn = array ();
      foreach ($results as $cap) {
         if($this->checkBelongsTo ($fromPath, $cap->name_ccpb)){//check if matches.
            $toReturn [] = $cap->name_ccpb;
         }
      }

      //we're gonna put the list in the correct order now
      return $toReturn;
   }

   /**
   * checks if $motherPath is really the mother of childPath
   * ie: we don't want to get things like site|1 being the mother of
   *    site|1234. (reason why we can't use substr here, but we have to explode
   *    strings)
   */
   function checkBelongsTo ($motherPath, $childPath) {
    $motherPath = '/^'.preg_replace ( '/([^\w\s\d])/', '\\\\\\1',$motherPath).'(\||$)/';
    return (preg_match ($motherPath,$childPath) > 0);
/*
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
*/
   }
}
?>