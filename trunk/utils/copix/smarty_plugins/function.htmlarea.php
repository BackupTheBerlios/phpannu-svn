<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: function.htmlarea.php,v 1.7 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Bertrand Yan
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
 * Name:     copixlogo
 * Version:  1
 * Date:     May 21, 2002
 * Author:   Gérald Croes
 * input: type :
 * Examples: {htmlarea}
 *
 * includes the required library for the js library htmlarea
 *  you can find this library at http://www.interactivetools.com/products/htmlarea/
 * -------------------------------------------------------------
 */
function smarty_function_htmlarea($params, &$smarty) {
    static $_init = false;

    extract($params);
    //check the initialisation
    if (! $_init){
       CopixHtmlHeader::addJsCode ('_editor_url = "'.CopixUrl::get ().'js/htmlarea/";');
       //path of the library
       if (empty ($path)){
           $path = CopixUrl::get().'js/htmlarea/';//default path under CopiX
       }
       CopixHTMLHeader::addJSLink ($path.'htmlarea.js');
       CopixHTMLHeader::addJSLink ($path.'dialog.js');
       if (empty ($lang)){
          $lang = CopixI18N::getLang ();
       }
       CopixHTMLHeader::addJSLink ($path.'lang/'.$lang.'.js');
       CopixHTMLHeader::addCSSLink ($path.'htmlarea.css');
       CopixHTMLHeader::addJSLink ($path.'popupwin.js');
       CopixHTMLHeader::addJSCode ('
                HTMLArea.loadPlugin("TableOperations");
                HTMLArea.loadPlugin("InsertAnchor");
                HTMLArea.loadPlugin("TableToggleBorder");
                HTMLArea.loadPlugin("AstonTools");
                HTMLArea.loadPlugin("ContextMenu");
                ');
       $_init = true;
    }
    
    if (empty ($content)){
       $content = '';
    }

    //name of the textarea.
    if (empty ($name)){
       $smarty->trigger_error('htmlarea: missing name parameter');
    }else{
//       CopixHTMLHeader::addOthers ($script);
       if (!$width) {
         $width = 500;
       }
       if (!$height) {
         $height = 500;
       }
       $out = '<textarea id="'.$name.'" name="'.$name.'" style="width: '.$width.'px; height:'.$height.'px;" >'.$content.'</textarea>';
       $out .= '<script type="text/javascript" defer="1">
       var editor'.$name.' = null;
       editor'.$name.' = new HTMLArea("'.$name.'");
       editor'.$name.'.registerPlugin("TableOperations");
       editor'.$name.'.registerPlugin("TableToggleBorder");
       editor'.$name.'.registerPlugin("InsertAnchor");
       editor'.$name.'.registerPlugin("AstonTools");
       editor'.$name.'.registerPlugin("ContextMenu");
       editor'.$name.'.config.pageStyle = "@import url(\"'.CopixUrl::get ().'styles/styles_copix.css\");";
       editor'.$name.'.generate ();
       </script>';
    }
    return $out;
}
?>