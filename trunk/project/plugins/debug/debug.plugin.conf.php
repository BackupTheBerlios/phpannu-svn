<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: debug.plugin.conf.php,v 1.8.2.1 2005/07/30 09:52:33 laurentj Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class PluginConfigDebug {

    /**
    * niveau de débugage souhaité:
    * 0 : Erreures fatales uniquement
    * 1 : Erreures fatales et avertissement
    * 2 : Erreures fatales, avertissements, information
    * 3 : Tout
        * @var  integer $DebugLevel
    */
    var $level = 3;

        /**
         * Indique si on souhaite qu'il y ait un filtrage par IP
         * et donc que le debuggage se fasse uniqement pour certain poste ou non.
         * @var boolean $DebugIpFilter
         */
    var $ipFilter = false;

        /**
         * Indique la liste des IP sur lesquelles le debuggage est actif
         * @var array   $DebugIpArray
         */
    var $ipArray = array ();

        /**
         * Indique si on souhaite logguer les messages de debuggage
         * @var boolean $DebugUseLogFile
         */
    var $useLogFile = true;


        /**
        * Nom du fichier de débuggage.
        * Si on veut un fichier par IP, ajouter le tag %ip% dans le nom.
        * @var string   $DebugLogFile
    */
    var $logFile = 'debug_file_%ip%.log';

    /**
         * format du message de debuggage enregistré dans les fichiers de log
         * tag à inclure dans le format :
         *  %date%  pour y mettre la date
         *  %ip%    ip du poste
         *  %from%  origine du message (indiqué lors du addInfo)
         *  %msg%   message
         * @var string  $DebugMessageFormat
         */
   var $messageFormat = "%date%\t%ip%\t%from%\t%msg%\n";

        /**
         * indique si on affiche ou pas les messages de debuggage
         * @var boolean $DebugDisplay
         */
   var $toDisplay = false;

   /**
      * indique si il faut afficher le contenu en detail des infos de type tableaux et objets
   * @var boolean $dumpArrayObject
   */
   var $dumpArrayObject = true;

        /**
         * format du message de debuggage pour affichage
         * tag à inclure dans le format :
         *  %ip%    ip du poste
         *  %from%  origine du message (indiqué lors du addInfo)
         *  %msg%   message
         * @var string  $DebugMessageFormatDisplay
         */
    var $messageFormatDisplay = "<p style=\"margin:0;\"><b>[ DEBUG ]</b> (%ip%) %from% : %msg%</p>\n";


    function PluginConfigDebug(){
         $config = & $GLOBALS['COPIX']['CONFIG'];

         $config->listeners_force_compile = false;
         $config->listeners_compile_check = true;

         $config->debugging      = true;
         $config->compile_check  = true;
         $config->force_compile  = false;
         $config->use_sub_dirs   = true;

         $config->compile_resource = true;//compilation des ressources ?
         $config->compile_resource_check = true;

         $config->compile_dao_check  = true;
         $config->compile_dao_forced = false;

         $config->compile_config_check  = true;
         $config->compile_config_forced = false;

         // on peut en profiter pour changer la conf d'un plugin, comme ici pour copixdb
         //$config->registerPlugin ('copixdb','copixdb_debug.plugin.conf.php');

          if($config->errorHandlerOn){

            $config->errorHandler->actions = array(
               E_ERROR         => ERR_MSG_ECHO_EXIT,
               E_WARNING       => ERR_MSG_ECHO,
               E_NOTICE        => ERR_MSG_ECHO,
               E_USER_ERROR    => ERR_MSG_ECHO_EXIT,
               E_USER_WARNING => ERR_MSG_ECHO,
               E_USER_NOTICE    => ERR_MSG_ECHO
            );

            //$config->errorHandler->messageFormat = "%date%\t[%code%]\t%msg%\t%file%\t%line%\n";
            //$config->errorHandler->messageFormatDisplay = "<p style=\"margin:0;\"><b>[%code%]</b> <span style=\"color:#FF0000\">%msg%</span> \t%file%\t%line%</p>\n";
            //$config->errorHandler->email='root@localhost';
            //$config->errorHandler->emailHeaders="From: webmaster@yoursite.com\nX-Mailer: Copix\nX-Priority: 1 (Highest)\n";
          }
    }
}
?>
