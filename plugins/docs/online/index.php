<?php

include('../classes/config.inc.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>redaxo - documentation</title>
</head>
<frameset cols="200,*">
	<frame src="code/menu.php?lang=<?=$_GET[lang]?>&doc=<?=$_GET[doc]?>&chapter=<?=$_GET[chapter]?>&title=<?=$_GET[title]?>" name="menu" frameborder="0" scrolling="no">
	<frame src="code/main.php?lang=<?=$_GET[lang]?>&doc=<?=$_GET[doc]?>&chapter=<?=$_GET[chapter]?>&title=<?=$_GET[title]?>#title<?=$_GET[title]?>" name="main" scrolling="yes" frameborder="0">
</frameset>
</html>