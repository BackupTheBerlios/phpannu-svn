<?php
/**
* @package  copix
* @subpackage admin
* @version  $Id: install.zone.php,v 1.1 2005/04/11 21:32:24 laurentj Exp $
* @author   Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * zone installfront, formulaire pour renseigner la connexion à la base de donnée.
 */
class ZoneInstall extends CopixZone {
    function _createContent (&$toReturn) {
        $tpl = & new CopixTpl ();

        // On récupère les noms des drivers de base de donnée installés dans copix
        $dbDriverPath = COPIX_PATH . 'db/drivers/';
        if ($handle = opendir ($dbDriverPath)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && !is_file($file) && strtoupper ($file) != "CVS") {
                    $tabDBType[]=$file;
                }
            }
            closedir($handle);
        }

        $services = & CopixClassesFactory::create ('InstallService');

        $tpl->assign ('arType',$tabDBType);
        $tpl->assign ('databaseNotOk', $this->params['databaseNotOk']);
        $tpl->assign ('configurationFileWritable', $services->checkConfigurationFileWritable ());
        $tpl->assign ('configurationFilePath', XML_COPIXDB_PROFIL);
        $tpl->assign ('currentParameters', $services->getCurrentParameters ());
        $tpl->assign('copixdbok',($GLOBALS['COPIX']['COORD']->getPlugin ('copixdb') !== null));

        // retour de la fonction :
        $toReturn = $tpl->fetch ('install.edit.tpl');
        return true;
    }
}
?>