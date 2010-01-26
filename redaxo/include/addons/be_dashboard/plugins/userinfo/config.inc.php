<?php

/**
 * Userinfo Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'userinfo';

/* Addon Parameter */
$REX['ADDON']['rxid'][$mypage] = '659';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['version'][$mypage] = '1.3';
$REX['ADDON']['author'][$mypage] = 'Markus Staab';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

if($REX["REDAXO"])
{
  require_once dirname(__FILE__) .'/functions/function_administrator.inc.php';
  require_once dirname(__FILE__) .'/functions/function_user.inc.php';

  // TODO isAvailable check funktioniert nicht!
  if(true || OOAddon::isAvailable('be_dashboard'))
  {
    require_once dirname(__FILE__) .'/classes/class.dashboard.inc.php';
    
    rex_register_extension(
      'DASHBOARD_COMPONENT',
      array(new rex_admin_stats_component(), 'registerAsExtension')
    );
  }
  
      //  
//  rex_register_extension('PAGE_HEADER', 'rex_be_style_css_add');
//  rex_register_extension('ADDONS_INCLUDED', 'rex_be_add_page');
}
