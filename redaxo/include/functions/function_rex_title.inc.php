<?

function title($head,$subline,$styleclass="grey")
{
	
	echo "<br>";
	
	echo "<table width=770 cellpadding=0 cellspacing=0 border=0>";
	echo "<tr>
		<td class=$styleclass>&nbsp;&nbsp;<b class=head>$head</b></td><td><img src=pics/leer.gif width=1 height=30></td>
		<td rowspan=3><img src=pics/logo.gif width=153 height=61></td></tr>";
	echo "<tr>
		<td><img src=pics/leer.gif width=616 height=1></td>
		<td><img src=pics/leer.gif width=1 height=1></td></tr>";
	echo "<tr>
		<td class=$styleclass><b style='line-height:18px;'>$subline</b></td>
		<td><img src=pics/leer.gif width=1 height=30></td></tr>";
	echo "</table><br>";
}

?>