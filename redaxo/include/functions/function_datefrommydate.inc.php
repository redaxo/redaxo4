<?

// V2: date_from_mydate($date) ok
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

function createDate($datum,$typ)
{
	$year = substr($datum,0,4);
	$month = substr($datum,4,2);
	$day = substr($datum,6,2);

	if ($month != 0)
	{
		$return = "$month-$year";
	}

	if ($day != 0)
	{
		if ($typ == "en")
		{
			$return = "$month-$day-$year";
		}else
		{
			$return = "$day-$month-$year";
		}
	}

	return $return;
	
}



?>