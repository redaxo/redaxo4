<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>redaxo - documentation - main</title>
</head>
<link rel=stylesheet type=text/css href=../../../../redaxo/css/style.css>
<body bgcolor=#FAF9F5>
<?php

include('../../classes/config.inc.php');
include('../../classes/class.doc.inc.php');

$Doc = new Doc();
$Doc->Lang      = $_GET[lang];
$Doc->DocPath = '../../source/';
$Doc->Doc = $_GET[doc];
$Doc->CodeStart = "<table bgcolor=#D7D6D3 style=border:1px;border-style:solid;border-color:#000000><tr><td>";
$Doc->CodeEnd   = "</td></tr></table>";
$Index = $Doc->loadIndex();

print "<b><span class=headline>".$Index[$_GET[chapter]]."</span></b><br>";

$DocChapter = $Doc->loadChapter($_GET[chapter]);

$c = 1;
if(is_array($DocChapter)){
	foreach($DocChapter as $var){
		print "<a name=title".$var[id]."></a>\n<br>";
		print "<table bgcolor=#AAB9A8 width=100%><tr><td><font color=#FFFFFF>";
		print "<b>".$_GET[chapter].".".$c." ".$var[title]."</b>";
		print "</td></tr></table>";
		print "<br><br>";
		print $var[text];
		print "<br>";
		$c++;
	}
}

$menuUrl = strrchr($_SERVER[REQUEST_URI],"?");

?>
<script>
parent.frames['menu'].location.href='menu.php<?=$menuUrl?>';
</script>

</body>
</html>
