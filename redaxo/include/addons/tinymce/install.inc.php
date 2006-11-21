<?php

//TinyMCE 2.0.6.1 Redaxo Mods by Dave Holloway @ GN2-Netwerk.de

include_once $REX['INCLUDE_PATH'] . '/addons/tinymce/classes/class.pclzip.inc.php';

$archive = new PclZip('include/addons/tinymce/js/tinymce.zip');
if ($archive->extract(PCLZIP_OPT_PATH, '../files') == 0)
{
  die("Error : " . $archive->errorInfo(true));
}

if (($list = $archive->listContent()) == 0)
{
  die("Error : " . $archive->errorInfo(true));
}

echo '<div style="height:200px;width:770px;overflow:auto;margin-bottom:10px;text-align:center;">';
echo '<h3>Addon wird extrahiert...</h3>';
echo '<table border="1" style="margin:0 auto 0 auto;">';
echo '<tr><th>Datei</th><th>Gr&ouml;&szlig;e</th>';
for ($i = 0; $i < count($list); $i++)
{
  echo '<tr>';
  echo '<td>' . $list[$i]['filename'] . '</td><td>' . $list[$i]['size'] . ' bytes</td>';
  echo '</tr>';
}
echo '</table>';
echo '</div>';

$REX['ADDON']['install']['tinymce'] = true;

?>
