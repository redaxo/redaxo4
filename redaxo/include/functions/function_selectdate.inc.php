<?

function selectdate($date,$extens){

	$ausgabe = "<select name=jahr$extens size=1>\n";
	$ausgabe .= "<option></option>";
	for ($i=date('Y')-1;$i<date('Y')+10;$i++){
		$ausgabe .= "<option value=\"$i\"";
		if ($i == substr($date,0,4)){ $ausgabe .= " selected"; }
		$ausgabe .= ">$i\n";
	}
	$ausgabe .= "</select>";


	$ausgabe .= "<select name=monat$extens size=1>\n";
	$ausgabe .= "<option></option>";
	for ($i=1;$i<13;$i++){
		if ($i<10){ $ii = "0".$i; }else{ $ii = $i; }
		$ausgabe .= "<option value=\"$ii\"";
		if ($ii == substr($date,4,2)){ $ausgabe .= " selected"; }
		$ausgabe .= ">$ii\n";
	}
	$ausgabe .= "</select>";

	$ausgabe .= "<select name=tag$extens size=1>\n";
	$ausgabe .= "<option></option>";
	for ($i=1;$i<32;$i++){
		if ($i<10){ $ii = "0".$i; }else{ $ii = $i; }
		$ausgabe .= "<option value=\"$ii\"";
		if ($ii == substr($date,6,2)){ $ausgabe .= " selected"; }
		$ausgabe .= ">$ii\n";
	}
	$ausgabe .= "</select>";

	return $ausgabe;
}

?>
