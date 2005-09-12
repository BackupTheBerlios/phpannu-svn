<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: speedview.plugin.conf.php,v 1.6 2005/02/09 08:31:18 gcroes Exp $
* @author   Croes Gérald, Laurent Jouanneau
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class PluginConfigSpeedView {
   /**
    * indique ce qui declenche le comptage
    * 'action' => doit y avoir un paramètre 'showTimeCounter' à true dans les paramètres de l'action
    * 'url' => doit y avoir un paramètre  showTimeCounter=1 dans l'url
    * 'actionreturn' => affiche pour toutes les actions qui retournent  COPIX_AR_(display|display_in|static)
    */

   var $trigger='actionreturn';

    /**
     * indique là où on affiche le temps d'execution
     * 'display' => sur la page résultat (attention! peut provoquer une page invalide !
     *            peut aussi provoquer des erreurs si c'est utilisé lorsqu'il s'agit d'une
     *            redirection ou génération autre que html !)
     * 'comment' => sur la page résultat mais en tant que commentaire
     * 'debug' => affiche via le traceur (necessite l'activation du plugin debug)
     */
    var $target='display';
}
?>
