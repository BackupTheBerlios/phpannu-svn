<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: i18n.plugin.php,v 1.9 2005/04/25 15:59:15 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * plugin gerant automatiquement la langue et la monnaie de l'utilisateur
 */

class PluginI18n extends CopixPlugin {

   /**
     *
    * @param   class   $config      objet de configuration du plugin
     */
   function PluginI18n($config){
        parent::CopixPlugin($config);
   }

   /**
     * traitements à faire avant execution du coordinateur de page
    */
   function beforeProcess(&$pageActionDesc){

      //-------- on determine la langue que l'on utilise

        if(isset($_SESSION['COPIXLANG'])){
            $languageCode=$_SESSION['COPIXLANG']['code'];
            $languageData=$_SESSION['COPIXLANG'];
        }else{
            $languageCode='';
            $languageData=array();
        }

      $isLanguageAsked=false;


      if($this->config->enableUserLanguageChoosen){ // si la detection est activée

            if(isset($this->coordination->vars[$this->config->urlParamNameLanguage])){ // si le parametre language est donné dans l'url
            // recuperation des données de la langue
                $languageData=$this->_getLanguageData($this->coordination->vars[$this->config->urlParamNameLanguage]);
            if(count($languageData)>0){
                $isLanguageAsked=true;
               $languageCode=$this->coordination->vars[$this->config->urlParamNameLanguage];
            }
         }
      }

      if(!$isLanguageAsked){ // si il n'y a pas eu de parametre langue dans l'url...
            if(!isset($_SESSION['COPIXLANG'])){ // y a t il les données de la langue en session ?
               // non -> on detecte automatiquement la langue puis on stocke en session
               if($this->config->useDefaultLanguageBrowser){
                  if( $languageData=$this->_getBrowserLanguage() )
                     $languageCode=$languageData['code'];
               }

               if($languageCode==''){ // si code language browser inactif ou non trouve, utilisation du language par defaut
                  $languageCode = $this->config->defaultLanguageCode;
                  $languageData = $this->_getLanguageData($languageCode);
               }
            }

      }

        $_SESSION['COPIXLANG']=$languageData;
        $GLOBALS['COPIX']['CONFIG']->tpl_sub_dirs[]=$languageCode;
        $GLOBALS['COPIX']['CONFIG']->default_language = $_SESSION['COPIXLANG']['lang'];
        $GLOBALS['COPIX']['CONFIG']->default_country  = $_SESSION['COPIXLANG']['country'];

      //------ on determine la monnaie à utiliser
      $currencyCode='';
        $currencyData=array();
      $isCurrencyAsked=false;
      // on regarde si l'utilisateur peut choisir sa monnaie, et donc si il y a ce qu'il faut dans l'url
      if($this->config->enableUserCurrencyChoosen){
            if(isset($this->coordination->vars[$this->config->urlParamNameCurrency])){
                $currencyData=$this->_getCurrencyData($this->coordination->vars[$this->config->urlParamNameCurrency]);
            if($currencyData){
                $isCurrencyAsked=true;
               $currencyCode=$this->coordination->vars[$this->config->urlParamNameCurrency];
            }
         }
      }

      // on rempli la variable en session
      if(!$isCurrencyAsked){
            if(!isset($_SESSION['COPIXCURRENCY'])){
            $_SESSION['COPIXCURRENCY']= $this->_getCurrencyData($this->config->defaultCurrencyCode);
          }
      }else{
            $_SESSION['COPIXCURRENCY']=$currencyData;
        }

      // on rempli la variable en session contenant la monnaie alternative (pour double affichage)
      if(!isset($_SESSION['COPIXCURRENCYALT'])){
         $_SESSION['COPIXCURRENCYALT']= $this->_getCurrencyData($this->config->defaultAlternateCurrencyCode);
      }

      //------ on determine le profil de connexion DB à utiliser
      if($this->config->useSpecificDbProfil){
        $dbplugin = & $GLOBALS['COPIX']['COORD']->plugins['copixdb']->config;

        $dbProfilPrefix = $dbplugin->default ;
        $dbProfilDefault = $dbProfilPrefix.'_'.$languageCode;

        //assignation
        if ( isset( $dbplugin->profils[$dbProfilDefault]) && is_object( $dbplugin->profils[$dbProfilDefault] ) ){
            $dbplugin->default = $dbProfilPrefix."_".$languageCode ;
        }
      }
   }


   function _getBrowserLanguage() {

      $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
      foreach($browser_languages as $bl){
         if(preg_match("/^([a-zA-Z]{2})([-_][a-zA-Z]{2})?(;q=[0-9]\\.[0-9])?$/",$bl,$match)){ // pour les user-agents qui livrent un code internationnal
            if($data=$this->_getLanguageData($match[0],$match[1]))
               return $data;
         }elseif(preg_match("/^([a-zA-Z ]+)(;q=[0-9]\\.[0-9])?$/",$bl,$match)){ // pour les user agent qui indique le nom en entier
            if($data=$this->_getLanguageData($match[1],'',false))
               return $data;
         }
      }
      return false;
   }


   /**
    * recupere les données de la langue à partir du code international
    */
    function _getLanguageData($code, $code2='', $direct=true){
        $code=strtolower(str_replace('-','_',$code));
        $code2=strtolower($code2);

        //include(realpath(dirname(__FILE__).'/../config/i18n.plugin.datas.php'));
        include('i18n.plugin.datas.php');
        if(!$direct){
            if(isset($i18n_alternate_languages_code[$code]))
               $code=$i18n_alternate_languages_code[$code];
            else
               return false;
        }

        $l=null;
        if(isset($i18n_languages[$code])){
            $l=&$i18n_languages[$code];
        }elseif($code2 !='' && isset($i18n_languages[$code2])){
            $l=&$i18n_languages[$code];
        }
        if($l !== null){
            return array('code'=>$code,
                    'lang'=>$l[0],
                    'country'=>$l[1],
                    'name'=>$l[2],
                    'default_currency' => $l[3]
                    );
        }else
          return false;
   }

   /**
    * recupere les données de la monnaie, disponible en base de donnée
    */
   function _getCurrencyData($code){
        include('i18n.plugin.datas.php');
        if(isset($i18n_currencies[$code])){
           $l=&$i18n_currencies[$code];
            return array('code'=>$code,
                    'name'=>$l[0],
                    'symbol_left' => $l[1],
                    'symbol right' => $l[2],
                    'decimal point' => $l[3],
                    'thousands point' => $l[4],
                    'decimal_places' => $l[5],
                    'value' => $l[6],
                    'last_updated' => $l[7]
                    );
       }else
          return false;

   }

   function getLang(){
      if(isset($_SESSION['COPIXLANG']))
         return $_SESSION['COPIXLANG']['lang'];
   }

   function getCountry(){
      if(isset($_SESSION['COPIXLANG']))
         return $_SESSION['COPIXLANG']['country'];
   }

}
?>
