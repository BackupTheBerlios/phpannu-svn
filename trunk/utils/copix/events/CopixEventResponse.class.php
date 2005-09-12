<?php
/**
* @package   copix
* @subpackage events
* @version   $Id: CopixEventResponse.class.php,v 1.4 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Patrice Ferlet
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* base class for responses.
*/
class CopixEventResponse {
   /**
   * @var array of array
   */
   var $_responses = array ();

   /**
   * add a response in the list
   * @param array response a single response
   */
   function add ($response) {
      $this->_responses[] = & $response;
   }

   /**
   * look in all the responses if we have a parameter having value as its answer
   * eg, we want to know if we have failed = true, we do
   * @param string $responseName the param we're looking for
   * @param mixed $value the value we're looking for
   * @param ref $response the response that have this value
   * @return boolean wether or not we have founded the response value
   */
   function inResponse ($responseName, $value, & $response){
      $founded  = false;
      $response = array ();

      foreach ($this->_responses as $key=>$listenerResponse){
         if (isset ($listenerResponse[$responseName]) && $listenerResponse[$responseName] == $value){
            $founded = true;
            $response[] = & $this->_responses[$key];
         }
      }

      return $founded;
   }

   /**
   * gets all the responses
   * @return array of associative array
   */
   function getResponse () {
      return $this->_responses;
   }
}
?>