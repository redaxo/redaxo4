<?php

/**
 * REDAXO Default-Theme
 *
 * @author Design
 * @author ralph.zumkeller[at]yakamara[dot]de Ralph Zumkeller
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 *
 * @author Umsetzung
 * @author thomas[dot]blum[at]redaxo[dot]de Thomas Blum
 * @author <a href="http://www.blumbeet.com">www.blumbeet.com</a>
 *
 
 Codemirror by : http://codemirror.net/
 Marijn Haverbeke <marijnh@gmail.com>
 
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'agk_skin';

$REX['ADDON']['version'][$mypage] = '4.5';
$REX['ADDON']['author'][$mypage] = 'Design: Ralph Zumkeller; Umsetzung: Thomas Blum';
$REX['ADDON']['supportpage'][$mypage] = 'www.redaxo.org/de/forum';

// --- DYN
$REX['ADDON']['be_style']['plugin_agk_skin']['labelcolor'] = "";
$REX['ADDON']['be_style']['plugin_agk_skin']['codemirror_theme'] = "eclipse";
$REX['ADDON']['be_style']['plugin_agk_skin']['codemirror'] = 1;
$REX['ADDON']['be_style']['plugin_agk_skin']['showlink'] = 1;
// --- /DYN

if($REX["REDAXO"]) {

  $I18N->appendFile(dirname(__FILE__) .'/lang/');

  function rex_be_style_agk_skin_css_add($params) {
    global $REX;

    if($REX['ADDON']['be_style']['plugin_agk_skin']['codemirror']) {
    
      $params["subject"] .= "\n".'<link rel="stylesheet" type="text/css" href="../'.$REX['MEDIA_ADDON_DIR'].'/be_style/plugins/agk_skin/codemirror/codemirror.css" media="screen" />';
      $params["subject"] .= "\n".'<script type="text/javascript">var agk_skin_codemirror_defaulttheme="'.$REX['ADDON']['be_style']['plugin_agk_skin']['codemirror_theme'].'";</script>';
    
      $params["subject"] .= "\n".'<script type="text/javascript" src="../'.$REX['MEDIA_ADDON_DIR'].'/be_style/plugins/agk_skin/codemirror/codemirror-compressed.js"></script>';
      $params["subject"] .= "\n".'<script type="text/javascript" src="../'.$REX['MEDIA_ADDON_DIR'].'/be_style/plugins/agk_skin/codemirror/rex-init.js"></script>';
    }
    if($REX['ADDON']['be_style']['plugin_agk_skin']['labelcolor'] != "") {
      $params["subject"] .= "\n".'<style>#rex-navi-logout {  border-bottom: 10px solid '.htmlspecialchars($REX['ADDON']['be_style']['plugin_agk_skin']['labelcolor']).'; }</style>';
    }
    return $params["subject"];
  }

  function rex_be_style_agk_skin_css_body($params)
  {
    $params["subject"]["class"][] = "be-style-agb-skin";
    return $params["subject"];
  }

  function rex_be_style_agk_skin_label_navi($params) {
    global $I18N;
    $params["subject"][] = array( 'agk_skin', $I18N->msg('agk_skin') );
    return $params["subject"];
  }

  function rex_be_style_agk_skin_label_content($params) {
    global $REX,$I18N;
    $content = "";
    if($params["subpage"] == "agk_skin") {
      ob_start();
      require $REX['INCLUDE_PATH'].'/addons/be_style/plugins/agk_skin/pages/specials.agk_skin.inc.php';
      $content = ob_get_contents();
      ob_end_clean();
    }
    return $content;

  }

  function rex_be_style_agk_skin_meta($params) {
    global $REX;
    $server = "";
    if(substr($REX["SERVER"],0,4) != "http") {
      $server = 'http://'.$REX["SERVER"];
    }
    $meta = array();
    foreach($params["subject"] as $k => $nav) {
      if($k == "user") {
        $meta[$k] = $nav;
        $meta["linktowebsite"] = '<li><a href="'.$server.'">'.htmlspecialchars($REX["SERVERNAME"]).'</a></li>';
      } else {
        $meta[$k] = $nav;
      }
    }
    return $meta;
  }

  rex_register_extension('PAGE_HEADER', 'rex_be_style_agk_skin_css_add');
  rex_register_extension('PAGE_BODY_ATTR', 'rex_be_style_agk_skin_css_body');
  rex_register_extension('PAGE_SPECIALS_MENU', 'rex_be_style_agk_skin_label_navi');
  rex_register_extension('PAGE_SPECIALS_OUTPUT', 'rex_be_style_agk_skin_label_content');

  if($REX['ADDON']['be_style']['plugin_agk_skin']['showlink']) {
    rex_register_extension('META_NAVI', 'rex_be_style_agk_skin_meta');
  }

}





