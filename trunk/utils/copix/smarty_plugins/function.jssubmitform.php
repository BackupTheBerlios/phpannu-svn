<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.jssubmitform.php,v 1.4 2005/02/17 16:19:00 gcroes Exp $
* @author   Bertrand Yan
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
 * Plugin smarty type fonction
 * Purpose:  send form by javascript to given href.
 *
 * Input:    href     = (required)  where to send the form
 *           form     = (required) id of the form
 *           assign   = (optional) name of the template variable we'll assign
 *                      the output to instead of displaying it directly
 *
 * Examples:
 */
function smarty_function_jssubmitform($params, &$this) {

   if (!isset ($params['href'])){
     $this->_trigger_fatal_error("[smarty jssubmitform] Missing href parameter");
     return;
   }
   
   if (!isset ($params['form'])){
     $this->_trigger_fatal_error("[smarty jssubmitform] Missing form parameter");
     return;
   }


   static $_init = false;

    if (! $_init){
       $jsCode = 'function doSubmitForm (pUrl, formId) {
                     var myForm = document.getElementById(formId);
                     myForm.action = pUrl;
                     myForm.submit ();
                     return false;
                  }';
       CopixHtmlHeader::addJsCode ($jsCode);
       $_init = true;
    }
    
    $toReturn = 'return doSubmitForm(\''.$params['href'].'\', \''.$params['form'].'\')';
    
    if (isset ($params['assign'])){
       $this->assign($params['assign'], $toReturn);
       return '';
    }else{
       return $toReturn;
    }

}
?>