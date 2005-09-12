<?php
/**
* @package    copix
* @subpackage plugins
* @version    $Id: significanturl.plugin.php,v 1.9.4.1 2005/08/19 08:40:17 laurentj Exp $
* @author    Laurent Jouanneau, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * ===========================================
 * PLUGIN EXPERIMENTAL ET OBSOLETE !!
 * À ne plus utiliser sauf pour des tests. N'est pas lié au fonctionnement des urls significatifs
 * actif actuellement dans CopixUrl.
 * le fonctionnement des urls significatifs sera completement revu dans Copix 2.3
 * ===========================================
 */

class PluginSignificantUrl extends CopixPlugin {

    /**
    * liste des données pour la création des urls (voir structure plus loin)
    */
    var $dataCreateUrl = array();

    /**
    * liste des scriptnames par defaut de chaque module
    */
    //var $scriptnameList = array ();

    /**
    * liste des données pour l'analyse des urls
    */
    var $dataDecodeUrlSuffixe = array();
    var $dataDecodeUrlOther = array();


    /**
    * Says if we have to compile the file
    */
    function _mustCompile (){
        if ($this->config->compile_forced){
            return true;
        }

        //no compiled file ?
        if (!is_readable ($config->compile_dir.'significanturl.php')){
            return true;
        }

        if ($this->config->compile_check){
            //compiled file, checking if there is an updated module.xml file since the compilation
            $compilationTime = filemtime ($config->compile_dir.'significanturl.php');
            $modulesList = CopixModule::getList();
            foreach ($modulesList as $dir){
                $xmlFilename = COPIX_MODULE_PATH.$dir.'/module.xml';
                if (is_readable ($xmlFilename)){
                    if (filemtime ($xmlFilename) > $compilationTime){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    function PluginSignificantUrl(&$config){
        //parent::CopixPlugin($config);
        $this->coordination = & $GLOBALS['COPIX']['COORD'];
        $this->config = & $config;

        // charger ici les données sur les urls
        //    soit à partir des sections <significanturl> des fichiers modules.xml -> compiler et stocker dans un fichier PHP compilé
        //    soit à partir du fichier PHP Compilé (voir sa structure dans CopixUrlCompiler.class.php)
        if ($this->_mustCompile()){
            require_once (COPIX_UTILS_PATH.'CopixUrlCompiler.class.php');
            $compiler = & new CopixUrlCompiler();
            $compiler->compile($config);
        }

        include ($config->compile_dir.'significanturl.php');

        $this->dataCreateUrl  = $_compile_createUrl;
        $this->dataDecodeUrlSuffixe  = $_compile_decodeUrl_suffixe;
        $this->dataDecodeUrlOther  = $_compile_decodeUrl_other;

        //$this->scriptnameList = $_compiled_scriptnameList;

        // aprés chargement des données, il faut analyser l'url
        // on le fait dans le constructeur car il faut savoir quel est le module courant
        // avant d'executer les beforeSessionStart() des plugins
        if ($config->enableDecodeUrl){

            $infodecode = null;
            if(isset($_SERVER ['PATH_INFO']) && preg_match('/(.*)\.(\w+)$/', $_SERVER ['PATH_INFO'],$matches)){
                // on a un suffixe
                if(isset($this->dataDecodeUrlSuffixe[$matches[2]])){
                    // le suffixe correspond à un schema connu
                    $infodecode = $this->dataDecodeUrlSuffixe[$matches[2]];
                    if(preg_match ($infodecode[3], $matches[1], $matches2))
                    $infodecode[6]= $matches2;
                }else{
                    // suffixe inconnu : on recherche une correspondance avec les schema d'URL sans suffixe
                    $infodecode = $this->_searchUrl();
                }
            }else{
                // pas de suffixe
                $infodecode = $this->_searchUrl();
            }

            if($infodecode){
                // on a les infos de décodage, on peut recupèrer les données indiquées dans l'URL
                $vars = & $GLOBALS['COPIX']['COORD']->vars;
                $vars['module']=$infodecode[0];
                $vars['desc']=$infodecode[1];
                $vars['action']=$infodecode[2];

                if (count ($infodecode[4])){
                    $vars = array_merge ($vars, $infodecode[4]); // on fusionne les parametres statiques
                }
                if(count($infodecode[6])){
                    foreach($infodecode[5] as $k=>$name){
                        if(isset($infodecode[6][$k+1]))
                        $vars[$name] = $infodecode[6][$k+1];
                    }
                }
                $GLOBALS['COPIX']['COORD']->vars=$vars;
            }
        }
    }

    /**
    * @param CopixUrl    $urlorigin    url à transformer
    */
    function transformUrl( $urlorigin){
        $url = $urlorigin; // Attention, il faut une copie ici, pas une référence ! (PHP5 -> clone !)
        /*
        a) recupere module|desc|action -> obtient les infos pour la creation de l'url
        b) récupère un à un les parametres indiqués dans params à partir de CopixUrl
        c) remplace la valeur récupérée dans le result et supprime le paramètre de l'url
        d) remplace scriptname de CopixUrl par le resultat
        */
        $module = $url->get ('module');
        if($module === null)
        $module = CopixContext::get();

        $desc= $url->get('desc');
        if($desc === null)
        $desc = COPIX_DEFAULT_VALUE_DESC;

        $action = $url->get('action');
        if($action === null)
        $action = COPIX_DEFAULT_VALUE_ACTION;


        $id = $module.'|'.$desc.'|'.$action;
        //if ($module !== null && isset ($this->scriptnameList[$module])){
        //    $url->scriptName = $this->scriptnameList[$module];
        //}

        /*$_compile_createUrl = array('test1|default|default'=>
        array('test',true, array('annee','mois','id'), '/%1/%2/%3',''));
        */
        if (isset ($this->dataCreateUrl [$id])){
            $urlinfo = &$this->dataCreateUrl[$id];
            if($urlinfo[1]){
                $dturl = & $urlinfo[2];
                $result = $urlinfo[3];
                foreach ($dturl as $k=>$param){
                    $result=str_replace('%'.($k+1), $url->get($param), $result);
                    $url->del($param);
                }
                $url->pathInfo = $result;
            }else{
                $class= $urlinfo[2];
                $method= $urlinfo[3];
                $file= CopixCoordination::extractFilePath($module.'|'.$class, 'classes', '.url.php');
                if($file){
                    include_once($file);
                    $obj = new $class();
                    $url = $obj->$method($url);
                }else{

                }
            }
            // rajouter le suffixe
            if($urlinfo[0] != ''){
                $url->pathInfo .= '.'.$urlinfo[0];
            }

            // indiquer l'entrypoint
            if($urlinfo[4] != ''){
                $url->scriptName= $urlinfo[4];
            }
        }
        $url->del('module');
        $url->del('desc');
        $url->del('action'); // vraiment supprimer ?

        return $url;
    }

    /**
    *
    * @return null ou matches de la regexp si trouvé
    */
    function _searchUrl(){
        $vars = & $GLOBALS['COPIX']['COORD']->vars;
        // si il y a un parametre module, on ne teste que les regexp correspondantes à ce module->performance
        $testmod = (isset($vars['module']));
        $found=false;
        foreach($this->dataDecodeUrlOther as $infodecode){
            if($testmod && $vars['module'] !=$infodecode[0]){
                continue;
            }
            if(preg_match ($infodecode[3], $_SERVER ['PATH_INFO'], $matches)){
                $infodecode[6]=$matches;
                $found=true;
                break;
            }
        }
        if($found)
        return $infodecode;
        else
        return null;
        /*    $infodecode = array( 'module','desc','action', 'pathinfo',
        array('bla'=>'cequejeveux' ) // tableau des valeurs statiques
        array('annee','mois') // tableau des valeurs dynamiques, classées par ordre croissant
        )*/
    }

}
?>