<?php
/**
* @package	copix
* @subpackage genericTools
* @version	$Id: copixlistener.zone.php,v 1.3 2005/02/09 08:27:43 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* A zone that processes zones that are registered as listeners
*/
class ZoneCopixListener extends CopixZone {
   /**
   * @param $this->params['event'] string the event name
   */
   function _createContent (& $toReturn){
      //new tpl
      $tpl = & new CopixTpl ();

      //Search only for the given event.
      $dao  = & CopixDAOFactory::create ('CopixListener');
      $sp   = CopixDAOFactory::createSearchParams ('event', '=', $this->params['event']);
      $sp   = CopixDAOFactory::createSearchParams ('kind',  '=', 'zone');

      $list = $dao->findBy ($sp);

      //go through the founded listeners, asks for their processing
      foreach ($list as $toProcess){
         $toReturn .= CopixZone::process ($toProcess, $this->params);
      }
      return true;
   }
}
?>
