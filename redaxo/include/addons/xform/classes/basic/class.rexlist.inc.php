<?php

// class liste 1.0 [redaxo/prozer]
// 
// erstellt 01.12.2003
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

// todos:
// aufraeumen/ vereinfachen/ vereinheitlichen
// 
// noch viel sinnloses/altes von anderen projekten drin


class rexlist
{

	var $data;   		// fuer die angezeigten felder
	var $data_name; 	// fuer die feldbezeichungen
	var $data_num; 		// fuer die anzahl der felder
	var $data_order; 	// 1/0 ob sortierbar
	var $connect;		// connect fuer verknuepfung zb.
	var $format;		// welche formatierung
	var $format_value1;	// formatierungswert
	var $format_value2;	// formatierungswert
	var $format_value3;	// formatierungswert
	var $list_amount;	// wieviel reihen gleichzeitig anzeigen ?
	var $order_name;
	var $order_type;
	var $default_order_name;
	var $default_order_type;
	var $blaettern_bottom;
	var $blaettern_top;
	var $blaettern_head;
	var $blaettern_link;	// blaettern zusatz
	var $anzeige;		// wenn 0 dann als echo sonst als return
	var $query;		// den select direkt eingeben
	var $page;
	var $DB;
	var $addonlink;
	var $rows;
	var $sql;
	var $var_next;
	var $var_ordername;
	var $var_ordertype;
	var $sort_char;
	
	
	// ------------------------------------------------ CONTSTRUCTOR
	
	function rexlist()
	{
		global $REX;
		
		$this->anzeige = 0;
		$this->list_amount = 10;
		$this->DB = 1;
		$this->blaettern_top = true;
		$this->blaettern_bottom = false;
		$this->blaettern_head = '<b><a href="###LINK_BACK###">&laquo;</a> '.
								'<a href="###LINK_NEXT###">&raquo;</a>'.
								' &nbsp; &nbsp; &nbsp; &nbsp; ###LIST_START### - ###LIST_END### of ###LIST_ALL###'.
								' </b>';
		$this->var_next = "FORM[next]";
		$this->var_ordername = "FORM[ordername]";
		$this->var_ordertype = "FORM[ordertype]";
		$this->table_header = "";
		$this->table_footer = "";
		$this->debugsql = FALSE;
		$this->sort_char = "#"; // HTML (Zeichen) was bei Sortierung auftaucht
	}
	
	// ------------------------------------------------ HEADER NACH <TABLE> HINZUFUEGEN
	
	function setTableHeader($table_header){
		$this->table_header = $table_header;
	}

	// ------------------------------------------------ HEADER VOR </TABLE> HINZUFUEGEN
	
	function setTableFooter($table_footer){
		$this->table_footer = $table_footer;
	}
	
	// ------------------------------------------------ HEADER VOR </TABLE> HINZUFUEGEN
	
	function setPaginated($var = true){
		if (!$var) $var = false;
		$this->blaettern_top = $var;
	}

	function setBottomPaginated($var = true){
		if (!$var) $var = false;
		$this->blaettern_bottom = $var;
	}
	
	// ------------------------------------------------ LISTEN QUERY SETZEN
	
	function setQuery($query){
		$this->query = $query;
		// $this->sql = new rex_sql($this->DB);
		// $this->sql->setQuery($this->query);
		// $this->rows = $this->sql->getRows();
	}
	
	// ------------------------------------------------ ROW QUERY SETZEN
	
	function setRowQuery($query)
	{
		// select count(id) from table
		$this->sql = new rex_sql($this->DB);
		$this->sql->setQuery($query);		
		if ($this->sql->getRows()==1) $this->rows = $this->sql->getValue("rows");
	}
	
	// ------------------------------------------------ WELCHE DATENBANK . DEFAULT = 1
	
	function setDB($DB){
		$this->DB = $DB;
	}
	
	// ------------------------------------------------ ZUSÄTZLICHE LINKS
	
	function setGlobalLink($addonlink)
	{
		$this->addonlink = $addonlink;
	}
	
	// ------------------------------------------------ Order setzen für rex_sql

	function setOrder($name,$type = "desc")
	{
		
		$order = array_search($name,$this->data);
		
		if ($this->data_order[$order])
		{
			$this->order_name = $name;
			if ($type != "desc") $type = "asc";
			$this->order_type = $type;
			return true;
		}else
		{
			return false;	
		}
	}

	// ------------------------------------------------ Order setzen für rex_sql

	function setDefaultOrder($name,$type = "desc")
	{
		$this->default_order_name = $name;
		$this->default_order_type = $type;
	}
	
	
	// ------------------------------------------------ SPALTE SETZEN MIT DATENBANKFELD

	function setValueOrder($set = true){
		if ($set == 1 || $set) $set = true;
		else $set = false;
		$this->data_order[$this->data_num] = $set;
	
	}
	
	// ------------------------------------------------ SPALTE SETZEN MIT DATENBANKFELD
	
	function setTD($tdextra){
		$this->td[$this->data_num] = $tdextra;
	}

	// ------------------------------------------------ SPALTE SETZEN MIT DATENBANKFELD
	
	function setValue($showname,$fieldname){
		$this->data_num++;
		$this->data[$this->data_num] 		= $fieldname;
		$this->data_name[$this->data_num] 	= $showname;
		$this->data_order[$this->data_num] 	= false;
		$this->connect[$this->data_num] 	= " value";
		$this->link[$this->data_num]		= "";
	}
	
	// ------------------------------------------------ FORMATIERUNG SETZEN - WIRD IN SHOWALL() DEFINIERT
	
	function setFormat($format,$format_value1="",$format_value2="",$format_value3="",$format_value4="")
	{
		$this->format[$this->data_num][] = $format;
		$this->format_value1[$this->data_num][] = $format_value1;
		$this->format_value2[$this->data_num][] = $format_value2;
		$this->format_value3[$this->data_num][] = $format_value3;
		$this->format_value4[$this->data_num][] = $format_value4;
	}
	
	// ------------------------------------------------ ANZAHL DER REIHEN
	
	function setLink($link,$feld)
	{
		$this->setFormat("link",$link,$feld);
	}
	
	// ------------------------------------------------ ANZAHL DER REIHEN
	
	function setList($amount)
	{
		$this->list_amount = $amount;
	}
	
	// ------------------------------------------------ LISTENAUSGABE
	
	function showall($next)
	{
		global $REX;
		// ------------- FALLS KEIN ROWSELECT ALLE DATENSAETZE HOLEN UND ANZAHL SETZEN
		
		if ($this->rows == "")
		{
			$this->sql = new rex_sql($this->DB);
			$this->sql->setQuery($this->query);			
			$this->rows = $this->sql->getRows();
		}		
		
		$echo =	"<table class=rex-table>";
		$echo.= $this->table_header;
		
		// ------------- BLAETTERN

		if (!($next>0 && $next <= $this->rows)){ $next = 0; }
		$list_start = $next;		
		$list_end = $next+$this->list_amount;
		if ($list_end>$this->rows) $list_end = $this->rows;
		$before = $next-$this->list_amount;
		if ($before<0) $before=0;
		$next = $next+$this->list_amount;
		if ($next>$this->rows) $next = $next-$this->list_amount;
		if ($next<0) $next=0;

		$bhead = $this->blaettern_head;
		$bhead = str_replace("###LINK_BACK###",$this->addonlink.$before,$bhead);
		$bhead = str_replace("###LINK_NEXT###",$this->addonlink.$next,$bhead);
		$bhead = str_replace("###LIST_START###",$list_start,$bhead);
		$bhead = str_replace("###LIST_END###",$list_end,$bhead);
		$bhead = str_replace("###LIST_ALL###",$this->rows,$bhead);

		if ($this->blaettern_top) $echo .= "<tr><td colspan=".($this->data_num)." class=lgrey>$bhead</td></tr>";

		// ------------ QUERY NEU ERSTELLEN MIT LIMIT

		$limit = "LIMIT ".$list_start.",".$this->list_amount;
		
		$order = "";
		if ($this->order_name != "") $order = " order by ".$this->order_name." ".$this->order_type;
		elseif($this->default_order_name != "") $order = " order by ".$this->default_order_name." ".$this->default_order_type;
		
		$SQL = new rex_sql($this->DB);
		$SQL->debugsql = $this->debugsql;
		$SQL->setQuery("$this->query $order $limit");
	
		// ------------ <TH>HEADLINES

		$echo .= "<tr>";
		for($i=1;$i<=$this->data_num;$i++)
		{
			$echo .= "<th>";
			
			if ($this->data_order[$i])
			{
				$type = $this->order_type;
				if ($type == "asc") $type = "desc";
				else $type = "asc";
				$echo .= " <a href=".$this->addonlink."&".$this->var_ordername."=".$this->data[$i]."&".$this->var_ordertype."=".$type."&".$this->var_next."=$before><b>".$this->data_name[$i]."</b>".$this->sort_char."</a>";
			}else
			{
				$echo .= $this->data_name[$i];
			}
			$echo .= "</th>";
		}
		$echo .= "</tr>";

		// ------------ ERSTELLUNG DER LISTE
	
		for($j=0;$j<$SQL->getRows();$j++)
		{

			for($i=1;$i <= $this->data_num;$i++)
			{
				
				// ----- START: DATENSATZ
			
				if (isset($this->td[$this->data_num])) $echo .= "<td class=grey valign=top ".$this->td[$this->data_num].">";
				else $echo .= "<td class=grey valign=top>";

				// ----- START: FORMAT

				if (isset($this->format[$i]) && !is_array($this->format[$i]))
				{
					$value = htmlspecialchars($SQL->getValue($this->data[$i]));
				}else
				{

					$value = @$SQL->getValue($this->data[$i]);
					$contentvalue = $this->data[$i];

					for ($k=0;$k<count(@$this->format[$i]);$k++)
					{
	
						switch($this->format[$i][$k])
						{

							case("link"):
								$linkid = $SQL->getValue($this->format_value2[$i][$k]);
								$value = '<a href="'.$this->format_value1[$i][$k].$linkid.$this->format_value3[$i][$k].'" '.$this->format_value4[$i][$k].'>'.$value.'</a>';
								break;
							
							case("ifvalue"):
								if ($value == $this->format_value1[$i][$k]) $value = $this->format_value2[$i][$k];
								break;
							
							case("ifempty"):
								if ($value == "") $value = $this->format_value1[$i][$k];
								break;
		
							case("prefix"):
								$value = $this->format_value1[$i][$k]."$value";
								break;
		
							case("suffix"):
								$value = "$value".$this->format_value1[$i][$k];
								break;
		
							case("callback"):
								$var = array();
								$var["value"] = &$value;
								$var["sql"] = &$SQL;
								$value = call_user_func($this->format_value1[$i][$k],$var);
								break;
							
							case("activestatus"):
								if ($value==0) $value = "inactive";
								else $value = "active";
								break;
								
							case("status"):
								if ($value==0) $value = "inactive user";
								elseif ($value==7) $value = "superadmin";
								elseif ($value>4) $value = "admin";
								elseif ($value==1) $value = "guest";
								else $value = "user";
								break;
								
							case("dt"):
								$dt = $this->format_value1[$i][$k];
								if ($dt == "") $dt = "M-d Y H:i:s";
								$value = date_from_mydate($value,$dt);
								break;
								
							case("hour"):
								$value = $value." h";
								break;
								
							case("minutes"):
								$value = "$value min";
								break;
								
							case("minute2hour"):
								$hours = intval($value/60);
								$minutes = ($value - ($hours*60))/60*100;
								if ($minutes<10) $minutes="0$minutes";
								elseif ($minutes==0) $minutes = "00";
								$value = "$hours,$minutes";
								break;
								
							case("date"):
								$format = $this->format_value1[$i][$k];
								if ($format == "") $format = "d.M.Y H:i:s";
								$value = date_from_mydate($value,$format);
								break;
								
							case("time"):
								$value = substr($value,0,2).":".substr($value,2,2)."";
								break;
								
							case("unixToDateTime"):
								$format = $this->format_value1[$i][$k];
								if ($format == "") $format = "d.M.Y H:i:s";
								$value = date($format,$value);
								break;
								
							case("nl2br"):
								$value = nl2br($value);
								break;
								
							case("prozent"):
								$value = "<img src=/pics/p_prozent/".show_prozent($value).".gif height=13 width=50>";
								break;
							
							case("wrap"):
								$value = $this->format_value1[$i][$k].$value.$this->format_value2[$i][$k];
								break;

							case("addfield"):
								$value = $value.$this->format_value2[$i][$k].$SQL->getValue($this->format_value1[$i][$k]).$this->format_value3[$i][$k];
								break;
							
							case("clear"):
								$value = "";
								break;
							
							case("substr"):
								/*
								$elements = imap_mime_header_decode($value);
								$value = "";
								for($l=0;$l<count($elements);$l++)
								{
									// echo "Charset: {$elements[$i]->charset}\n";
									$value .= $elements[$l]->text;
								}
								*/
								$value = substr($value,0,$this->format_value1[$i][$k]);
								// $value = htmlentities($value);
								break;
								
							case("content"):
								$value = $contentvalue;
								break;
		
							case("size"):
								$value = "<div style='text-align:right;width:auto;'>".$this->human_file_size($value)."</div>";
								break;
		
							case("js"):
								$elements = imap_mime_header_decode($value);
								$value = "";
								for($l=0;$l<count($elements);$l++)
								{
									// echo "Charset: {$elements[$i]->charset}\n";
									$value .= $elements[$l]->text;
								}		
								
								if ($value == "") $value = "<no entry>";
								
								if ($this->format_value4[$i][$k] == "") $value = substr($value,0,30);
								else if ($this->format_value4[$i][$k] == "nosubstr") $value = $value;
								else $value = substr($value,0,$this->format_value4[$i][$k]);
								
								$value = nl2br(htmlentities($value));
								$value = "<a href=javascript:".$this->format_value1[$i][$k].$SQL->getValue($this->format_value2[$i][$k]).$this->format_value3[$i][$k].">$value</a>";
								break;
		
							case("boldstatus"):
								// **********************
								// Prozer Special: MAIL
								// zum anzeigen von bold falls TRUE(1)
								// **********************
								
								$elements = imap_mime_header_decode($value);
								$value = "";
								for($l=0;$l<count($elements);$l++)
								{
									// echo "Charset: {$elements[$i]->charset}\n";
									$value .= $elements[$l]->text;
								}		
								
								if ($value == "") $value = "<no subject entered>";
								$value = substr($value,0,30);
								$value = htmlentities($value);
								//echo "<!-- ".$SQL->getValue("header")."-->";
								if ($SQL->getValue("spam") != 0 && eregi("X-Spam-Flag: YES", $SQL->getValue("header"))) {
									if (!$SQL->getValue($this->format_value1[$i][$k])) {
										$value = "<b style=\"color:red\">$value</b>";
									}
									else {
										$value = "<span style=\"color:red\">$value</span>";
									}
								}
								elseif (!$SQL->getValue($this->format_value1[$i][$k])) {
									$value = "<b>$value</b>";
								}
								break;
								
							case("image"):
								// **********************
								// Prozer Special
								// zum anzeigen von bold falls TRUE(1)
								// **********************
								if ($SQL->getValue($this->format_value1[$i][$k]) > 0)
									$value = $this->format_value2[$i][$k]." ".htmlentities($value);
								else $value = " ";
								break;
								
							case("statustodo"):
								// **********************
								// Prozer Special
								// zum anzeigen von bold falls TRUE(1)
								// **********************

								if ($value == 0) $value = "done";
								elseif ($value == 1) $value = "in work";
								elseif ($value == 2) $value = "new";
								break;
										
							case("replace_value"):
								$stype = explode("|",$this->format_value1[$i][$k]);
								$lvalue = $value;
								$defaultvalue = -1;
								for ($l=0;$l<count($stype);$l++)
								{
									$svalue = $stype[$l];
									$l++;
									$sname = @$stype[$l];
									if ($svalue === "") $defaultvalue = $sname;
									if ($lvalue == $svalue) $lvalue = $sname;
								}
								if ($lvalue == $value && $defaultvalue != -1) $lvalue = $defaultvalue;
								$value = $lvalue;
								break;
							
							case("showsql"):
								$query = str_replace("###VALUE###",$value,$this->format_value2[$i][$k]);
								$GG = new rex_sql;
								$GG->setQuery($query);
								$res = $GG->get_array();
								$value = "";
								foreach($res as $group)
								{
									$value .= "* ".$group["name"]."<br />";
								}
								// $value = $query;
								// $value = $res.$query;
								break;
							case("checkbox"):
								$value = "<input onclick=setTRColor('tr$j','#f0efeb','#d8dca5',this.checked); type=checkbox name='".$this->format_value1[$i][$k]."' value='".$value."'>";
								break;

						}
					}
				}
				// if ($value==""){ $value = "-"; }
	
				// ----- END: FORMAT
	
				$echo .= $value;
				$echo .= "</td>\n";
	
				// ----- END: DATENSATZ
			}
			
			$echo .= "</tr>";

			// ----- END: REIHE
	
			$SQL->next();
		}
		
		if ($this->blaettern_bottom) $echo .= "<tr><td colspan=".($this->data_num)." class=lgrey>$bhead</td></tr>";
	
		$echo.= $this->table_footer;
		$echo .= "</table>";
		
		return $echo;
	}
}

?>