<?php
/**
* @package   copix
* @subpackage project
* @version   $Id: copix.conf.php,v 1.27.2.1 2005/08/04 22:23:06 laurentj Exp $
* @author   Croes G�rald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
*/

/*
 Configuration du framework

 Certains param�tres sont en commentaire. Si vous voulez changer leur valeur par
 d�faut, d�commentez les.

 Tout les param�tres ne sont pas forc�ment enonc� ici. Regardez le
 fichier utils/copix/core/CopixConfig.class.php pour savoir
 tous les param�tres existants.
*/

//define the path to the configuration file we use.
define ('COPIX_CONFIG_PATH', dirname (__FILE__).'/');

//alias of GLOBALS['COPIX']['CONFIG'].
$config = & $GLOBALS['COPIX']['CONFIG'];

/**
 * ======== plugins ========
 */

// plugin � activer pour �tre en mode debug. ATTENTION, l'activation de ce plugin
// peut modifier des param�tres de configuration d�finit ci-apr�s !!
// Aller voir la config du plugin debug pour voir les valeurs effectives
$config->registerPlugin ('debug');     // debuggage, tracage du code


// plugin � activer si les magic_quotes sont activ�s ou si vous n'etes pas s�r de l'activation des magic_quotes
// il est recommand� de d�sactiver les magic_quotes pour un developpement ais� et de meilleures performances.
$config->registerPlugin ('MagicQuotes');

$config->registerPlugin ('copixdb');     //support des Bases de donn�es.
// ou par exemple
//$config->registerPlugin ('copixdb','fichier_conf_alternatif.php');     //support des Bases de donn�es.

$config->registerPlugin ('auth|auth');        //Authentification
$config->registerPlugin ('profile|profile');     // gestion des profiles
//$config->registerPlugin ('Skinner'); //changement de template dynamique
//$config->registerPlugin ('SpeedView');   //configuration de l'envoi de Mails
//$config->registerPlugin ('i18n'); //gestion de l'affichage du menu

/**
 * ======= Param�tres d'autorisation de modules ========
 */

// activation de l'autorisation des modules
$config->checkTrustedModules = false;

// liste des modules autoris�s
//$config->trustedModules = array('exemple'=>true, 'welcome'=>true);

/**
 * EXPERIMENTAL : support des urls significatifs
*/
$config->significant_url_mode = 'none'; // "none" (inactif) ou "prepend" (actif)
//$config->significant_url_prependIIS_path_key = '__COPIX_SIGNIFICANT_URL__';
//$config->stripslashes_prependIIS_path_key    = true;

/**
 * ======== parametres traitement des erreurs =======
 */

/**
 * indique si on active le gestionnaire d'erreur de Copix
 */
$config->errorHandlerOn = true;

/**
 * fichier de configuration du gestionnaire d'erreur de Copix
 */
$config->errorHandlerConfigFile = 'errorhandler.conf.php';

/**
 * ======= Param�trage du cache ========
 */

/**
* Si la fonctionnalit� de cache est autoris�e ?
*/
$config->cacheEnabled = false;

/**
* Cache par d�faut.
*/
//$config->defaultCache = 'Default';

/**
* Liste des autorisation de cache.
*/
$config->cacheTypeEnabled = array ('zones'=>true,
                                   'default'=>true
                                  );

/**
* R�pertoires des diff�rents types de cache..
* terminer le nom par un / ou \
* n'indiquer que le chemin relatif au chemin des caches d�finit dans la constante COPIX_CACHE_PATH
*/
$config->cacheTypeDir = array ('zones'=>'zones/');

/**
* Utilisation d'un syst�me de lock ?
*/
$config->useLockFile = true;


/**
* ======= Internationalisation (I18N) =======
*/
$config->default_language = 'fr';
$config->default_country  = 'FR';


/**
* ======== CopixTpl (for Smarty)  =========
*/
$config->template_dir   = COPIX_PROJECT_PATH.'templates/';
$config->compile_dir    = COPIX_CACHE_PATH.'tpl_compile/';
$config->config_dir     = './configs';

$config->caching        = 0;
$config->cache_dir      = COPIX_CACHE_PATH.'tpl_cache/';

/**
* the main template.
*/

$config->mainTemplate   = '|copix.ptpl';


/**
 * XML FILE COMPILER PARAMETERS
 * using debug plugin, you can override this value in developpement. see debug plugin config
 */
// chemin des caches
$config->compile_resource_dir = COPIX_CACHE_PATH.'resource_compile/';
$config->compile_dao_dir    = COPIX_CACHE_PATH.'dao_compile/';
$config->listeners_compile_path = COPIX_CACHE_PATH;
$config->compile_config_dir    = COPIX_CACHE_PATH.'config_compile/';



/*
Use this when you're in production for high performances
*/

$config->listeners_force_compile = false;
$config->listeners_compile_check = false;

$config->debugging      = false;
$config->compile_check  = false;
$config->force_compile  = false;
$config->use_sub_dirs   = false;

$config->compile_resource_check = false;
$config->compile_resource_forced = false;

$config->compile_dao_check  = false;
$config->compile_dao_forced = false;

$config->compile_config_check  = false;
$config->compile_config_forced = false;

//*/

/*
Use this when you're in developpment and still caring performances
*/
$config->listeners_force_compile = false;
$config->listeners_compile_check = true;

$config->debugging      = true;
$config->compile_check  = true;
$config->force_compile  = false;
$config->use_sub_dirs   = false;

$config->compile_resource_check = true;
$config->compile_resource_forced = false;

$config->compile_dao_check  = true;
$config->compile_dao_forced = false;

$config->compile_config_check  = true;
$config->compile_config_forced = false;

//*/

/*
* Use this when you're in developpment and don't care about performances

$config->listeners_force_compile = true;
$config->listeners_compile_check = true;

$config->debugging      = true;
$config->compile_check  = true;
$config->force_compile  = true;
$config->use_sub_dirs   = false;

$config->compile_resource_check = true;
$config->compile_resource_forced = true;

$config->compile_dao_check  = true;
$config->compile_dao_forced = true;

$config->compile_config_check  = true;
$config->compile_config_forced = true;
//*/
?>