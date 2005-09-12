<?php
/**
* @package  copix
* @subpackage admin
* @version  $Id: admin.zone.php,v 1.2.2.1 2005/05/10 23:33:49 laurentj Exp $
* @author   Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * Admin zone, to display administration management
 */

class ZoneAdmin extends CopixZone {
    function _createContent (&$toReturn) {
        $tpl = & new CopixTpl ();
      //page de présentation
      $homePageUrl = CopixConfig::get ('|homePage');

      // special CopixCms : à retirer plus tard
      $foundPage = preg_match('/.*module=cms&action=get&id=(\d*).*/',$homePageUrl,$matches);
      if ($foundPage) {
          require_once (COPIX_MODULE_PATH.'cms/'.COPIX_CLASSES_DIR.'cmspage.services.class.php');
          Copixcontext::push('cms');
          $homePage = ServicesCMSPage::getOnline($matches[1]);
          Copixcontext::pop();
          if (isset($homePage->title_cmsp)) {
                $homePageUrl = $homePage->title_cmsp;
          }
      } // special CopixCms


      $tpl->assign ('homePage',$homePageUrl);

      $toReturn = $tpl->fetch ('admin.tpl');
      return true;
    }
}
?>
