<?php

ini_set("auto_detect_line_endings", true);

$show_importform = TRUE;
$show_list = FALSE;

if(rex_request('send',"int",0)==1)
{
	// Daten wurden übertragen

	if(!isset($_FILES['file_new']) || $_FILES['file_new']["tmp_name"] == "")
	{
		echo rex_warning('pleasechooseafile');
	
	}else
	{
	
		$fieldarray = array();
		$filename = $_FILES['file_new']['tmp_name'];

		$what = 1;

		if($what == 2)
		{
			// Feldnamen der DB auslesen und eins nach dem anderen einspielen
			// $gc->setQuery('SHOW COLUMNS FROM "'.$table['table_name'].'"');
		
		
		}else
		{
			// 1.Zeile der CSV Datei auslesen und Feldnamen nehmem

			$counter = 0; // importierte
			$dcounter = 0; // nicht imporierte
			$ecounter = 0; // leere reihen
			$i = rex_sql::factory();
			$fp = fopen($filename,'r');
			while ( ($line_array = fgetcsv ($fp, 4096, ";")) !== FALSE )
			{
			
				if(count($fieldarray) == 0)
				{
					$fieldarray = $line_array;
				}else
				{
					if(!$line_array) break;
					elseif(count($fieldarray) != count($line_array))
					{
						if(count($line_array)==1) $ecounter++;
						$dcounter++;
						echo "<pre>Fehler.. Dieser Datensatz ist ungleich lang dem ersten Datensatz. <br /><br />"; var_dump($line_array); echo "</pre>";
					}else
					{
						$counter++;
						$i->setTable($table['table_name']);
						foreach($line_array as $k => $v)
						{
							$i->setValue($fieldarray[$k],mysql_real_escape_string($v));
						}
						$i->replace();
					}
				}
			}
		
		}

		$show_list = TRUE;
		echo rex_info('Es wurden '.$counter.' Datensätze importiert');
		if($dcounter >0)
			echo rex_warning('Es wurde/n '.$dcounter.' Datensätze nicht importiert.');

		$func = "";
		$show_importform = FALSE;

	}
	
}







if($show_importform){
	
	?>
	<div class="rex-form" id="rex-form-mediapool-other">
	<form action="index.php" method="post" enctype="multipart/form-data">
	<fieldset class="rex-form-col-1">
		<legend>CSV Datei hochladen. </legend>
		<div class="rex-form-wrapper">

			<div class="rex-area-content">
				<p class="rex-tx1">Trennzeichen muss ";" sein, wenn id als Feld in der CSV Datei gesetzt ist, werden die entsprechenden Datensätze ausgetauscht.</p>
			</div>
			
			<?php
			foreach($this->getLinkVars() as $k => $v)
			{
				echo '<input type="hidden" name="'.$k.'" value="'.addslashes($v).'" />';
			}
			?>	
			<input type="hidden" name="func" value="import" />
			<input type="hidden" name="send" value="1" />
		  
			<div class="rex-form-row">
			  <p class="rex-form-file">
			    <label for="file_new">Datei</label>
			    <input class="rex-form-file" type="file" id="file_new" name="file_new" size="30" />
			  </p>
			</div>

			<div class="rex-form-row">
			  <p class="rex-form-submit">
			   <input class="rex-form-submit" type="submit" name="save" value="Hinzufügen" title="Hinzufügen" />
			  </p>
			</div>

			<div class="rex-clearer"></div>
		</div>
	</fieldset>
	</form>
	</div>
	<?php

}

