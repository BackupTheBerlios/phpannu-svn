<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: peardb.plugin.conf.php,v 1.6 2005/02/09 08:29:09 gcroes Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
include_once('DB.php');

class PluginConfigPearDB {
    var $dsn = 'mysql://login:pwd@localhost/base';
    var $fetchMode = DB_FETCHMODE_ASSOC;
    var $persistantCnx = true;
}
?>
