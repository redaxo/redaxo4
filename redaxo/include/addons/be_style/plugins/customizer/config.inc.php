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
$REX['ADDON']['be_style']['plugin_customizer']['labelcolor'] = '#47a1ce';
$REX['ADDON']['be_style']['plugin_customizer']['codemirror_theme'] = 'eclipse';
$REX['ADDON']['be_style']['plugin_customizer']['codemirror'] = 1;
$REX['ADDON']['be_style']['plugin_customizer']['showlink'] = 1;
$REX['ADDON']['be_style']['plugin_customizer']['textarea'] = 1;
$REX['ADDON']['be_style']['plugin_customizer']['liquid'] = 0;
// --- /DYN

if ($REX['REDAXO']) {

  $I18N->appendFile(dirname(__FILE__) . '/lang/');

  function rex_be_style_customizer_css_add($params)
  {
    global $REX;
    $add = "\n" . '<link rel="stylesheet" type="text/css" href="../' . $REX['MEDIA_ADDON_DIR'] . '/be_style/plugins/customizer/customizer.css" media="screen" />';
    if ($REX['ADDON']['be_style']['plugin_customizer']['codemirror']) {
      $add .= "\n" . '<link rel="stylesheet" type="text/css" href="../' . $REX['MEDIA_ADDON_DIR'] . '/be_style/plugins/customizer/codemirror/codemirror.css" media="screen" />';
      $add .= "\n" . '<script type="text/javascript">var customizer_codemirror_defaulttheme="' . $REX['ADDON']['be_style']['plugin_customizer']['codemirror_theme'] . '";</script>';
      $add .= "\n" . '<script type="text/javascript" src="../' . $REX['MEDIA_ADDON_DIR'] . '/be_style/plugins/customizer/codemirror/codemirror-compressed.js"></script>';
      $add .= "\n" . '<script type="text/javascript" src="../' . $REX['MEDIA_ADDON_DIR'] . '/be_style/plugins/customizer/codemirror/rex-init.js"></script>';
    }
    if ($REX['ADDON']['be_style']['plugin_customizer']['labelcolor'] != '') {
      $add .= "\n" . '<style>#rex-navi-logout {  border-bottom: 10px solid ' . htmlspecialchars($REX['ADDON']['be_style']['plugin_customizer']['labelcolor']) . '; }</style>';
    }
    return str_replace('</body>', $add . '</body>', $params['subject']);
  }
  rex_register_extension('OUTPUT_FILTER', 'rex_be_style_customizer_css_add');


  function rex_be_style_customizer_label_navi($params)
  {
    if (isset($params['pages']['specials'])) {
      global $I18N;
      $page = new rex_be_page($I18N->msg('customizer'), array('page' => 'specials', 'subpage' => 'customizer'));
      $page->setRequiredPermissions('isAdmin');
      $page->setHref('index.php?page=specials&subpage=customizer');
      $params['pages']['specials']->getPage()->addSubPage($page);
    }
  }
  rex_register_extension('PAGE_CHECKED', 'rex_be_style_customizer_label_navi');


  function rex_be_style_customizer_label_content($params)
  {
    global $REX, $I18N;
    $content = '';
    if ($params['subpage'] == 'customizer') {
      ob_start();
      require $REX['INCLUDE_PATH'] . '/addons/be_style/plugins/customizer/pages/specials.customizer.inc.php';
      $content = ob_get_contents();
      ob_end_clean();
    }
    return $content;
  }
  rex_register_extension('PAGE_SPECIALS_OUTPUT', 'rex_be_style_customizer_label_content');


  function rex_be_style_customizer_extra($params)
  {
    global $REX;
    $server = $REX['SERVER'];
    if (substr($REX['SERVER'], 0, 4) != 'http') {
      $server = 'http://' . $REX['SERVER'];
    }
    $class = (strlen($REX['SERVERNAME']) > 50) ? ' be-style-customizer-small' : '';
    $params['subject'] = str_replace('<div id="rex-extra">',
      '<div id="rex-extra"><h1 class="be-style-customizer-title' . $class . '"><a href="' . $server . '" onclick="window.open(this.href); return false">' . $REX['SERVERNAME'] . '</a></h1>',
      $params['subject']);
    return $params['subject'];
  }
  if ($REX['ADDON']['be_style']['plugin_customizer']['showlink']) {
    rex_register_extension('OUTPUT_FILTER', 'rex_be_style_customizer_extra');
  }


  function rex_be_style_customizer_body($params)
  {
    global $REX;
    if ($REX['ADDON']['be_style']['plugin_customizer']['textarea'])
      $params['subject']['class'][] = 'be-style-customizer-textarea-big';
    if ($REX['ADDON']['be_style']['plugin_customizer']['liquid'])
      $params['subject']['class'][] = 'rex-layout-liquid';
    return $params['subject'];
  }
  if ($REX['ADDON']['be_style']['plugin_customizer']['textarea'] || $REX['ADDON']['be_style']['plugin_customizer']['liquid']) {
    rex_register_extension('PAGE_BODY_ATTR', 'rex_be_style_customizer_body');
  }

}
