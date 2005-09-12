<?php
/**
* @package   copix
* @subpackage core
* @version   $Id: CopixAction.class.php,v 1.10 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes G�rald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Objet de description des actions normales
*
* D�crit une action copix � effectuer : nom de la methode, nom de la classe CopixPage � instancier
* Les objets CopixAction sont � utiliser dans les fichiers desc.
* Ceux ci d�clarent le lien entre les actions indiqu�s dans les urls
* (param�tre action par d�faut), et les objets CopixPage.
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
    * Le type d'action a mener. vaut COPIX_ACTION_TYPE_OBJ. � modifier dans les classes filles.
    * @var int
    */
    var $type;
    /**
    * identifiant de l'objet � utiliser dans le cas de Type = COPIX_ACTION_TYPE_OBJ
    * @var string
    */
    var $useObj;
    /**
    * le nom de la m�thode � utiliser de l'objet.
    * @var string
    */
    var $useMeth;
    /**
    * contient les parametres destin�s aux plugins
    * @var array
    */
    var $params;
    /**
    * @var CopixModuleFileSelector le selecteur de fichier � ex�cuter.
    */
    var $file;


    /**
    * Contructeur.
    *
    * @param string $UseObj l'identifiant de l'objet � utiliser. (le nom de l'objet r�el peut �tre compl�ter par  des pr�fixes / suffixes automatiques, cf le coordinateur de module)
    * @param string $UseMeth l'identifiant de la m�thode de l'objet � utiliser.
    * @param mixed   $params tableau associatif de param�tre qui seront trait�s par les plugins
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
* CopixActionFile sert � faire le lien entre une action et,
* non pas un objet CopixPage mais un fichier PHP ne faisant pas parti du
* framework.
* A n'utiliser CopixActionFile que dans des conditions bien particuli�res !
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
    * @param string $UseFile nom du fichier � utiliser
    * @param mixed   $params tableau associatif de param�tre qui seront trait�s par les plugins
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
    * Le nom du fichier � ins�rer.
    * @var string
    */
    var $useFile;
}

/**
* Pour les redirections automatiques depuis les fichiers de description.
*/
class CopixActionRedirect  extends CopixAction {
    /*
    * @param string $UseFile nom du fichier � utiliser
    * @param mixed   $params tableau associatif de param�tre qui seront trait�s par les plugins
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
    * @param string $UseFile nom du fichier � utiliser
    * @param mixed  $params tableau associatif de param�tre qui seront trait�s par les plugins
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