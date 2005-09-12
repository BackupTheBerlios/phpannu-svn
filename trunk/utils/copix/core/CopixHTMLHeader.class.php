<?php
/**
* @package   copix
* @subpackage generaltools
* @version   $Id: CopixHTMLHeader.class.php,v 1.9 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* permet de completer la partie entete (<head></head>) du template principal du processus standard
*
*/
class CopixHTMLHeader {
    var $_CSSLink = array ();
    var $_Styles  = array ();
    var $_JSLink  = array ();
    var $_JSCode  = array ();
    var $_Others  = array ();

    function & _getInstance (){
        static $instance = false;
        if ($instance === false){
            $instance = new CopixHTMLHeader ();
        }
        return $instance;
    }

    function addJSLink ($src, $params=array()){
        $me = & CopixHTMLHeader::_getInstance ();
        if (!isset ($me->_JSLink[$src])){
            $me->_JSLink[$src] = $params;
        }
    }
    function addCSSLink ($src, $params=array ()){
        $me = & CopixHTMLHeader::_getInstance ();
        if (!isset ($me->_CSSLink[$src])){
            $me->_CSSLink[$src] = $params;
        }
    }
    function addStyle ($selector, $def){
        $me = & CopixHTMLHeader::_getInstance ();
        if (!isset ($me->_Styles[$selector])){
            $me->_Styles[$selector] = $def;
        }
    }
    function addOthers ($content){
        $me = & CopixHTMLHeader::_getInstance ();
        $me->_Others[] = $content;
    }

    function addJSCode ($code){
        $me = & CopixHTMLHeader::_getInstance ();
        $me->_JSCode[] = $code;
    }


    function get (){
        $me = & CopixHTMLHeader::_getInstance ();
        return $me->getCSSLink () . "\n\r" . $me->getJSLink () . "\n\r" . $me->getStyles ()."\n\r" .$me->getJSCode ().$me->getOthers ();

    }

    function getOthers (){
        $me = & CopixHTMLHeader::_getInstance ();
        return implode ("\n\r", $me->_Others);
    }

    function getJSCode (){
        $me = & CopixHTMLHeader::_getInstance ();
        if(($js= implode ("\n", $me->_JSCode)) != '')
        return '<script type="text/javascript">
// <![CDATA[
 '.$js.'
// ]]>
</script>';
        else
        return '';
    }

    function getStyles (){
        $me = & CopixHTMLHeader::_getInstance ();
        $built = array ();
        foreach ($me->_Styles as $selector=>$value){
            if (strlen (trim($value))){
                //il y a une paire clef valeur.
                $built[] = $selector.' {'.$value.'}';
            }else{
                //il n'y a pas de valeur, c'est peut être simplement une commande.
                //par exemple @import qqchose, ...
                $built[] = $selector;
            }
        }
        if(($css=implode ("\n", $built)) != '')
        return '<style type="text/css"><!--
         '.$css.'
         //--></style>';
        else
        return '';
    }

    function getCSSLink (){
        $built = array ();
        $me = & CopixHTMLHeader::_getInstance ();
        foreach ($me->_CSSLink as $src=>$params){
            //the extra params we may found in there.
            $more = '';
            foreach ($params as $param_name=>$param_value){
                $more .= $param_name.'="'.$param_value.'" ';
            }
            $built[] = '<link rel="stylesheet" type="text/css" href="'.$src.'" '.$more.' />';
        }
        return implode ("\n\r", $built);
    }

    function getJSLink (){
        $built = array ();
        $me = & CopixHTMLHeader::_getInstance ();
        foreach ($me->_JSLink as $src=>$params){
            //the extra params we may found in there.
            $more = '';
            foreach ($params as $param_name=>$param_value){
                $more .= $param_name.'="'.$param_value.'" ';
            }
            $built[] = '<script type="text/javascript" src="'.$src.'" '.$more.'></script>';
        }
        return implode ("\n\r", $built);
    }

    function clear ($what){
        $cleanable = array ('CSSLink', 'Styles', 'JSLink', 'JSCode', 'Others');
        foreach ($what as $elem){
            if (in_array ($elem, $cleanable)){
                $name = '_'.$elem;
                $this->$name = array ();
            }
        }
    }
}
?>