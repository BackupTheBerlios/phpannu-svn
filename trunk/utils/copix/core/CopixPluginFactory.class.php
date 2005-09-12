<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixPluginFactory.class.php,v 1.11.4.2 2005/08/17 20:06:53 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* fabrique de plugin
* permet au coordinateur de gérer les plugins
* @package   copix
* @subpackage core
*/
class CopixPluginFactory {
    /**
    * instanciation d'un objet plugin.
    * instancie également l'objet de configuration associé
    * @param   string   $name   nom du plugin
    * @param string $conf   nom d'un fichier de configuration alternatif. si chaine vide = celui par défaut
    * @return   CopixPlugin      le plugin instancié
    */
    function & create ($name, $conf=null){
        require_once (COPIX_CORE_PATH .'CopixPlugin.class.php');
        $fic  = & new CopixModuleFileSelector($name);
        $nom  = strtolower($fic->fileName);

        $path = $fic->getPath(COPIX_PLUGINS_DIR) .$nom.'/';
        $path_plugin = $path .$nom.'.plugin.php';
        if(is_null ($conf)){
           $path_config = $path .$nom.'.plugin.conf.php';
        }else{
           $path_config = $path . $conf;
        }

        if (is_file ($path_plugin) && is_file ($path_config)){
            require_once ($path_config);
            require_once ($path_plugin);

            $classname = 'PluginConfig'.$fic->fileName;//nom de la classe de configuration.
            $config    = & new $classname ();//en deux étapes, impossible de mettre la ligne dans les paramètres du constructeur.

            $name     = 'Plugin'.$fic->fileName;
            $toReturn = & new $name ($config);//nouvel objet plugin, on lui passe en paramètre son objet de configuration.
        } else {
            trigger_error(CopixI18N::get ('copix:copix.error.unfounded.plugin',$name), E_USER_ERROR);
            $toReturn = null;
        }
        return $toReturn;
    }

    /**
    * retourne la liste des plugins trouvés dans le répertoire des plugins.
    * @return array   la liste des plugins
    */
    function getPluginList (){
        $toReturn = array ();
        $rep=opendir(COPIX_PLUGINS_PATH);//open the plugin path
        if (!$rep){
            return null;
        }
        //throw the files.
        while ($file = readdir($rep)) {
            if($file != '..' && $file !='.' && $file !=''){
                if (is_file(COPIX_PLUGINS_PATH.$file) && !is_dir (COPIX_PLUGINS_PATH.$file)){
                    $toReturn[] = $file;
                }
            }
        }
        //close&go
        closedir($rep);
        clearstatcache();
        return $toReturn;
    }
}
?>