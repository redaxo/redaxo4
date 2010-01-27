<?php

/**
 * REDAXO Version Checker Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'version_checker';

/* Addon Parameter */
$REX['ADDON']['rxid'][$mypage] = '657';
$REX['ADDON']['version'][$mypage] = '1.3';
$REX['ADDON']['author'][$mypage] = 'Markus Staab, Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

if($REX["REDAXO"])
{
  $I18N->appendFile(dirname(__FILE__). '/lang/');
  
  require_once dirname(__FILE__) .'/functions/function_version_check.inc.php';
  
  if(rex_request('page', 'string') == 'be_dashboard')
  {
    require_once dirname(__FILE__) .'/classes/class.dashboard.inc.php';
    
    rex_register_extension('DASHBOARD_NOTIFICATION', array(new rex_version_checker_notification(), 'registerAsExtension'));
  }
  
//  
//  rex_register_extension('PAGE_HEADER', 'rex_be_style_css_add');
//  rex_register_extension('ADDONS_INCLUDED', 'rex_be_add_page');
}
