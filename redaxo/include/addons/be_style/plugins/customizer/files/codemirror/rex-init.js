jQuery(document).ready(function()
{
  var cm_editor = {};
  
  cm = 0;
  jQuery("#rex-page-cronjob textarea, #rex-page-module #rex-wrapper textarea, #rex-page-template #rex-wrapper textarea, textarea.codemirror").each(function(){

    cm++;
    id = jQuery(this).attr("id");
    if(typeof id === "undefined") {
      id = 'codemirror-id-'+cm;
      jQuery(this).attr("id",id);
    }

    mode = "application/x-httpd-php";
    theme = customizer_codemirror_defaulttheme;

    new_mode = jQuery(this).attr("data-codemirror-mode");
    new_theme = jQuery(this).attr("data-codemirror-theme");

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
      .css("margin-top", jQuery(this).css("margin-top"))
      .css("margin-left", jQuery(this).css("margin-left"))
      .css("margin-bottom", jQuery(this).css("margin-bottom"));

    height = parseInt(jQuery(this).height());
    if(height < 150) height = 150;

    cm_editor[cm].setSize(jQuery(this).width(), height);
    cm_editor[cm].refresh();

  });

});