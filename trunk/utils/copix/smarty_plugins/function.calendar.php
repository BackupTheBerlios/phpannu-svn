<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.calendar.php,v 1.5 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     calendar
 * Version:  1
 * Date:     May 21, 2002
 * input: type :
 * Examples: {calendar name="test_date"}
 * -------------------------------------------------------------
 */
function smarty_function_calendar($params, &$smarty) {
    static $_init = false;
 
    extract($params);
    //check the initialisation
    if (! $_init){
       //path of the library
       CopixHTMLHeader::addCSSLink (CopixUrl::get ()."js/dynCal/dynCalendar.css", array ('media'=>'screen'));
       CopixHTMLHeader::addJSLink  (CopixUrl::get ()."js/dynCal/browserSniffer.js");
       CopixHTMLHeader::addJSLink  (CopixUrl::get ()."js/dynCal/dynCalendar.js");
       if (empty ($lang)){
          $lang = CopixI18N::getLang ();
       }
       CopixHTMLHeader::addJSLink  (CopixUrl::get ()."js/dynCal/lang/".$lang.".js");
       $_init = true;
    }
 
    //Calculating the jsCode (kind of silly trick to use a separator as a part of jsCode, but still quick)
    $jsCode = str_replace (array ('d', 'm', 'Y'), array ('day', 'month', 'year'), CopixI18N::getDateFormat (" + '/' + "));
 
    CopixHTMLHeader::addJSCode ("\n\r".'
                        function calendarCallback'.$name.'(day, month, year) {
            var tmp;
            if (String(month).length == 1) {
               month = \'0\' + month;
            }
            if (String(day).length == 1) {
               day = \'0\' + day;
            }
            tmp = document.getElementById (\''.$name.'\');
                                   tmp.value = '.$jsCode.';
                        }
    '."\n\r");
 
    //name of the textarea.
    if (empty ($name)){
        $smarty->trigger_error('[smarty_calendar] missing name parameter');
    }else{
      $out = '<input type="text" class="calendar" id="'.$name.'" name="'.$name.'" value="'.$value.'">'."\n\r";
      $out.= '<script language="javascript" type="text/javascript">'."\n\r";
      $out.= '<!--'."\n\r";
      $out.= 'calendar_'.$name.' = new dynCalendar(\'calendar_'.$name.'\', \'calendarCallback'.$name.'\', \''.CopixUrl::get ().'js/dynCal/images/\');'."\n\r";
      $out.= '//-->'."\n\r";
      $out.='</script>'."\n\r";
    }
    return $out;
}
?>