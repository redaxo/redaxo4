<?php

/**
 * TinyMCE Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 *
 * @package redaxo4
 * @version $Id: function_pclzip.inc.php,v 1.3 2008/03/11 16:05:55 kills Exp $
 */

include_once $REX['INCLUDE_PATH'] . '/addons/tinymce/classes/class.pclzip.inc.php';

function rex_a52_extract_archive($file, $msg = '', $path=null )
{
  global $REX;
  if(!$path) $path = '../files/'. $REX['TEMP_PREFIX'];

	$archive = new PclZip($file);
	if ($archive->extract(PCLZIP_OPT_PATH, $path) == 0)
	{
	  die("Error : " . $archive->errorInfo(true));
	}

	if (($list = $archive->listContent()) == 0)
	{
	  die("Error : " . $archive->errorInfo(true));
	}
}

?>