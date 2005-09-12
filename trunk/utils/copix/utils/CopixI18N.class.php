<?php
/**
* Internationnalisation handling
* @package   copix
* @subpackage copixtools
* @version   $Id: CopixI18N.class.php,v 1.23.2.1 2005/05/12 23:34:02 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/*
*
* glossaire
*   bundle :
*   ressource :   nom du lieu de stockage de la ressource = "plugin", "copix" ou rien (= module ou projet)
*
* Les identifiants des éléments des ressources ont le format suivant :
*     [type:][module|]bundleid.stringId
*
*     [resource:][module|]id.for.the.bundle
*     if a resource is given, then we won't check for a module.
*     if a module is given, it will be checked for an overloaded string in the project.
*     the first part (the string before the first dot) will be considered as the file id.
*     eg in our example: id_lang_COUNTRY.properties.
*
*     for a given lang_COUNTRY, lang_LANG will be the defaults values if no
*     key is specified in lang_COUNTRY.
*
*     the loading process is the following:
*
*     Loading in the module lang_LANG
*     Loading in the module lang_COUNTRY
*     loading overloaded keys in the project for lang_LANG
*     loading overloaded keys in the project for lang_COUNTRY
*/

/*
* Contient un ensemble de traductions concernant une langue donnée
* (et pour tout les pays concernés)
*/
class CopixBundle {
    var $fic;
    var $lang;

    var $_loadedCountries = array ();
    var $_messages;

    /**
    * constructor
    * @param CopixFileSelector   $file
    * @param string            $lang        the language we wants to load
    */
    function CopixBundle ($file, $lang){
        $this->fic  = $file;
        $this->lang = $lang;

        //creates, we load the defaults.
        $this->_loadLocales ($lang, strtoupper($lang));
    }

    /**
    * get the translation
    */
    function get ($key, $country){
        $country = strtoupper ($country);

        if (!in_array ($country, $this->_loadedCountries)){
            $this->_loadLocales ($this->lang, $country);
        }

        // check if the key exists for the specified country
        if (isset ($this->_messages[$country][$key])){
            return $this->_messages[$country][$key];
        }elseif ($country !== strtoupper ($this->lang)){
            // the key doesn't exist for the specified country,
            // so get the key of the native country
            return $this->get ($key, $this->lang);
        }else{
            return null;
        }
    }

    /**
    * Loads the resources for a given lang/country.
    * will automatically loads the default (lang lang)
    * @param string $lang     the language
    * @param string $country the country
    */
    function _loadLocales ($lang, $country){
        $this->_loadedCountries[] = $country;

        //file names for different cases.
        $bundleLang     = $this->fic->fileName.'_'.$lang.'.properties';
        $bundleCountry  = $this->fic->fileName.'_'.$lang.'_'.$country.'.properties';

        $path=$this->fic->getPath(COPIX_RESOURCES_DIR);
        $toLoad[] = array('file'=>$path .$bundleLang, 'lang'=>$lang,'country'=>$lang);
        $toLoad[] = array('file'=>$path .$bundleCountry, 'lang'=>$lang,'country'=>$country);

        $overloadedPath =$this->fic->getOverloadedPath(COPIX_RESOURCES_DIR);
        if($overloadedPath !== null){
            $toLoad[] = array('file'=>$overloadedPath .$bundleLang, 'lang'=>$lang,'country'=>$lang);
            $toLoad[] = array('file'=>$overloadedPath .$bundleCountry, 'lang'=>$lang,'country'=>$country);
        }

        // check if we have a compiled version of the ressources
        $_compileResourceId = $this->_getCompileId ($lang, $country);
        if (is_readable ($_compileResourceId)){
            if ($GLOBALS['COPIX']['CONFIG']->compile_resource_check || $GLOBALS['COPIX']['CONFIG']->compile_resource_forced){
                if ($GLOBALS['COPIX']['CONFIG']->compile_resource_forced){
                    //force compile, compiled files are never assumed to be ok.
                    $okcompile = false;
                }else{
                    // on verifie que les fichiers de ressources sont plus anciens que la version compilée
                    $compiledate = filemtime($_compileResourceId);
                    $okcompile   = true;//Compiled files are assumed to be ok.

                    foreach ($toLoad as $infos){
                        if (is_readable ($infos['file']) && filemtime($infos['file']) > $compiledate){
                            $okcompile = false;
                            break;
                        }
                    }
                }
            }else{
                //no compile check, it's ok then
                $okcompile = true;
            }

            if ($okcompile) {
                include ($_compileResourceId);
                $this->_messages[$country] = $_loaded;
                //everything was loaded.
                return;
            }
        }

        //loads the founded resources.
        foreach ($toLoad as $infos){
            if (is_readable ($infos['file'])){
                $this->_loadResources ($infos['file'], $infos['country']);
            }
        }

        //we want to use the PHP compilation of the resources.
        $first = true;
        $_resources = '<?php $_loaded=array (';
        if (isset ($this->_messages[$country])){
            foreach ($this->_messages[$country] as $key=>$elem){
                if (!$first){
                    $_resources .= ', ';
                }
                $elem = str_replace ('\\', '\\\\', $elem);
                $elem = str_replace ("'", "\\'", $elem);

                $key = str_replace ('\\', '\\\\', $key);
                $key = str_replace ("'", "\\'", $key);

                $_resources .= '\''.$key.'\'=>\''.$elem.'\'';
                $first = false;
            }
        }
        $_resources .= '); ?>';

        require_once (COPIX_UTILS_PATH . 'CopixFile.class.php');
        $objectWriter = & new CopixFile ();
        $objectWriter->write ($_compileResourceId, $_resources);
    }
    /**
    * Récupération de l'identifiant de compilation d'une ressource pour une langue / pays
    */
    function _getCompileId ($lang, $country){
        return $GLOBALS['COPIX']['CONFIG']->compile_resource_dir.str_replace (array (':', '|'), array ('_RESOURCE_', '_MODULE_'), strtolower ($this->fic->getSelector()).'_FOR_BUNDLE_'.$lang.'_'.$country).'.php';
    }

    /**
    * loads a given resource from its path.
    * Will be considered as lang_country
    *
    * @param string $path     the path to the properties file.
    * @param string $country the country
    */
    function _loadResources ($path, $country){
        $country = strtoupper ($country);

        if (($f = fopen ($path, 'r')) !== false) {
            $multiline=false;
            $linenumber=0;
            while (!feof($f)) {
                if($line=fgets($f,1024)){ // length required for php < 4.2
                    $linenumber++;

                    if($multiline){
                        if(preg_match("/^([^#]+)(\#?.*)$/", $line, $match)){ // toujours vrai en fait
                        $value=trim($match[1]);
                        if (strpos( $value,"\\u" ) !== false){
                            $value=$this->_utf16( $value );
                        }
                        if($multiline= (substr($value,-1) =="\\"))
                        $this->_messages[$country][$key].=substr($value,0,-1);
                        else
                        $this->_messages[$country][$key].=$value;
                        }
                    }elseif(preg_match("/^\s*(([^#=]+)=([^#]+))?(\#?.*)$/",$line, $match)){
                        if($match[1] != ''){
                            // on a bien un cle=valeur
                            $value=trim($match[3]);
                            if($multiline= (substr($value,-1) =="\\"))
                            $value=substr($value,0,-1);

                            $key=trim($match[2]);

                            if (strpos( $match[1],"\\u" ) !== false){
                                $key=$this->_utf16( $key );
                                $value=$this->_utf16( $value );
                            }
                            $this->_messages[$country][$key] =$value;
                        }else{
                            if($match[4] != '' && substr($match[4],0,1) != '#')
                                trigger_error('Syntaxe error in file properties '.$path.' line '.$linenumber, E_USER_NOTICE);
                        }
                    }else {
                        trigger_error('Syntaxe error in file properties '.$path.' line '.$linenumber, E_USER_NOTICE);
                    }
                }
            }
            fclose ($f);
        }else{
            trigger_error ('Cannot load the resource '.$path, E_USER_ERROR);
        }
    }

    /**
    * converts an utf16 string to html string.
    * @param   string $str   string to convert
    * @return   string   string converted to html
    */
    function _utf16 ( $str ) {
        while (ereg( "\\\\u[0-9A-F]{4}",$str,$unicode )) {
            $repl="&#".hexdec( $unicode[0] ).";";
            $str=str_replace( $unicode[0],$repl,$str );
        }
        return $str;
    }
}

class CopixI18N {
    var $_bundles;//[module][lang]

    function & instance (){
        static $instance = false;
        if ($instance === false){
            $instance = new CopixI18N ();
        }
        return $instance;
    }

   /**
    * Alias for the datetotimestamp method. Deprecated.
    * @see CopixI18N::dateToTimestamp
    * @deprecated
    */
    function dateToBD ($date, $separator = '/'){
       return CopixI18N::dateToTimestamp ($date, $separator);
    }

    /**
    * gets the current lang
    */
    function getLang (){
        return $GLOBALS['COPIX']['CONFIG']->default_language;
    }

    /**
    * Transform a given dateformat into a timestamp (YYYYMMDD)
    * @param string date the date to transform. Should be like DD/MM/YYYY
    * @return string the timestamp. Null if no date is given. False if the date format is incorrect
    */
    function dateToTimestamp ($date, $separator = '/'){
        //is the given date not null or not empty?
        if (($date === null) || (strlen ($date = trim ($date)) === 0)){
            return null;
        }

        //is the date have exactly 3 parts (day, month, year)
        if (count ($tmp = explode ($separator, $date)) !== 3){
           return false;
        }

        //gets the format & pos of each elements
        $format = CopixI18N::getDateFormat ($separator);

        //Very very weird thing to get the date in our requested format.
        //array of positions for day, month and year (D, M, Y) in the tab
        //we wants to order positions to get 0, 1, 2
        $positions = array ('d'=>strpos ($format, 'd'),
                            'm'=>strpos ($format, 'm'),
                            'Y'=>strpos ($format, 'Y'));
        //we know the first match will be 0 (at least we start with d m or Y)
        switch (array_search (0, $positions)){
           case 'd': if ($positions['m'] > $positions['Y']){
                        $positions['m'] = 2;
                        $positions['Y'] = 1;
                     }else{
                        $positions['m'] = 1;
                        $positions['Y'] = 2;
                     }
              break;
           case 'm': if ($positions['d'] > $positions['Y']){
                        $positions['d'] = 2;
                        $positions['Y'] = 1;
                     }else{
                        $positions['d'] = 1;
                        $positions['Y'] = 2;
                     }
              break;
           case 'Y': if ($positions['d'] > $positions['m']){
                        $positions['d'] = 2;
                        $positions['m'] = 1;
                     }else{
                        $positions['d'] = 1;
                        $positions['m'] = 2;
                     }
              break;
        }

        if (! checkdate ($tmp[$positions['m']], $tmp[$positions['d']], $tmp[$positions['Y']])){
            return false;
        }

        //the timestamp (YYYMMDD)
        return $tmp[$positions['Y']].$tmp[$positions['m']].$tmp[$positions['d']];
    }

    /**
    * Transform a timestamp into a given date format, according to the current language.
    * @param string timestamp. If timestamp is false,
    *
    * @return string the date. null if no timestamp is given. False is the timestamp is incorrect
    */
    function timestampToDate ($timestamp, $separator='/'){
       //check if a timestamp was given
       if (($timestamp !== false) && (($timestamp === null) || (strlen ($timestamp = trim ($timestamp)) === 0))){
          return null;
       }

       if ((strlen ($timestamp) !== 8) ||
           (! checkdate (substr ($timestamp, 4, 2), substr ($timestamp, 6, 2), substr ($timestamp, 0, 4))) ||
           (($timestamp = strtotime ($timestamp)) === -1)){
               return false;
       }

       return date (CopixI18N::getDateFormat ($separator), $timestamp);
    }
   /**
   * Transform a timestamp into a given time format, according to the current language.
   * @param string timestamp. If timestamp is false,
    *
    * @return string the time. null if no timestamp is given. False is the timestamp is incorrect
    */
    function timestampToTime ($timestamp, $separator=':'){
       //check if a timestamp was given
       if (($timestamp !== false) && (($timestamp === null) || (strlen ($timestamp = trim ($timestamp)) === 0))){
          return null;
       }
       $arTime=array();
       switch (strlen($timestamp)) {
       case 6 :
          $arTime[2]=substr($timestamp,4,2);
          if ($arTime[2] > 59) return false;
       case 4 :
          $arTime[1]=substr($timestamp,2,2);
          if ($arTime[1] > 59) return false;
       case 2 :
          $arTime[0]=substr($timestamp,0,2);
          if ($arTime[0] > 23) return false;
       }
       ksort($arTime);
       if (count($arTime)>0) {
          return implode(':',$arTime);
       }else{
          return false;
       }
    }


    /**
    * Gets the date format according to the current language / country
    */
    function getDateFormat ($separator){
       $lang    = CopixI18N::getLang ();
       $country = CopixI18N::getCountry ();
       switch ($lang){
          case 'fr': $format = "d".$separator."m".$separator."Y";break;
          case 'en': $format = "m".$separator."d".$separator."Y";break;
          trigger_error (CopixI18N::get ('copix:copix.error.i18n.unknowDateFormat', array ($lang, $country)), E_USER_ERROR);
       }
       return $format;
    }

    /**
    * gets the current country.
    */
    function getCountry (){
        return $GLOBALS['COPIX']['CONFIG']->default_country;
    }

    /**
    * gets the correct string, for a given language.
    *   if it can't get the correct language, it will try to gets the string
    *   from the default language.
    *   if both fails, it will raise a fatal_error.
    */
    function get ($key, $args=null, $locale=null) {
        $me = & CopixI18N::instance();

        //finds out required lang / coutry
        if ($locale === null){
                $lang    = $GLOBALS['COPIX']['CONFIG']->default_language; // rempli par le plugin egalement
                $country = $GLOBALS['COPIX']['CONFIG']->default_country;
        }else{
            $ext = explode ('_', $locale);
            if (count($ext) > 1){
                $lang    = $ext[0];
                $country = $ext[1];
            }else{
                $lang    = $ext[0];
                $country = $ext[0];
            }
        }

        //Gets the bundle for the given language.
        $keySelector = substr ($key, 0, strpos ($key, '.'));
        $trans     = & CopixSelectorFactory::create($keySelector);
        if (!$trans->isValid){
            trigger_error (CopixI18N::get ('copix:copix.error.i18n.keyNotExists', $key), E_USER_ERROR);
        }

        $key    = $me->_extractMessageKey ($key);
        $bundle = & $me->getBundle ($trans, $lang);

        //try to get the message from the bundle.
        $string = $bundle->get ($key, $country);
        if ($string === null){
            //if the message was not found, we're gonna
            //use the default language and country.
            if ($lang    == $GLOBALS['COPIX']['CONFIG']->default_language &&
                $country == $GLOBALS['COPIX']['CONFIG']->default_country){
                if ($key == 'copix:copix.error.i18n.keyNotExists'){
                    $msg = 'Can\'t find message key (which should actually be THIS message): '.$key;
                }else{
                    $msg = CopixI18N::get ('copix:copix.error.i18n.keyNotExists',$key);
                }
                trigger_error ($msg, E_USER_ERROR);
            }
            return $me->get ($key, $args, $GLOBALS['COPIX']['CONFIG']->default_language.'_'.$GLOBALS['COPIX']['CONFIG']->default_country);
        }else{
            //here, we know the message
            if ($args!==null){
                $string = call_user_func_array('sprintf', array_merge ($string, $args));
            }
            return $string;
        }
    }

    /**
    * extracting the message key
    * @param $key the message key we wants to extract
    * @return string the key only (withoout its file or resource informations)
    */
    function _extractMessageKey ($key){
        //static $knownKeys = array ();
        if (isset ($knownKeys[$key])){
            return $knownKeys[$key];
        }
        $parsedKey = $key;
        //extracting the message id
        if (($posPipe = strpos ($parsedKey, '|')) !== false){
            $parsedKey = substr ($parsedKey, $posPipe+1);
        }
        if (($posColon = strpos ($parsedKey, ':')) !== false){
            $parsedKey = substr ($parsedKey, $posColon+1);
        }
        $knownKeys[$key] = $parsedKey;
        return $knownKeys[$key];
    }

    /**
    * gets the bundle for a given language.
    */
    function & getBundle ($fileSelector, $lang){
        $s = $fileSelector->getSelector ();
        if (!isset ($this->_bundles[$s][$lang])){
            $this->_bundles[$s][$lang] = & new CopixBundle ($fileSelector, $lang);
        }
        return $this->_bundles[$s][$lang];
    }
}
?>