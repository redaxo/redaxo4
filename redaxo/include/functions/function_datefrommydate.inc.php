<?

function date_from_mydate($date,$format){

	if ($format=="" or $format=="date"){ $format="d M Y"; }
	if ($format=="time"){ $format="H:i:s"; }
	if ($format=="datetime"){ $format="d M Y H:i\h"; }

	$new_date = date($format,mktime(
			substr($date,8,2),
			substr($date,10,2),
			substr($date,12,2),
			substr($date,4,2),
			substr($date,6,2),
			substr($date,0,4)
			));

	return $new_date;

}

?>