// AstonTools Plugin for FCKEditor2RC2
// Register the related commands.
FCKCommands.RegisterCommand( 'phototheque', new FCKDialogCommand( FCKLang['DlgPhototheque'], FCKLang['DlgPhototheque'], '../../../index.php?module=pictures&desc=browser&action=browse&popup=FCKEDITOR&editorName=text_content'	, 1024, 768 ) ) ;
FCKCommands.RegisterCommand( 'document', new FCKDialogCommand( FCKLang['DlgDocument'], FCKLang['DlgDocument'], '../../../index.php?module=document&desc=admin&action=selectDocument&select=FCKEDITOR&popup=true&editorName=text_content'	, 1024, 768 ) ) ;
FCKCommands.RegisterCommand( 'cmslink', new FCKDialogCommand( FCKLang['DlgCmsLink'], FCKLang['DlgCmsLink'], '../../../index.php?module=htmleditor&desc=cms&action=selectPage&editor=fckeditor&editorName=text_content'	, 1024, 768 ) ) ;
FCKCommands.RegisterCommand( 'cmslinkpopup', new FCKDialogCommand( FCKLang['DlgCmsLinkPopup'], FCKLang['DlgCmsLinkPopup'], '../../../index.php?module=htmleditor&desc=cms&action=selectPage&popup=true&editor=fckeditor&editorName=text_content'	, 1024, 768 ) ) ;

// Create the "Find" toolbar button.
var oPhotothequeItem	= new FCKToolbarButton( 'phototheque', FCKLang['DlgPhototheque'] ) ;
oPhotothequeItem.IconPath	= FCKConfig.PluginsPath + 'AstonTools/img/browser.gif' ;

var oDocumentItem	= new FCKToolbarButton( 'document', FCKLang['DlgDocument'] ) ;
oDocumentItem.IconPath	= FCKConfig.PluginsPath + 'AstonTools/img/doc.jpg' ;

var oCmsLinkItem	= new FCKToolbarButton( 'cmslink', FCKLang['DlgCmsLink'] ) ;
oCmsLinkItem.IconPath	= FCKConfig.PluginsPath + 'AstonTools/img/cms.gif' ;

var oCmsLinkPopupItem	= new FCKToolbarButton( 'cmslinkpopup', FCKLang['DlgCmsLinkPopup'] ) ;
oCmsLinkPopupItem.IconPath	= FCKConfig.PluginsPath + 'AstonTools/img/popuppage.jpg' ;

// Enregistre les items pour la toolbar
if (FCKConfig.viewPhototheque)
   FCKToolbarItems.RegisterItem( 'phototheque', oPhotothequeItem ) ;
if (FCKConfig.viewDocument)
   FCKToolbarItems.RegisterItem( 'document', oDocumentItem ) ;
if (FCKConfig.viewCmsLink)
   FCKToolbarItems.RegisterItem( 'cmslink', oCmsLinkItem ) ;
if (FCKConfig.viewLinkPopup)
   FCKToolbarItems.RegisterItem( 'cmslinkpopup', oCmsLinkPopupItem ) ;
