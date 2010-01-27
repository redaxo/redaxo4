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
  $I18N->appendFile(dirname(__FILE__). '/lang/');
  
  if(!defined('A659_DEFAULT_LIMIT'))
  {
    define('A659_DEFAULT_LIMIT', 5);
  }
  
  require_once dirname(__FILE__) .'/functions/function_userinfo.inc.php';

  if(rex_request('page', 'string') == 'be_dashboard')
  {
    require_once dirname(__FILE__) .'/classes/class.dashboard.inc.php';
    
    $adminComponents = array(
      'rex_stats_component',
      'rex_articles_component',
      'rex_templates_component',
      'rex_modules_component',
      'rex_actions_component',
      'rex_users_component',
    );
    
    foreach($adminComponents as $compClass)
    {
      rex_register_extension (
        'DASHBOARD_COMPONENT',
        array(new $compClass(), 'registerAsExtension')
      );
    }
  }
  
      //  
//  rex_register_extension('PAGE_HEADER', 'rex_be_style_css_add');
//  rex_register_extension('ADDONS_INCLUDED', 'rex_be_add_page');
}
