<?php

/**
 * TinyMCE Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 *
 * @package redaxo3
 * @version $Id$
 */

include_once $REX['INCLUDE_PATH'] . '/addons/tinymce/classes/class.pclzip.inc.php';

function rex_a52_extract_archive($file, $msg = '' )
{
	$archive = new PclZip($file);
	if ($archive->extract(PCLZIP_OPT_PATH, '../files') == 0)
	{
	  die("Error : " . $archive->errorInfo(true));
	}

	if (($list = $archive->listContent()) == 0)
	{
	  die("Error : " . $archive->errorInfo(true));
	}

	echo '<div style="height:150px;width:770px;overflow:auto;margin-bottom:10px;text-align:center;">';

  if($msg != '')
	  echo '<h3>'. $msg .'</h3>';

	echo '<table border="1" style="margin:0 auto 0 auto; width: 600px">';
	echo '<tr><th>Datei</th><th>Gr&ouml;&szlig;e</th>';
	for ($i = 0; $i < count($list); $i++)
	{
	  echo '<tr>';
	  echo '<td>' . $list[$i]['filename'] . '</td><td>' . $list[$i]['size'] . ' bytes</td>';
	  echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
}

?>