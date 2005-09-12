<?php
/**
* @package   copix
* @subpackage events
* @version   $Id: CopixListenerFactory.class.php,v 1.10 2005/04/05 15:06:08 gcroes Exp $
* @author   Croes Gérald, Patrice Ferlet
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Listener Factory.
*/
class CopixListenerFactory {
    /**
    * handles the listeners singleton (all listeners will be stored in here)
    *    events are stored by events listened
    * @var array of CopixListener
    */
    var $_listenersSingleton = array ();

    /**
    * hash table for evet listened.
    * $_hash['eventName'] = array of events (by reference)
    * @var associative array of object
    */
    var $_hashListened = array ();

    /**
    * here we keep all the declared listeners (name, module, events listened)
    * We'll use this to know "what to compile".
    */
    var $_eventInfos = null;

    /**
    * singleton of create
    * @see CopixListenerFactory::_create ();
    */
    function & create ($module, $id){
        $me = & CopixListenerFactory::instance ();
        return $me->_create ($module, $id);
    }

    /**
    * @param string $eventName the event name we wants the listeners for.
    * @return array of objects
    */
    function createFor ($eventName) {
        $me = & CopixListenerFactory::instance ();
        $me->_loadListeners ();
        $me->_createForEvent ($eventName);

        return $me->_hashListened[$eventName];
    }

    /**
    * Says if we have to compile the file
    */
    function _mustCompile (){
        if ($GLOBALS['COPIX']['CONFIG']->listeners_force_compile){
            return true;
        }

        //no compiled file ?
        if (!is_readable ($this->_compiledFileName())){
            return true;
        }

        if ($GLOBALS['COPIX']['CONFIG']->listeners_compile_check){
            $compilationTime = filemtime ($this->_compiledFileName ());
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
        return false;//no need to compile again
    }

    /**
    * Compilation des listeners
    */
    function _loadListeners () {
        //have we compiled or load this before ?
        if ($this->_eventInfos === null) {
            require_once (COPIX_EVENTS_PATH.'CopixListener.class.php');

            //we have to compile, then go trhougth the modules.
            if ($this->_mustCompile ()){
                $listenersToLoad = array ();
                require_once (COPIX_UTILS_PATH.'CopixSimpleXml.class.php');

                $modulesList = CopixModule::getList();
                $parser      = & new CopixSimpleXml ();

                foreach ($modulesList as $dir) {
                    $xmlFilename = COPIX_MODULE_PATH.$dir.'/module.xml';
                    if (is_readable ($xmlFilename)){
                        if (!($xml = & $parser->parseFile ($xmlFilename))){
                            $parser->raiseError ();
                        }
                        if (isset ($xml->EVENTS->LISTENERS->LISTENER)){
                            foreach (is_array ($xml->EVENTS->LISTENERS->LISTENER) ? $xml->EVENTS->LISTENERS->LISTENER : array ($xml->EVENTS->LISTENERS->LISTENER) as $listener){
                                $listenTo = array ();
                                foreach (is_array ($listener->EVENT) ? $listener->EVENT : array ($listener->EVENT) as $eventListened){
                                    $attributes = $eventListened->attributes ();
                                    $listenTo[] = $attributes['NAME'];
                                }
                                $attributes = $listener->attributes ();
                                //Before we had this
                                //$listenersToLoad[] = array ('dir'=>$dir, 'name'=>$attributes['NAME'], 'listenTo'=>$listenTo);
                                //For speed purposes, we did 0==dir, 1==name, listenTo==2
                                //this is significant enougth to keep this
                                $listenersToLoad[] = array (0=>$dir, 1=>$attributes['NAME'], 2=>$listenTo);
                            }
                        }
                    }
                }
                $this->_writePHPCode ($listenersToLoad);
            } else {
                //We load the PHP Code
                require ($this->_compiledFileName ());
            }

            //now we load listeners.
            foreach ($listenersToLoad as $arListenerInformation) {
                $this->_addListenerInfo($arListenerInformation[0], $arListenerInformation[1], $arListenerInformation[2]);
            }
        }
    }

    /**
    * Write PHP Code from a given array of listeners we wants to laod.
    * @param array $listenersToLoad the array of listeners we wants to write into PHP
    */
    function _writePHPCode ($listenersToLoad){
        //generating the PHP Code
        $first = true;
        $_resources = '<?php $listenersToLoad=array (';
        foreach ($listenersToLoad as $key=>$elem){
            //parcours des tableaux 0=>array
            if (!$first){
                $_resources .= ', ';
            }
            $listenerString  = 'array (\''.str_replace ("'", "\\'", $elem[0]).'\', ';
            $listenerString .= '\''.str_replace ("'", "\\'", $elem[1]).'\', ';

            //Création de la partie tableau d'éléments écoutés.
            $listenToString = 'array (';
            $firstElem = true;
            foreach ($elem[2] as $elemKey=>$elemValue){//Speed purposes : 2 is "listenTo"
            if (!$firstElem){
                $listenToString .= ', ';
            }
            //$listenToString .= '\''.str_replace ("'", "\\'", $elemKey).'\'=>\''.str_replace ("'", "\\'", $elemValue).'\'';
            $listenToString .= '\''.str_replace ("'", "\\'", $elemValue).'\'';
            $firstElem = false;
            }
            $listenToString .= ')';
            $listenerString .= $listenToString.')';
            $first = false;
            $_resources .= $listenerString;
        }
        $_resources .= '); ?>';

        //writing the PHP code to the disk
        require_once (COPIX_UTILS_PATH .'CopixFile.class.php');
        $objectWriter = & new CopixFile ();
        $objectWriter->write ($this->_compiledFileName (), $_resources);
    }

    /**
    * Gets the compiled fileName.
    * @return string the compiled fileName.
    */
    function _compiledFileName (){
        return $GLOBALS['COPIX']['CONFIG']->listeners_compile_path.'listeners.instance.php';
    }

    /**
    * Clear the compiled file
    */
    function clearCompiledFile (){
        if (is_file (CopixListenerFactory::_compiledFileName())){
            unlink (CopixListenerFactory::_compiledFileName());
        }
    }

    /**
    * singleton
    * @return CopixListenerFactory.
    */
    function & instance () {
        static $me = false;
        if ($me === false) {
            $me = new CopixListenerFactory ();
        }
        return $me;
    }

    /**
    * adds a gieven listener informations.
    * @param string $listenerModule le nom du module auquel appartient le listener.
    * @param string $listenerName   le nom du listener
    * @param string $eventListened  la liste des événements que l'on écoute avec ce listener
    */
    function _addListenerInfo ($listenerModule, $listenerName, $eventListened) {
        $this->_eventInfos[$listenerModule][$listenerName] = $eventListened;
    }

    /**
    * Creates listeners for the given eventName
    * @param string eventName the eventName we wants to create the listeners for
    */
    function _createForEvent ($eventName) {
        if (! isset ($this->_hashListened[$eventName])){
            foreach ((array)$this->_eventInfos as $module=>$events){
                foreach ($events as $listenerName=>$eventsInfos){
                    if (in_array ($eventName, $eventsInfos)){
                        $this->_hashListened[$eventName][] = & CopixListenerFactory::create ($module, $listenerName);
                    }
                }
            }
        }
    }

    /**
    * creates a single listener
    */
    function & _create ($module, $listenerName){
        if (! isset ($this->_listenersSingleton[$module][$listenerName])){
            //if (! is_readable ($fileName = )){
            //    trigger_error (CopixI18N::get ('copix:copix.error.noListener', array ($eventName, $fileName)), E_USER_ERROR);
            //}else{
                require_once (COPIX_MODULE_PATH.$module.'/'.COPIX_CLASSES_DIR.strtolower ($listenerName).'.listener.class.php');
                $className = 'Listener'.$listenerName;
                $this->_listenersSingleton[$module][$listenerName] = & new $className ();
            //}
        }
        return $this->_listenersSingleton[$module][$listenerName];
    }
}
?>