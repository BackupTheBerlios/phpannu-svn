<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixPlugin.class.php,v 1.8 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes G�rald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* classe de base pour les plugins
* @package   copix
* @subpackage core
*/
class CopixPlugin{
    /**
    * objet de configuration dont la classe � pour nom  nom.plugin.conf.php (nommage par d�faut)
    * @var object
    */
    var $config;

    /**
    * ref�rence sur le coordinateur du framework
    * @var  CopixCoordination
    */
    var $coordination;
    /**
    * constructeur
    * @param   object   $config      objet de configuration du plugin
    */
    function CopixPlugin(& $config){
        $this->coordination = & $GLOBALS['COPIX']['COORD'];
        $this->config = & $config;
    }

    /**
    * surchargez cette methode si vous avez des traitements � faire, des classes � declarer avant
    * la recuperation de la session
    * @abstract
    */
    function beforeSessionStart(){
    }

    /**
    * traitements � faire avant execution de l'action demand�e
    * @param   CopixAction   $action   le descripteur de l'action demand�e.
    * @abstract
    */
    function beforeProcess(& $action){
    }

    /**
    * traitements � faire apres execution de l'action
    * @abstract
    * @param CopixActionReturn      $actionreturn
    */
    function afterProcess($actionreturn){
    }

    /**
    * Just before displaying content, giving the programm the opportunity to change it.
    */
    function beforeDisplay (& $display){
    }
}
?>