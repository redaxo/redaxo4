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
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/cronjob/lang/');
	
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
	
} else
{
  $EP = 'ADDONS_INCLUDED';
}

require_once $REX['INCLUDE_PATH'].'/addons/cronjob/classes/class.rex_a630_cronjob.inc.php';

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
    rex_a630_cronjob::execute();
}