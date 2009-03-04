<?php
/**
 * TinyMCE Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @author andreas[dot]eberhard[at]redaxo[dot]de Andreas Eberhard
 * @author <a href="http://rex.andreaseberhard.de">rex.andreaseberhad.de</a>
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

	$mypage = 'tinymce';

	// Addon Settings
	$REX['ADDON']['rxid'][$mypage] = '52';
	$REX['ADDON']['page'][$mypage] = $mypage;
	$REX['ADDON']['name'][$mypage] = 'TinyMCE';
	$REX['ADDON']['perm'][$mypage] = 'tiny_mce[]';
	$REX['ADDON']['version'][$mypage] = '1.5';
	$REX['ADDON']['author'][$mypage] = 'Wolfgang Hutteger, Markus Staab, Dave Holloway, Andreas Eberhard';
	$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

	$REX['PERM'][] = 'tiny_mce[]';

	// REDAXO-Version
	$rxa_tinymce['rexversion'] = isset($REX['VERSION']) ? $REX['VERSION'] . $REX['SUBVERSION'] : '';

	// Versions-Spezifische Variablen/Konstanten
	$rxa_tinymce['medienpool'] = ($rxa_tinymce['rexversion'] > '41') ? 'mediapool' : 'medienpool';
   $rxa_tinymce['linkmap'] = 'linkmap';
   


// Konfigurationsvariablen, werden in pages/settings.inc.php geschrieben
// -----------------------------------------------------------------------------
// --- DYN
$REX['ADDON'][$mypage]['active'] = 'on';
$REX['ADDON'][$mypage]['lang'] = 'de';
$REX['ADDON'][$mypage]['pages'] = 'content, metainfo';
$REX['ADDON'][$mypage]['foreground'] = '';
$REX['ADDON'][$mypage]['background'] = '';
$REX['ADDON'][$mypage]['validxhtml'] = '';
$REX['ADDON'][$mypage]['theme'] = 'default';
$REX['ADDON'][$mypage]['skin'] = 'default';
$REX['ADDON'][$mypage]['emoticons'] = 'on';
$REX['ADDON'][$mypage]['media'] = 'on';
$REX['ADDON'][$mypage]['highlight'] = '';
// --- /DYN
// -----------------------------------------------------------------------------



	// Wenn nicht von REDAXO aufgerufen dann return
	// Wird benötigt für den JavaScript Aufruf
	//   <script type="text/javascript" src="../redaxo/include/addons/tinymce/tinymce/jscripts/tiny_mce_init.php?clang=0&amp;version=41" id="TinyMCEInit"></script>
	if (!isset($REX['REDAXO']))
		return;

	// Nur im Backend
	if ($REX['REDAXO'])
	{
		// rexTinyMCEEditor-Klasse
		include_once $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/classes/class.tinymce.inc.php';

		// Funktionen für TinyMCE
		include_once $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/functions/function_rex_tinymce.inc.php';

		// Kompatibilitäts-Funktionen
		include_once $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/functions/function_rex_compat.inc.php';

		// Im Backend Sprachobjekt anlegen
		$I18N_A52 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');

		// Request-Variablen
		$page = rex_request('page', 'string');
		$tinymce = rex_request('tinymce', 'string');

		// ausgewählte Seiten laut Konfiguration
		$includepages = explode(',', trim(str_replace(' ', '', $REX['ADDON'][$mypage]['pages'])));
		if (!in_array('content', $includepages)) // Bei 'content' immer!
			$includepages[] = 'content';

		// TinyMCE ins Backend integrieren, nur in ausgewählten Seiten laut Konfiguration
		if(($page <> '') and in_array($page, $includepages) and ($REX['ADDON'][$mypage]['active'] == 'on'))
		{
			rex_register_extension('OUTPUT_FILTER', 'a52_tinymce_opf');
		}

		// Outputfilter für Medienpool und Linkmap
		if ($REX['ADDON'][$mypage]['active'] == 'on') // nur wen TinyMCE aktiv
		{
			if ((($page == $rxa_tinymce['medienpool']) or ($page == $rxa_tinymce['linkmap'])) and ($tinymce == 'true'))
			{
				rex_register_extension('MEDIA_ADDED', 'a52_tinymce_mediaadded');
				rex_register_extension('OUTPUT_FILTER', 'a52_tinymce_opf_media_linkmap');
			}
		}
	} // Ende nur im Backend
?>