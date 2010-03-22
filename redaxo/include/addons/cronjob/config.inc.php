<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

if($REX['REDAXO'])
{

  // Sprachdateien anhaengen
  $I18N->appendFile(dirname(__FILE__) .'/lang/');
  
  $REX['ADDON']['rxid']['cronjob'] = '630';
  $REX['ADDON']['name']['cronjob'] = $I18N->msg('cronjob_title');
  $REX['ADDON']['perm']['cronjob'] = 'admin[]';
  
  // Credits
  $REX['ADDON']['version']['cronjob'] = '1.0';
  $REX['ADDON']['author']['cronjob'] = 'Gregor Harlan';
  $REX['ADDON']['supportpage']['cronjob'] = 'forum.redaxo.de';
  
  // Subpages
  $REX['ADDON']['navigation']['cronjob']['subpages'] = array(
    array(
      'href' => 'index.php?page=cronjob',
      'active_when' => array(
        'page'=>'cronjob',
        'subpage'=>''),
      'title' => $I18N->msg('cronjob_title')), 
    array(
      'href' => 'index.php?page=cronjob&subpage=log',
      'active_when' => array(
        'page'=>'cronjob',
        'subpage'=>'log'),
      'title' => $I18N->msg('cronjob_log'))
  );
  
  $EP = 'PAGE_CHECKED';

  
  if($REX['USER'] && rex_request('page', 'string') == 'be_dashboard')
  {
    require_once dirname(__FILE__) .'/classes/class.dashboard.inc.php';
    
    rex_register_extension (
      'DASHBOARD_COMPONENT',
      array(new rex_cronjob_component(), 'registerAsExtension')
    );
  }
} else
{
  $EP = 'ADDONS_INCLUDED';
}

define('REX_CRONJOB_LOG_FOLDER', $REX['INCLUDE_PATH'] .'/addons/cronjob/logs/');
define('REX_CRONJOB_TABLE'     , $REX['TABLE_PREFIX'] .'630_cronjobs');

require_once dirname(__FILE__) .'/classes/class.manager.inc.php';
require_once dirname(__FILE__) .'/classes/class.log.inc.php';
require_once dirname(__FILE__) .'/classes/class.cronjob.inc.php';
require_once dirname(__FILE__) .'/classes/types/class.phpcode.inc.php';
require_once dirname(__FILE__) .'/classes/types/class.phpcallback.inc.php';
require_once dirname(__FILE__) .'/classes/types/class.urlrequest.inc.php';

// --- DYN
$REX['ADDON']['nexttime']['cronjob'] = "0";
// --- /DYN

if (isset($REX['ADDON']['nexttime']['cronjob']) 
  && $REX['ADDON']['nexttime']['cronjob'] != 0 
  && time() >= $REX['ADDON']['nexttime']['cronjob'])
{
  rex_register_extension($EP, 'rex_a630_extension');
}

function rex_a630_extension($params) 
{
  global $REX;
  if (!$REX['REDAXO'] || !in_array($REX['PAGE'], array('setup', 'login', 'cronjob')))
  {
    $manager = rex_cronjob_manager::factory();
    $manager->check();
  }
}