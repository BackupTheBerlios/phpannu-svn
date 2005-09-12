function TableToggleBorder(editor) {
  this.editor = editor;
  var cfg = editor.config;
  var tt = TableToggleBorder.I18N;
  var bl = TableToggleBorder.btnList;
  var self = this;
  
  // register the toolbar buttons provided by this plugin
  var id = "TTB-toggle-borders";
  for (var i=0; i<cfg.toolbar.length; i++) {
    for (var j=0; j<cfg.toolbar[i].length; j++) {
      // Insert the new "TTB-toggle-borders" button after the "inserttable" button (if exists)
      if ((cfg.toolbar[i][j] == "inserttable") && (cfg.toolbar[i].length > j+1) && (cfg.toolbar[i][j+1] != id)) {
        
        cfg.registerButton(id, tt[id], "js/htmlarea/plugins/TableToggleBorder/img/" + "toggle-borders.gif", false,
          function(editor, id) {
            // dispatch button press event
            self._toggleBorders(editor);
          }, '');
        
        var _toolbar_left = cfg.toolbar[i].slice(0,j+1);
        var _toolbar_right = (cfg.toolbar[i].length > j+1)?cfg.toolbar[i].slice(j+1,cfg.toolbar[i].length):new Array();
        cfg.toolbar[i] = _toolbar_left.concat(new Array(id)).concat(_toolbar_right);
      }
    }
  }
}

TableToggleBorder._pluginInfo = {
  name          : "TableToggleBorder",
  version       : "1.0",
  developer     : "Andre Rabold",
  developer_url : "http://dynarch.com/mishoo/",
  c_owner       : "Andre Rabold",
  sponsor       : "MR Printware GmbH",
  sponsor_url   : "http://www.mr-printware.de",
  license       : "htmlArea"
};

TableToggleBorder.prototype._toggleBorders = function(editor)
{
  var id = "TTB-show-border";
  
  editor.borders = (!editor.borders);
  editor._toolbarObjects['TTB-toggle-borders'].state("active", editor.borders);
  
  style = editor._doc.getElementById(id);
  if (style == null) {
    style = editor._doc.createElement("LINK");
    style.setAttribute('id', id);
    style.setAttribute('rel', 'stylesheet');
    editor._doc.getElementsByTagName("HEAD")[0].appendChild(style);
  }

  if (editor.borders) {
    style.setAttribute('href', 'js/htmlarea/plugins/TableToggleBorder/table-show-border.css');
  }
  else {
    style.setAttribute('href', 'js/htmlarea/plugins/TableToggleBorder/table-hide-border.css');
  }
  return true;
}
