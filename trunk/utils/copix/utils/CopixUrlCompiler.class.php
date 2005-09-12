<?php
/**
* @package    copix
* @subpackage generaltools
* @version    $Id: CopixUrlCompiler.class.php,v 1.7 2005/02/09 08:21:44 gcroes Exp $
* @author    Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @todo This is an experimental package
*/

/**
* Compilation d'un fichier de cache PHP contenant les infos sur les urls significatifs
* @package    copix
* @subpackage generaltools
*
*/
class CopixUrlCompiler {
    function compile(&$pluginConfig){
        // la verification pour savoir si il faut compiler ou non : dans le plugin significanturl
        require_once (COPIX_UTILS_PATH.'CopixSimpleXml.class.php');

        $modulesList = CopixModule::getList(false);
        $parser      = & new CopixSimpleXml ();

        $_compile_decodeUrl_suffixe='';
        $_compile_decodeUrl_other='';
        $_compile_createUrl='';

        foreach ($modulesList as $module) {
            if (! ($xml = & $parser->parseFile (COPIX_MODULE_PATH.$module.'/module.xml'))){
                $parser->raiseError ();
            }

            if (!isset ($xml->SIGNIFICANTURL))
            continue;
            if (!isset ($xml->SIGNIFICANTURL->URL))
            continue;
            /* schema attendu

            <significanturl>
            <url desc="" action=""  suffix="" entrypoint="toto.php">
            <encode params="annee,mois,id" schema="/test/%1/%2/%3"/>
            <encoder class="" method="" />
            <decode pathinfo="/ ... /">
            <createvar name="bla" value="cequejeveux" />
            <createvar name="mois" valuefrom="2" />
            <createvar name="annee" valuefrom="1" />
            </decode>
            </url>
            </significanturl>
            */
            foreach (is_array ($xml->SIGNIFICANTURL->URL) ? $xml->SIGNIFICANTURL->URL : array ($xml->SIGNIFICANTURL->URL) as $sigurl){

                $attr=$sigurl->attributes();
                if (isset ($attr['DESC'])){
                    $desc = ($attr['DESC'] != ''?$attr['DESC']:COPIX_DEFAULT_VALUE_DESC);
                }else{
                    $desc=COPIX_DEFAULT_VALUE_DESC;
                }

                if (isset ($attr['ACTION'])){
                    $action = ($attr['ACTION'] != ''?$attr['ACTION']:COPIX_DEFAULT_VALUE_ACTION);
                }else{
                    $action=COPIX_DEFAULT_VALUE_ACTION;
                }
                $suffixe = (isset($attr['SUFFIX']) ? $attr['SUFFIX']:'');
                $entrypoint = (isset($attr['ENTRYPOINT']) ? $attr['ENTRYPOINT']:'');

                // debut partie traitement des balises DECODE
                if (isset ($sigurl->DECODE)){
                    /*
                    <decode pathinfo="/ ... /">
                    <createvar name="bla" value="cequejeveux" />
                    <createvar name="mois" valuefrom="2" />
                    <createvar name="annee" valuefrom="1" />
                    </decode>

                    génèee
                    $_compile_dataDecodeUrl_suffix = array(
                    'suffix1'=>$infodecode
                    )

                    $_compile_dataDecodeUrl_other = array(
                    $infodecode,$infodecode...
                    )
                    où
                    $infodecode = array( 'module','desc','action', 'pathinfo',
                    array('bla'=>'cequejeveux' ) // tableau des valeurs statiques
                    array('annee','mois') // tableau des valeurs dynamiques, classées par ordre croissant
                    )

                    */
                    foreach (is_array ($sigurl->DECODE) ? $sigurl->DECODE : array ($sigurl->DECODE) as $decurl){

                        if (!isset ($sigurl->DECODE->CREATEVAR)){
                            trigger_error('erreur dans module.xml significanturl, balises createvar manquante',E_USER_ERROR);
                        }

                        // analyse des createvars
                        $str_createvars_statique = '';
                        $_createvars_dyn         = array ();
                        foreach (is_array ($sigurl->DECODE->CREATEVAR) ? $sigurl->DECODE->CREATEVAR : array ($sigurl->DECODE->CREATEVAR) as $createvar){
                            $attrcv = $createvar->attributes();
                            if (isset ($attrcv['NAME'])){
                                if (isset ($attrcv['VALUE'])){
                                    $str_createvars_statique.=', \''.$attrcv['NAME'].'\'=>\''.str_replace ("'", "\\'", $attrcv['VALUE']).'\'';
                                }elseif (isset ($attrcv['VALUEFROM'])){
                                    $_createvars_dyn [intval ($attrcv['VALUEFROM'])] = $attrcv['NAME'];
                                }else{
                                    trigger_error('erreur dans module.xml significanturl, pas d\'attribut value ou valuefrom pour une balise createvar', E_USER_ERROR);
                                }
                            }else{
                                trigger_error('erreur dans module.xml significanturl, pas d\'attribut name pour une balise createvar', E_USER_ERROR);
                            }
                        }

                        $str_createvars_statique = ', array('.substr($str_createvars_statique,1).')';
                        if (count ($_createvars_dyn)){
                            ksort ($_createvars_dyn);
                            $_createvars_dyn = array_merge($_createvars_dyn, array ()); // on consolide les indexes
                            $str_createvars_dyn = '';
                            foreach ($_createvars_dyn as $varnom){
                                $str_createvars_dyn .= ', \''.$varnom.'\'';
                            }
                            $str_createvars_dyn = ', array('.substr ($str_createvars_dyn,1).')';

                        }else
                        $str_createvars_dyn = ', array()';

                        // analyse des attributs de decode
                        $attrdec = $decurl->attributes();
                        if(!isset($attrdec['PATHINFO'])){
                            trigger_error('erreur dans module.xml significanturl, pas d\'attribut pathinfo pour une balise decodeurl',E_USER_ERROR);
                        }

                        // génération du code PHP pour decode
                        $infodecode = 'array(\''.$module.'\',\''.$desc.'\',\''.$action.'\',\''.str_replace ("'", "\\'", $attrdec['PATHINFO']).'\'';
                        $infodecode .=$str_createvars_statique.$str_createvars_dyn.') ';

                        if($suffixe !=''){
                            if($_compile_decodeUrl_suffixe != ''){
                                $_compile_decodeUrl_suffixe.=",\n'".$suffixe.'\'=>'.$infodecode;
                            }else{
                                $_compile_decodeUrl_suffixe.='\''.$suffixe.'\'=>'.$infodecode;
                            }
                        }else{
                            if($_compile_decodeUrl_other != ''){
                                $_compile_decodeUrl_other.=",\n".$infodecode;
                            }else{
                                $_compile_decodeUrl_other.=$infodecode;
                            }
                        }
                    }//fin foreach decode
                } // fin partie sur les balises <decode>


                // debut partie sur les balises <encode> et <encodeurl>
                /*
                on doit obtenir
                $_compile_createUrl = array(
                'news|default|show' =>
                array(suffix, bool,// bool = indique si c'est sur un schema(=true) ou un encoder(=false)
                array('annee','mois','jour','id','titre'),
                "/news/%1/%2/%3/%4-%5",
                ou
                "class",
                "method",
                'entrypoint'
                ),
                'cms_truc'=> ...
                )
                */
                if (isset ($sigurl->ENCODE)){
                    //<encode params="annee,mois,id" schema="/test/%1/%2/%3"/>
                    $attrenc = $sigurl->ENCODE->attributes();
                    if (!isset ($attrenc['PARAMS']) || !isset ($attrenc['SCHEMA'])){
                        trigger_error('erreur dans module.xml significanturl, attribut params ou schema manquant dans un encode',E_USER_ERROR);
                    }
                    $params = str_replace ("'", "\\'",$attrenc['PARAMS']);
                    $params = str_replace (",", "','",$params);
                    $createurl_str = 'true, array(\''.$params .'\'), \''.str_replace ("'", "\\'",$attrenc['SCHEMA']).'\'';
                }elseif (isset ($sigurl->ENCODER)){
                    //<encoder class="" method="" />
                    $attrenc = $sigurl->ENCODER->attributes();
                    if (!isset ($attrenc['CLASS']) || !isset ($attrenc['METHOD'])){
                        trigger_error('erreur dans module.xml significanturl, attribut class ou method manquant dans un encoder',E_USER_ERROR);
                    }
                    $createurl_str = 'false, \''. str_replace ("'", "\\'",$attrenc['CLASS']).'\', \''.str_replace ("'", "\\'",$attrenc['METHOD']).'\'';
                }
                if($createurl_str !=''){
                    if ($_compile_createUrl!=''){
                        $_compile_createUrl.= ",\n'";
                    }else{
                        $_compile_createUrl.= "'";
                    }
                    $_compile_createUrl.= $module.'|'.$desc.'|'.$action.'\'=>array(\''.$suffixe.'\','.$createurl_str.',\''.$entrypoint.'\')';
                }
                // fin partie sur les balises <createurl>

            } // fin boucle sur les urls
        }// fin boucle sur les modules

        $_compile_decodeUrl_suffixe = '$_compile_decodeUrl_suffixe = array('.$_compile_decodeUrl_suffixe.");\n\n";
        $_compile_decodeUrl_other = '$_compile_decodeUrl_other = array('.$_compile_decodeUrl_other.");\n\n";
        $_compile_createUrl = '$_compile_createUrl = array('.$_compile_createUrl.");\n\n";

        $_resources = "<?php\n\n".$_compile_decodeUrl_suffixe."\n\n".$_compile_decodeUrl_other."\n\n".$_compile_createUrl."\n\n?>";
        //echo '<pre>',htmlspecialchars($_resources),'</pre>';
        //writing the PHP code to the disk
        require_once (COPIX_UTILS_PATH .'CopixFile.class.php');
        $objectWriter = & new CopixFile ();
        $fic = $pluginConfig->compile_dir.'significanturl.php';
        //$fic=$this->_compiledFileName (); // peut pas y faire appel -> le plugin n'est pas encore enregistré auprés du coord, car compile() est appelé dans le constructeur du plugin
        $res = $objectWriter->write ($fic, $_resources);
    }

    /**
    * Gets the compiled fileName.
    * @return string the compiled fileName.
    */
    function _compiledFileName (){
        $plugin = $GLOBALS['COPIX']['COORD']->getPlugin('significanturl');
        if ($plugin === null){
            trigger_error (CopixI18N::get ('copix:copix.error.unfounded.plugin', array ('significanturl')), E_USER_ERROR);
        }
        return $plugin->config->compile_dir.'significanturl.php';
    }

    /**
    * Clear the compiled file
    */
    function clearCompiledFile (){
        $fic = CopixUrlCompiler::_compiledFileName();
        if (is_file ($fic)){
            unlink ($fic);
        }
    }

    /**
    * singleton
    * @return CopixListenerFactory.
    */
    function & instance () {
        static $me = false;
        if ($me === false) {
            $me = new CopixUrlCompiler ();
        }
        return $me;
    }
}
?>