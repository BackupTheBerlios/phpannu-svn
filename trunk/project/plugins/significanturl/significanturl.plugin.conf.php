<?php
/**
* @package    copix
* @subpackage plugins
* @version    $Id: significanturl.plugin.conf.php,v 1.8 2005/02/09 08:31:18 gcroes Exp $
* @author    Laurent Jouanneau, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * ===========================================
 * PLUGIN EXPERIMENTAL !!
 * À n'utiliser que pour des tests. le fonctionnement des urls significatifs est succeptible
 * d'être modifié dans les futures versions !
 * ===========================================
 */



class PluginConfigSignificantUrl {

    /**
     * indique si il faut décoder les URLs. si vous avez utilisé le module rewrite
     * au niveau d'apache, et que les rêgles du rewrite renvoie des urls copix traditionnelles
     * alors mettez ici false.
     * si vous utilisez le path_info : mettez true
     * sinon désactivez le plugin.
     */
    var $enableDecodeUrl = true;

    /**
     * indique si le multiview dans apache est activé
     * en clair, si vous pouvez faire http://monsite.com/index/ce/que/je/veux au lieu de
     * http://monsite.com/index.php/ce/que/je/veux (donc sans indiquer l'extension php)
     * mettez alors true. C'est juste une question d'"esthetisme".
     * Mettre false ne causera pas de disfonctionnement et permettra aux urls significatfs
     * de fonctionner, multiview ou non.
     */
    var $multiviewOn=false;

    var $compile_check  = true;
    var $compile_forced = true;

    var $compile_dir    = null;

    function PluginConfigSignificantUrl (){
        $this->compile_dir = COPIX_CACHE_PATH.'url_compile/';
    }
}
?>
