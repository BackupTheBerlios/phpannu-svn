<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: copixdb.plugin.conf.php,v 1.13 2005/05/02 14:18:18 gcroes Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

require_once (COPIX_PATH . 'db/CopixDbFactory.class.php');

class PluginConfigCopixDb {

    /**
   * @var boolean     si activé lorsque l'utilisateur ajoute showRequest=1 dans l'url, les requêtes effectuées en
   * base sont affichées. Uniquement celles passant par les CopixDB.... bien sur.
   */
    var $showQueryEnabled = true;

    /**
    * If Copix should close connexions at the end of the script
    * You may not need this featre as PHP do it by itself, still, you may encounter weird problem with
    * several databases, then you should try this option
    */
    var $closeConnectionsOnShutdown = false;

    /**
   * @var boolean
   * Chargement de la config depuis fichier xml ou non
   */
    var $fromXml = true;
    var $configFile ='profils.definition.xml';

    // private, don't touch
    var $profils;//tab of configs/
    var $default;//the name of the default database acces
    /**
   * constructor, we here initialize the several connections.
   */
    function PluginConfigCopixDb (){
        // chargement du fichier XML
        if ($this->fromXml === true){
            require_once (COPIX_UTILS_PATH.'CopixSimpleXml.class.php');
            $xmlParser = & new CopixSimpleXML ();
            $fileName  = COPIX_PLUGINS_PATH.'copixdb/'.$this->configFile;
            if (file_exists($fileName)) {
                if (! ($parsedFile     = $xmlParser->parseFile ($fileName))){
                    $xmlParser->raiseError ();
                }
                $this->profils  = $this->_loadFrom($parsedFile);
                $defaultProfil  = $parsedFile->GENERAL->DEFAULTPROFIL->attributes ();
                $this->default  = $defaultProfil['NAME'];
            }
        }else{
            $this->_defineProfiles ();
        }
        if ($this->closeConnectionsOnShutdown){
            register_shutdown_function ('_COPIX_DB_SHUTDOWN_FUNCTION');
        }
    }

    /**
   * Load profils from xlm parsed file
   * @params object $parsedFile xml parsed file
   * @return array of profils
   * @access private
   */
    function _loadFrom(& $parsedFile) {
        $toReturn = array ();
        if (is_array ($parsedFile->PROFILS->PROFIL)){
            foreach ($parsedFile->PROFILS->PROFIL as $profil){
                $attributes = $profil->attributes ();
                $toReturn[$attributes['NAME']] = $this->_createProfil($attributes);
            }
        }else{
            $attributes = $parsedFile->PROFILS->PROFIL->__attributes;
            $toReturn[$attributes['NAME']] = $this->_createProfil($attributes);
        }
        return $toReturn;
    }

    /**
   * Create a new copixDBprofil from XMLattribute
   * @params object  $attributes xml attributes
   * @return CopixDbProfil
   * @access private
   */
    function _createProfil ($attributes) {
        $attributes['SHARED']      = ($attributes['SHARED']      == "false") ? false : true;
        $attributes['PERSISTANCE'] = ($attributes['PERSISTANCE'] == "false") ? false : true;
        return new CopixDbProfil (
        $attributes['DRIVER'],
        $attributes['DATABASE'],
        $attributes['HOST'],
        $attributes['USER'],
        $attributes['PASSWORD'],
        $attributes['PERSISTANCE'],
        $attributes['SHARED'],
        $attributes['SCHEMA']);
    }

    /**
    * Define profiles without any XML request (for speed improvements)
    */
    function _defineProfiles (){
        $this->profils['Select'] = & new CopixDbProfil (
        'mysql',
        'copix',
        'localhost',
        'root',
        '',
        true,
        true,
        '');

        $this->default = 'Select';
    }

}
?>