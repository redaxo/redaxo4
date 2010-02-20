<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

if($REX["REDAXO"])
{

  // Sprachdateien anhaengen
	$I18N->appendFile(dirname(__FILE__) .'/lang/');
	
	$REX['ADDON']['rxid']["cronjob"] = '630';
  $REX['ADDON']['name']["cronjob"] = $I18N->msg("cronjob_title");
  $REX['ADDON']['perm']["cronjob"] = 'admin[]';
  
  // Credits
  $REX['ADDON']['version']["cronjob"] = '0.1';
  $REX['ADDON']['author']["cronjob"] = 'Gregor Harlan';
  $REX['ADDON']['supportpage']["cronjob"] = 'forum.redaxo.de';
	
	// Subpages
	$REX['ADDON']['cronjob']['SUBPAGES'] = array(array('',$I18N->msg("cronjob_title")), array('log','Log'));
	
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

define('REX_CRONJOB_LOG_FOLDER', $REX['INCLUDE_PATH'].'/addons/cronjob/logs/');

require_once dirname(__FILE__) .'/classes/class.rex_cronjob_manager.inc.php';
require_once dirname(__FILE__) .'/classes/class.rex_cronjob.inc.php';
require_once dirname(__FILE__) .'/classes/class.rex_cronjob_phpcode.inc.php';
require_once dirname(__FILE__) .'/classes/class.rex_cronjob_phpcallback.inc.php';
require_once dirname(__FILE__) .'/classes/class.rex_cronjob_urlrequest.inc.php';
require_once dirname(__FILE__) .'/classes/class.rex_cronjob_log.inc.php';

// --- DYN
$REX["ADDON"]["nexttime"]["cronjob"] = "0";
// --- /DYN

if (isset($REX["ADDON"]["nexttime"]["cronjob"]) 
  && $REX["ADDON"]["nexttime"]["cronjob"] != 0 
  && time() >= $REX["ADDON"]["nexttime"]["cronjob"])
{
  rex_register_extension($EP, 'rex_a630_extension');
}

function rex_a630_extension($params) 
{
  global $REX;
  if (!$REX['REDAXO'] || !in_array($REX['PAGE'], array('setup', 'login', 'cronjob')))
    rex_cronjob_manager::checkCronjobs();
}