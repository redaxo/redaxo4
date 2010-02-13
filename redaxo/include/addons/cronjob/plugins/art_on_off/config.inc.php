<?php

/**
 * Cronjob Addon - Plugin art_on_off
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
	
	$REX['ADDON']['rxid']["art_on_off"] = '630';
  
  // Credits
  $REX['ADDON']['version']["art_on_off"] = '0.1';
  $REX['ADDON']['author']["art_on_off"] = 'Gregor Harlan';
  $REX['ADDON']['supportpage']["art_on_off"] = 'forum.redaxo.de';
  
  rex_register_extension(
	  'REX_CRONJOB_EXTENSIONS',
	  array('rex_a630_manager','registerExtension'),
	  array(
	    'class' => 'rex_a630_cronjob_art_on_off', 
	    'name' => 'translate:art_on_off'
	  )
  );
  
  require_once dirname(__FILE__).'/classes/class.cronjob.inc.php';

}