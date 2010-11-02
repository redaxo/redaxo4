<?php

/**
 * Editme
 *
 * @author jan@kristinus.de
 *
 * @package redaxo4
 * @version svn:$Id$
 * 
 * 
 * TODO:
 * - translate bei den Eingabefeldern setzen
 * - export einbauen, sollte direkt auch als import gehen
 * - import umbauen so dass, wenn Id gesetzt ist, Datensaetze ersetzt werden
 * - wenn medien im medienpool geloescht werden Ÿber EP auch prŸfen ob in EM etwas vorhanden ist
 * - Einfaches OOF fŸr EM bauen, Datensaetze, Listen, Relationen, Export und Import
 * - Caching einbauen
 * - Lšsung finden um einfach spezifische Feldtypen definieren zu kšnnen, INT, VARCHAR, FLOAT etc.
 * - onDelete bei Feldern einbauen
 * - Mehrsprachige Felder besser einbauen, XForm erweitern
 * - weitere XForm-Klassen umbauen fŸr EM.
 * - Generate All immer nach €nderungen bei Tabellen + Feldern
 * - Ÿbersetzung vervollstŠndigen, nur noch tables.inc.php und englisch
 * 
 */

$mypage = 'editme';

if($REX["REDAXO"] && !$REX['SETUP'])
{
	// Sprachdateien anhaengen
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/editme/lang/');

	$REX['ADDON']['name'][$mypage] = $I18N->msg("editme");

	// Credits
	$REX['ADDON']['version'][$mypage] = '2.2';
	$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
	$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
	$REX['ADDON']['navigation'][$mypage] = array(
	  // rootPage nur aktivieren wenn sie direkt ausgewaehlt ist
	  // da alle pages main-pages und daher separate oberpunkte sind
      'activateCondition' => array('page' => $mypage, 'subpage' => ''),
      'hidden' => FALSE
	);
  
	if($REX['USER'] && !$REX['USER']->isAdmin())
    {
      $REX['ADDON']['navigation'][$mypage]['hidden'] = TRUE;
    }
	
	// include $REX['INCLUDE_PATH'].'/addons/editme/functions/functions.inc.php';

	if (!class_exists('rex_xform_manager'))
		require_once($REX['INCLUDE_PATH'].'/addons/xform/manager/classes/basic/class.rex_xform_manager.inc.php');
	
	$t = new rex_xform_manager();
	$t->setType('em');
	$REX['ADDON']['tables'][$mypage] = $t->getTables();

	$subpages = array();
	if(is_array($REX['ADDON']['tables'][$mypage]))
	{
		foreach($REX['ADDON']['tables'][$mypage] as $table)
		{
			// Recht um das AddOn ueberhaupt einsehen zu koennen
			$table_perm = 'em['.$table["table_name"].']';
			$REX['EXTPERM'][] = $table_perm;

			// check active-state and permissions
			if($table['status'] == 1 && $table['hidden'] != 1 &&
			$REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($table_perm)))
			{
				$be_page = new rex_be_page($table['name'], array('page'=>$mypage, 'subpage' => $table['table_name']));
				$be_page->setHref('index.php?page=editme&subpage='.$table['table_name']);
				$subpages[] = new rex_be_main_page($mypage, $be_page);
			}
		}
	}
	$REX['ADDON']['pages'][$mypage] = $subpages;

}

