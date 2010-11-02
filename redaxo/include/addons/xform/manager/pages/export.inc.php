<?php

ob_end_clean();

$data = "";
$fields = array();
$g = rex_sql::factory();
$g->setQuery($sql);

foreach($g->getArray() as $d)
{
	if($data == "")
	{
		foreach($d as $a => $b)
		{
			$fields[] = '"'.$a.'"';
		}
		$data = implode(';',$fields);
	}

	foreach($d as $a => $b)
	{
		$d[$a] = '"'.str_replace('"','""',$b).'"';
	}
	$data .= "\n".implode(';',$d);
}

// ----- download - save as

$filename = 'export_data_'.date('YmdHis').'.csv';
$filesize = strlen($data);
$filetype = "application/octetstream";
$expires = "Mon, 01 Jan 2000 01:01:01 GMT";
$last_modified = "Mon, 01 Jan 2000 01:01:01 GMT";

header("Expires: ".$expires); // Date in the past
header("Last-Modified: " . $last_modified); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
header('Content-Type: '.$filetype.'; name="'.$filename.'"');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Description: "'.$filename.'"');
header('Content-Length: '.$filesize);

echo $data;

exit;

// Feldnamen der DB auslesen und eins nach dem anderen einspielen
			// $gc->setQuery('SHOW COLUMNS FROM "'.$table['table_name'].'"');
