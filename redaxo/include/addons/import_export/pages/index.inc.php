<?php

// Für größere Exports den Speicher für PHP erhöhen.

@ini_set('memory_limit', '32M');

include_once $REX['INCLUDE_PATH']. '/addons/'. $page .'/classes/class.tar.inc.php';
include_once $REX['INCLUDE_PATH']. '/addons/'. $page .'/functions/function_folder.inc.php';

// ------------------------------ FUNC
$msg = "";

if ($impname != "")
{
	$impname = str_replace("/","",$impname);

	if ($function == "dbimport" && substr($impname,-4,4) != ".sql") $impname = "";
	elseif ($function == "fileimport" && substr($impname,-7,7) != ".tar.gz") $impname = "";

}

if ($exportfilename == "") $exportfilename = 'rex_'.$REX[version].'_'.date("Ymd");


if ($function == "delete")
{
	
	// ------------------------------ FUNC DELETE
	
	if (unlink($REX[INCLUDE_PATH]."/addons/$page/files/$impname"));
	$msg = $I18N_ADDON->msg("file_deleted");

}elseif ($function == "dbimport")
{
	
	// ------------------------------ FUNC DBIMPORT
	
	// noch checken das nicht alle tabellen geloescht werden
	// install/temp.sql aendern
		
	if($_FILES['FORM']['size']['importfile'] < 1 && $impname == "")
		$msg = $I18N_ADDON->msg("no_import_file_chosen_or_wrong_version")."<br>";
	else {
		if ($impname != "") $file_temp = $REX[INCLUDE_PATH]."/addons/$page/files/$impname";
		else $file_temp = $REX[INCLUDE_PATH]."/addons/$page/files/sql.temp";

		if ($impname != "" || @move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'],$file_temp))
		{
			
			$h = fopen($file_temp,"r");
			$conts = fread($h,filesize($file_temp));
			
			// ## Redaxo Database Dump Version x.x
			
			if(ereg("## Redaxo Database Dump Version ".$REX[version]."\n",$conts))
				$msg = $I18N_ADDON->msg("no_valid_import_file").". [## Redaxo Database Dump Version ".$REX[version]."] is missing<br>";
			else {
				$conts = str_replace("## Redaxo Database Dump Version ".$REX[version]." \n","",$conts);
				$all = explode("\n",$conts);
				
				/*
				$tabs = new sql;
				$tabs->setquery("SHOW TABLES");
				$del = new sql;
				for($i=0;$i<$tabs->rows;$i++,$tabs->next(),$del->flush()) 
					if($tabs->getvalue("Tables_in_".$DB[1][NAME]) != "rex_user") 
						$del->setquery("DROP TABLE ".$tabs->getvalue("Tables_in_".$DB[1][NAME]));
				*/
				
				$add = new sql;
				foreach($all as $hier){
					$add->setquery(Trim(str_replace("||||||+N+||||||","\n",$hier),";"));
					$add->flush();
				}

				$msg = $I18N_ADDON->msg("database_imported").". ".$I18N_ADDON->msg("entry_count",count($all))."<br>";
				
				unset($REX[CLANG]);
				$gl = new sql;
				$gl->setQuery("select * from rex_clang");
				for ($i=0;$i<$gl->getRows();$i++)
				{
					$id = $gl->getValue("id");
					$name = $gl->getValue("name");
					$REX[CLANG][$id] = $name;
					$gl->next();
				}
				$msg .= rex_generateAll();
				
			}
			if ($impname == "") @unlink($file_temp);
		}else
		{
			$msg = $I18N_ADDON->msg("file_could_not_be_uploaded")." ".$I18N_ADDON->msg("you_have_no_write_permission_in","addons/$page/files/")." <br>";
		}		
	}

}elseif($function == "fileimport")
{

	// ------------------------------ FUNC FILEIMPORT


	if($_FILES['FORM']['size']['importfile'] < 1 && $impname == ""){
		$msg = $I18N_ADDON->msg("no_import_file_chosen")."<br>";
	}else {
		if ($impname != "") $file_temp = $REX[INCLUDE_PATH]."/addons/$page/files/$impname";
		else $file_temp = $REX[INCLUDE_PATH]."/addons/$page/files/tar.temp";

		if ($impname != "" || @move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'],$file_temp))
		{
			$tar = new tar;
			$tar->openTAR($file_temp);
			if(!$tar->extractTar())
			{
				$msg = $I18N_ADDON->msg("problem_when_extracting")."<br>";
				if (count($tar->message) > 0)
				{
					$msg .= $I18N_ADDON->msg("create_dirs_manually")."<br>";
					reset($tar->message);
					for ($fol=0;$fol<count($tar->message);$fol++)
					{
						$msg .= absPath( str_replace( "'", "", key( $tar->message)))."<br>";
						
						next($tar->message);
					}
				}
			}
			else $msg = $I18N_ADDON->msg("file_imported")."<br>";
			if ($impname == "") @unlink($file_temp);
		}else
		{
			$msg = $I18N_ADDON->msg("file_could_not_be_uploaded")." ".$I18N_ADDON->msg("you_have_no_write_permission_in","addons/$page/files/")." <br>";
		}
	}



	
}elseif($function == "export")
{
	
	// ------------------------------ FUNC EXPORT	
	
	$exportfilename = strtolower($exportfilename);
	$exportfilename = stripslashes($exportfilename);
	$filename = ereg_replace("[^\.a-z0-9_\-]","",$exportfilename);
	
	
	if ($filename != $exportfilename)
	{
		$msg = $I18N_ADDON->msg("filename_updated");
		$exportfilename = $filename;
	}else
	{
		$content = "";
		if ($exporttype == "sql")
		{
			
			// ------------------------------ FUNC EXPORT SQL

			$header = "plain/text";
			$ext = ".sql";
			
			$tabs = new sql;
			$tabs->setquery("SHOW TABLES");
			
			for($i=0;$i<$tabs->rows;$i++,$tabs->next())
			{
                $tab = $tabs->getvalue("Tables_in_".$DB[1][NAME]);
				if( strstr($tab, $REX[TABLE_PREFIX]) == $tab && $tab != "rex_user"){
					$cols = new sql;
					$cols->setquery("SHOW COLUMNS FROM ". $tab);
					$query = "DROP TABLE IF EXISTS ". $tab .";\nCREATE TABLE ". $tab ." (";
					$key = array();
					for($j=0;$j<$cols->rows;$j++,$cols->next()){
						$colname = $cols->getvalue("Field");
						$coltype = $cols->getvalue("Type");
						if($cols->getvalue("Null") == 'YES') $colnull = "NULL"; 
						else $colnull = "NOT NULL";
						if($cols->getvalue("Default") != '') $coldef = "DEFAULT ".$cols->getvalue("Default")." ";
						else $coldef = "";
						$colextra = $cols->getvalue("Extra");
						if($cols->getvalue("Key") != '') { $key[] = $colname; $colnull = "NOT NULL"; }
						$query .= " $colname $coltype $colnull $coldef $colextra";
						if($j+1 != $cols->rows) $query .= ",";
					}
					if(count($key) > 0){
						$query .= ", PRIMARY KEY(";
						for($k=0,reset($key);$k<count($key);$k++,next($key)) {      // <-- yeah super for schleife, rock 'em hard :)
							$query .= current($key);
							if($k+1 != count($key)) $query .= ",";
						}
						$query .= ")";
					}
					$query .= ")TYPE=MyISAM;";
					$dump .= $query."\n";
					$cont = new sql;
					$cont->setquery("SELECT * FROM ". $tab);
					for($j=0;$j<$cont->rows;$j++,$cont->next()){
						$query = "INSERT INTO ". $tab ." VALUES (";
						$cols->counter = 0;
						for($k=0;$k<$cols->rows;$k++,$cols->next()){
                            $con = $cont->getvalue($cols->getvalue("Field"));
							if(is_numeric( $con)) $query .= "'". $con ."'";
							else $query .= "'".addslashes( $con)."'";
							if($k+1 != $cols->rows) $query .= ",";
						}
						$query .= ");";
						$dump .= str_replace(array( "\r\n", "\n"),'\r\n',$query)."\n";
					}
				}
								
			}
			
			$content = "## Redaxo Database Dump Version ".$REX[version]." \n".str_replace("\r","",$dump);


			// ------------------------------ /FUNC EXPORT SQL		
			
		}elseif ($exporttype == "files")
		{
		
			// ------------------------------ FUNC FILES
		
			$header = "tar/gzip";
			$ext = ".tar.gz";

			if ($EXPDIR == "")
			{
				$msg = $I18N_ADDON->msg("please_choose_folder");
			}else
			{
			
			
				function add_file_reku($predir,$dir)
				{
					global $tar;
					$handle = opendir($predir.$dir);
					$array_indx = 0;
					#$tar->addFile($predir.$dir."/",TRUE);
					while (false !== ($file = readdir($handle)))
					{
						$dir_array[$array_indx] = $file;
						$array_indx++;
					}
					foreach ($dir_array as $n)
					{
						#echo $n."<br>";
						if(($n != '.') AND ($n != '..')){
							#echo "hier : $n <br>";
							if(is_dir($predir.$dir."/".$n)) add_file_reku($predir.$dir."/",$n); 
							if(!is_dir($predir.$dir."/".$n)) $tar->addFile($predir.$dir."/".$n,TRUE);
							#echo $predir.$dir."/".$n."<br>";
						}
					}
				}
				
				$tar = new tar;
				foreach($EXPDIR as $key => $item)
					add_file_reku($REX[INCLUDE_PATH]."/../../",$key);
			
				$content = $tar->toTarOutput($filename.$ext,TRUE);
						
			}
		
			// ------------------------------ /FUNC FILES

		}
	
		if ($content != "" && $exportdl == 1)
		{
			$filename = $filename.$ext;
			header("Content-type: $header");
			header("Content-Disposition: attachment; filename=$filename");
			echo $content;
			exit;
		
		}elseif ($content != "")
		{
			// check filename ob vorhanden
			// aendern filename
			// speicher content in files
			
			$filename = $REX[INCLUDE_PATH]."/addons/$page/files/$filename";
			
			if (file_exists($filename.$ext))
			{
				for ($i=0;$i<1000;$i++)
				{
					if (!file_exists($filename."_$i".$ext))
					{
						$filename = $filename."_$i".$ext;
						break;
					}
				}
			}else
			{
				$filename .= $ext;
			}
						
			if ($fp = @fopen($filename, "w"))
			{
				fputs($fp,$content);
				fclose($fp);
			}else
			{
				$MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX[INCLUDE_PATH]."/addons/$page/files";
			}
			
			// echo $content;
		}
	

	}
	
}





include $REX[INCLUDE_PATH]."/layout/top.php";
title($I18N_ADDON->msg("importexport"),"");
if ($msg != "") echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=warning>$msg</td></tr></table><br>";

?>

<table border=0 cellpadding=5 cellspacing=1 width=770>

<tr>
	<th align=left width=50%><?php echo $I18N_ADDON->msg('import'); ?></th>
	<th align=left><?php echo $I18N_ADDON->msg('export'); ?></th>
</tr>


<tr>
	<td class=dgrey valign=top><?
	
	// ----------------------------------------------------------------- IMPORT
	
	// DB IMPORT
	echo "<br>".$I18N_ADDON->msg("intro_import")."	
	
	<br><br><table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>
	<tr><td align=left colspan=2 class=lgrey>".$I18N_ADDON->msg("database")."</td>
	<form action=index.php method=post enctype='multipart/form-data'>
	<input type=hidden name=page value=$page>
	<input type=hidden name=function value=dbimport>
	<tr>
		<td class=lgrey><input type=file name=FORM[importfile]></td>
		<td class=lgrey width=129><input type=submit value='".$I18N_ADDON->msg("db_import")."'></td>
	</tr>
	</form>
	</table>";
	
    echo "<br><table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>";
    echo "<tr><td align=left class=lgrey>".$I18N_ADDON->msg("filename")."</td><td width=60 class=lgrey>".$I18N_ADDON->msg("createdate")."</td><td width=60 class=lgrey>&nbsp;</td><td width=60 class=lgrey>&nbsp;</td>";
    
    // DB IMPORT LIST
    // all files in files with .sql als endung
    $dir = getImportDir();
    $folder = readImportFolder( ".sql");
    
    foreach( $folder as $file) 
    {
        $filepath = $dir .'/'.$file;
        $filec = date( "d.m.Y H:i", filemtime( $filepath));
        echo "<tr>
        <td class=lgrey><b>$file</b></td>
        <td class=lgrey>$filec</td>
        <td class=lgrey><a href=index.php?page=$page&function=dbimport&impname=$file title='". $I18N_ADDON->msg( 'import_file') ."'>".$I18N_ADDON->msg("import")."</a></td>
        <td class=lgrey><a href=index.php?page=$page&function=delete&impname=$file title='". $I18N_ADDON->msg( 'delete_file') ."'>".$I18N_ADDON->msg("delete")."</a></td></tr>";
    }
	echo "</table>";

	// FILE IMPORT
	echo "<br><table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>
	<tr><td align=left colspan=2 class=lgrey>".$I18N_ADDON->msg("files")."</td>
	<form action=index.php method=post enctype='multipart/form-data'>
	<input type=hidden name=page value=$page>
	<input type=hidden name=function value=fileimport>
	<tr>
		<td class=lgrey><input type=file name=FORM[importfile]></td>
		<td class=lgrey width=130><input type=submit value='".$I18N_ADDON->msg("db_import")."'></td>
	</tr>
	</form>
	</table>";

    echo "<br><table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>";
    echo "<tr><td align=left class=lgrey>".$I18N_ADDON->msg("filename")."</td><td width=60 class=lgrey>".$I18N_ADDON->msg("createdate")."</td><td width=60 class=lgrey>&nbsp;</td><td width=60 class=lgrey>&nbsp;</td>";
    
    // FILE IMPORT LIST
    // all files in files with .tar.gz als endung
    
    $dir = getImportDir();
    sort( $folder = readImportFolder( ".tar.gz"));
    
    foreach( $folder as $file)
    {
        $filepath = $dir .'/'.$file;
        $filec = date( "d.m.Y H:i", filemtime( $filepath));
        echo "<tr>
        <td class=lgrey><b>$file</b></td>
        <td class=lgrey>$filec</td>
        <td class=lgrey><a href=index.php?page=$page&function=fileimport&impname=$file title='". $I18N_ADDON->msg( 'import_file') ."'>".$I18N_ADDON->msg("import")."</a></td>
        <td class=lgrey><a href=index.php?page=$page&function=delete&impname=$file title='". $I18N_ADDON->msg( 'delete_file') ."'>".$I18N_ADDON->msg("delete")."</a></td></tr>";
    }
    
	echo "</table><br>";

	// ----------------------------------------------------------------- /IMPORT

	?></td><td class=dgrey valign=top><?
	
	// ----------------------------------------------------------------- EXPORT
	
	echo "<br>".$I18N_ADDON->msg("intro_export")."<br><br>";
	
	echo "<table width=100% border=0 cellspacing=1 cellpadding=4 bgcolor=#ffffff>
	
	<form action=index.php method=post enctype='multipart/form-data'>
	<input type=hidden name=page value=$page>
	<input type=hidden name=function value=export>	
	
	";
	
	$checkedsql = "";
	$checkedfiles = "";
	
	if ($exporttype == "files") $checkedfiles = " checked";
	else $checkedsql = " checked";
	
	echo "<tr>";
	echo "<td class=lgrey width=30><input type=radio id=exporttype[sql] name=exporttype value=sql $checkedsql></td>";
	echo "<td class=lgrey><label for=exporttype[sql]>".$I18N_ADDON->msg("database_export")."</label></td>";
	echo "</tr>"; 
	
	echo "<tr>";
	echo "<td class=lgrey><input type=radio id=exporttype[files] name=exporttype value=files $checkedfiles></td>";
	echo "<td class=lgrey><label for=exporttype[files]>".$I18N_ADDON->msg("file_export")."</label></td>";
	echo "</tr>"; 
	
    echo "<tr><td class=grey>&nbsp;</td><td class=lgrey><table width=100%>";
    // FILE EXPORT LIST
    // all folders of the webpage except the cms dir

    $dir = $REX[INCLUDE_PATH]."/../..";
    $folders = readSubFolders( $dir);
    
    foreach ( $folders as $file)
    {
        if ( $file == 'redaxo')
        {
            continue;
        }
        
        $checked = "";
        if (is_Array($EXPDIR)) if (array_key_exists($file,$EXPDIR) !== false) $checked = " checked";
        echo "<tr>";
        echo "<td class=lgrey width=30><input type=checkbox id=EXPDIR[$file] name=EXPDIR[$file] value=true $checked></td>";
        echo "<td class=lgrey><label for=EXPDIR[$file]>$file</label></td>";
        echo "</tr>";
    }
    
	echo "</table></td></tr>";
	
	$checked0 = "";
	$checked1 = "";
	
	if ($exportdl == 1) $checked1 = " checked";
	else $checked0 = " checked";
	
	echo "<tr>";
	echo "<td class=lgrey><input type=radio id=exportdl[server] name=exportdl value=0 $checked0></td>";
	echo "<td class=lgrey><label for=exportdl[server]>".$I18N_ADDON->msg("save_on_server")."</label></td>";
	echo "</tr>"; 
	echo "<tr>";
	echo "<td class=lgrey><input type=radio id=exportdl[download] name=exportdl value=1 $checked1></td>";
	echo "<td class=lgrey><label for=exportdl[download]>".$I18N_ADDON->msg("download_as_file")."</label></td>";
	echo "</tr>"; 
	echo "<tr>";
	echo "<td class=lgrey></td>";
	echo "<td class=lgrey><input type=text size=20 name=exportfilename class=inp100 value='$exportfilename'></td>";
	echo "</tr>"; 
	
	echo "<tr>";
	echo "<td class=lgrey></td>";
	echo "<td class=lgrey><input type=submit value='".$I18N_ADDON->msg("db_export")."'></td>";
	echo "</tr>";
	
	echo "</form>";
	echo "</table><br>";

	


	// ----------------------------------------------------------------- /EXPORT
	
	?></td>
</tr>
</table>




<?

include $REX[INCLUDE_PATH]."/layout/bottom.php";

?>