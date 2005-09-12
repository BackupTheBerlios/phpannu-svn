<?php
/**
* @package   copix
* @subpackage project
* @version   $Id: install.php,v 1.1.2.1 2005/05/18 21:15:32 laurentj Exp $
* @author   Bertrand Yan, Laurent Jouanneau
* @copyright 2001-2005 Aston S.A.
* @link      http://copix.aston.fr
* @licence  http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
*/

   $messages=array();
   $files = array(
       COPIX_LOG_PATH,
       COPIX_CACHE_PATH,
       COPIX_CACHE_PATH.'config_compile',
       COPIX_CACHE_PATH.'dao_compile',
       COPIX_CACHE_PATH.'default',
       COPIX_CACHE_PATH.'resource_compile',
       COPIX_CACHE_PATH.'tpl_cache',
       COPIX_CACHE_PATH.'tpl_compile',
       COPIX_CACHE_PATH.'zones'
       );

   $badfiles= array();
   $canInstall = true;

   foreach($files as $file){
      if(!is_writable($file)){
         $canInstall = false;
         $badfiles[]= $file;
      }
   }

   if( (!function_exists('version_compare')) || version_compare(PHP_VERSION, '4.2.0') == -1 ){
       $canInstall = false;
       $messages[]='Votre version de PHP est trop vieille. Installez PHP 4.2 ou 4.3.';
   }else if(version_compare('5.0.0',PHP_VERSION) < 1){
       $canInstall = false;
       $messages[]='Cette version de Copix n\'est pas prévue pour PHP 5 et supérieur.';
   }

   if (!$canInstall) {

       ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
   <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type" />
   <title>Installation de Copix</title>
   <link rel="stylesheet" href="styles/styles_copix.css" type="text/css"/>
</head>
<body>
     <div id="all_content">
        <div id="COPIX_TITLE_PAGE"><h1>Installation de Copix</h1></div>
        <div id="content">
          <img id="logo_copix" alt="Copix Framework" src="img/copix/logo-framework.gif" />
          <div id="main_layout">
             <div id="COPIX_MAIN">
                 <h2>Erreurs sur l'installation !!!</h2>
                 <ul style="color:red;">
                <?php
                if(count($badfiles)){
                    echo '<li>Mauvaises permissions en écriture sur les fichiers suivants<ul>';
                    foreach( $badfiles as $file){
                        echo '<li>',$file,'</li>';
                    }
                    echo '</ul></li>';
                }
                foreach($messages as $msg){
                    echo '<li>',$msg,'</li>';
                }
                ?>
                </ul>
             </div>
             <br style="clear:both;" />
          </div>

         </div>
      </div>
</body>
</html>
       <?php
      exit;
   }

    //creates the main object, giving it the configuration file to use.
    //will register itself to $GLOBALS['COPIX']['COORD']
    new ProjectCoordination($copix_config_file);

    define ('COPIX_INSTALL', 1);

    // si il y a dejà un module=admin, c'est qu'on est en cours d'installation
    if(!isset($GLOBALS['COPIX']['COORD']->vars['module'])
        || $GLOBALS['COPIX']['COORD']->vars['module'] !='install'){
            $GLOBALS['COPIX']['COORD']->vars['module']='install';
            $GLOBALS['COPIX']['COORD']->vars['desc']='install';
            $GLOBALS['COPIX']['COORD']->vars['action']='install';
    }


    //now we go, Copix is launched.
    $GLOBALS['COPIX']['COORD']->process();

?>