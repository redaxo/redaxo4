<?

// class rexform
// 
// erstellt 01.12.2003
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

// sql.class.inc.php
// select.class.inc.php


class rexform
{

	var $method;
	var $submit_value;
	var $formname;
	var $counter;
	var $width;
	var $labelwidth;
	var $url;
	var $rfid;
	var $form_header;

	var $type;
	var $value_form;
	var $value_tbl;
	var $value_check;
	var $value_type;
	var $type_value1;
	var $type_value2;
	var $type_value3;
	var $type_value4;
	var $type_value5;
	var $type_value6;
	var $type_value7;
	
	var $cols = array();
	
	var $form_type;
	var $form_show;

	var $tbl_name;
	var $form_where;
	var $sql;
	
	var $ShowFormAlways;

	var $Action;

	function rexform()
	{
		$this->counter = 0;
		$this->method = "post";
		$this->formname = "REX_FORM";
		$this->submit_value = "submit";
		$this->width = "100%";
		$this->url = "index.php";
		$this->rfid = "okl";
		$this->tbl_name = "standard_tbl";
		$this->form_type = "edit";
		$this->form_show = false;
		$this->ShowFormAlways = false;
		$this->cols[0] = 1;
		
		$this->sql = new sql;

	}

	function setValue($value_type,$value_form="",$value_tbl="",$value_check = "", $type_value1 = "", $type_value2 = "", $type_value3 = "", $type_value4 = "", $type_value5 = "", $type_value6 = "", $type_value7 = "")
	{
		$this->value_type[$this->counter] = $value_type;
		$this->value_form[$this->counter] = $value_form;
		$this->value_tbl[$this->counter] = $value_tbl;
		$this->value_check[$this->counter] = $value_check;
		$this->type_value1[$this->counter] = $type_value1;
		$this->type_value2[$this->counter] = $type_value2;
		$this->type_value3[$this->counter] = $type_value3;
		$this->type_value4[$this->counter] = $type_value4;
		$this->type_value5[$this->counter] = $type_value5;
		$this->type_value6[$this->counter] = $type_value6;
		$this->type_value7[$this->counter] = $type_value7;
		$this->counter++;
	}
	
	function setCols($cols)
	{
		$this->cols[$this->counter-1] = $cols;
	}
	
	
	function showForm()
	{
		
		global $FORM,$REX;
		
		
		// --------------------------------- EDIT: 1. WERTE AUS DB HOLEN

		for ($i=0;$i<$this->counter;$i++)
		{
			if ($this->value_type[$i] != "multipleselectsql")
			{
				if ($FORM[$this->rfid][submit]!=1 && $this->form_type == "edit") $FORM[$this->rfid][values][$i] = htmlentities($this->sql->getValue($this->value_tbl[$i]));
				else $FORMVAL[$this->rfid][values][$i] = htmlentities($this->sql->getValue($this->value_tbl[$i]));
			}else
			{
				$selsql = new sql;
				$selsql->setQuery("select * from ".$this->type_value5[$i]." where ".$this->type_value6[$i]);
				for ($j=0;$j<$selsql->getRows();$j++)
				{
					if ($FORM[$this->rfid][submit]!=1 && $this->form_type == "edit") $FORM[$this->rfid][values][$i][] = $selsql->getValue($this->type_value7[$i]);
					else $FORMVAL[$this->rfid][values][$i][] = $selsql->getValue($this->type_value7[$i]);
					$selsql->next();	
				}
				
			}
		}
		
		
		
				
		
		// --------------------------------- ABGESCHICKTE EINGABEN CHECKEN
		
		if ($FORM[$this->rfid][submit]==1)
		{
			// ----------------------------- eingaben überprüfen
			$this->form_show = false;
			for ($i=0;$i<$this->counter;$i++)
			{
				if ($this->value_check[$i]!="")
				{
					if ($FORM[$this->rfid][values][$i]=="")
					{
						$errmsg .= "Bitte tragen Sie '".$this->value_form[$i]."' ein! <br>";
						$this->form_show = true;
					}
				}
			}
		}


		// --------------------------------- EDIT: SPEICHERN FALLS MÖGLICH
		
		if ($FORM[$this->rfid][submit]==1 && $this->form_type == "edit")
		{
			if ($errmsg=="")
			{
				$aa = new sql;
				 $aa->debugsql = 0;
				$aa->setTable($this->tbl_name);
				$aa->where($this->form_where);
				for ($i=0;$i<$this->counter;$i++)
				{

					if ($this->value_type[$i] == "picjpg")
					{
						$folder = $this->type_value1[$i];
						$foldertmp = $REX[INCLUDE_PATH]."/../../ss_pics/";

						$fname = $_FILES[FORM][name][$this->rfid][values][$i];
						if ($fname != "")
						{
							// neues file
							$nfname = $this->checkFilename($fname,$folder);
							
							if ($nfname[ext] == ".jpg")
							{
								$ftmpname = $_FILES[FORM][tmp_name][$this->rfid][values][$i];
								move_uploaded_file($ftmpname,$foldertmp.$nfname[nname]);
								$this->resizeJPGImage($foldertmp.$nfname[nname], $folder.$nfname[nname],$this->type_value3[$i],$this->type_value4[$i]);
								$FORM[$this->rfid][values][$i] = $nfname[nname];
								$aa->setValue($this->value_tbl[$i],$FORM[$this->rfid][values][$i]);	
							}

						}elseif($FORM[$this->rfid][values][$i][delete] != "")
						{
							$FORM[$this->rfid][values][$i] = "";
							$aa->setValue($this->value_tbl[$i],$FORM[$this->rfid][values][$i]);
						}else
						{
							$FORM[$this->rfid][values][$i] = $FORMVAL[$this->rfid][values][$i];
						}

					}elseif ($this->value_type[$i] == "file")
					{
						$folder = $REX[INCLUDE_PATH]."/../../ss_pics/";
						$fname = $_FILES[FORM][name][$this->rfid][values][$i];
						if ($fname != "")
						{
							$nfname = $this->checkFilename($fname,$folder);
							$ftmpname = $_FILES[FORM][tmp_name][$this->rfid][values][$i];
							move_uploaded_file($ftmpname,$folder.$nfname[nname]);
							$FORM[$this->rfid][values][$i] = $nfname[nname];
							$aa->setValue($this->value_tbl[$i],$FORM[$this->rfid][values][$i]);	
						}elseif($FORM[$this->rfid][values][$i][delete] != "")
						{
							$FORM[$this->rfid][values][$i] = "";
							$aa->setValue($this->value_tbl[$i],$FORM[$this->rfid][values][$i]);
						}else
						{
							$FORM[$this->rfid][values][$i] = $FORMVAL[$this->rfid][values][$i];
						}

					}elseif ($this->value_type[$i] == "multipleselectsql") 
					{
						// multipleselect
						$ms = new sql;
						$ms->query("delete from ".$this->type_value5[$i]." where ".$this->type_value6[$i]);
						if (is_Array($FORM[$this->rfid][values][$i]))
						{
							reset($FORM[$this->rfid][values][$i]);
							for ($j=0;$j<count($FORM[$this->rfid][values][$i]);$j++)
							{
								$val = 	current($FORM[$this->rfid][values][$i]);
								$sql = "insert into ".$this->type_value5[$i]." set ".$this->type_value6[$i].", ".$this->type_value7[$i]."=$val";
								$ms->query($sql);
								next($FORM[$this->rfid][values][$i]);
							}
						}
					}elseif($this->value_type[$i] == "subline" || $this->value_type[$i] == "empty"){
						
					}elseif($this->value_type[$i] == "datum"){
						
						$tag = substr($FORM[$this->rfid][values][$i],0,2);
						$monat = substr($FORM[$this->rfid][values][$i],3,2);						
						$jahr = substr($FORM[$this->rfid][values][$i],6,4);
						
						$aa->setValue($this->value_tbl[$i], mktime(0, 0, 0, $monat, $tag, $jahr));	
						
					}else
					{
						$aa->setValue($this->value_tbl[$i],$FORM[$this->rfid][values][$i]);	
					}
					
					
				}
				$aa->update();
				$msg = "Daten wurden gespeichert";
			}else
			{
				for ($i=0;$i<$this->counter;$i++)
				{	
					if ($this->value_type[$i] != "multipleselectsql") $FORM[$this->rfid][values][$i] = htmlentities(stripslashes($FORM[$this->rfid][values][$i]));
				}
			}
			
			for ($i=0;$i<$this->counter;$i++)
			{
				if ($this->value_type[$i] != "multipleselectsql") $FORM[$this->rfid][values][$i] = htmlentities(stripslashes($FORM[$this->rfid][values][$i]));
				else
				{
					// multipleselect
					if (is_Array($FORM[$this->rfid][values][$i]))
					{
						reset($FORM[$this->rfid][values][$i]);
						for ($j=0;$j<count($FORM[$this->rfid][values][$i]);$j++)
						{
							$val = $FORM[$this->rfid][values][$i][j];
						}
					}				
						
				}
			}
			
			
		}	
		
		// --------------------------------- ADD: SPEICHERN FALLS MÖGLICH
		
		if ($FORM[$this->rfid][submit]==1 && $this->form_type == "add")
		{
			if ($errmsg=="")
			{
				$aa = new sql;
				$aa->debugsql = 0;
				$aa->setTable($this->tbl_name);
				for ($i=0;$i<$this->counter;$i++)
				{
					if($this->value_type[$i] == "datum"){
						
						$tag = substr($FORM[$this->rfid][values][$i],0,2);
						$monat = substr($FORM[$this->rfid][values][$i],3,2);						
						$jahr = substr($FORM[$this->rfid][values][$i],6,4);
						
						$aa->setValue($this->value_tbl[$i], mktime(0, 0, 0, $monat, $tag, $jahr));	
						
					}elseif ($this->value_type[$i] != "multipleselectsql" && $this->value_type[$i] != "subline" && $this->value_type[$i] != "empty") $aa->setValue($this->value_tbl[$i],$FORM[$this->rfid][values][$i]);
					
				}
				$aa->insert();				
				$msg = "Daten wurden gespeichert";
				
				for ($i=0;$i<$this->counter;$i++)
				{	
					$FORM[$this->rfid][values][$i] = htmlentities(stripslashes($FORM[$this->rfid][values][$i]));
				}
				
			}else
			{
				for ($i=0;$i<$this->counter;$i++)
				{	
					$FORM[$this->rfid][values][$i] = htmlentities(stripslashes($FORM[$this->rfid][values][$i]));
				}
			}
		}		
		
	
		// --------------------------------- FORMULAR
		
		if ($this->form_show || $this->ShowFormAlways)
		{
		
			$ausgabe = "<table width=".$this->width." cellpadding=6 cellspacing=1 border=0 >";
			$ausgabe.= "<form ENCTYPE='multipart/form-data' action='".$this->url."' method='".$this->method."' name='".$this->formname."'>".$this->form_header;			
			$ausgabe.= "<input type=hidden name=FORM[$this->rfid][submit] value=1>";
			
			
			// ---------------------- FORM REIHEN
	
			$colcounter = $this->cols[0];
			for ($i=0;$i<$this->counter;$i++)
			{
				if ($this->cols[$i]!="") $colcounter = $this->cols[$i];
				else $this->cols[$i] = $colcounter;
				if ($maxcount<$this->cols[$i]) $maxcount = $this->cols[$i];
			}
			$colcounter = 0;
			
			if ($errmsg!="") $ausgabe .= "<tr><td colspan=".($maxcount+2)." class=warning>$errmsg<br>Daten wurden noch nicht gespeichert</td></tr>";
			if ($msg!="") $ausgabe .= "<tr><td colspan=".($maxcount+2)." class=warning>$msg</td></tr>";
			
			
			for ($i=0;$i<$this->counter;$i++)
			{
				$name = "FORM[$this->rfid][values][$i]";
				$value = $FORM[$this->rfid][values][$i];

				// echo "<br>$i $maxcounter ".$this->cols[$i]." ".$this->cols[$i-1]." ".$this->value_form[$i];
				
				$colcounter++;
				
				if ($this->cols[$i-1] != $this->cols[$i])
				{
					if ($i!=0) $ausgabe .= "</tr>\n\n";
					$ausgabe .= "\n\n<tr>";
					$colcounter = 0;
					
				}else
				{
					// anfang
					
					// ende
					if ($colcounter==$this->cols[$i])
					{
						$ausgabe .= "</tr>\n\n";
						$ausgabe .= "\n\n<tr>";
						$colcounter = 0;	
					}
					
				}


				$addcolspawn = 0;
				if ($this->cols[$i]<$maxcount) $addcolspawn = 2;



			
	
				switch($this->value_type[$i])
				{
				
				// ---------------------- MULTIPLE SQL SELECT AUSGABE
				case("multipleselectsql"):
					if ($this->form_type == "add")
					{
						$ausgabe .= "<td colspan=2>Multiple Felder nur bei edit möglich	</td>";
					}else
					{
						$ausgabe .= "\n\n";
						$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
	
						$ssql = new sql;
						$ssql->setQuery($this->type_value1[$i]);
	
						$ssel = new rexselect();
						$ssel->setName($name."[]");
						$ssel->setMultiple(1);
						$ssel->setSize($this->type_value4[$i]);
						$ssel->setStyle("width:100%;");
						for ($j=0;$j<$ssql->getRows();$j++)
						{
							$ssel->addOption($ssql->getValue($this->type_value3[$i]),$ssql->getValue($this->type_value2[$i]));
							$ssql->next();
						}
						
						// $selsql = new sql;
						// $selsql->setQuery("select * from ".$this->type_value5[$i]." where ".$this->type_value6[$i]);
						if (is_Array($FORM[$this->rfid][values][$i]))
						{
							reset($FORM[$this->rfid][values][$i]);
							for ($j=0;$j<count($FORM[$this->rfid][values][$i]);$j++)
							{
								$ssel->setSelected(current($FORM[$this->rfid][values][$i]));
								next($FORM[$this->rfid][values][$i]);
							}
						}	
						$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn).">".$ssel->out()."</td>";
						$ausgabe .= "";
					}
					break;
				
				
				
				// ---------------------- SINGLE SQL SELECT AUSGABE
				case("singleselectsql"):
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";

					$ssql = new sql;
					$ssql->setQuery($this->type_value1[$i]);

					$ssel = new rexselect();
					$ssel->setName($name);
					$ssel->setStyle("width:100%;");
					if ($this->value_check[$i]!=1) $ssel->addOption("----------------- keine Angabe -----------------","0");
					for ($j=0;$j<$ssql->getRows();$j++)
					{
						$ssel->addOption($ssql->getValue($this->type_value3[$i]),$ssql->getValue($this->type_value2[$i]));
						$ssql->next();
					}
					$ssel->setSelected($value);
					$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn).">".$ssel->out()."</td>";
					$ausgabe .= "";
					break;
				
				// ---------------------- SINGLE SELECT AUSGABE
				case("singleselect"):
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
					$stype = explode("|",$this->type_value1[$i]);
					$ssel = new rexselect();
					$ssel->setName($name);
					$ssel->setStyle("width:100%;");
					for ($j=0;$j<count($stype);$j++)
					{
						$svalue = $stype[$j];
						$j++;
						$sname = $stype[$j];
						$ssel->addOption($sname,$svalue);
					}
					$ssel->setSelected($value);
					$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn).">".$ssel->out()."</td>";
					$ausgabe .= "";
					break;

				// ---------------------- Checkbox
				case("checkbox"):
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
					$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn)."><input type=checkbox name=$name value=1 ";
					if ($value == 1 || $value == "on") $ausgabe .= "checked";
					$ausgabe .= "></td>";
					$ausgabe .= "";
					break;

				// ---------------------- PIC/JPG
				case("picjpg"):
					if ($value!="")
					{
						$ausgabe .= "\n\n";
						$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
						$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn)."><table cellpadding=2 cellspacing=0><tr><td><input name=$name type=file size=10></td><td rowspan=2>&nbsp;&nbsp;&nbsp;</td><td rowspan=2><img src=".$this->type_value2[$i]."$value width=".$this->type_value3[$i]." height=".$this->type_value4[$i]."></td></tr>";
						$ausgabe .= "<tr><td valign=middle align=left class=grey><input type=checkbox name=FORM[$this->rfid][values][$i][delete]>&nbsp;&nbsp;Datei löschen </td></tr></table>";
						$ausgabe .= "</td>";
					}else
					{
						$ausgabe .= "\n\n";
						$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
						$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn)."><input name=$name type=file size=10></td>";
						$ausgabe .= "";
					}
					break;
									

				// ---------------------- FILE
				case("file"):
					$myout = "";
					if ($value!="")
					{
						$myout = "\n\n<table><tr>";
						$myout .= "<td valign=middle align=right class=grey><input type=checkbox name=FORM[$this->rfid][values][$i][delete]></td>";
						$myout .= "<td class=grey>Datei löschen <a href=".$this->type_value2[$i]."$value target=_blank>$value</a></td>";
						$myout .= "</tr></table>";
					}
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."<br>$myout</td>";
					$ausgabe .= "<td class=grey><input name=$name type=file size=10></td>";
					$ausgabe .= "";
					break;

				// ---------------------- HTMLAREA
				case("htmlarea"):
					if ($this->type_value1[$i]=="") $this->type_value1[$i] = "width:100%; height:100px;";
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=top class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
					$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn).">".REXHTMLAREA($name,$value)."</td>";
					$ausgabe .= "";
					break;



				// ---------------------- TEXTAREA
				case("textarea"):
					if ($this->type_value1[$i]=="") $this->type_value1[$i] = "width:100%; height:100px;";
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=top class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
					$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn)."><textarea name=$name cols=30 rows=5 style='".$this->type_value1[$i]."'>$value</textarea></td>";
					$ausgabe .= "";					
					break;

				// ---------------------- HIDDEN
				case("hidden"):
					$ausgabe .= "<input type=hidden name=$name value=\"".$this->type_value1[$i]."\">";
					break;
					
				// ---------------------- TEXT
				case("text"):
					if ($this->type_value1[$i]=="") $this->type_value1[$i] = "width:100%;";
					if ($this->type_value2[$i]!="") $this->type_value2[$i] = "maxlength=".$this->type_value2[$i];
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
					$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn)."><input type=text name=$name value=\"$value\" ".$this->type_value2[$i]." size=20 style='".$this->type_value1[$i]."'></td>";
					$ausgabe .= "";					
					break;

				// ---------------------- DATUM
				case("datum"):
					if ($this->type_value1[$i]=="") $this->type_value1[$i] = "width:100%;";
					if ($this->type_value2[$i]!="") $this->type_value2[$i] = "maxlength=".$this->type_value2[$i];
					if(!preg_match("![0-9]{2}\.[0-9]{2}\.[0-9]{4}!", $value))
						$value = date("d.m.Y", $value);
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
					$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn)."><input type=text name=$name value=\"$value\" ".$this->type_value2[$i]." size=20 style='".$this->type_value1[$i]."'></td>";
					$ausgabe .= "";					
				break;

				// ---------------------- Überschrift
				case("subline"):
				
					$ausgabe .= "\n\n";
					$ausgabe .= "<th valign=middle align=".$this->value_tbl[$i]." colspan=".(2+$addcolspawn).">".$this->value_form[$i]."</th>\n";
					$ausgabe .= "\n";
				break;
				
				// ---------------------- Überschrift
				case("empty"):				
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=middle class=grey colspan=".(2+$addcolspawn).">&nbsp;</td>\n";
					$ausgabe .= "\n";
				break;
				

				// ---------------------- STANDARD AUSGABE - TEXT
				default:
					$ausgabe .= "\n\n";
					$ausgabe .= "<td valign=middle class=grey width=".$this->labelwidth." >".$this->value_form[$i]."</td>";
					$ausgabe .= "<td class=grey colspan=".(1+$addcolspawn)."><input type=text name=$name value=\"$value\" size=20 style='width:100%'></td>";
					$ausgabe .= "";					

				}
			}	
	
			$ausgabe .= "</tr>";
	
			// ---------------------- SUBMIT
		
			$ausgabe .= "<tr>\n\n";
			$ausgabe .= "<td class=dgrey  width=".$this->labelwidth." >&nbsp;</td>\n\n";
			$ausgabe .= "<td align=left class=dgrey colspan=".($maxcount+1)."><input type=submit value='".$this->submit_value."'></td>\n\n";
			$ausgabe .= "</tr>\n\n";
	
			$ausgabe .= "</form></table>\n\n";
	
			return $ausgabe;
		}else
		{
			if ($msg != "")
			{
				$ausgabe = "<table width=".$this->width." cellpadding=6 cellspacing=1 border=0 bgcolor=#ffffff>";
				$ausgabe .="<tr><td class=warning>$msg</td></tr>";
				$ausgabe .="</table>";
				
				return $ausgabe;
			}
		}

	}

	function setName($formname)
	{
		$this->formname	= $formname;
	}
	
	function setWidth($width)
	{
		$this->width = $width;
	}
	// TODO:
	function setLabelWidth($labelwidth)
	{
		$this->labelwidth = $labelwidth;
	}
	
	function setTablename($name)
	{
		$this->tbl_name = $name;
	}
	
	function setFormtype($type,$where='',$error='')
	{
		$this->form_type = $type;
		$this->form_where = $where;

		if ($type == "edit")
		{
			$this->sql->setQuery("select * from $this->tbl_name where $where");
			if ($this->sql->getRows() != 1) echo $error;
			else $this->form_show = true;

		}elseif($type == "add")
		{
			$this->form_show = true;
		}
	}
	
	function setFormheader($name)
	{
		$this->form_header = $name;
	}

	function setShowFormAlways($status = true)
	{
		if ($status == true or $status == 1) $this->ShowFormAlways = true;
		else $this->ShowFormAlways = false;
	}

	function setSubmitValue($submit)
	{
		$this->submit_value = $submit;
	}
	
	function checkFilename($name,$folder)
	{

		$FILENAME = $name;
		$NFILENAME = "";
		for ($cn=0;$cn<strlen($FILENAME);$cn++)
		{
			$char = substr($FILENAME,$cn,1);
			if ( preg_match("([_A-Za-z0-9\.-])",$char) ) $NFILENAME .= strtolower($char);
			else if ($char == " ") $NFILENAME .= "_";
		}

		if (strrpos($NFILENAME,".") != "")
		{
			$NFILE_NAME = substr($NFILENAME,0,strlen($NFILENAME)-(strlen($NFILENAME)-strrpos($NFILENAME,".")));
			$NFILE_EXT  = substr($NFILENAME,strrpos($NFILENAME,"."),strlen($NFILENAME)-strrpos($NFILENAME,"."));
		}else
		{
			$NFILE_NAME = $NFILENAME;
			$NFILE_EXT  = ".txt";
		}
		if ( $NFILE_EXT == ".php" || $NFILE_EXT == ".php3" || $NFILE_EXT == ".php4" || $NFILE_EXT == ".php5" || $NFILE_EXT == ".phtml" || $NFILE_EXT == ".pl" || $NFILE_EXT == ".asp"|| $NFILE_EXT == ".aspx"|| $NFILE_EXT == ".cfm" ) $NFILE_EXT .= ".txt";
		$NFILENAME = $NFILE_NAME.$NFILE_EXT;
		
		if (file_exists("$folder/$NFILENAME"))
		{
			for ($cf=0;$cf<1000;$cf++)
			{
				$NFILENAME = $NFILE_NAME."_$cf"."$NFILE_EXT";
				if (!file_exists("$folder/$NFILENAME")) break;
			}
		}
		
		$return[name] = $FILENAME;
		$return[nname] = $NFILENAME;
		$return[ext] = $NFILE_EXT;

		return $return;
	}

	function resizeJPGImage($sourcefile, $destfile, $nwidth,$nheight) {

		$g_imgcomp = 90;
	
		if (file_exists($sourcefile)) {

			$g_is = getimagesize($sourcefile);
			$width = $g_is[0];
			$height = $g_is[1];
					
			$img_src = imagecreatefromjpeg($sourcefile);
			$img_dst = imagecreatetruecolor($nwidth, $nheight);
			imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $nwidth, $nheight, $width, $height);
			imagejpeg($img_dst, $destfile, $g_imgcomp);

			imagedestroy($img_dst);
			imagedestroy($img_src);

			return true;

		} else
			return false;
	}














}

?>