<?php
/**
* @subpackage core
* @package    copix
* @version    $Id: CopixErrorHandler.lib.php,v 1.15.2.2 2005/07/30 09:52:33 laurentj Exp $
* @author    Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

error_reporting (E_ALL);

define ('ERR_MSG_NOTHING'   ,0);
define ('ERR_MSG_ECHO'      ,1);
define ('ERR_MSG_LOG_FILE'  ,2);
define ('ERR_MSG_LOG_MAIL'  ,4);
define ('ERR_MSG_LOG_SYSLOG',8);

define ('ERR_ACT_REDIRECT',  128);
define ('ERR_ACT_EXIT',      256);
define ('ERR_ACT_NOTHING',   0);
define ('ERR_MSG_ECHO_EXIT', ERR_MSG_ECHO | ERR_ACT_EXIT);

class CopixErrorHandlerConfig {
    /* ========================================= parametres traitement des erreurs  */

    /**
    * configuration du handler de message
    * le tableau indique, pour chaque code erreur, les actions à effectuer
    * pour traiter les messages, et ce qu'il faut faire apres traitement du message
    * la valeur indiquant tout ça est une des constantes ERR_*, ou une combinaison
    * de celle-ci (ou logique entre les constantes) definit dans le constructeur
    * @var array
    */
    var $actions = array();

    /**
    * chaine des codes erreurs
    * @var array
    */
    var $codeString = array ();

    /**
    * Action par défaut lorsque le code erreur n'existe pas
    * @var int
    */
    var $defaultAction = ERR_MSG_ECHO_EXIT;

    /**
    * chaine de formatage pour l'enregistrement des messages
    * @var string
    */
    var $messageFormat = "%date%\t[%code%]\t%msg%\t%file%\t%line%\n";

    /**
    * chaine de formatage pour l'affichage des messages
    * @var string
    */
    var $messageFormatDisplay = "<p style=\"margin:0;\"><b>[%code%]</b> <span style=\"color:#FF0000\">%msg%</span> \t%file%\t%line%</p>\n";

    /**
    * fichier où sont stockées les erreurs si l'enregistrement est demandé
    * @var string
    */
    var $logFile = 'cpx_error.log';

    /**
    * url de redirection quand une redirection est demandé et lorsque une erreur survient
    * @var string
    */
    var $redirect = 'errors.php';

    /**
    * addresse email où envoyer un email d'avertissement lorsque une erreur survient
    * @var string
    */
    var $email = 'root@localhost';

    /**
    * entete du mail
    * @var string
    */
    var $emailHeaders = "From: webmaster@yoursite.com\nX-Mailer: Copix\nX-Priority: 1 (Highest)\n";

   /* =========================================  Paramétrage du cache */

    function CopixErrorHandlerConfig (){
        $this->codeString = array(
        E_ERROR         => 'ERREUR',
        E_WARNING       => 'WARNING',
        E_NOTICE        => 'NOTICE',
        E_USER_ERROR    => 'CPX_ERREUR',
        E_USER_WARNING  => 'CPX_WARNING',
        E_USER_NOTICE   => 'CPX_NOTICE'
        );

        $this->actions = array(
        E_ERROR         => ERR_MSG_ECHO_EXIT,
        E_WARNING       => ERR_MSG_ECHO,
        E_NOTICE        => ERR_MSG_NOTHING,
        E_USER_ERROR    => ERR_MSG_ECHO_EXIT,
        E_USER_WARNING  => ERR_MSG_ECHO,
        E_USER_NOTICE   => ERR_MSG_NOTHING
        );
    }
}

/**
* Gestionnaire d'erreur du framework
* Remplace le gestionnaire par defaut du moteur PHP
* @param   integer     $errno      code erreur
* @param   string      $errmsg     message d'erreur
* @param   string      $filename   nom du fichier où s'est produit l'erreur
* @param   integer     $linenum    numero de ligne
* @param   array       $vars       variables de contexte
* @todo format de message different selon si on est en debug ou non, ou selon si on affiche ou log (inclure plus d'info dans les logs)
* @todo inclure l'url dans les logs
*/
function CopixErrorHandler($errno, $errmsg, $filename, $linenum, $vars){
    if (error_reporting() == 0)
        return;

    $configData = & $GLOBALS['COPIX']['CONFIG']->errorHandler;

    $conf = & $configData->actions;

    if (isset ($conf[$errno])){
        $action = $conf[$errno];
    }else{
        $action = $configData->defaultAction;
    }

    // formatage du message
    $messageLog = strtr($configData->messageFormat, array(
    '%date%' => date("Y-m-d H:i:s"),
    '%code%' => $configData->codeString[$errno],
    '%msg%'  => $errmsg,
    '%file%' => $filename,
    '%line%' => $linenum
    ));
    $messageToDisplay=strtr($configData->messageFormatDisplay, array(
    '%date%' => date("Y-m-d H:i:s"),
    '%code%' => $configData->codeString[$errno],
    '%msg%'  => htmlentities($errmsg),
    '%file%' => $filename,
    '%line%' => $linenum
    ));

    // traitement du message
    if($action & ERR_MSG_ECHO){
        if($action & ERR_ACT_EXIT){
            header("HTTP/1.1 500 Internal copix error");
        }

        echo $messageToDisplay;
         //On n'utilise pas flush, qui a pour effet de terminer les en têtes. Si on termine les en têtes alors
         //que nous sommes en train de travailler sur un template intermédiaire (d'une zone par exemple),
         // alors le Coordinateur n'aura pas l'opportunité de pouvoir modifier l'en tête pour spécifier le
         //content-type. Le seul interêt de flush ici est d'afficher ASAP un message d'erreur non bloquante(ou
         //le processus peut continuer), alors qu'un traitement long reste en suspends (affichage de milliers
         //d'enregistrement d'une table par exemple). Si l'on souhaitait utiliser ce système dans Copix, il nous
         //faudrait passer par un ob_flush (), car tout l'affichage se fait en buffer (ob_start ()).
         //Pourtant, mettre ob_flush a pour effet (logique) d'afficher le contenu du template actuellement dans le
         //buffer en tout début de template ($config->main_template) et de produire un affichage complètement inexploitable
         //(surtout dans le cas d'erreurs minimes). Ces contraintes ne sont toutefois que des contraintes de
         //développement. Enlever le flush à pour effet de n'afficher les messages d'erreurs qu'une fois le
         // traitement final effectué (- templates calculés - page abordée - autre ). Peut être que le seul cas
         // ou cette manipulation est interressante est lorsque le processus devient non maitrisable, et que
         //l'on souhaite disposer de l'affichage des messages d'erreur alors que la page n'arrivera jamais à
         // sa fin (et donc les buffers ne seront jamais affichés.) => Il sera interressant alors de lire les
         // logs (réaction normale)
	     //flush ()
    }
    if($action & ERR_MSG_LOG_FILE){
        error_log($messageLog,3, COPIX_LOG_PATH.$configData->logFile);
    }
    if($action & ERR_MSG_LOG_MAIL){
        error_log($messageLog,1, $configData->email, $configData->emailHeaders);
    }
    if($action & ERR_MSG_LOG_SYSLOG){
        error_log($messageLog,0);
    }

    // action
    if($action & ERR_ACT_REDIRECT){
        header('location: '.$configData->redirect);
        exit;
    }

    if($action & ERR_ACT_EXIT){
        exit;
    }
}
?>