<?php

/**
 * be_style plugin: codemirror
 * 
 * Copyright (C) 2013 by Marijn Haverbeke <marijnh@gmail.com>
 * https://github.com/marijnh/CodeMirror
 *
 */

$mypage = 'codemirror';

$REX['ADDON']['version'][$mypage] = '1.0';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.redaxo.org/de/forum';

$REX['codemirror']['settings']['theme'] = 'ambiance';

/* THEMES:
 * ambiance, blackboard, cobalt, eclipse, elegant, erlang-dark,
 * lesser-dark, monokai, neat, night, rubyblue, vibrant-ink, xq-dark,
 * custom: redaxo
 */

if($REX["REDAXO"]) {
  rex_register_extension('PAGE_HEADER', 'be_style_codemirror_include');
  rex_register_extension('OUTPUT_FILTER', 'be_style_codemirror_init');

  function be_style_codemirror_include($params) {
    global $REX;

    $cm = '
  <!-- codemirror_inlude -->
  <link rel="stylesheet" href="../files/addons/be_style/plugins/codemirror/lib/codemirror.css">
  <link rel="stylesheet" href="../files/addons/be_style/plugins/codemirror/rex_init.css">

  <script src="../files/addons/be_style/plugins/codemirror/lib/codemirror.js"></script>

  <script src="../files/addons/be_style/plugins/codemirror/addon/edit/matchbrackets.js"></script>
  <script src="../files/addons/be_style/plugins/codemirror/addon/edit/continuecomment.js"></script>

  <script src="../files/addons/be_style/plugins/codemirror/mode/clike/clike.js"></script>
  <script src="../files/addons/be_style/plugins/codemirror/mode/xml/xml.js"></script>
  <script src="../files/addons/be_style/plugins/codemirror/mode/css/css.js"></script>
  <script src="../files/addons/be_style/plugins/codemirror/mode/htmlembedded/htmlembedded.js"></script>
  <script src="../files/addons/be_style/plugins/codemirror/mode/htmlmixed/htmlmixed.js"></script>
  <script src="../files/addons/be_style/plugins/codemirror/mode/php/php.js"></script>
  <script src="../files/addons/be_style/plugins/codemirror/mode/javascript/javascript.js"></script>
  <!-- /codemirror_inlude -->';
  
    return $params["subject"].$cm;
  }

  function be_style_codemirror_init($params) {
  
    global $REX;
  
     $script = '
  <!-- codemirror_init -->
  <script type="text/javascript">
  var codemirrors = {};

  function isFullScreen(cm) {
    return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
  }

  function winHeight() {
    return window.innerHeight || (document.documentElement || document.body).clientHeight;
  }

  function setFullScreen(cm, full) {
    var wrap = cm.getWrapperElement(), scroll = cm.getScrollerElement();
    if (full) {
      wrap.className += " CodeMirror-fullscreen";
      scroll.style.height = winHeight() + "px";
      document.documentElement.style.overflow = "hidden";
      cm.setOption("lineWrapping", false);
    } else {
      wrap.className = wrap.className.replace(" CodeMirror-fullscreen", "");
      scroll.style.height = "";
      document.documentElement.style.overflow = "";
      cm.setOption("lineWrapping", true);
    }
    cm.refresh();
  }

  (function ($) {
  
    i = 1;

    $("#rex-page-template #rex-wrapper textarea, #rex-page-module textarea#eingabe, #rex-page-module textarea#ausgabe, #rex-page-module textarea#previewaction, #rex-page-module textarea#presaveaction, #rex-page-module textarea#postsaveaction, textarea.rex-textarea-type-php,textarea.rex-textarea-type-html,textarea.rex-textarea-type-javascript,textarea.rex-textarea-type-css").each(function(){
      area = $(this);

      theme = area.attr("codemirror-theme");
      if(typeof theme == "undefined") {
        theme = "redaxo";
      };

      $(\'<style type="text/css">@import url("../files/addons/be_style/plugins/codemirror/theme/\' + theme + \'.css")</style>\').appendTo("head");

      mode = "application/x-httpd-php";
      if(area.hasClass("rex-textarea-type-css")) {
        mode = "application/x-ejs";
      }

      if(area.hasClass("rex-textarea-type-html")) {
        mode = "application/x-ejs";
      }

      if(area.hasClass("rex-textarea-type-javascript")) {
        mode = "application/x-ejs";
      }

      cm = true;
      if(cm){
  
        id = area.attr("id");
        if(id=="undefined"){
          id = "cm-id-"+i;
          area.attr("id",id);
        }
  
        w = area.width();
        h = area.height();
        ml = area.css("margin-left");
  
        codemirrors[id] = CodeMirror.fromTextArea(area.get(0), {
          lineNumbers: true,
          lineWrapping: false,
          theme: theme,
          matchBrackets: true,
          mode: mode,
          indentUnit: 4,
          indentWithTabs: true,
          enterMode: "keep",
          tabMode: "shift",
          /* onGutterClick: foldFunc, */
          extraKeys: {
            "F11": function(cm) {
              setFullScreen(cm, !isFullScreen(cm));
            },
            "Esc": function(cm) {
              if (isFullScreen(cm)) setFullScreen(cm, false);
            }
          }
        });
  
        codemirrors[id].getWrapperElement().style.width = w+"px";
        codemirrors[id].getWrapperElement().style.marginLeft = ml;
        codemirrors[id].getScrollerElement().style.height = h+"px";
        codemirrors[id].refresh()
      }
  
      i++;
      });

    })(jQuery);
    </script>
  
    <!-- /codemirror_init -->';

    return str_replace('</body>',$script.'</body>',$params['subject']);

  }
}
