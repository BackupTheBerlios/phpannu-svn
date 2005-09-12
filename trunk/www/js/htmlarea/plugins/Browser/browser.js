// plugin pour insertion d'image depuis la phototeque
function Browser(editor) {
	this.editor = editor;

	var cfg = editor.config;
	//var tt = Browser.I18N;
	var self = this;

	// register the toolbar buttons provided by this plugin
	 var cfg = editor.config; // this is the default configuration
  cfg.registerButton({
    id        : "phototeque",
    tooltip   : "Ajouter une image",
    image     : "js/htmlarea/plugins/Browser/img/browser.gif",
    textMode  : false,
    action    : function(editor) {
                  self.buttonPress(editor);
                }
  });

	// add a new line in the toolbar
  cfg.toolbar.push(["phototeque"]);
};

/************************
 * UTILITIES
 ************************/

// this function gets called when some button from the TableOperations toolbar
// was pressed.
Browser.prototype.buttonPress = function(editor) {
	this.editor = editor;
   window.open('index.php?module=pictures&popup=HTMLArea&editorName=editor'+editor._textArea.name,'popup','toolbar=no,scrollbars=yes');
}
