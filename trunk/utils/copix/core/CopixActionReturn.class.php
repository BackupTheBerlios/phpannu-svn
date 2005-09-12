<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixActionReturn.class.php,v 1.8.4.2 2005/08/01 22:17:54 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Contient les infos de retour des actions d'un coordinateur de page
*
* Cet objet permet à CopixCoordination de savoir quoi faire apres une action.
* Il contient un code retour, et des données associées à ce code retour.
* Dans les traitements par défaut, ce code est un entier.
*
* <code>
*  $tpl= & new CopixTpl();
*  //...
*  return new CopixActionReturn ( COPIX_AR_DISPLAY, $tpl);
* </code>
*
* @package   copix
* @subpackage core
* @see CopixPage
* @see CopixCoordination
* @see CopixCoordination::_processResult
*/
class CopixActionReturn{
    /**
    * code de retour. vaut une des constantes COPIX_AR_*
    * @var int
    */
    var $code = null;

    /**
    * paramètre pour le traitement du retour. sa nature dépend du code retour
    * @var mixed
    */
    var $data = null;

    /**
    * paramètre supplémentaire pour le traitement du retour. sa nature et sa présence dépend du code retour
    * @var mixed
    */
    var $more = null;

    /**
    * Contruction et initialisation du descripteur.
    * @param int    $pCode      Code (COPIX_AR_DISPLAY, COPIX_AR_REDIRECT, COPIX_AR_DOWNLOAD, ...)
    * @param mixed  $pData      Parameters (template / url / ...)
    * @param mixed  $pMore      Extra parameters
    */
    function CopixActionReturn ($pCode, $pData, $pMore=null){
        $this->data = & $pData;
        $this->more = & $pMore;
        $this->code = & $pCode;
    }
}
?>