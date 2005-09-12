<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: magicquotes.plugin.php,v 1.6 2005/02/09 08:29:09 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class PluginMagicQuotes extends CopixPlugin {
    /**
     * magic_quotes are on or off... ?*
     * @var boolean   $magic_quotes
     */
    var $magic_quotes;
   /**
    * @var  CopixCoordination  $app
    * @param   class   $config      objet de configuration du plugin
     */
   function PluginMagicQuotes($config){
      parent::CopixPlugin($config);
      $this->magic_quotes = get_magic_quotes_gpc();
   }


   /**
    * surchargez cette methode si vous avez des traitements à faire, des classes à declarer avant
    * la recuperation de la session
    * @abstract
    */
   function beforeSessionStart(){
      foreach ($GLOBALS['COPIX']['COORD']->vars as $key=>$elem){
             $GLOBALS['COPIX']['COORD']->vars[$key] = $this->_stripSlashes ($elem);
      }
   }

   /**
   * enleve tout les slashes d'une chaine ou d'un tableau de chaine
   * @param string/array   $string
   * @return string/array   l'objet transformé
   */
   function _stripSlashes ($string){
      if ($this->magic_quotes){
         if (is_array ($string)){
            $toReturn = array ();
            // c'est un tableau, on traite un à un tout les elements du tableau
            foreach ($string as $key=>$elem){
               $toReturn[$key] = $this->_stripSlashes ($elem);
            }
            return $toReturn;
         }else{
            return stripSlashes ($string);
         }
      }else{
         return $string;
      }
   }


   function onRegister (){
      if (! $GLOBALS['COPIX']['COORD']->magic_quotes){
         echo "COPIX WARNING. Plugin magicquotes. This plugin should not be registered
         as the serveur configuration don't needs it. You may encounter unexpected
         behaviours with this plugin registered. To unregister this plugin, you should
         edit the following file ".$GLOBALS['COPIX']['COORD']->configFile." and remove the following line
         ".'$config->registerPlugin (\'magicquotes\')';
      }
   }
}
?>
