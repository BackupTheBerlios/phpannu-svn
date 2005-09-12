<?php
/**
* @package   copix
* @subpackage project
* @version   $Id: errorhandler.conf.php,v 1.3 2005/02/09 08:27:42 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/*
Voir la config du plugin debug
*/


/**
 * configuration du handler de message
 * le tableau indique, pour chaque code erreur, les actions à effectuer
 * pour traiter les messages, et ce qu'il faut faire apres traitement du message
 * la valeur indiquant tout ça est une des constantes ERR_*, ou une combinaison
 * de celle-ci (ou logique entre les constantes)
 */
$config->errorHandler->actions = array(
   E_ERROR         => ERR_MSG_ECHO_EXIT,
   E_WARNING       => ERR_MSG_ECHO,
   E_NOTICE        => ERR_MSG_NOTHING,
   E_USER_ERROR    => ERR_MSG_ECHO_EXIT,
   E_USER_WARNING => ERR_MSG_ECHO,
   E_USER_NOTICE    => ERR_MSG_NOTHING
);

/**
* Action par défaut lorsque le code erreur n'existe pas
*/
//$config->errorHandler->defaultAction = ERR_MSG_ECHO_EXIT;

/**
* chaine de formatage pour l'enregistrement des messages et leur affichage
*/
//$config->errorHandler->messageFormat = "%date%\t[%code%]\t%msg%\t%file%\t%line%\n";
//$config->errorHandler->messageFormatDisplay = "<p style=\"margin:0;\"><b>[%code%]</b> <span style=\"color:#FF0000\">%msg%</span> \t%file%\t%line%</p>\n";

/**
* fichier où sont stockées les erreurs si l'enregistrement est demandé
*/
//$config->errorHandler->logFile='cpx_error.log';

/**
* url de redirection quand une redirection est demandé lorsque une erreur survient
*/
//$config->errorHandler->redirect='errors.php';

/**
* addresse email où envoyer un email d'avertissement lorsque une erreur survient
*/
//$config->errorHandler->email='root@localhost';

/**
* entete du mail
*/
//$config->errorHandler->emailHeaders="From: webmaster@yoursite.com\nX-Mailer: Copix\nX-Priority: 1 (Highest)\n";

/**
 * chaine des codes erreurs
 * @var array
 */
/*
$config->errorHandler->errorCodeString = array(
        E_ERROR         => 'ERREUR',
        E_WARNING       => 'WARNING',
        E_NOTICE        => 'NOTICE',
        E_USER_ERROR    => 'CPX_ERREUR',
        E_USER_WARNING  => 'CPX_WARNING',
        E_USER_NOTICE   => 'CPX_NOTICE'
);
*/

?>