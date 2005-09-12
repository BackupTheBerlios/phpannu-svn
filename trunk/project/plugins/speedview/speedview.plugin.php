<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: speedview.plugin.php,v 1.7 2005/05/04 11:08:00 laurentj Exp $
* @author   Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class PluginSpeedView extends CopixPlugin {
   var $startTime = 0;
   var $stopTime = 0;
   var $elapsedTime = 0;
   var $speedprocess = false;

   function PluginSpeedView($config){
     parent::CopixPlugin($config);
   }

   function beforeSessionStart(){
     $this->startTime = $this->_getMicroTime();
   }

   /**
    * @param CopixAction   $copixaction   action courante
    */
   function beforeProcess(&$copixaction){
     if($this->config->trigger == 'action' && isset($copixaction->params['speedview'])
         && $copixaction->params['speedview'])
         $this->speedprocess=true;
   }

   /**
    * @param CopixActionReturn      $ToProcess
    */
   function afterProcess($actionreturn){

      $this->stopTime = $this->_getMicroTime();
      $this->elapsedTime = max(0, intval(($this->stopTime - $this->startTime)*1000) / 1000);

      switch($this->config->trigger){
         case 'action': break;
         case 'url':
            if(isset ($GLOBALS['COPIX']['COORD']->vars['showTimeCounter']) &&
               ($GLOBALS['COPIX']['COORD']->vars['showTimeCounter']==1))
               $this->speedprocess=true;
            break;
         case 'actionreturn':
            if($actionreturn->code == COPIX_AR_DISPLAY ||
               $actionreturn->code == COPIX_AR_DISPLAY_IN ||
               $actionreturn->code == COPIX_AR_STATIC)
               $this->speedprocess=true;
            break;
      }

      if($this->speedprocess){
         switch($this->config->target){
            case 'comment': $this->elapsedTime = '<!-- '.$this->elapsedTime.' -->';
            case 'display':
               if($actionreturn->code == COPIX_AR_DISPLAY ||
                  $actionreturn->code == COPIX_AR_DISPLAY_IN ||
                  $actionreturn->code == COPIX_AR_STATIC)
                  echo $this->elapsedTime;
               break;
            case 'debug': if(function_exists('debug')) debug($this->elapsedTime, 'Temps d\'execution'); break;
         }
      }
   }


   function _getMicroTime(){
      list($micro,$time) = explode (' ', microtime());
      return $micro + $time;
   }

}
?>
