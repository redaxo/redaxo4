<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>redaxo - documentation - menu</title>
</head>
<link rel=stylesheet type=text/css href=../../../../redaxo/css/style.css>
<body bgcolor=#FAF9F5>
<?php

include('../../classes/config.inc.php');
include('../../classes/class.doc.inc.php');

$Doc = new Doc();

$Doc->Lang = $_GET[lang];
$Doc->DocPath = '../../source/';
$Doc->Doc = $_GET[doc];
$Index = $Doc->loadIndex();

print "<b>Redaxo ".ucfirst($Doc->Doc)." Documentation</b><br><br>";

$chap=1;
foreach($Index as $key=>$var){

	print "<a href=main.php?doc=".$_GET[doc]."&lang=".$_GET[lang]."&chapter=".$key." target=main class=title>".$chap.". ".$var."</a><br>";
	if($_GET[chapter]==$key){
	    $Titles = $Doc->loadChapterTitles($key);
	    if(is_array($Titles)){
	    	$title = 1;
	        foreach($Titles as $k=>$v){
	            print "&nbsp;";
	            print "<a href=main.php?doc=".$_GET[doc]."&lang=".$_GET[lang]."&chapter=".$key."#title".$k." target=main class=black>".$chap.".".$title." ".$v."</a><br>";
	            $title++;
	        }
	    }
	}
	$chap++;
}

?>
<br><br>
<a href="../../pdf/index.php?lang=<?=$_GET[lang]?>&doc=<?=$_GET[doc]?>">Download PDF Version</a><br>
</body>
</html>
