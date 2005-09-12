<?php
/**
* @package   copix
* @subpackage copixtools
* @version   $Id: CopixCache.class.php,v 1.14 2005/03/17 16:00:12 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* implemente le comportement d'un cache sur une donnée (issu d'un fichier, sources ...)
* @package   copix
* @subpackage generaltools
*/
class CopixCache {
    /**
    * cache id
    * @var string
    * @access protected
    */
    var $_id;
    /**
    * cache type
    * @var string
    * @access protected
    */
    var $_type;
    /**
    * cache subtype
    * @var string
    * @access protected
    */
    var $_extension;

    /**
    * file name of the cache.
    * @var string
    * @access protected
    */
    var $_fileName;
    /**
    * active cache ?
    * @var boolean
    * @access protected
    */
    var $_enabled;

    /**
    * Constructeur
    * @param   boolean $enabled    indique si on active ou non le cache
    * @param   string  $type       type du cache. peut mettre aussi type|soustype. Type doit être déclaré dans la configuration de copix (cacheTypeDir)
    */
    function CopixCache($id, $type = null){
        if ($type === null){
            $type = $GLOBALS['COPIX']['CONFIG']->defaultCache;
        }
        $this->_id   = $id;
        $this->_makeTypeAndExtension ($type);
        $this->_makeFileName ();
        $this->_enabled = $this->enabled ();
    }

    /**
    * indique si le cache existe
    * @return  boolean     true: oui
    */
    function isCached (){
        return $this->_enabled && $this->_exists ();
    }

    /**
    * Détermine le nom de fichier du cache.
    */
    function _makeFileName (){
        //explosion en Type|SousType
        //Type jouant le role de répertoire, sous type d'extension supplémentaire.
        $fileMainName = md5 (serialize ($this->_id));
        if(isset($GLOBALS['COPIX']['CONFIG']->cacheTypeDir[$this->_type]))
        $this->_fileName = COPIX_CACHE_PATH.$GLOBALS['COPIX']['CONFIG']->cacheTypeDir[$this->_type].$this->_extension.$fileMainName.'.cache';
        else
        $this->_fileName ='';
    }

    /**
    * création des types / extension.
    */
    function _makeTypeAndExtension ($from){
        $elems = explode ('|', $from);
        $this->_type      = count ($elems) > 1 ? $elems[0] : $from;
        $this->_extension = count ($elems) > 1 ? $elems[1].'.' : '';
    }

    /**
    * Test si le cache actuel est actif.
    * @return    boolean resultat du test
    */
    function enabled (){
        if ($GLOBALS['COPIX']['CONFIG']->cacheEnabled){//cache global.
        if (!isset ($GLOBALS['COPIX']['CONFIG']->cacheTypeEnabled[$this->_type])){
            //pas de contredirective pour le cache de type
            return true;
        }else{
            //si contredirective, retourne sa valeur.
            return $GLOBALS['COPIX']['CONFIG']->cacheTypeEnabled[$this->_type];
        }
        }
        //pas de cache du tout
        return false;
    }

    /**
    * lit le cache.
    *
    * @return string    contenu du cache si il existe ou null
    */
    function read () {
        if ($this->isCached ()){
            require_once (COPIX_UTILS_PATH . 'CopixFile.class.php');
            $objectReader = & new CopixFile ();
            return $objectReader->read ($this->_fileName);
        }else{
            return null;
        }
    }

    /**
    * Ecriture dans le cache.
    * utilise un objet de type CopixFileLocker.
    *
    * @param string $ToWrite ce qu'il faut écrire dans le fichier.
    * @return boolean indique si l'ecriture a bien eu lieu
    */
    function write ( $toWrite ) {
        if ($this->_enabled){
            require_once (COPIX_UTILS_PATH . 'CopixFile.class.php');
            $objectWriter = & new CopixFile ();
            return $objectWriter->write ($this->_fileName, $toWrite);
        }else{
            return null;
        }
    }

    /**
    * Indique si le cache existe.
    * @return bool
    */
    function _exists (){
        return is_readable ($this->_fileName);
    }

    /**
    * efface le cache d'un type donné.
    */
    function clear ($type = null){
        if ($type === null){
            CopixCache::_clearAll ();
        }else{
            CopixCache::_clearType ($type);
        }
    }

    /**
    * Efface l'élément courant.
    */
    function remove (){
        if(file_exists($this->_fileName))
        unlink ($this->_fileName);
    }

    /**
    * efface le cache d'un type donné.
    */
    function _clearType ($type){
        $elems = explode ('|', $type);
        $cacheType = count ($elems) > 1 ? $elems[0] : $type;
        $cacheExtension = count ($elems) > 1 ? $elems[1].'.' : null;

        if (isset($GLOBALS['COPIX']['CONFIG']->cacheTypeDir[$cacheType])){
            $dir = COPIX_CACHE_PATH . $GLOBALS['COPIX']['CONFIG']->cacheTypeDir[$cacheType];
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..'){
                        if (is_file ($dir.$file) && !is_dir ($dir.$file)){
                            if ($cacheExtension !== null){
                                if (strpos ($file, $cacheExtension) === 0){
                                    unlink ($dir.$file);
                                }
                            }else{
                                unlink ($dir.$file);
                            }
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
    * supression de tous les types de cache.
    */
    function _clearAll (){
        foreach ($GLOBALS['COPIX']['CONFIG']->cacheTypeDir as $key=>$elem){
            CopixCache::_clearType ($key);
        }
    }
}
?>