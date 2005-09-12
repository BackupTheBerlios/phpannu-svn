<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixZone.class.php,v 1.11.4.1 2005/08/01 22:17:54 laurentj Exp $
* @author   Croes G�rald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* Squelette d'un objet capable de g�rer une zone avec un cache.
*
* @package   copix
* @subpackage core
* @abstract
* @see CopixCoordination
*/
class CopixZone {
    /**
    * If we're using cache in this zone
    * @var boolean
    */
    var $_useCache = false;

    /**
    * nom des parametres de la zone permettant de l'identifiant de fa�on unique
    * @var array
    */
    var $_cacheParams = array ();

    /**
    * Param�tres d'ex�cution pass�s � la zone.
    */
    var $params;

    /**
    * Replaces the old (and long) syntax $GLOBALS['COPIX']['COORD']->processZone (something, $params);
    * is'nt this cleaner: CopixZone::process (something, $params); ?
    * @static
    */
    function process ($name, $params=array ()){
        return $GLOBALS['COPIX']['COORD']->processZone ($name, $params);
    }

    /**
    * Replaces the old (and long) syntax $GLOBALS['COPIX']['COORD']->clearZone (something, $params);
    * is'nt this cleaner: CopixZone::clear (something, $params); ?
    * @static
    */
    function clear ($name, $params=array ()){
        return $GLOBALS['COPIX']['COORD']->clearZone ($name, $params);
    }

    /**
    * M�thode qui g�re la zone
    * Selon si le cache doit �tre utilis�, et est valide ou non, on retournera le contenu du cache
    * ou on calculera la zone puis la retournera apr�s l'avoir stock�e de nouveau dans le cache
    * @param array  $Params les param�tres de contexte pour la zone. (g�n�ralement le contenu de l'url)
    * @return   string  le contenu de la zone
    * @access public
    */
    function processZone ($params = array ()){
        $this->params = & $params;

        //if (count ($this->_cacheParams) > 0){
        if ($this->_useCache){
            $module = CopixContext::get ();
            /* if ($module == '') $module.='_'; */

            $cache = & new CopixCache ($this->_makeId (), 'zones|'.$module.get_class($this));
            if(($contents = $cache->read ()) === null){
                if ($this->_createContent($contents)){
                    $cache->write($contents);
                }
            }
            unset ($cache);
        }else{
            $this->_createContent($contents);
        }
        return $contents;
    }

    /**
    * M�thode qui efface le cache de la zone
    * @param array  $Params les param�tres de contexte pour la zone.
    * @return   boolean  si tout s'est bien pass�
    * @access public
    */
    function clearZone ($params = array ()){
        $this->params = $params;
        //if (count ($this->_cacheParams) > 0){
        if ($this->_useCache){
            $module=CopixContext::get ();
            if($module .= '') $module.='_';

            $cache = & new CopixCache ($this->_makeId (), 'zones|'.$module.get_class($this));
            $cache->remove();
        }
        return true;
    }

    /**
    * M�thode de cr�ation de contenu pour la zone.
    *
    * Contient le processus de r�cup�ration et de cr�ation de contenu a partir des param�tres donn�s.
    * C'est cette m�thode qui sera invoqu�e par processZone pour cr�er le contenu
    * s'il n'existe pas en cache
    * @param string   $ToReturn   contient le contenu de la zone, � recuperer apr�s appel de la methode
    * @return boolean   indique si on peut mettre le contenu g�n�r� en cache ou pas
    * @access protected
    * @abstract
    */
    function _createContent (&$toReturn){
        return false;
    }

    /**
    * cr�ation de l'identifiant � partir des param�tres de la zone.
    * @access private
    */
    function _makeId (){
        $toReturn = array ();
        foreach ($this->_cacheParams as $key){
            $toReturn[$key] = isset ($this->params[$key]) ? $this->params[$key] : null;
        }
        return $toReturn;
    }

    /**
    * gets the value of a parameter, if defined. Returns the default value instead.
    * @param string $paramName the parameter name
    * @param mixed $paramDefaultValue the parameter default value
    * @return mixed the param value
    */
    function getParam ($paramName, $paramDefaultValue=null){
       return array_key_exists ($paramName, $this->params) ? $this->params[$paramName] : $paramDefaultValue;
    }
}
?>