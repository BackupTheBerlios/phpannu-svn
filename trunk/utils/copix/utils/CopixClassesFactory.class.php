<?php
/**
* @package   copix
* @subpackage generaltools
* @version   $Id: CopixClassesFactory.class.php,v 1.13.4.1 2005/08/08 22:12:19 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* permet d'instancier des classes
* @package   copix
* @subpackage generaltools
*/
class CopixClassesFactory {
    /**
    * cache for instances
    */
    var $_instanceCache = array ();

   /**
   * cache for DAO instances
   */
   var $_cacheInstanceDAO = array ();

    /**
    * creates a new object from a classe store in classes directory
    */
    function & create ($name){
        //Récupération des éléments critiques.
        $file     = & CopixSelectorFactory::create($name);
        $filePath = $file->getPath() .COPIX_CLASSES_DIR.strtolower ($file->fileName).'.class.php' ;

        //if (is_readable($filePath)){
            require_once ($filePath);
            $fileClass = $file->fileName;
            return $return = & new $fileClass;
        //}else{
        //    trigger_error (CopixI18N::get('copix:copix.error.unfounded.class',$name.'-'.$filePath), E_USER_ERROR);
        //    return null;
        //}
    }

    /**
    * creates a new object DAO (old, very old version)
    * No link with CopixDAO
    * @deprecated
    */
    function & createDAO ($name){
        //Récupération des éléments critiques.
        $file     = & CopixSelectorFactory::create($name);
        $filePath = $file->getPath() .COPIX_CLASSES_DIR.strtolower ($file->fileName).'.class.php' ;

        //if (is_readable($filePath)){
            require_once ($filePath);
            $fileClass = 'DAO'.$file->fileName;
            return $return = & new $fileClass;
        //}else{
        //    trigger_error (CopixI18N::get('copix:copix.error.unfounded.class',$name.'-'.$filePath), E_USER_ERROR);
        //    return null;
        //}
    }

    /**
    * Includes the class definition
    * @param string $name the selector
    */
    function fileInclude ($name){
        $file     = & CopixSelectorFactory::create($name);
        $filePath = $file->getPath() .COPIX_CLASSES_DIR.strtolower ($file->fileName).'.class.php' ;
        //if (is_readable($filePath)){
            require_once ($filePath);
        //}else{
        //    trigger_error (CopixI18N::get('copix:copix.error.unfounded.class',$name.'-'.$filePath), E_USER_ERROR);
        //}
    }

    /**
    * same as create, but handles singleton
    *   internaly calling _instanceOf ()
    * @param $id the id of the object we wants to get
    */
    function & instanceOf ($id){
        $me = & CopixClassesFactory::_instance ();
        return $me->_instanceOf ($id);
    }
   /**
   * Same as createDAO, but handles  singleton
   */
   function & instanceOfDAO ($id){
      $me = & CopixClassesFactory::_instance ();
      return $me->_instanceOfDAO ($id);
   }

    /**
   * same as createDAO, but handles the singleton pattern.
   * @param $id the id of the object we wants to get.
   */
   function & _instanceOfDAO ($id){
      //gets the fileSelctor
      $file = & CopixSelectorFactory::create($id);

      //check if exists in the cache (while getting the fullIdentifier in id)
      if (! isset ($this->_cacheInstanceDAO [$id = $file->getSelector ()])){
         $this->_cacheInstanceDAO[$id] = & $this->createDAO ($id);
      }

      return $this->_cacheInstanceDAO[$id];
   }

    /**
    * same as create, but handles the singleton pattern.
    * @param $id the id of the object we wants to get.
    */
    function & _instanceOf ($id){
        //gets the fileSelctor
        $file = & CopixSelectorFactory::create($id);

        //check if exists in the cache (while getting the fullIdentifier in id)
        if (! isset ($this->_cacheInstance [$id = $file->getSelector ()])){
            $this->_cacheInstance[$id] = & $this->create ($id);
        }

        return $this->_cacheInstance[$id];
    }

    /**
    * gets the instance of the CopixClassesFactory
    */
    function & _instance () {
        static $me = false;
        if ($me == false) {
            $me = new CopixClassesFactory ();
        }
        return $me;
    }
}
?>