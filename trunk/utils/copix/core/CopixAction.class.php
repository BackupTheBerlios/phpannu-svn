<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixAction.class.php,v 1.10 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Objet de description des actions normales
*
* Décrit une action copix à effectuer : nom de la methode, nom de la classe CopixPage à instancier
* Les objets CopixAction sont à utiliser dans les fichiers desc.
* Ceux ci déclarent le lien entre les actions indiqués dans les urls
* (paramètre action par défaut), et les objets CopixPage.
*
* <code>
* $monAction= & new CopixAction("MonObjetPage", "methodeAExecutee");
* </code>
*
* @package   copix
* @subpackage core
* @see CopixCoordination
*/
class CopixAction {
    /**
    * Le type d'action a mener. vaut COPIX_ACTION_TYPE_OBJ. à modifier dans les classes filles.
    * @var int
    */
    var $type;
    /**
    * identifiant de l'objet à utiliser dans le cas de Type = COPIX_ACTION_TYPE_OBJ
    * @var string
    */
    var $useObj;
    /**
    * le nom de la méthode à utiliser de l'objet.
    * @var string
    */
    var $useMeth;
    /**
    * contient les parametres destinés aux plugins
    * @var array
    */
    var $params;
    /**
    * @var CopixModuleFileSelector le selecteur de fichier à exécuter.
    */
    var $file;


    /**
    * Contructeur.
    *
    * @param string $UseObj l'identifiant de l'objet à utiliser. (le nom de l'objet réel peut être compléter par  des préfixes / suffixes automatiques, cf le coordinateur de module)
    * @param string $UseMeth l'identifiant de la méthode de l'objet à utiliser.
    * @param mixed   $params tableau associatif de paramètre qui seront traités par les plugins
    */
    function CopixAction ($useObj, $useMeth, $params = array ()){
        $this->type    = COPIX_ACTION_TYPE_OBJ;
        $this->useMeth = $useMeth;
        $this->useObj  = $useObj;
        $this->params  = $params;
        $this->file = new CopixModuleFileSelector ($useObj);
    }


}

/**
* Objet de description des actions "fichiers"
*
* CopixActionFile sert à faire le lien entre une action et,
* non pas un objet CopixPage mais un fichier PHP ne faisant pas parti du
* framework.
* A n'utiliser CopixActionFile que dans des conditions bien particulières !
* <code>
* $monAction= & new CopixActionFile('monFichier.php');
* </code>
*
* @package   copix
* @subpackage core
* @see CopixCoordination
*/
class CopixActionFile extends CopixAction {
    /**
    * Contructeur.
    *
    * @param string $UseFile nom du fichier à utiliser
    * @param mixed   $params tableau associatif de paramètre qui seront traités par les plugins
    */
    function CopixActionFile ($UseFile, $params =null){
        $this->file = new CopixModuleFileSelector ('');//current, we don't care, there's no use for that.

        $this->type    = COPIX_ACTION_TYPE_FILE;
        $this->useFile  = $UseFile;
        if($params != null){
            $this->params = $params;
        }
    }

    /**
    * Le nom du fichier à insérer.
    * @var string
    */
    var $useFile;
}

/**
* Pour les redirections automatiques depuis les fichiers de description.
*/
class CopixActionRedirect  extends CopixAction {
    /*
    * @param string $UseFile nom du fichier à utiliser
    * @param mixed   $params tableau associatif de paramètre qui seront traités par les plugins
    */
    function CopixActionRedirect ($useUrl, $params =null) {
        $this->file = new CopixModuleFileSelector ('');//current, we don't care, there's no use for that.

        $this->type    = COPIX_ACTION_TYPE_REDIRECT;
        $this->url  = $useUrl;
        if($params != null){
            $this->params = $params;
        }
    }
}

/**
* Pour les fichiers statiques (html souvent)
*/
class CopixActionStatic  extends CopixAction {
    /*
    * @param string $UseFile nom du fichier à utiliser
    * @param mixed  $params tableau associatif de paramètre qui seront traités par les plugins
    */
    function CopixActionStatic ($useFile, $more = array (), $params =null){
        $this->file = new CopixModuleFileSelector ($useFile);

        $this->type = COPIX_ACTION_TYPE_STATIC;
        $this->more  = $more;
        $this->useFile = $useFile;
        if($params != null){
            $this->params = $params;
        }
    }
}

/**
* Pour afficher directement des zones dans la zone principale du template du processus standard
*/
class CopixActionZone  extends CopixAction {
    var $titlePage  = null;
    var $titleBar   = null;
    var $zoneParams = array ();
    var $zoneId     = null;
    var $params     = array ();

    function CopixActionZone ($zoneId, $more = array (), $params = null){
        $this->file = new CopixModuleFileSelector ($zoneId);

        if (isset ($more['TITLE_PAGE'])){
            $this->titlePage = $more['TITLE_PAGE'];
        }
        if (isset ($more['TITLE_BAR'])){
            $this->titleBar = $more['TITLE_BAR'];
        }
        if (isset ($more['Params'])){
            $this->zoneParams = $more['Params'];
        }
        if($params != null){
            $this->params = $params;
        }

        $this->type   = COPIX_ACTION_TYPE_ZONE;
        $this->more   = $more;
        $this->zoneId = $zoneId;
    }
}
?>