<?php
/**
* @package   copix
* @subpackage events
* @version   $Id: CopixListener.class.php,v 1.4 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* The abstract listener class
*/
class CopixListener {
   /**
   * perform a given event
   * @param CopixEvent event the event itself
   */
   function & performEvent (& $event, & $eventResponse) {
      $methodName = 'perform'.$event->getName ();
      return $this->$methodName ($event, $eventResponse);
   }
}
?>