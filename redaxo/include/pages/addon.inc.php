<?php

title($I18N->msg("addon"),"");

$dir = $REX[INCLUDE_PATH]."/addons/";
chdir($dir);
$hdl = opendir(".");
while (false !== ($file = readdir($hdl)))
{
	if($file != ".." AND $file != ".") if(is_dir($file))
	{
		$ADDONS[] = $file;
	}
}
chdir("../..");

$SP = true;	// SHOW PAGE ADDON LIST
$WA = false;	// WRITE ADDONS TO FILE: include/addons.inc.php

// ----------------- HELPPAGE
if ($spage=="help" && array_search($addonname,$ADDONS) !== false)
{
	echo "<table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>";
	echo "<tr><th>".$I18N->msg("addon_help")." $addonname</th></tr>";
	echo "<tr><td>";
	if (!@include $REX[INCLUDE_PATH]."/addons/$addonname/help.inc.php") echo $I18N->msg("addon_no_help_file");
	echo "&nbsp;</td></tr>";
	echo "<tr><td><a href=index.php?page=addon>".$I18N->msg("addon_back")."</a></td></tr>";
	echo "</table>";
	$SP = false;
}


// ----------------- FUNCTIONS
if (array_search($addonname,$ADDONS) !== false)
{
	// $addonname ist vorhanden
	if ($install == 1)
	{
		if (!@include $REX[INCLUDE_PATH]."/addons/$addonname/install.inc.php")
		{
			$errmsg = $I18N->msg("addon_install_not_found");
		}else
		{
			if ($REX[ADDON][install][$addonname] != 1)
			{
				$errmsg = "'$addonname' ".$I18N->msg("addon_no_install")." ";
				if ($REX[ADDON][installmsg][$addonname] == "") $errmsg .= $I18N->msg("addon_no_reason");
				else $errmsg .= $REX[ADDON][installmsg][$addonname];
			}else
			{
				// include config.
				// if config is broken installation prozess will be terminated -> no install -> no errors in redaxo
				
				// skip config if it is a reinstall !
				if($REX[ADDON][status][$addonname]!=1){
					include $REX[INCLUDE_PATH]."/addons/$addonname/config.inc.php";
				}
				$errmsg = $addonname." ".$I18N->msg("addon_installed");
				$REX[ADDON][install][$addonname] = 1;
				$errmsg = $I18N->msg("addon_installed");
				$WA = true;
			}
		}
	}elseif($activate == 1)
	{
		if ($REX[ADDON][install][$addonname]!=1)
		{
			$errmsg = $I18N->msg("addon_no_activation");
		}else
		{
			$REX[ADDON][status][$addonname] = 1;
			$errmsg = $I18N->msg("addon_activated");
			$WA = true;
		}
	}elseif($activate == "0")
	{
		$REX[ADDON][status][$addonname] = 0;
		$errmsg = $I18N->msg("addon_deactivated");
		$WA = true;
	}
	
}

// ----------------- WRITE INCLUDE/ADDONS FILE
if ($WA)
{
	$content = "// --- DYN\n\r";
	reset($ADDONS);
	for ($i=0;$i<count($ADDONS);$i++)
	{
		$cur = current($ADDONS);
		if ($REX['ADDON']['install'][$cur]!=1) $REX['ADDON']['install'][$cur] = 0;
		if ($REX['ADDON']['status'][$cur]!=1) $REX['ADDON']['status'][$cur] = 0;
		
		$content .= "
\$REX['ADDON']['install']['$cur'] = ".$REX['ADDON']['install'][$cur].";
\$REX['ADDON']['status']['$cur'] = ".$REX['ADDON']['status'][$cur].";
";
		next($ADDONS);	
	}
	$content .= "\n\r// --- /DYN";

	$file = $REX[INCLUDE_PATH]."/addons.inc.php";
	$h = fopen($file,"r");
	$fcontent = fread($h,filesize($file));
	$fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)",$content,$fcontent);
	fclose($h);

	$h = fopen($file,"w+");
	fwrite($h,$fcontent,strlen($fcontent));
	fclose($h);

	// echo nl2br(htmlspecialchars($fcontent));
}

// ----------------- OUT
if ($SP)
{

	if ($errmsg != "") echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=warning>$errmsg</td></tr></table><br>";
	
	echo "<table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
		<form action=index.php method=post>
		<input type=hidden name=page value=user>
		<input type=hidden name=user_id value=$user_id>
		<tr>
			<th>".$I18N->msg("addon_hname")."</th>
			<th>".$I18N->msg("addon_hinstall")."</th>
			<th>".$I18N->msg("addon_hactive")."</th>
			<th><b></b></th>
		</tr>";
	
	reset($ADDONS);
	for ($i=0;$i<count($ADDONS);$i++)
	{
		$cur = current($ADDONS);
		if ($REX[ADDON][install][$cur] == 1) $install = $I18N->msg("addon_yes")." - <a href=index.php?page=addon&addonname=$cur&install=1>".$I18N->msg("addon_reinstall")."</a>";
		else $install = $I18N->msg("addon_no")." - <a href=index.php?page=addon&addonname=$cur&install=1>".$I18N->msg("addon_install")."</a>";
		if ($REX[ADDON][status][$cur] == 1) $status = $I18N->msg("addon_yes")." - <a href=index.php?page=addon&addonname=$cur&activate=0>".$I18N->msg("addon_deactivate")."</a>";
		else $status = $I18N->msg("addon_no")." - <a href=index.php?page=addon&addonname=$cur&activate=1>".$I18N->msg("addon_activate")."</a>";
	
		echo "<tr>
			<td width=100>$cur [<a href=index.php?page=addon&spage=help&addonname=$cur>?</a>]</td>
			<td width=100>$install</td>
			<td width=100>$status</td>
			<td width=100></td>
			</tr>";
			
		next($ADDONS);	
	}
	echo "</table>";
}

?>