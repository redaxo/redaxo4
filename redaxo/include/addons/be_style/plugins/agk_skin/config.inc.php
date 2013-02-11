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

if($REX["REDAXO"]) {

  $I18N->appendFile(dirname(__FILE__) .'/lang/');

  function rex_be_style_agk_skin_css_body($params)
  {
    $params["subject"]["class"][] = "be-style-agk-skin";
    return $params["subject"];
  }

  rex_register_extension('PAGE_BODY_ATTR', 'rex_be_style_agk_skin_css_body');

}





