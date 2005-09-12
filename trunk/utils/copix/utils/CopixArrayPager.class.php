<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: CopixArrayPager.class.php,v 1.5 2005/02/09 08:21:44 gcroes Exp $
* @author   Bertrand Yan
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


class CopixArrayPager extends CopixPager {

    /**
     * Tableau des donn�es � traiter
     *
     * Valeur par d�faut : tableau vide
     * @var string $query
     * @see createSQL(), sql2array()
     */
   var $recordSet;

    function CopixArrayPager($options) {
        $this-> recordSet = '';
        parent::CopixPager($options);
    }


    /**
     * Retourne le nombre d'enregistrement contenu dans le tableau des donn�es
     *
     * @access private
     * @since 3.2
     */
    function getNbRecord() {
        return count($this-> recordSet);
    } // end func getNbRecord



    /**
     * Retourne le tableau des donn�es "d�coup�"
     *
     * @access private
     * @return array
     * @since 3.2
     */
    function getRecords() {

        $aTmp = Array();

        for ($i = $this-> firstline; $i < ($this-> firstline + $this-> perPage); $i++) {
            $aTmp[$i] = $this-> recordSet[$i];

            if (!isSet($this-> recordSet[$i+1])) {
                break;
            }
        }

        return $aTmp;
    } // end func getRecords

    /**
     * Initialisation de la classe mode tableau
     *
     * @access private
     * @return void
     * @since 3.2
      */
    function init() {
        if (!is_array($this-> recordSet)) trigger_error('Propri�t� <b>recordSet</b> mal configur�e <br>', E_USER_ERROR);
    }

    /**
     * Termine l'appel � la classe
     *
     * @access public/private
     * @return void
     * @since 3.2
      */
    function close() {
        unset($this-> recordSet);
        return true;
    }
}
?>