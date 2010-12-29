<?php

// TODO:
// Check ob Datei vorhanden..

class rex_xform_mediafile extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		global $REX;

		if ($this->elements[8] == "") $mediacatid = 0;
		else $mediacatid = (int) $this->elements[8];

		if ($this->elements[9] == "") $image_manager_type = "rex_mediapool_preview";
		else $image_manager_type = $this->elements[9];

		if ($this->elements[10] != "") $filename_prefix = $this->elements[10];
		else $filename_prefix = "";

		$minsize = 0;
		$maxsize = 50000;

		$sizes = explode(",",$this->elements[3]);
		if(count($sizes) > 1)
		{
			$minsize = (int) ($sizes[0]*1024); // -> bytes
			$maxsize = (int) ($sizes[1]*1024); // -> bytes
		}elseif($sizes[0] != "")
		{
			$maxsize = (int) ($sizes[0]*1024); // -> bytes
		}

		// Grš§encheck
		if (	$send
		&& isset($_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->id])
		&& $_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->id] != ""
		&& ($_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->id]>$maxsize || $_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->id]<$minsize)
		)
		{
			$_FILES["FORM"]["name"][$this->params["form_name"]]["el_".$this->id] = "";
			$this->value = "";
			$this->elements[5] = 1; // auf "error message true" setzen, wenn datei fehlerhaft

		}

		if ($send)
		{
			if (isset($_FILES["FORM"]["name"][$this->params["form_name"]]["el_".$this->id])
			&& $_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->id] != ""
			)
			{

				$FILE["size"] = $_FILES["FORM"]["size"][$this->params["form_name"]]["el_".$this->id];
				$FILE["name"] = $_FILES["FORM"]["name"][$this->params["form_name"]]["el_".$this->id];
				$FILE["type"] = $_FILES["FORM"]["type"][$this->params["form_name"]]["el_".$this->id];
				$FILE["tmp_name"] = $_FILES["FORM"]["tmp_name"][$this->params["form_name"]]["el_".$this->id];
				$FILE["error"] = $_FILES["FORM"]["error"][$this->params["form_name"]]["el_".$this->id];

				$extensions_array = explode(",",$this->elements[4]);
				$NEWFILE = $this->saveMedia($FILE,$REX["INCLUDE_PATH"]."/../../files/",$extensions_array,$mediacatid, $filename_prefix);

				if ($NEWFILE["ok"])
				{
					$this->value = $NEWFILE['filename'];
				}else
				{
					$this->value = "";
					$this->elements[5] = 1; // auf "error message true" setzen, wenn datei fehlerhaft
				}
			}
		}

		if ($send)
		{
			if ($this->value == ""
			&& @$_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->id.'_filename'] != ""
			&& @$_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->id.'_delete'] != 1)
			{
				$this->value = $_REQUEST["FORM"][$this->params["form_name"]]['el_'.$this->id.'_filename'];
			}

			$email_elements[$this->elements[1]] = stripslashes($this->value);
			if ($this->elements[7] != "no_db") $sql_elements[$this->elements[1]] = $this->value;
		}

		$tmp = "";
		$check_delete = "";
		if ($this->value != "")
		{
			$this->elements[2] .= '<br />Dateiname: <a href="files/'.$this->value.'">'.$this->value.'</a><br />';

			$fileendung = substr(strtolower($this->value),-3);
			if ($fileendung == 'jpg' || $fileendung == 'png' || $fileendung == 'gif') {
				$this->elements[2] .= '<br /><img src="?rex_img_type='.$image_manager_type.'&amp;rex_img_file='.$this->value.'" />';
			}
			$check_delete = '
   			<span class="formmcheckbox" style="width:300px;clear:none;">
	   			<input id="el_'.$this->id.'_delete" type="checkbox" name="FORM['.$this->params["form_name"].'][el_'.$this->id.'_delete]" value="1" />
	   			<label for="el_' . $this->id . '_delete">Datei lšschen</label>
   			</span>
   			';
		}

		if ($send && $this->elements[5]==1 && $this->value == "")
		{
			$warning["el_" . $this->id] = $this->params["error_class"];
			$this->params["warning_messages"][] = $this->elements[6];
		}

		$wc = "";
		$warningmsg = '';
		if (isset($warning["el_" . $this->getId()])) {
			$wc = $warning["el_" . $this->getId()];
		}

		$out = '
			<input type="hidden" name="FORM['.$this->params["form_name"].'][el_'.$this->id.'_filename]" value="'.$this->value.'" />
			<p class="formfile" id="'.$this->getHTMLId().'">
				<label class="text ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] .'</label>
				'.$check_delete.'
				<input class="uploadbox clickmedia '.$wc.'" id="el_'.$this->id.'" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" type="file" />
			</p>';

		$form_output[] = $out;

	}

	function getDescription()
	{
		return "mediafile -> Beispiel: mediafile|label|Bezeichnung|groesseinkb|endungenmitpunktmitkommasepariert|pflicht=1|Fehlermeldung|[no_db]|mediacatid|image_manager_type|fileprefix";
	}


	function getDefinitions()
	{

		return array(
						'type' => 'value',
						'name' => 'mediafile',
						'values' => array(
		array( 'type' => 'label',   'label' => 'Label' ),
		array( 'type' => 'text',    'label' => 'Bezeichnung'),
		array( 'type' => 'text',    'label' => 'Maximale Grš§e in Kb oder Range 100,500'),
		array( 'type' => 'text',    'label' => 'Welche Dateien sollen erlaubt sein, kommaseparierte Liste. ".gif,.png"'),
		array( 'type' => 'boolean', 'label' => 'Pflichtfeld'),
		array( 'type' => 'text',    'label' => 'Fehlermeldung'),
		array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 1),
		array( 'type' => 'text',    'label' => 'Mediakategorie ID'),
		),
						'description' => 'Mediafeld, welches Dateien aus dem Medienpool holen',
						'dbtype' => 'text'
						);
	}






	function postAction($email_elements,$sql_elements)
	{
	}


	function saveMedia($FILE,$filefolder,$extensions_array,$rex_file_category,$prefix = ""){

		global $REX;

		$FILENAME = $FILE['name'];
		$FILESIZE = $FILE['size'];
		$FILETYPE = $FILE['type'];
		$NFILENAME = "";
		$message = '';

		// ----- neuer filename und extension holen
		$NFILENAME = strtolower(preg_replace("/[^a-zA-Z0-9.\-\$\+]/","_",$FILENAME));
		if (strrpos($NFILENAME,".") != "")
		{
			$NFILE_NAME = substr($NFILENAME,0,strlen($NFILENAME)-(strlen($NFILENAME)-strrpos($NFILENAME,".")));
			$NFILE_EXT  = substr($NFILENAME,strrpos($NFILENAME,"."),strlen($NFILENAME)-strrpos($NFILENAME,"."));
		}else
		{
			$NFILE_NAME = $NFILENAME;
			$NFILE_EXT  = "";
		}

		// ---- ext checken
		$ERROR_EXT = array(".php",".php3",".php4",".php5",".phtml",".pl",".asp",".aspx",".cfm");
		if (in_array($NFILE_EXT,$ERROR_EXT))
		{
			$NFILE_NAME .= $NFILE_EXT;
			$NFILE_EXT = ".txt";
		}

		$standard_extensions_array = array(".rtf",".pdf",".doc",".gif",".jpg",".jpeg");
		if (count($extensions_array) == 0) $extensions_array = $standard_extensions_array;

		if (!in_array($NFILE_EXT,$extensions_array))
		{
			$RETURN = FALSE;
			$RETURN['ok'] = FALSE;
			return $RETURN;
		}

		$NFILE_NAME = $prefix.$NFILE_NAME;
		$NFILENAME = $NFILE_NAME.$NFILE_EXT;

		// ----- datei schon vorhanden -> namen aendern -> _1 ..
		if (file_exists($filefolder."/$NFILENAME"))
		{
			for ($cf=1;$cf<1000;$cf++)
			{
				$NFILENAME = $NFILE_NAME."_$cf"."$NFILE_EXT";
				if (!file_exists($filefolder."/$NFILENAME")) break;
			}
		}

		// ----- dateiupload
		$upload = true;
		if(!move_uploaded_file($FILE['tmp_name'],$filefolder."/$NFILENAME") )
		{
			if (!copy($FILE['tmp_name'],$filefolder."/$NFILENAME"))
			{
				$message .= "move file $NFILENAME failed | ";
				$RETURN = FALSE;
				$RETURN['ok'] = FALSE;
				return $RETURN;
			}
		}

		@chmod($filefolder."/$NFILENAME", $REX['FILEPERM']);
		$RETURN['type'] = $FILETYPE;
		$RETURN['msg'] = $message;
		$RETURN['ok'] = TRUE;
		$RETURN['filename'] = $NFILENAME;


		$FILESQL = rex_sql::factory();
		// $FILESQL->debugsql=1;
		$FILESQL->setTable($REX['TABLE_PREFIX']."file");
		$FILESQL->setValue("filetype",$FILETYPE);
		$FILESQL->setValue("filename",$NFILENAME);
		$FILESQL->setValue("originalname",$FILENAME);
		$FILESQL->setValue("filesize",$FILESIZE);
		$FILESQL->setValue("category_id",$rex_file_category);
		$FILESQL->setValue("createdate",time());
		$FILESQL->setValue("createuser","system");
		$FILESQL->setValue("updatedate",time());
		$FILESQL->setValue("updateuser","system");
		$FILESQL->insert();

		return $RETURN;
	}


}

?>