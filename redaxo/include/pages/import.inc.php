<?

title("Im-/Export","");




##
## main out
##


if($FORM[submit])
{
	
	if($_FILES['FORM']['size']['importfile'] < 1)
		$err_msg = $I18N->msg("no_import_file_chosen_or_wrong_version")."<br>";
	else {
		$file_temp = $REX[INCLUDE_PATH]."/install/temp.sql";
		if (@move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'],$file_temp))
		{
			$h = fopen($file_temp,"r");
			$conts = fread($h,filesize($file_temp));
			if(!ereg("## Redaxo Database Dump Version ".$REX[version]." \n",$conts))
				$err_msg = $I18N->msg("no_valid_import_file").".<br>";
			else {
				$conts = str_replace("## Redaxo Database Dump Version ".$REX[version]." \n","",$conts);
				$all = explode("\n",$conts);
				$tabs = new sql;
				$tabs->setquery("SHOW TABLES");
				
				$del = new sql;
				for($i=0;$i<$tabs->rows;$i++,$tabs->next(),$del->flush()) 
					if($tabs->getvalue("Tables_in_".$DB[1][NAME]) != "rex_user") 
						$del->setquery("DROP TABLE ".$tabs->getvalue("Tables_in_".$DB[1][NAME]));
				
				$add = new sql;
				foreach($all as $hier){
					$add->setquery(Trim(str_replace("||||||+N+||||||","\n",$hier),";"));
					$add->flush();
				}
				
				$err_msg = $I18N->msg("database_imported").". ".$I18N->msg("entry_count",count($all))." | ".$I18N->msg("go_to_special_features").".<br>";
				@unlink($file_temp);
			}
		}else
		{
			$err_msg = $I18N->msg("file_could_not_be_uploaded")." ".$I18N->msg("you_have_no_write_permission_in","include/install")." ".$I18N->msg("check_install_php")."<br>";
		}		
	}
	
}


if($FORM[filesubmit])
{
	if($_FILES['FORM']['size']['importfile'] < 1)
		$err_msg = $I18N->msg("no_import_file_chosen")."<br>";
	elseif((substr($_FILES['FORM']['name']['importfile'],-6) != 'tar.gz') OR (substr($_FILES['FORM']['name']['importfile'],0,7) != 'redaxo_'))
		$err_msg = $I18N->msg("imported_file_not_valid")."<br>";
	else {
		$file_temp = $REX[INCLUDE_PATH]."/install/temp.tar.gz";
		if (@move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'],$file_temp))
		{
			$tar = new tar;
			$tar->openTAR($file_temp);
			if(!$tar->extractTar())
			{
				$err_msg = $I18N->msg("problem_when_extracting")."<br>";
				if (count($tar->message) > 0)
				{
					$err_msg .= $I18N->msg("create_dirs_manually")."<br>";
					reset($tar->message);
					for ($fol=0;$fol<count($tar->message);$fol++)
					{
						$err_msg .= key($tar->message)."<br>";
						
						next($tar->message);
					}
				}
			}
			else $err_msg = $I18N->msg("file_imported")."<br>";
			@unlink($file_temp);
		}else
		{
			$err_msg = $I18N->msg("file_could_not_be_uploaded")." ".$I18N->msg("you_have_no_write_permission_in","include/install")." ".$I18N->msg("check_install_php")."<br>";
		}
	}
}

if(isset($err_msg)) $err_msg = "<tr><td class=warning colspan=2><font color=error>$err_msg</font></td></tr>";


## make dir inputs
chdir("..");
$handle = opendir(".");
$dirstring = "";

while (false !== ($file = readdir($handle)))
{
	if($file != ".." AND $file != ".")
	{
		if(is_dir($file))
		{
			if($file != "redaxo")
			{
				$dirstring .= "<input type=checkbox name=DIR[$file] value=true>$file/ &nbsp;&nbsp;\n";
			}
		}
	}
	
	
}
 
chdir("redaxo");

?>

<table border=0 cellpadding=5 cellspacing=1 width=770>
<form action=index.php method=post name=formu enctype="multipart/form-data">
<input type=hidden name=FORM[submit] value=true>

<tr><th colspan=2 align=left>
<?=$I18N->msg('db_import_export')?>
</th></tr>

<?=$err_msg?>

<tr><td class=dgrey>
<?=$I18N->msg("db_import")?></td><td class=dgrey><input type=file name=FORM[importfile]>&nbsp;&nbsp;&nbsp;<input type=submit name=page value=<?=$I18N->msg("import")?>>
</td></tr>

<tr><td class=dgrey>
<?=$I18N->msg("db_export")?></td><td class=dgrey><input type=submit name=page value=<?=$I18N->msg("export")?>> 
</td></form></tr>

<tr>
<form action=index.php method=post name=formu enctype="multipart/form-data">
<input type=hidden name=FORM[filesubmit] value=TRUE>
<th colspan=2 align=left>
<?=$I18N->msg("files_import_export")?>
</th></tr>

<tr><td class=dgrey>
<?=$I18N->msg("files_import")?></td><td class=dgrey><input type=file name=FORM[importfile]>&nbsp;&nbsp;&nbsp;<input type=submit name=page value=<?=$I18N->msg("import")?>>
</td></tr>

<tr><td class=dgrey>
<?=$I18N->msg("files_export")?></td><td class=dgrey><input type=submit name=page value=<?=$I18N->msg("export")?>> &nbsp;&nbsp;<? echo $dirstring; ?>
</td></form></tr>


</table>