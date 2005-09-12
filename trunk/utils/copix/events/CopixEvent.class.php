<?php
/**
* @package   copix
* @subpackage events
* @version   $Id: CopixEvent.class.php,v 1.4.4.2 2005/08/17 21:06:10 laurentj Exp $
* @author   Croes Gérald, Patrice Ferlet
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* A single event. Will be created by the event launchers and passed to the
*   EventNotifier.
*/
class CopixEvent {
   /**
   * The name of the event.
   * @var string name
   */
   var $_name = null;

   /**
   * the event parameters
   */
   var $_params = null;

   /**
   * New event.
   */
   function CopixEvent ($name, $params){
      $this->_name   = $name;
      $this->_params = & $params;
   }

   /**
   * gets the ame of the event
   *    will be used internally for optimisations
   */
   function getName (){
      return $this->_name;
   }

   /**
   * gets the given param
   * @param string $name the param name
   */
   function & getParam ($name){
      if (isset ($this->_params[$name])){
         $ret =  & $this->_params[$name];
      }else{
         $ret = null;
      }
      return $ret;
   }
}
?>