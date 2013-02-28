jQuery(document).ready(function()
{
  var cm_editor = {};
  
  cm = 0;
  jQuery("#rex-rex_cronjob_phpcode textarea, #rex-page-module #rex-wrapper textarea, #rex-page-template #rex-wrapper textarea, textarea.codemirror").each(function(){

    t = jQuery(this);

    id = t.attr("id");
    if(typeof id === "undefined") {
      cm++;
      id = 'codemirror-id-'+cm;
      t.attr("id",id);
    }

    mode = "application/x-httpd-php";
    theme = customizer_codemirror_defaulttheme;

    new_mode = t.attr("data-codemirror-mode");
    new_theme = t.attr("data-codemirror-theme");

    if(typeof new_mode !== "undefined") {
      mode = new_mode;
    }
    
    if(typeof new_theme !== "undefined") {
      theme = new_theme;
    }
  
    jQuery("head").append('<link rel="stylesheet" type="text/css" href="../files/addons/be_style/plugins/customizer/codemirror/theme/'+theme+'.css" media="screen" />');
    
    cm_editor[cm] = CodeMirror.fromTextArea(document.getElementById(id), {
      lineNumbers: true,
      lineWrapping: true,
      styleActiveLine: true,
      matchBrackets: true,
      mode: mode,
      indentUnit: 4,
      indentWithTabs: true,
      enterMode: "keep",
      tabMode: "shift",
      theme: theme
    });
  
    jQuery(cm_editor[cm].getWrapperElement())
      .css("margin-top", t.css("margin-top"))
      .css("margin-left", t.css("margin-left"))
      .css("margin-bottom", t.css("margin-bottom"))
      .css("margin-right", t.css("margin-right"));

    height = parseInt(t.height());
    if(height < 150) height = 150;
    width = parseInt(t.width());
    if(width < 300) width = 300;

    cm_editor[cm].setSize(width, height);
    cm_editor[cm].refresh();

  });

});