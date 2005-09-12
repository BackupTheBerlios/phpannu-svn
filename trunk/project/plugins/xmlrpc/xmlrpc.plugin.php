<?php

/**
* @package   copix
* @subpackage plugins
* @version   $Id: xmlrpc.plugin.php,v 1.3 2005/02/09 08:31:18 gcroes Exp $
* @author   Gildas Givaja <giviz@pyronux.net>
* @contributor Laurent Jouanneau
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/*
 ========================== PLUGIN EXPERIMENTAL ================================
 n'utiliser le support xmlrpc qu'� des fins de test !
 Les sp�cifications du support xmlrpc dans copix sont succeptibles d'�tre modifi�e
 dans la prochaine version !!
*/



require_once(COPIX_UTILS_PATH.'CopixXmlRpc.class.php');

class PluginXmlRpc extends CopixPlugin {
    function beforeSessionStart() {
        // D�tection Requete XmlRpc
        if($this->coordination->vars['action'] == 'xmlrpc') {
            // R�cup�ration de la requete
            global $HTTP_RAW_POST_DATA;
            if(isset($HTTP_RAW_POST_DATA)){
                $requestXml = $HTTP_RAW_POST_DATA;
            }else{
                $requestXml = file('php://input');
                $requestXml = implode("\n",$requestXml);
            }

            // D�codage de la requete
            list($nom,$vars) = CopixXmlRpc::decodeRequest($requestXml);
            list($module, $action) = explode('.',$nom);
            // D�finition de l'action a executer et des param�tres
            $this->coordination->vars['module'] = $module;
            $this->coordination->vars['action'] = $action;
            $this->coordination->vars['params'] = $vars;
            $this->coordination->vars['desc']   = 'xmlrpc';
        }
    }
}
?>