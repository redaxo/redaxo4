<?php

ini_set('auto_detect_line_endings', true);


$table_name = rex_request("table_name","string");
$func = rex_request("func","string");
$table = 'rex_em_data_'.$table_name;

if($func == "add_csv")
{

	$f = $_FILES["file_new"];

	if($table_name == "")
	{
		echo rex_warning('Es wurde keine Tabelle angegeben.');
	}elseif($f['name'] != "")
	{
		// Datei wurde hochgeladen
		
		
		$fields = array();
		$first = true;
		$error = array();

		$imp = new rex_sql;
		$imp->debugsql = 1;
		$tmp_name = $f['tmp_name'];
		$handle = fopen ($f['tmp_name'],"r");
	
		while ( ($data = fgetcsv ($handle, 15000, ";")) !== FALSE ) {
		
			if($first)
			{
				$fields = $data;
				$t = rex_em_getFields($table_name);
				
				$table_fields = array();
				$table_fields_left = array();
				foreach($t as $u)
				{
					if($u["type_id"] == "value")
					{
						$table_fields[$u["f1"]] = $u["f1"];
						$table_fields_left[$u["f1"]] = $u["f1"];
					}
				}

				foreach($fields as $f)
				{
					if(!in_array($f,$table_fields))
					{
						$error[] = '´'.$f.'´ wurde nicht gefunden.';
					}else
					{
						unset($table_fields_left[$f]);
					}
					
				}
				
				$first = FALSE;
			}else
			{
		
				$sql = 'INSERT INTO '.$table.' set ';
				
				for ($a=0;$a<=count($fields);$a++)
				{
					if($fields[$a] != "")
					{
						if($a>0) $sql .= ', ';
						$sql .= '`'.$fields[$a].'`=\''.addslashes(utf8_decode($data[$a])).'\'';
					}
				}
				$sql .= ';';
				$imp->setQuery($sql);
				// echo '<br />'.$sql.'<br />';
			}
			
			if(count($error) > 0)
			{
				break;
			}
			
		}
		fclose ($handle);
		
		if(count($error) == 0)
		{
			echo rex_info('CSV Datei wurde importiert.');
		}else
		{
			foreach($error as $e) 
				echo rex_warning($e);
			
			$t = "Bitte eines der folgenden restlichen Felder verwenden: ";
			foreach($table_fields_left as $f)
			{
				$t .= $f.', ';
			}
			echo rex_info($t);
		}	
			
	}else
	{
		echo rex_warning('Es wurde keine Datei hochgeladen.');
	}

	/*
	echo '<pre>';
	var_dump($_FILES["file_new"]);
	echo '</pre>';
	*/
	
}






?>

<h1>Tabelle: <?php echo $table.' ['.$table_name.']'; ?></h1>
<p>&nbsp;</p>

<div class="rex-form" id="rex-form-mediapool-other">
 <form action="index.php" method="post" enctype="multipart/form-data">
   <fieldset class="rex-form-col-1">
     <legend>CSV Datei auswaehlen</legend>

     <div class="rex-form-wrapper">
       <input type="hidden" name="page" value="editme" />
       <input type="hidden" name="subpage" value="import" />
       <input type="hidden" name="func" value="add_csv" />
       <input type="hidden" name="table_name" value="<?php echo $table_name; ?>" />

       <div class="rex-form-row">
           <p class="rex-form-file">
             <label for="file_new">Datei</label>
             <input class="rex-form-file" type="file" id="file_new" name="file_new" size="30" />
           </p>
       </div>

       <div class="rex-form-row">
         <p class="rex-form-submit">
          <input class="rex-form-submit" type="submit" name="save" value="Hinzuf&uuml;gen" title="Hinzuf&uuml;gen" />
         </p>
       </div>

       <div class="rex-clearer"></div>
     </div>
   </fieldset>
</form></div>

