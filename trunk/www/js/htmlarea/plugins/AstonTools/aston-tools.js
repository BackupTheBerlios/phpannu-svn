// AstonTools Plugin for HTMLArea-3.0
function AstonTools(editor) {
	this.editor = editor;

	var cfg = editor.config;
	var tt = AstonTools.I18N;
	var bl = AstonTools.btnList;
	var self = this;
	// register the toolbar buttons provided by this plugin
	var toolbar = ["linebreak"];
	for (var i in bl) {
		var btn = bl[i];
		if (!btn) {
			toolbar.push("separator");
		} else {
         var id = btn[0];

			cfg.registerButton(id, tt[id], editor.imgURL(btn[1], "AstonTools") , false,
					   function(editor, id) {
						   // dispatch button press event
						   self.buttonPress(editor, id);
					   });
			toolbar.push(id);
		}
	}

	// add a new line in the toolbar
	cfg.toolbar.push(toolbar);
	
};

AstonTools._pluginInfo = {
  name          : "AstonTools",
  version       : "1.0",
  developer     : "Yan Bertrand",
  developer_url : "http://copix.aston.fr",
  c_owner       : "Yan Bertrand",
  sponsor       : "aston",
  sponsor_url   : "http://copix.aston.fr",
  license       : "htmlArea"
};

/************************
 * UTILITIES
 ************************/

// this function gets called when some button from the TableOperations toolbar
// was pressed.
AstonTools.prototype.buttonPress = function(editor, button_id) {
	this.editor = editor;
   switch (button_id) {
      case "phototeque":editor.focusEditor();window.open(_editor_url + '../../index.php?module=pictures&desc=browser&action=browse&popup=HTMLArea&select=editor'+editor._textArea.name,'popup','toolbar=no,scrollbars=yes,width=1024,height=768,resizable=yes');break;
      case "document":editor.focusEditor();window.open(_editor_url + '../../index.php?module=document&desc=admin&action=selectDocument&popup=true&editorName=editor'+editor._textArea.name,'popup','toolbar=no,scrollbars=yes,width=1024,height=768,resizable=yes');break;
      case "cmsLink":window.open(_editor_url + '../../index.php?module=htmleditor&desc=cms&action=selectPage&editorName=editor'+editor._textArea.name,'popup','toolbar=no,scrollbars=yes,width=1024,height=768,resizable=yes');break;
      case "cmsPopupLink":window.open(_editor_url + '../../index.php?module=htmleditor&desc=cms&action=selectPage&popup=true&editorName=editor'+editor._textArea.name,'popup','toolbar=no,scrollbars=yes,width=1024,height=768,resizable=yes');break;
      case "popup"     :editor._popupDialog('addpopup.html',function(param) {
                     if (!param) {	// user must have pressed Cancel
			               return false;
                     }
		               for (field in param) {
			               var value = param[field];
			               if (!value) {
				              continue;
			               }
			               //par defaut pas de scrollbar
			               var auto = 'no';
   			            switch (field) {
   			               case "f_pop"  : var url = value;break;
   			               case "h_pop"  : var popHeight = value;break;
   			               case "yes_pop": if (value) {auto = 'yes';}break;
   			               case "no_pop" : if (value) {auto = 'no';}break;
            			   }
                     }
                     editor.surroundHTML('<a href="#" onClick="window.open(\''+url+'\',\'popup\',\'toolbar=no,scrollbars='+auto+',height='+popHeight+',width=450\');" >', '</a>');
                     }, null);break;
      default:
		alert("Button [" + button_id + "] not yet implemented");
   }
  //<A NAME="section2"></A>
}

AstonTools.btnList = [
	["document"     ,"doc.jpg","Ajouter un téléchargement de document"],
	null,
	["phototeque","browser.gif","Ajouter une image"],
	null,
	["popup"     , "popup.jpg","Ajouter une popup"],
	null,
	["cmsLink"     , "cms.gif","Ajouter un lien vers une page de gestion de contenu"],
	null,
	["cmsPopupLink", "popuppage.jpg","Ajouter un lien vers une page de gestion de contenu avec affichage en popup"]
	];
