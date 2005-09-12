<?php
/**
* @package   copix
* @subpackage copixtools
* @version   $Id: CopixSimpleXml.class.php,v 1.10.4.2 2005/08/17 20:06:54 laurentj Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * Implemente les propriétés d'une balise XML
 * @package   copix
 * @subpackage xmltools
 */
class CopixXmlTag {
   /**
    * nom de la balise
    * @var $string
    * @access private
    */
   var $__name;
   /**
    * liste des attributs de la balise
    * @var array
    * @access private
    */
   var $__attributes=array();
   /**
    * contenu texte (entre balise ouvrante/fermante)
    * @access private
    */
   var $__content='';
   /**
    * reference vers le tag parent dans une arborescence XML
    * @var CopixXmlTag
    * @access private
    */
   var $__parentTag=null;
   /**
    * liste des balise enfants
    * @var array of CopixXmlTag
    * @access private
    */
   var $__childs=array();

   /**
    * constructeur
    * @param   string   $name nom de la balise
    * @param   array    $attributes liste des attributs
    */
   function CopixXmlTag($name, $attributes=array()){
      $this->__name       = $name;
      $this->__attributes = $attributes;
   }

   /**
    * @return  array liste des attributs
    */
   function attributes(){ return $this->__attributes; }
   /**
    * @return string nom de la balise
    */
   function name(){ return $this->__name; }
   /**
    * @return string  contenu texte
    */
   function content(){ return $this->__content; }
   /**
    * @return array   liste des balises enfants
    */
   function & childs(){ return $this->__childs; }

   /**
    * ajoute une balise fille
    * @param   CopixXmlTag $tag  Balise fille
    */
   function addChild(&$tag){
      $name=$tag->__name;
      if(isset($this->$name)){
         if(!is_array($this->$name)){
            $old=&$this->$name;
            unset($this->$name);

            $this->$name=array();
            // on n'utilise pas array_push car il nous faut une réference vers l'objet tag, pas une copie
            //array_push($this->$name, $old);
            // on ne peut pas faire $this->$name[]=... donc, on passe par une référence
            $t = & $this->$name;
            $t[] = &$old;
         }
         // on n'utilise pas array_push car il nous faut une réference vers l'objet tag, pas une copie
         //array_push($this->$name, $tag);
         // on ne peut pas faire $this->$name[]=... donc, on passe par une référence
         $t= & $this->$name;
         $t[] = &$tag;

      }else{
        $this->$name=&$tag;
      }

      /*
       if(isset($this->$name)){
         array_push($this->$name, $tag);
      }else{
         $this->$name= array();
         array_push($this->$name, $tag);
      }
      */
      $this->__childs[] = & $tag;
   }
}

/**
 * cette classe implémente un parseur XML, permettant de récuperer une arborescence
 * d'un fichier XML sous forme d'arbre d'objet CopixXmlTag.
 * Elle comporte aussi d'autres fonctions utilitaires pour manipuler cet arbre.
 * @package   copix
 * @subpackage xmltools
 */
class CopixSimpleXml {
   var $_root=null;
   var $_currentTag=null;
   var $_parser=null;
   var $_charset=null;

   /**
   * Error code
   */
   var $_err_code   = null;
   /**
   * Error message
   */
   var $_err_string = null;
   /**
   * Line where the error occured
   */
   var $_err_line   = null;
   /**
   * COlumn where the error occured
   */
   var $_err_col    = null;

   /**
    *
    */
   function CopixSimpleXml($charset='ISO-8859-1'){
      $this->_charset=$charset;
      //$this->_forceCase=$forceCase;

   }

   /**
    * analyse un fichier xml
    * @param   string   $file chemin/nom du fichier à analyser
    * @return  CopixXmlTag tag racine et ses fils
    */
   function & parseFile($file){
        $root =false;
        $fp = fopen($file, "rb");
        if (is_resource($fp)) {
            $this->_initParser();
            while ($data = fread($fp, 20)) {
               if ($this->_parse($data, feof($fp)) === false) {
                  $this->_err_file = $file;
                  fclose($fp);
                  $this->_free();
                  return $root;
               }
            }
            fclose($fp);
            $this->_free();
            return $this->_root;
        }else{
            return $root;
        }
   }

   /**
    * analyse une chaine contenant un fichier xml
    * @param   string   $string  chaine contenant du xml valide
    * @return  CopixXmlTag tag racine et ses fils
    */
   function & parse($string){
      $this->_initParser();
      $this->_parse($string);
      $this->_free();
      return $this->_root;
   }
   /**
    * Génere une chaine de caractère à partir d'un XmlTag (inverse de parse)
    * @param   CopixXmlTag $xmltag  tag qu'il faut transformer en chaine (y compris ses fils)
    * @param   integer  $level   niveau d'indentation. utilisé en interne.
    * @return  string
    */
   function toString(& $xmltag, $level=0){

      $str= str_repeat('  ',$level). '<'.$xmltag->__name;
      foreach($xmltag->__attributes as $nom=>$valeur){
         $str.= ' '.$nom.'="'.$valeur.'"';
      }
      if($xmltag->__content == '' && count($xmltag->__childs) == 0){
         $str.='/>';
         return $str;
      }else{
         $str.= '>'. $xmltag->__content;
         foreach($xmltag->__childs as $child){
            $str.= "\n";
            $str.= $this->toString($child, $level+1);
         }
         return $str."\n".str_repeat('  ',$level). '</'.$xmltag->__name.'>';
      }
   }
   /**
    * analyse un contenu xml
    * @access private
    */
   function _parse(&$content, $eof=true){
      if (xml_parse($this->_parser, $content, $eof)) {
         return true;
      }else{
         // Error while parsing document
         $this->_registerError (xml_get_error_code($this->_parser), xml_error_string($this->_parser), xml_get_current_line_number($this->_parser), xml_get_current_column_number($this->_parser));
//         trigger_error("Erreur lecture fichier Xml :\ncode=$err_code\n$err_string\nLine=$err_line\nColumn=$err_col", E_USER_ERROR);
         return false;
      }
   }

   /**
   * Register an error
   * @param int $code the error code
   * @param string $string the error message
   * @param int $line the line
   * @param int $col the error column
   * @return void
   * @private
   */
   function _registerError ($code, $string, $line, $col){
         $this->_err_code   = $code;
         $this->_err_string = $string;
         $this->_err_line   = $line;
         $this->_err_col    = $col;
   }

   /**
   * Gets the error message
   * @return associative array where keys are code, string, line and col
   * @access public
   */
   function getError (){
       return array ('code'=>$this->_err_code, 'string'=>$this->_err_string, 'line'=>$this->_err_line, 'col'=>$this->_err_col);
   }

   /**
   * Raise the last error
   * @return void
   * @access public
   */
   function raiseError (){
      trigger_error(CopixI18N::get ('copix:copix.error.xmlRead', array ($this->_err_code, $this->_err_string, $this->_err_line, $this->_err_col, $this->_err_file)), E_USER_ERROR);
   }

   /**
    * initialise le parser
    * @access private
    */
   function _initParser(){
      $this->_parser = xml_parser_create($this->_charset);
      xml_set_object( $this->_parser, $this);
      xml_set_element_handler($this->_parser, "_startHandler", "_endHandler");
      xml_set_default_handler ($this->_parser, '_defaultHandler');
      xml_set_character_data_handler ( $this->_parser, '_dataHandler');
      $this->_root=null;
      $this->_currentTag=null;
   }
   /**
    * libere les ressources du parser
    * @access private
    */
   function _free(){
      xml_parser_free($this->_parser);
      $this->_parser=null;
   }
   /**
    * fonction callback pour le parser
    * @access private
    */
   function _startHandler($parser,$name, $attributes=array()){
      $tag=new CopixXmlTag($name, $attributes);
      if($this->_currentTag === null){
         // c'est la racine
         $this->_root= & $tag;
         $this->_currentTag= &$tag;
      }else{
         $tag->__parentTag= & $this->_currentTag;
         $this->_currentTag->addChild($tag);
         $this->_currentTag= & $tag;
      }
   }

   /**
    * fonction callback pour le parser
    * @access private
    */
   function _endHandler($parser, $name){
      if($this->_currentTag->__parentTag !== null){
         $this->_currentTag= & $this->_currentTag->__parentTag;
      }
   }

   /**
    * fonction callback pour le parser
    * @access private
    */
   function _defaultHandler($parser, $data){
   }

   /**
    * fonction callback pour le parser
    * @access private
    */
   function _dataHandler($parser, $data){
      if ($this->_charset == 'UTF-8'){
         $data = preg_replace("/\015\012|\015|\012/",'',utf8_decode ($data));
      }else{
         $data = preg_replace("/\015\012|\015|\012/",'',$data);
      }

      if($data != ''){
         $this->_currentTag->__content .= $data;
      }
   }
}
?>