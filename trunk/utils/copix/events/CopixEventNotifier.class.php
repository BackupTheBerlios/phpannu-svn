<?php
/**
* @package   copix
* @subpackage events
* @version   $Id: CopixEventNotifier.class.php,v 1.4 2005/04/05 15:06:08 gcroes Exp $
* @author   Croes Gérald, Patrice Ferlet
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
require_once (COPIX_EVENTS_PATH.'CopixEvent.class.php');

class CopixEventNotifier {
   /**
   * the listeners list.
   */
   var $_listeners = array ();

   /**
   * notify a launched event
   * @param CopixEvent $event the launched event
   */
   function notify ($event) {
      require_once (COPIX_EVENTS_PATH.'CopixEventResponse.class.php');
      $me       = & CopixEventNotifier::instance ();
      $response = & new CopixEventResponse ();

      $me->_dispatch ($event, $response);
      return $response;
   }

   /**
   * Gets the instance
   */
   function & instance () {
      static $me = false;
      if ($me === false) {
         $me = new CopixEventNotifier ();
      }
      return $me;
   }

   /**
   * dispatch listeners
   * Only dispatch the events on the listeners that are saying they are listening
   *  to the given event.
   */
   function _dispatch (& $event, & $response) {
      $this->_load ($event);

      //to go throught the object themselves (PHP 4), we get the keys
      if (isset ($this->_listeners[$event->getName ()])){
         foreach (array_keys ($this->_listeners[$event->getName ()]) as $key) {
            $this->_listeners[$event->getName ()][$key]->performEvent ($event, $response);
         }
      }
   }

   /**
   * Load listeners for a given event
   * @param CopixEvent $event the event we wants to load the listeners for.
   */
   function _load (& $event) {
      require_once (COPIX_EVENTS_PATH.'CopixListenerFactory.class.php');       
      $this->_listeners[$event->getName ()] = CopixListenerFactory::createFor ($event->getName ());
   }
}
?>