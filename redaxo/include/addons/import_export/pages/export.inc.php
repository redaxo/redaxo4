<?

// -------------------------------------- EXPORT

if($FORM[submit]){
	
	$tabs = new sql;
	$tabs->setquery("SHOW TABLES");
	
	for($i=0;$i<$tabs->rows;$i++,$tabs->next()){
		#echo "HIER !!!!! ".$tabs->getvalue("Tables_in_".$DB[1][NAME])." JOJOJOJ!!!!!";
		if($tabs->getvalue("Tables_in_".$DB[1][NAME]) != "rex_user"){
		    #echo $tabs->getvalue("Tables_in_".$DB[1][NAME])."<br>";
			$cols = new sql;
			$cols->setquery("SHOW COLUMNS FROM ".$tabs->getvalue("Tables_in_".$DB[1][NAME]));
			
			$query = "DROP TABLE IF EXISTS ".$tabs->getvalue("Tables_in_".$DB[1][NAME]).";\nCREATE TABLE ".$tabs->getvalue("Tables_in_".$DB[1][NAME])." (";
			$key = array();
			for($j=0;$j<$cols->rows;$j++,$cols->next()){
				$colname = $cols->getvalue("Field");
				$coltype = $cols->getvalue("Type");
				if($cols->getvalue("Null") == 'YES') $colnull = "NULL"; 
				elseif($cols->getvalue("Null") == 'NO') $colnull = "NOT NULL";
				else $colnull = "";
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
			$cont->setquery("SELECT * FROM ".$tabs->getvalue("Tables_in_".$DB[1][NAME]));
			for($j=0;$j<$cont->rows;$j++,$cont->next()){
				$query = "INSERT INTO ".$tabs->getvalue("Tables_in_".$DB[1][NAME])." VALUES (";
				$cols->counter = 0;
				for($k=0;$k<$cols->rows;$k++,$cols->next()){
					if(is_numeric($cont->getvalue($cols->getvalue("Field")))) $query .= "'".$cont->getvalue($cols->getvalue("Field"))."'";
					else $query .= "'".addslashes($cont->getvalue($cols->getvalue("Field")))."'";
					if($k+1 != $cols->rows) $query .= ",";
				}
				$query .= ");";
				$dump .= str_replace(array( "\r\n", "\n"),'\r\n',$query)."\n";	// <<<---- schrecklich aber zweckmäßig --- workaround??
			}  // end for für content der tabelle
		} // end else fuer rex_user tabelle rausnehmen 
		
	}
	header("Content-type: plain/text");
	header("Content-Disposition: attachment; filename=redaxo_export_".date("Ymd").".sql");
	echo "## Redaxo Database Dump Version ".$REX[version]." \n";
	echo str_replace("\r","",$dump);
	exit;
	
}

function add_file_reku($predir,$dir){
		global $tar;
		$handle = opendir($predir.$dir);
		$array_indx = 0;
		#$tar->addFile($predir.$dir."/",TRUE);
		while (false !== ($file = readdir($handle))){
   	     	$dir_array[$array_indx] = $file;
   	     	$array_indx++;
   		}
   		foreach ($dir_array as $n){
   		#echo $n."<br>";
		if(($n != '.') AND ($n != '..')){
			#echo "hier : $n <br>";
			if(is_dir($predir.$dir."/".$n)) add_file_reku($predir.$dir."/",$n); 
			if(!is_dir($predir.$dir."/".$n)) $tar->addFile($predir.$dir."/".$n,TRUE);
			#echo $predir.$dir."/".$n."<br>";
		}}
}
   			
if($FORM[filesubmit]){
	$tar = new tar;
	foreach($DIR as $key => $item)
		add_file_reku("../",$key);

	header("Content-type: tar/gzip");
	header("Content-Disposition: attachment; filename=redaxo_export_".date("Ymd").".tar.gz");
			
	echo $tar->toTarOutput("redaxo_export_".date("Ymd").".tar.gz",TRUE);
	exit;
}




if(isset($err_msg)) $err_msg = "<tr><td class=dgrey colspan=2><font color=error>$err_msg</font></td></tr>";


## make dir inputs
chdir("..");
$handle = opendir(".");
$dirstring = "";

while (false !== ($file = readdir($handle))) 
	if(is_dir($file)) 
		if($file != ".." AND $file != ".") 
			if($file != "redaxo") $dirstring .= "<input type=checkbox name=DIR[$file] value=true>$file/ &nbsp;&nbsp;\n";
 
chdir("redaxo");
?>

<table border=0 cellpadding=5 cellspacing=1 width=770>
<form action=<? echo $PHP_SELF; ?> method=post name=formu enctype="multipart/form-data">
<input type=hidden name=FORM[submit] value=true>

<tr><th colspan=2 align=left>
<? echo $I18N->msg("db_import_export"); ?>
</th></tr>

<? echo $err_msg; ?>

<tr><td class=dgrey>
<? echo $I18N->msg("db_import"); ?></td><td class=dgrey><input type=file name=FORM[importfile]>&nbsp;&nbsp;&nbsp;<input type=submit name=page value=<? echo $I18N->msg("import"); ?>>
</td></tr>

<tr><td class=dgrey>
<? echo $I18N->msg("db_export"); ?></td><td class=dgrey><input type=submit name=page value=<? echo $I18N->msg("export"); ?>> 
</td></form></tr>

<tr>
<form action=<? echo $PHP_SELF; ?> method=post name=formu enctype="multipart/form-data">
<input type=hidden name=FORM[filesubmit] value=TRUE>
<th colspan=2 align=left>
<? echo $I18N->msg("files_import_export"); ?>
</th></tr>

<tr><td class=dgrey>
<? echo $I18N->msg("files_import"); ?></td><td class=dgrey><input type=file name=FORM[importfile]>&nbsp;&nbsp;&nbsp;<input type=submit name=page value=<? echo $I18N->msg("import"); ?>>
</td></tr>

<tr><td class=dgrey>
<? echo $I18N->msg("files_export"); ?></td><td class=dgrey><input type=submit name=page value=<? echo $I18N->msg("export"); ?>> &nbsp;&nbsp;<? echo $dirstring; ?>
</td></form></tr>


</table>
