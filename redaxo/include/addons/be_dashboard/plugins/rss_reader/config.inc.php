<?php

/**
 * RSS Reader Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'rss_reader';

/* Addon Parameter */
$REX['ADDON']['rxid'][$mypage] = '656';
$REX['ADDON']['version'][$mypage] = '1.3';
$REX['ADDON']['author'][$mypage] = 'Markus Staab';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

if($REX["REDAXO"])
{
  $I18N->appendFile(dirname(__FILE__). '/lang/');
  
  $oldReporting = error_reporting(0);
  require_once dirname(__FILE__) .'/classes/class.rss_reader.inc.php';
  error_reporting($oldReporting);
  
  require_once dirname(__FILE__) .'/functions/function_reader.inc.php';

  if(rex_request('page', 'string') == 'be_dashboard')
  {
    require_once dirname(__FILE__) .'/classes/class.dashboard.inc.php';

    rex_register_extension(
      'DASHBOARD_COMPONENT',
      array(new rex_rss_reader_component(), 'registerAsExtension')
    );
  }
  
//  require_once $REX['INCLUDE_PATH'].'/addons/'. $mypage .'/extensions/function_extensions.inc.php';
//  
//  rex_register_extension('PAGE_HEADER', 'rex_be_style_css_add');
//  rex_register_extension('ADDONS_INCLUDED', 'rex_be_add_page');
}
