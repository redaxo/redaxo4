<?

title("Statistiken","&nbsp;&nbsp;&nbsp;<a href=index.php?page=stats&sub=stats>Statistiken</a> | <a href=index.php?page=stats&sub=auswertung>Auswertung</a>");

//
// REACTING
//

if ( $funktion == 'evaluate' )
{

	$a = new stat;
	if ( $a->evaluate($year,$month) ) $err_msg = "Die Auswertung wurde erfolgreich erstellt.<br>";
	else $err_msg = "Fehler bei der Auswertung....<br>";
	
}

//
// suche monate und jahre für die wir logs haben
$pfad = $REX[INCLUDE_PATH]."/generated/logs/";

$months = Array();
$years = Array();

if (is_dir($pfad)) 
{
	if ($dh = opendir($pfad)) 
	{
		while (($file = readdir($dh)) !== false)
		{
			if ( strstr($file,".log") == ".log" )
			{
				$years[substr($file, 0, 4)] = TRUE;
				$months[substr($file, 5, 2)] = TRUE;	 
			}
		}  	
	}
	closedir($dh);
} else 
{
	echo "error: ".$this->path." is no dir";

}

if (count($years)==0)
{
	echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=warning>Es existiert noch keine Logdatei !</td></tr></table>";

}else
{
	
	$monname = Array ( "01" => "Januar","02" => "Februar","03" => "Maerz", "04" => "April","05" => "Mai","06" => "Juni", "07" => "Juli","08" => "August","09" => "September", "10" => "Oktober","11" => "November","12" => "Dezember");
	
	$amon = Array();
	$ajahr = Array();
	foreach ( $months as $k => $v )
	{
		$amon[] = $k;
	}
	
	foreach ( $years as $k => $v )
	{
		$ajahr[] = $k;
	}
	
	//
	// build selects
	//
	
	$msel = "<select name=month size=1>";
	foreach( $amon as $k => $v )
	{
		if ( $v == $month ) $msel .= "<option value=$v selected>".$monname[$v]."</option>";
		else $msel .= "<option value=$v>".$monname[$v]."</option>";
	}
	$msel .= "</select>";
	
	$jsel = "<select name=year size=1>";
	foreach( $ajahr as $k => $v )
	{
		if ( $v == $year ) $jsel .= "<option selected>$v</option>";
		else $jsel .= "<option>$v</option>";
	}
	$jsel .= "</select>";
	
	//
	// ACTING 
	//
	
	if ( $sub == 'stats' OR !isset($sub) )
	{
		if ( !isset($show) && isset($year) && isset($month)) $show = "day";
	
		if ($year == "") $year = date("Y");
		if ($month == "") $month = date("m");
				
		// echo $REX[INCLUDE_PATH]."/generated/logs/".$year."_".$month.".php";
		
			
		if (  $funktion == "show" OR isset($show))
		{
			if ( !file_exists($REX[INCLUDE_PATH]."/generated/logs/".$year."_".$month.".php") )
			{
				$err_msg = "Diese Auswertung wurde noch nicht erstellt";
			}
		}
		
		if ( isset($err_msg) ) $err_msg = "<tr><td colspan=4 class=warning>$err_msg</td></tr>";
		
		
		echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
			<tr>
				<th align=left colspan=4>Statistiken einsehen</th>
			</tr>
			$err_msg
			<tr>
				<form action=index.php?page=stats&sub=stats method=post>
				<input type=hidden name=funktion value=show>
				<td class=grey>Monat:</td>
				<td class=grey>$msel</td>
				<td class=grey>Jahr:</td>
				<td class=grey>$jsel</td>
			</tr>
			<tr>
				<td class=grey colspan=4><input type=submit value=Einsehen></td>
			</tr>
			</form>
		  </table>";
		
		if (  $funktion == "show" OR isset($show))
		{
			if ( file_exists($REX[INCLUDE_PATH]."/generated/logs/".$year."_".$month.".php") )
			{
				 include($REX[INCLUDE_PATH]."/generated/logs/".$year."_".$month.".php");
			}
		}
	
	}
	
	
	if ($sub == 'auswertung' )
	{
	
		if ( isset($err_msg) ) $err_msg = "<tr><td colspan=4 class=warning>$err_msg</td></tr>";
		
		echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
			<tr>
				<th align=left colspan=4>Auswertungen erstellen</th>
			</tr>
			$err_msg
			<tr>
				<form action=index.php?page=stats&sub=auswertung method=post>
				<input type=hidden name=funktion value=evaluate>
				<td class=grey>Monat:</td>
				<td class=grey>$msel</td>
				<td class=grey>Jahr:</td>
				<td class=grey>$jsel</td>
			</tr>
			<tr>
				<td class=grey colspan=4><input type=submit value=Auswerten></td>
			</tr>
			</form>
		  </table>
			";
	
	}
}

?>
