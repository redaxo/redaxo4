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
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'default_theme';

$REX['ADDON']['version'][$mypage] = '1.2';
$REX['ADDON']['author'][$mypage] = 'Design: Ralph Zumkeller; Umsetzung: Thomas Blum';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';



if($REX["REDAXO"])
{
  
  
  /**
   * Fügt die zusätzlichen zu css_main.css benötigten Stylesheets ein
   * 
   * @param $params Extension-Point Parameter
   */
  function rex_be_style_default_theme_css_add($params)
  {
    echo '      
      <!--[if lte IE 7]>
        <link rel="stylesheet" href="../files/addons/be_style/plugins/default_theme/css_ie_lte_7.css" type="text/css" media="screen, projection, print" />
      <![endif]-->
    
      <!--[if lte IE 6]>
        <link rel="stylesheet" href="../files/addons/be_style/plugins/default_theme/css_ie_lte_6.css" type="text/css" media="screen, projection, print" />
      <![endif]-->';

  }
  
  rex_register_extension('PAGE_HEADER', 'rex_be_style_default_theme_css_add');
}
?>