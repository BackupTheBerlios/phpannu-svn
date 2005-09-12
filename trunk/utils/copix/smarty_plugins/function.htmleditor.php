<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     
 * Version:  1
 * Date:     January 13, 2005
 * Author:   Sylvain DACLIN
 * input: type :
 * Examples: {htmleditor name="text_content" content="Default XHTML content"}
 *
 * includes the required library for the js library fckeditor
 * you can find this library at http://www.fckeditor.net
 * -------------------------------------------------------------
 */
function smarty_function_htmleditor($params, &$smarty) {
   static $_init = false;
   extract($params);
   
   //check the initialisation
   if (! $_init){
      switch (strtolower(CopixConfig::get('htmleditor|type'))) {
      case 'htmlarea' :
         CopixHtmlHeader::addJsCode ('_editor_url = "'.CopixUrl::get ().'js/htmlarea/";');
         //path of the library
         if (empty ($htmlPath)){
           $htmlPath = CopixUrl::get().'js/htmlarea/';//default path under CopiX
         }
         CopixHTMLHeader::addJSLink ($htmlPath.'htmlarea.js');
         CopixHTMLHeader::addJSLink ($htmlPath.'dialog.js');
         if (empty ($lang)){
          $lang = CopixI18N::getLang ();
         }
         CopixHTMLHeader::addJSLink ($htmlPath.'lang/'.$lang.'.js');
         CopixHTMLHeader::addCSSLink ($htmlPath.'htmlarea.css');
         CopixHTMLHeader::addJSLink ($htmlPath.'popupwin.js');
         
         $jsCode = 'HTMLArea.loadPlugin("TableOperations");
                HTMLArea.loadPlugin("InsertAnchor");
                HTMLArea.loadPlugin("TableToggleBorder");
                HTMLArea.loadPlugin("ContextMenu");';
         if (CopixModule::isValid('pictures') && CopixModule::isValid('cms') && CopixModule::isValid('document')) {
            $jsCode = 'HTMLArea.loadPlugin("AstonTools");';
         }
         CopixHTMLHeader::addJSCode ($jsCode);
         
         break;
      case 'fckeditor' : 
      default :
         $path = COPIX_MODULE_PATH.'htmleditor/'.COPIX_CLASSES_DIR;
         $htmlPath = CopixUrl::get ().'js/FCKeditor/';
         require_once( $path.'fckeditor.php' );
         break;
      }
      
      //     CopixHTMLHeader::addJSLink ($path.'fckconfig.js');
      //		 CopixHTMLHeader::addJSLink ($path.'fckeditor.js');
      $_init = true;
   }
    
   if (empty ($content)){
      $content = '&nbsp;';
   }
   //name of the textarea.
   if (empty ($name)){
      $smarty->trigger_error('htmleditor: missing name parameter');
   }else{
      if (!$width) {
         $width = CopixConfig::get('htmleditor|width');
         //$width = '100%';
      }
      if (!$height) {
         $height = CopixConfig::get('htmleditor|height');
         //$height = '450px';
      }
      switch (strtolower(CopixConfig::get('htmleditor|type'))) {
      case 'htmlarea' :
         $out = '<textarea id="'.$name.'" name="'.$name.'" style="width: '.$width.'px; height:'.$height.'px;" >'.$content.'</textarea>';
         $out .= '<script type="text/javascript" defer="1">
         var editor'.$name.' = null;
         editor'.$name.' = new HTMLArea("'.$name.'");
         editor'.$name.'.registerPlugin("TableOperations");
         editor'.$name.'.registerPlugin("TableToggleBorder");
         editor'.$name.'.registerPlugin("InsertAnchor");
         editor'.$name.'.registerPlugin("ContextMenu");';
         if (CopixModule::isValid('pictures') && CopixModule::isValid('cms') && CopixModule::isValid('document')) {
            $out .= 'editor'.$name.'.registerPlugin("AstonTools");';
         }
         $out .= 'editor'.$name.'.config.pageStyle = "@import url(\"'.CopixUrl::get ().'styles/styles_copix.css\");";
         editor'.$name.'.generate ();
         </script>';
         break;
      case 'fckeditor' : 
      default :
         /*
          * ATTENTION les éléments de config viewPhototèque etc font doublon avec la sélection de la toolbarset, mais sont nécessaire à Copix
          * Par contre si on ne les load pas, on a une erreur de FCKeditor, il faut donc supprimer ce gestionnaire d'erreur sinon on se prend un alert javascript
          * le gestionnaire en question se trouve dans "FCKToolbarItems.GetItem" (chercher cette chaîne pour le trouver) et désactiver "alert( FCKLang.UnknownToolbarItem.replace( /%1/g, itemName ) ) ;"
          */
         $oFCKeditor = new FCKeditor( $name ) ;
         
	 $oFCKeditor->BasePath	= $htmlPath ;
         $oFCKeditor->Value		= $content ;
         $oFCKeditor->ToolbarSet = 'Copix' ;
         $oFCKeditor->Width 		= $width ;
         $oFCKeditor->Height		= $height ;
         
         $oFCKeditor->Config['viewPhototheque']=   (CopixModule::isValid('pictures')) ? 'true' : 'false';
         $oFCKeditor->Config['viewCmsLink']=       (CopixModule::isValid('cms'))      ? 'true' : 'false';
         $oFCKeditor->Config['viewLinkPopup']=     (CopixModule::isValid('cms'))      ? 'true' : 'false';
         $oFCKeditor->Config['viewDocument']=      (CopixModule::isValid('document')) ? 'true' : 'false';
         
         // Configuration de la feuille de style à utiliser.
         $oFCKeditor->Config['EditorAreaCSS']=     CopixUrl::get ().'styles/styles_copix.css';
         $out = $oFCKeditor->CreateHtml() ;		 
         break;
      }
   }
   return $out;
}
?>