<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: speedview.plugin.conf.php,v 1.6 2005/02/09 08:31:18 gcroes Exp $
* @author   Croes G�rald, Laurent Jouanneau
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class PluginConfigSpeedView {
   /**
    * indique ce qui declenche le comptage
    * 'action' => doit y avoir un param�tre 'showTimeCounter' � true dans les param�tres de l'action
    * 'url' => doit y avoir un param�tre  showTimeCounter=1 dans l'url
    * 'actionreturn' => affiche pour toutes les actions qui retournent  COPIX_AR_(display|display_in|static)
    */

   var $trigger='actionreturn';

    /**
     * indique l� o� on affiche le temps d'execution
     * 'display' => sur la page r�sultat (attention! peut provoquer une page invalide !
     *            peut aussi provoquer des erreurs si c'est utilis� lorsqu'il s'agit d'une
     *            redirection ou g�n�ration autre que html !)
     * 'comment' => sur la page r�sultat mais en tant que commentaire
     * 'debug' => affiche via le traceur (necessite l'activation du plugin debug)
     */
    var $target='display';
}
?>
