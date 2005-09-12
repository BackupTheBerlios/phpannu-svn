<?php
/**
* This library comes originaly from the WikiRenderer 2.0 Library made by Laurent Jouanneau,
* and was integrated by himself into Copix.
*
* How to use it :
* $cwr = new CopixWikiRenderer();
* $XHTMLText = $ctr->render($wikitexte);
*
* If you want to change the configuration, create a new config object,
* or overload CopixWikiRendererConfig, or instancy it and modify
* (method depend if you want only to modify few things or to change all tags lib)
*
* $cwr = new CopixWikiRenderer( new MyNewCopixWikiRendererConfig() );
* or
* $conf = new CopixWikiRendererConfig();
* $conf->bloctags['table']=false;
* $cwr = new CopixWikiRenderer($conf);
* or
* $cwr = new CopixWikiRenderer('mymodule|ConfWiki');
*   in this example, you must have a file named confwiki.conf.php in
*   project/modules/mymodule/config/ directory, and in this file, a ConfWiki class that extends CopixWikiRendererConfig class
*
* @package   copix
* @subpackage generaltools
* @version   $Id: CopixWikiRenderer.lib.php,v 1.9 2005/02/09 08:21:44 gcroes Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
define('COPIXWIKIRENDERER_PATH', dirname(__FILE__).'/');

/**
* Impl�mente les propri�t�s d'un tag inline wiki et le fonctionnement pour la g�n�ration
* du code html correspondant
*/
class CopixWikiTag {
    var $name;
    var $beginTag;
    var $endTag;
    var $useSeparator=true;
    var $attribute=array();
    var $builderFunction=null;

    var $contents=array();
    var $separatorCount=0;
    var $isDummy=false;

    function CopixWikiTag($name, $properties){
        $this->name=$name;
        $this->beginTag=$properties[0];
        $this->endTag=$properties[1];
        if($this->name == 'dummie')
        $this->isDummy=true;

        if(is_null($properties[2])){
            $this->attribute=array();
            $this->useSeparator=false;
        }else{
            $this->attribute=$properties[2];
            $this->useSeparator=(count($this->attribute)>0);
        }

        $this->builderFunction=$properties[3];
    }

    function addContent($string, $escape=true){
        if(!isset($this->contents[$this->separatorCount]))
        $this->contents[$this->separatorCount]='';

        if($escape)
        $this->contents[$this->separatorCount] .= htmlspecialchars($string);
        else
        $this->contents[$this->separatorCount] .= $string;
    }

    function addseparator(){
        $this->separatorCount++;
    }

    function getHtmlContent(){
        if(is_null($this->builderFunction)){
            $attr='';
            if($this->useSeparator){
                $cntattr=count($this->attribute);
                $count=($this->separatorCount > $cntattr?$cntattr:$this->separatorCount);
                for($i=1;$i<=$count;$i++){
                    $attr.=' '.$this->attribute[$i-1].'="'.$this->contents[$i].'"';
                }
            }
            return '<'.$this->name.$attr.'>'.$this->contents[0].'</'.$this->name.'>';
        }else{
            $fct=$this->builderFunction;
            return $fct($this->contents, $this->attribute);
        }
    }
}

/**
* Moteur permettant de transformer les tags wiki inline d'une chaine en �quivalent HTML
*/
class CopixWikiInlineParser {

    var $resultline='';
    var $error=false;
    var $listTag=array();
    var $str=array();
    var $splitPattern='';
    var $checkWikiWord=false;
    var $checkWikiWordFunction=null;
    var $_separator;
    var $escapeHtml=true;
    /**
    * constructeur
    * @param   array    $inlinetags liste des tags permis
    *   @param   string   caract�re s�parateur des diff�rents composants d'un tag wiki
    */
    function CopixWikiInlineParser($inlinetags, $simpletags, $separator='|', $checkWikiWord=false,
    $funcCheckWikiWord=null, $escapeHtml=true  ){

        foreach($inlinetags as $name=>$prop){
            $this->listTag[$prop[0]]=new CopixWikiTag($name,$prop);

            $this->splitPattern.=preg_replace ( '/(.)/', '\\\\\\1',$prop[0]).')|(';
            if($prop[1] != $prop[0])
            $this->splitPattern.=preg_replace ( '/(.)/', '\\\\\\1',$prop[1]).')|(';
        }
        foreach($simpletags as $tag=>$html){
            $this->splitPattern.=preg_replace ( '/(.)/', '\\\\\\1',$tag).')|(';
        }

        $this->simpletags= $simpletags;
        $this->_separator=$separator;
        $this->checkWikiWord=$checkWikiWord;
        $this->checkWikiWordFunction=$funcCheckWikiWord;
        $this->escapeHtml=$escapeHtml;
    }

    /**
    * fonction principale du parser.
    * @param   string   $line avec des eventuels tag wiki
    * @return  string   chaine $line avec les tags wiki transform� en HTML
    */
    function parse($line){
        $this->error=false;

        $this->str=preg_split('/('.$this->splitPattern.'\\'.$this->_separator.')|(\\\\)/',$line, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $this->end=count($this->str);
        if($this->end > 1){
            $firsttag=new CopixWikiTag('dummie',array('','', null,'wikibuilddummie'));
            $pos=-1;
            return $this->_parse($firsttag, $pos);
        }else{
            if($this->escapeHtml){
                if($this->checkWikiWord && $this->checkWikiWordFunction !== null)
                return  $this->_doCheckWikiWord(htmlspecialchars($line));
                else
                return htmlspecialchars($line);
            }else{
                if($this->checkWikiWord && $this->checkWikiWordFunction !== null)
                return  $this->_doCheckWikiWord($line);
                else
                return $line;
            }
        }
    }

    /**
    * coeur du parseur. Appel� r�cursivement
    */
    function _parse($tag, &$posstart){

        $checkNextTag=true;
        $checkBeginTag=true;

        // on parcours la chaine,  morceau apr�s morceau
        for($i=$posstart+1; $i < $this->end; $i++){
            $t=&$this->str[$i];
            // a t-on un antislash ?
            if($t=='\\'){
                if($checkNextTag){
                    $t=''; // oui -> on l'efface et on ignore le tag (on continue)
                    $checkNextTag=false;
                }else{
                    $tag->addContent('\\',false);
                }

                // est-ce un s�parateur ?
            }elseif($t == $this->_separator){
                if($tag->isDummy)
                $tag->addContent($this->_separator,false);
                elseif($tag->useSeparator){
                    $checkBeginTag=false;
                    $tag->addSeparator();
                }
                // a-t-on une balise de fin du tag ?
            }elseif($checkNextTag && $tag->endTag == $t && !$tag->isDummy){
                $posstart=$i;
                return $tag->getHtmlContent();

                // a-t-on une balise de debut de tag quelconque ?
            }elseif($checkBeginTag && $checkNextTag && isset($this->listTag[$t]) ){

                $content = $this->_parse($this->listTag[$t],$i);
                if($content)
                $tag->addContent($content,false);
                else{
                    if($tag->separatorCount == 0 && $this->checkWikiWord && $this->checkWikiWordFunction !== null){
                        if($this->escapeHtml)
                        $tag->addContent($this->_doCheckWikiWord(htmlspecialchars($t)),false);
                        else
                        $tag->addContent($this->_doCheckWikiWord($t),false);
                    }else
                    $tag->addContent($t,$this->escapeHtml);
                }

                // a-t-on un saut de ligne forc� ?
            }elseif($checkNextTag && $checkBeginTag && isset($this->simpletags[$t])){
                $tag->addContent($this->simpletags[$t],false);
            }else{
                if($tag->separatorCount == 0 && $this->checkWikiWord && $this->checkWikiWordFunction !== null){
                    if($this->escapeHtml)
                    $tag->addContent($this->_doCheckWikiWord(htmlspecialchars($t)),false);
                    else
                    $tag->addContent($this->_doCheckWikiWord($t),false);
                }else
                $tag->addContent($t,$this->escapeHtml);
                $checkNextTag=true;
            }
        }
        if(!$tag->isDummy ){
            //--- on n'a pas trouv� le tag de fin
            // on met en erreur
            $this->error=true;
            return false;
        }else
        return $tag->getHtmlContent();
    }

    function _doCheckWikiWord($string){
        if(preg_match_all("/(?<=\b)[A-Z][a-z]+[A-Z0-9]\w*/", $string, $matches)){
            $fct=$this->checkWikiWordFunction;
            $match = array_unique($matches[0]); // il faut avoir une liste sans doublon, � cause du str_replace plus loin...
            $string=str_replace($match, $fct($match), $string);
        }
        return $string;
    }


}



/**
* classe de base pour la transformation des �lements de type bloc
* @abstract
*/
class CopixWikiRendererBloc {

    /**
    * @var string  code identifiant le type de bloc
    */
    var $type='';

    /**
    * @var string  chaine contenant le tag XHTML d'ouverture du bloc
    * @access private
    */
    var $_openTag='';

    /**
    * @var string  chaine contenant le tag XHTML de fermeture du bloc
    * @access private
    */
    var $_closeTag='';
    /**
    * @var boolean    indique si le bloc doit �tre immediatement ferm� apr�s d�tection
    * @access private
    */
    var $_closeNow=false;

    /**
    * @var WikiRenderer      r�f�rence � la classe principale
    */
    var $engine=null;

    /**
    * @var   array      liste des �lements trouv�s par l'expression r�guli�re regexp
    */
    var $_detectMatch=null;

    /**
    * @var string      expression r�guli�re permettant de reconnaitre le bloc
    */
    var $regexp='';

    /**
    * constructeur � surcharger pour d�finir les valeurs des diff�rentes propriet�s
    * @param WikiRender    $wr   l'objet moteur wiki
    * @abstract
    */
    function CopixWikiRendererBloc(&$wr){
        $this->engine = &$wr;
    }

    /**
    * renvoi une chaine correspondant � l'ouverture du bloc
    * @return string
    */
    function open(){
        return $this->_openTag;
    }

    /**
    * renvoi une chaine correspondant � la fermeture du bloc
    * @return string
    */
    function close(){
        return $this->_closeTag;
    }

    /**
    * indique si le bloc doit etre imm�diatement ferm�
    * @return string
    */
    function closeNow(){
        return $this->_closeNow;
    }

    /**
    * test si la chaine correspond au debut ou au contenu d'un bloc
    * @param string   $string
    * @return boolean   true: appartient au bloc
    */
    function detect($string){
        return preg_match($this->regexp, $string, $this->_detectMatch);
    }

    /**
    * renvoi la ligne, trait�e pour le bloc. A surcharger �ventuellement.
    * @return string
    * @abstract
    */
    function getRenderedLine(){
        return $this->_renderInlineTag($this->_detectMatch[1]);
    }

    /**
    * traite le rendu des signes de type inline (qui se trouvent necessairement dans des blocs
    * @param   string  $string une chaine contenant une ou plusieurs balises wiki
    * @return  string  la chaine transform�e en XHTML
    * @see WikiRendererInline
    */
    function _renderInlineTag($string){
        return $this->engine->inlineParser->parse($string);
    }

    /**
    * d�tection d'attributs de bloc (ex:  >��attr1|attr2|attr3�� la citation )
    * @todo � terminer pour une version ulterieure
    */
    function _checkAttributes(&$string){
        $bat=$this->engine->config->blocAttributeTag;
        if(preg_match("/^$bat(.*)$bat(.*)$/",$string,$result)){
            $string=$result[2];
            return explode($this->engine->config->inlineTagSeparator,$result[1]);
        }else
        return false;
    }

}

require(COPIXWIKIRENDERER_PATH . 'CopixWikiRenderer.conf.php');



/**
* Moteur de rendu. Classe principale � instancier pour transformer un texte wiki en texte XHTML.
* utilisation :
*      $ctr = new WikiRenderer();
*      $monTexteXHTML = $ctr->render($montexte);
*/
class CopixWikiRenderer {

    /**
    * @var   string   contient la version HTML du texte analys�
    * @access private
    */
    var $_newtext;

    /**
    * @var   boolean
    * @access private
    */
    var $_isBlocOpen=false;

    /**
    * @var WikiRendererBloc element bloc ouvert en cours
    * @access private
    */
    var $_currentBloc;

    /**
    * @var array       liste des differents types de blocs disponibles
    * @access private
    */
    var $_blocList= array();

    /**
    * @var   array      liste de param�tres pour le moteur
    */
    var $params=array();

    /**
    * @var WikiInlineParser   analyseur pour les tags wiki inline
    */
    var $inlineParser=null;

    /**
    * liste des lignes o� il y a une erreur wiki
    */
    var $errors;


    var $config=null;

    /**
    * instancie les diff�rents objets pour le rendu des elements inline et bloc.
    */
    function CopixWikiRenderer( $config=null){
        if(is_null($config))
        $this->config= & new CopixWikiRendererConfig;
        else{
            if(is_object($config))
            $this->config=$config;
            else{
                $fs = CopixSelectorFactory::create($config);
                if($fs===null)
                $this->config= & new CopixWikiRendererConfig;
                else{
                    require_once($fs->getPath('config/').strtolower($fs->fileName).'.conf.php');
                    $name=$fs->fileName;
                    $this->config= & new $name();
                }
            }
        }

        $this->_currentBloc = new CopixWikiRendererBloc($this); // bloc 'fantome'
        $this->inlineParser =& new CopixWikiInlineParser($this->config->inlinetags,
        $this->config->simpletags, $this->config->inlineTagSeparator,
        $this->config->checkWikiWord, $this->config->checkWikiWordFunction, $this->config->escapeSpecialChars );

        foreach($this->config->bloctags as $name=>$ok){
            $name='WRB_'.$name;
            if($ok) $this->_blocList[]=& new $name($this);
        }


    }

    /**
    * Methode principale qui transforme les tags wiki en tag XHTML
    * @param   string  $texte le texte � convertir
    * @return  string  le texte converti en XHTML
    */
    function render ($texte){
        $lignes=preg_split("/\015\012|\015|\012/",$texte); // on remplace les \r (mac), les \n (unix) et les \r\n (windows) par un autre caract�re pour d�couper proprement

        $this->_newtext=array();
        $this->_isBlocOpen=false;
        $this->errors=false;
        $this->_currentBloc = new CopixWikiRendererBloc($this);

        // parcours de l'ensemble des lignes du texte
        foreach($lignes as $num=>$ligne){
            if(trim($ligne) == '') {
                // ligne vide
                $this->_closeBloc();
            }else{
                // detection de debut de bloc (liste, tableau, hr, titre)
                foreach($this->_blocList as $bloc){
                    if($bloc->detect($ligne))
                    break;
                }
                // c'est le debut d'un bloc (ou ligne d'un bloc en cours)
                if($bloc->type != $this->_currentBloc->type){
                    $this->_closeBloc(); // on ferme le precedent si c'etait un different
                    $this->_currentBloc= $bloc;
                    if($this->_openBloc()){
                        $this->_newtext[]=$this->_currentBloc->getRenderedLine();
                    }else{
                        $this->_newtext[]=$this->_currentBloc->getRenderedLine();
                        $this->_newtext[]=$this->_currentBloc->close();
                        $this->_isBlocOpen = false;
                        $this->_currentBloc = new CopixWikiRendererBloc($this);
                    }
                }else{
                    $this->_currentBloc->_detectMatch=$bloc->_detectMatch;
                    $this->_newtext[]=$this->_currentBloc->getRenderedLine();
                }
                if($this->inlineParser->error){
                    $this->errors[$num+1]=$ligne;
                }
            }
        }
        $this->_closeBloc();
        return implode("\n",$this->_newtext);
    }

    /**
    * ferme un bloc
    * @access private
    */
    function _closeBloc(){
        if($this->_isBlocOpen){
            $this->_isBlocOpen=false;
            $this->_newtext[]=$this->_currentBloc->close();
            $this->_currentBloc = new CopixWikiRendererBloc($this);
        }
    }

    /**
    * ouvre un bloc et le referme eventuellement suivant sa nature
    * @return boolean  indique si le bloc reste ouvert ou pas
    * @access private
    */
    function _openBloc(){
        if(!$this->_isBlocOpen){
            $this->_newtext[]=$this->_currentBloc->open();
            $this->_isBlocOpen=true;
            return !$this->_currentBloc->closeNow();
        }else
        return true;
    }

}
?>