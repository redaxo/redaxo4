<?

// rechte einbauen
// admin[]
// developer[]
// clang[xx], clang[0]
// $REX_USER->isValueOf("rights","csw[0]")

reset($REX[CLANG]);


if (count($REX[CLANG])>1)
{
	echo "<table width=770 cellpadding=0 cellspacing=1 border=0><tr><td width=30 class=dgrey><img src=pics/leer.gif width=16 height=16 vspace=5 hspace=12></td><td class=dgrey>&nbsp;<b>Sprachen:</b> | ";
	$stop = false;
	while( list($key,$val) = each($REX[CLANG]) )
	{
		if (!$REX_USER->isValueOf("rights","admin[]")
		 && !$REX_USER->isValueOf("rights","developer[]") 
		 && !$REX_USER->isValueOf("rights","clang[all]") 
		 && !$REX_USER->isValueOf("rights","clang[$key]") 
		)
		{
			echo "<strike>$val</strike> | ";
			if ($clang == $key)	$stop = true;
		}elseif ($key==$clang) echo "$val | ";
		else echo "<a href=index.php?page=$page&clang=$key$sprachen_add>$val</a> | "; 
	}
	echo "</b></td></tr></table><br>";
	if ($stop)
	{
		echo "<table width=770 cellpadding=0 cellspacing=1 border=0><tr><td width=30 class=warning><img src=pics/warning.gif width=16 height=16 vspace=5 hspace=12></td><td class=warning>&nbsp;&nbsp;You have no permission to this area</td></tr></table>";
		include $REX[INCLUDE_PATH]."/layout_redaxo/bottom.php"; 
		exit;	
	}
}else
{
	$clang = 0;	
}

?>