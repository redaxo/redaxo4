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

	header("content-type: text/css");
	include(dirname( __FILE__) . '/../config.inc.php');

	$mypage = 'tinymce';
	$n = "\n";
	$clang = 0;

	if (isset($_GET['clang']) and trim($_GET['clang']) <> '' and strlen($_GET['clang']) == 1)
		$clang = $_GET['clang'];
?>

Standardwerte TinyMCE-Konfiguration:


<?php
	include(dirname( __FILE__) . '/../classes/class.tinymce.inc.php');
	$tiny = new rexTinyMCEEditor();
	echo $tiny->getConfiguration();
?>