<?php
/**
* @package   copix
* @subpackage generaltools
* @version   $Id: CopixDebugObjectViewer.class.php,v 1.7 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
* permet d'afficher le contenu d'un objet (toutes les proprietes)...
* @package   copix
* @subpackage generaltools
*/
class CopixDebugObjectViewer {
    /**
    * @var mixed L'objet à explorer
    */
    var $theObject;
    /**
    * @var int   profondeur maximal d'affichage
    * empeche l'affichage recursif des objets/tableaux où il y a des références circulaires
    */
    var $maxRecursiveLoop = 5;
    /**
    * Constructeur
    * @param mixed   $myObject   l'objet à explorer
    */
    function CopixDebugObjectViewer( & $myObject){
        $this->theObject=$myObject;
    }
    /**
    * construit une representation 'chaine' du contenu de l'objet
    * @return string   la representation
    */
    function toString (){
        return $this->_toString($this->theObject);
    }
    /**
    * affiche l'objet
    */
    function showString (){
        echo '<pre>'.$this->toString ().'</pre>';
    }
    /**
    * construit une representation 'chaine' du contenu d'un objet
    * methode appelée recursivement
    * @param   object   $obj   l'objet à analyser
    * @param   string   $tab   chaine contenant les tabulations pour l'indentation recursive
    * @return string   la representation
    * @access private
    */
    function _toString (&$obj, $tab = ''){
        $toReturn = '';
        if (is_array ($obj)){
            $toReturn .= 'Array{';
            if(strlen($tab)/2 > $this->maxRecursiveLoop){
                $toReturn.=' ... }';
            }else{
                $toReturn.="\n";
                foreach ($obj as $key=>$elem){
                    $toReturn .= $tab."\t [ $key ] = ".$this->_toString($elem, $tab."\t\t")." \n";
                }
                $toReturn .= $tab."\t}";
            }
        }else if (is_object ($obj)){
            $className = get_class($obj);
            $objVars   = get_object_vars($obj);
            $toReturn.= "Object $className {";
            if(strlen($tab)/2 > $this->maxRecursiveLoop){
                $toReturn.=" ... }";
            }else{
                $toReturn.="\n";
                foreach ($objVars as $key=>$elem){
                    $toReturn .= $tab."\t -> $key  = ".$this->_toString($elem, $tab."\t\t")." \n";
                }
                $toReturn.= $tab."}\n";
            }
        }else if (is_bool($obj)){
            $toReturn .= '( boolean ) '.($obj?'true':'false');
        }else{
            $toReturn .= '('.gettype($obj).') '.strval($obj);
        }
        return $toReturn;
    }
}
?>