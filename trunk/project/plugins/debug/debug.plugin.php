<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: debug.plugin.php,v 1.5 2005/02/09 08:29:09 gcroes Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * plugin permettant le debuggage
 * on n'a pas besoin de traitement en cours de fonctionnement du framework : inutile de surcharger les differentes fonctions.
 */
class PluginDebug extends CopixPlugin {
   function PluginDebug($config){
      parent::CopixPlugin($config);
      $GLOBALS['COPIX']['DEBUG'] = & $this;
   }

   /**
    * avant la recuperation de la session
    */
   function beforeSessionStart(){
      $this->addInfo('START','Debuggage',0);
   }

   /*
   * Demande d'ajout d'information par le débugger
   *
   * @param string      $Infos      l'information a ajouter
   * @param string      $from       l'identité du demandeur de l'ajout
   * @param integer     $Lvl        le niveau du débuggage de l'information ajoutée
   * @param boolean     $NoLog      n'enregistre pas le message dans le fichier de log
   * @return void
   * @access public
   */
   function addInfo ($Infos, $From='', $Lvl=0, $NoLog=false){
       if($this->config->dumpArrayObject && (is_array($Infos) || is_object($Infos))){
         include_once(COPIX_UTILS_PATH.'CopixDebugObjectViewer.class.php');
         $dbgv = & new CopixDebugObjectViewer($Infos);
         $Infos=$dbgv->toString();
      }
        if(is_bool($Infos)){
             $Infos= '( boolean ) '.($Infos?'true':'false');
        }

         if ($Lvl <= $this->config->level){ //test le niveau de débuggage demandé.

                        $ip = $this->GetIp ();

            if ((!$this->config->ipFilter) || ($this->config->ipFilter && in_array ($ip, $this->config->ipArray))){
                        //Si niveau normal ou si Niveau Ip et que l'ip est dans les ip à tracker.
                           //Conditions remplies, on rensigne les informations.
               $datetime = date ("Y-m-d H:i:s");
               //Ecriture de la chaine dans un fichier de log si demandé
               if ($this->config->useLogFile && !$NoLog && $this->config->logFile !=''){
               $StrDebug=strtr($this->config->messageFormat, array(
                     '%date%' => $datetime,
                     '%ip%' => $ip,
                     '%msg%'  => $Infos,
                     '%from%' => $From
                     ));
               $filename=COPIX_LOG_PATH.strtr($this->config->logFile, array('%ip%'=>$ip));

               error_log($StrDebug,3, $filename);
               }
               // affichage de la chaine si demandée
               if ($this->config->toDisplay){
               $messageToDisplay=strtr($this->config->messageFormatDisplay, array(
                  '%date%' => $datetime,
                  '%ip%' => $ip,
                  '%msg%'  => $Infos,
                  '%from%' => $From
                  ));
               echo $messageToDisplay;
               }
            }
         }
   }//addInfo.

   /**
   * récupère l'adresse ip du visiteur.
   * @return string    l'adresse ip
   */
   function getIp (){
        $ip = $_SERVER['REMOTE_ADDR'];
        $forwarder = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'');
        if (($forwarder != '')&&($forwarder != 'unknown')){
            $ip = $forwarder;
        }
        return $ip;
   }

}

/**
 * alias raccourci pour le debugage
 */
function debug($infos, $from='', $level=0){
   $GLOBALS['COPIX']['DEBUG']->addInfo($infos, $from,$level);
}
?>
