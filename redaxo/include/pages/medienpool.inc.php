<?

// hier kommt das popup fenster für den medienpool rein
//
// dateien auswählen und löschbar machen.
// eventuell spaeter editierbar
// 
// 
// Imagemagick nutzen ?



// FUNC: Datei add
if ($func == "add")
{

	echo "add";	
	
}

// FUNC: Datei del
// nur wenn datei nicht mehr in rex_article auftaucht..


// FUNC: Datei edit


// FUNC: Überprüfung ob alle Dateien auch im files Ordner
// cleanup

// Dateisuche


// Dateiliste
// Bei Bilder mit thumbnail





echo "<html><head><title>".$REX[SERVERNAME]." - $page_name</title>
<link rel=stylesheet type=text/css href=css/style.css>
<script language=Javascript>
<!--
var redaxo = true;
function selectMedia(filename)
{
	opener.document.REX_FORM.$opener_input_field.value = filename;
	self.close();
}
//-->
</script>
</head><body bgcolor=#ffffff>
<table border=0 cellpadding=5 cellspacing=0 width=100%>
<tr><td colspan=3 class=grey align=right>".$REX[SERVERNAME]."</td></tr>
<tr><td class=greenwhite><b>Medienpool</b></td></tr>";

$files = new sql;
$files->setQuery("select * from rex_file order by filename");

for ($i=0;$i<$files->getRows();$i++)
{
	echo "<tr><td class=grey><br><a href=javascript:selectMedia('".$files->getValue("filename")."');>".$files->getValue("filename")."</a></td></tr>";
	$files->next();	
}

echo "<tr><td class=grey>&nbsp;</td></tr>";
echo "</table></body></html>";

?>