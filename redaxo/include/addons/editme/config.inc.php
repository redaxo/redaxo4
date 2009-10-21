<?php

/**
 * Editme
 *
 * @author jan@kristinus.de
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// Sprachdateien anhaengen
if($REX["REDAXO"])
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/editme/lang/');


// $REX['ADDON']['rxid']["editme"] = '';
// $REX['ADDON']['page']["editme"] = "editme";
if($REX["REDAXO"])
	$REX['ADDON']['name']["editme"] = $I18N->msg("editme");

// Recht um das AddOn überhaupt einsehen zu können
$REX['ADDON']['perm']["editme"] = 'editme[1]';

// Credits
$REX['ADDON']['version']["editme"] = '0.2';
$REX['ADDON']['author']["editme"] = 'Jan Kristinus';
$REX['ADDON']['supportpage']["editme"] = 'forum.redaxo.de';

// *************
// $REX['PERM'][] = 'editme[1]';
// $REX['PERM'][] = 'editme[2]';

// Für Benutzervewaltung
// $REX['EXTPERM'][] = 'editme[]';


// Linke Navigation
if($REX["REDAXO"])
{
	
	include $REX['INCLUDE_PATH'].'/addons/editme/functions/functions.inc.php';
	
	$REX['ADDON']['editme']['SUBPAGES'] = array( );
	
  $REX['ADDON']['editme']['SUBPAGES'][] = array( '' , $I18N->msg("em_overview"));
 $REX['ADDON']['editme']['SUBPAGES'][] = array( 'generate' , $I18N->msg("em_generate"));
  

	
}
