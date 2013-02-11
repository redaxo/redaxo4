<?php

/**
 * REDAXO customizer
 *
 * Codemirror by : http://codemirror.net/
 * Marijn Haverbeke <marijnh@gmail.com>
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'customizer';

$REX['ADDON']['version'][$mypage] = '4.5';
$REX['ADDON']['author'][$mypage] = 'Umsetzung: Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.redaxo.org/de/forum';

// --- DYN
$REX['ADDON']['be_style']['plugin_customizer']['labelcolor'] = "#090";
$REX['ADDON']['be_style']['plugin_customizer']['codemirror_theme'] = "eclipse";
$REX['ADDON']['be_style']['plugin_customizer']['codemirror'] = 1;
$REX['ADDON']['be_style']['plugin_customizer']['showlink'] = 1;
$REX['ADDON']['be_style']['plugin_customizer']['textarea'] = 1;
$REX['ADDON']['be_style']['plugin_customizer']['liquid'] = 0;
// --- /DYN

if($REX["REDAXO"]) {

  $I18N->appendFile(dirname(__FILE__) .'/lang/');

  function rex_be_style_customizer_css_add($params) {
    global $REX;

    if($REX['ADDON']['be_style']['plugin_customizer']['codemirror']) {
    
      $params["subject"] .= "\n".'<link rel="stylesheet" type="text/css" href="../'.$REX['MEDIA_ADDON_DIR'].'/be_style/plugins/customizer/codemirror/codemirror.css" media="screen" />';
      $params["subject"] .= "\n".'<script type="text/javascript">var customizer_codemirror_defaulttheme="'.$REX['ADDON']['be_style']['plugin_customizer']['codemirror_theme'].'";</script>';
    
      $params["subject"] .= "\n".'<script type="text/javascript" src="../'.$REX['MEDIA_ADDON_DIR'].'/be_style/plugins/customizer/codemirror/codemirror-compressed.js"></script>';
      $params["subject"] .= "\n".'<script type="text/javascript" src="../'.$REX['MEDIA_ADDON_DIR'].'/be_style/plugins/customizer/codemirror/rex-init.js"></script>';
    }
    if($REX['ADDON']['be_style']['plugin_customizer']['labelcolor'] != "") {
      $params["subject"] .= "\n".'<style>#rex-navi-logout {  border-bottom: 10px solid '.htmlspecialchars($REX['ADDON']['be_style']['plugin_customizer']['labelcolor']).'; }</style>';
    }
    return $params["subject"];
  }

  function rex_be_style_customizer_label_navi($params) {
    global $I18N;
    $params["subject"][] = array( 'customizer', $I18N->msg('customizer') );
    return $params["subject"];
  }

  function rex_be_style_customizer_label_content($params) {
    global $REX,$I18N;
    $content = "";
    if($params["subpage"] == "customizer") {
      ob_start();
      require $REX['INCLUDE_PATH'].'/addons/be_style/plugins/customizer/pages/specials.customizer.inc.php';
      $content = ob_get_contents();
      ob_end_clean();
    }
    return $content;
  }

  function rex_be_style_customizer_extra($params) {
    global $REX;

    $server = '';
    if(substr($REX["SERVER"],0,4) != 'http') {
      $server = 'http://'.$REX["SERVER"];
    }

    $params['subject'] = str_replace('<div id="rex-extra"></div>',
      '<div id="rex-extra"><h1><a href="' . $server . '" onclick="window.open(this.href); return false">' . $REX['SERVERNAME'] . '</a></h1></div>',
      $params['subject']);

    return $params['subject'];
  }

  function rex_be_style_customizer_body($params) {
    global $REX;

    if ($REX['ADDON']['be_style']['plugin_customizer']['textarea'])
      $params['subject']['class'][] = 'be-style-agk-skin-textarea';

    if ($REX['ADDON']['be_style']['plugin_customizer']['liquid'])
      $params['subject']['class'][] = 'rex-layout-liquid';

    return $params['subject'];
  }

  rex_register_extension('PAGE_HEADER', 'rex_be_style_customizer_css_add');
  rex_register_extension('PAGE_SPECIALS_MENU', 'rex_be_style_customizer_label_navi');
  rex_register_extension('PAGE_SPECIALS_OUTPUT', 'rex_be_style_customizer_label_content');

  if($REX['ADDON']['be_style']['plugin_customizer']['showlink']) {
    rex_register_extension('OUTPUT_FILTER', 'rex_be_style_customizer_extra');
  }

  if($REX['ADDON']['be_style']['plugin_customizer']['textarea'] || $REX['ADDON']['be_style']['plugin_customizer']['liquid']) {
    rex_register_extension('PAGE_BODY_ATTR', 'rex_be_style_customizer_body');
  }

}





