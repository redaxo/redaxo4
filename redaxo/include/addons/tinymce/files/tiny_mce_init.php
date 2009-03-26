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
 
	header("content-type: application/x-javascript");
	include('../../../redaxo/include/addons/tinymce/config.inc.php');
	$mypage = 'tinymce';
	$n = "\n";
	$clang = 0;
	$version = 0;
	if (isset($_GET['clang']) and trim($_GET['clang']) <> '' and strlen($_GET['clang']) == 1)
		$clang = $_GET['clang'];
	if (isset($_GET['version']) and trim($_GET['version']) <> '' and strlen($_GET['version']) == 2)
		$version = $_GET['version'];

	// Versions-Spezifische Variablen/Konstanten
	$rxa_tinymce['medienpool'] = ($version > '41') ? 'mediapool' : 'medienpool';
?>

function rexCustomFileBrowser(field_name, url, type, win)
{
	if (type == 'image' || type == 'media')
	{
		cmsURL = "index.php?page=<?php echo $rxa_tinymce['medienpool']; ?>&tinymce=true&opener_input_field="+field_name+"&clang="+<?php echo $clang; ?>;
		popupTitle = 'Medienpool';
	}
	if (type == 'file')
	{
		cmsURL = "index.php?page=<?php echo $rxa_tinymce['linkmap']; ?>&tinymce=true&opener_input_field="+field_name+"&clang="+<?php echo $clang; ?>;
		popupTitle = 'Linkmap';
	}

	tinyMCE.activeEditor.windowManager.open({
		file : cmsURL,
		title : popupTitle,
		width : 760,  // Your dimensions may differ - toy around with them!
		height : 500,
		resizable : "yes",
		inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
		close_previous : "no"
	}, {
		window : win,
		typeid : type,
		input : field_name
	});

	return false;
}

/*
function rexCustomURLConverter(url, node, on_save) {

	if (url.substr(0,6) == 'files/')
	{
		url = '../' + url;
	}

	// Return new URL
	return url;
}
*/

<?php
	// Tiny-Standardkonfiguration ausgeben
	include('../../../redaxo/include/addons/tinymce/classes/class.tinymce.inc.php');
	$tiny = new rexTinyMCEEditor();
	echo $tiny->getConfiguration();
	echo $n . 'tinyMCE.init(tinyMCEInitArray);';
?>