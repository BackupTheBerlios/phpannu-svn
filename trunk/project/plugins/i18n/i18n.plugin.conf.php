<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: i18n.plugin.conf.php,v 1.8 2005/04/25 15:59:15 laurentj Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


class PluginConfigI18n {

    /**
     * codes des langages disponibles sur le site
     */
    var $availableLanguageCode = array('fr','en');

    /**
    * code language par defaut
    */
    var $defaultLanguageCode='fr';

   /**
    * code monnaie par defaut
    */
   var $defaultCurrencyCode='EUR';

   /**
    * deuxieme code monnaie par defaut  (utile pour le double affichage)
    * chaine vide : pas de seconde monnaie
    */
   var $defaultAlternateCurrencyCode='FRF';

   /**
    * utilisation du language indiqué dans le navigateur
    */
   var $useDefaultLanguageBrowser=false;

   /**
    * active la detection du changement de language via l'url fournie
    */
   var $enableUserLanguageChoosen=true;
   /**
    * active la detection du changement de monnaie via l'url fournie
    */
   var $enableUserCurrencyChoosen=true;

   /**
    * indique le nom du parametre url qui contient la langue choisie par l'utilisateur
    */
   var $urlParamNameLanguage='lang';

   /**
    * indique le nom du parametre url qui contient la monnaie choisie par l'utilisateur
    */
    var $urlParamNameCurrency='curr';


   /**
    * indique si on doit selectionner une base differente en fonction de la langue
    */
   var $useSpecificDbProfil = false;

}


?>