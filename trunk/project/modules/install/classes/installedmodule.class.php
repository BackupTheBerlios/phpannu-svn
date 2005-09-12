<?php
/**
* @package  copix
* @subpackage admin
* @version  $Id: installedmodule.class.php,v 1.1 2005/04/11 21:32:24 laurentj Exp $
* @author   Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class installedModule {

var $arModules = array ();

function installedModule () {

$this->arModules[] = 'auth';
$this->arModules[] = 'parameters';
$this->arModules[] = 'profile';

}

}

?>
