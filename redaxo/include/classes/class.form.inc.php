<?

// class form 1.0 [redaxo/prozer]
// 
// erstellt 01.12.2003
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

// changed 02.04.04 Carsten Eckelman <careck@circle42.com>
//   * i18n

class form
{

	var $action;
	var $method;
	var $submit_value;

	var $type;
	var $name;
	var $field;
	var $value1;
	var $value2;
	var $value3;

	var $counter;
	var $width;

	var $nosubmit;

	var $confirm;

	function form()
	{
		global $I18N;
		$this->counter 	= 0;
		$this->method 	= "post";
		$this->formname = "standard";
		$this->submit_value = $I18N->msg('submit_data');
		$this->width 	= "100%";
		$this->action 	= "index.php";
		$this->nosubmit	= false;
		$this->confirm 	= "";
	}

	function hideSubmit()
	{
		$this->nosubmit = true;
	}

	function setArticleID($article_id)
	{
	}

	function setConfirm($text)
	{
		$this->confirm 	= $text;		
	}

	function setName($formname)
	{
		$this->formname	= $formname;
	}
	
	function setWidth($width)
	{
		$this->width = $width;
	}

	function setAction($action)
	{
		$this->action 	= $action;
	}

	function set_submit_value($submit){
		$this->submit_value = $submit;
	}

	function setValue($type,$name,$field="",$value1="",$value2="",$value3="")
	{
		$this->type[$this->counter] 	= $type;
		$this->name[$this->counter]	= $name;
		$this->field[$this->counter]	= $field;
		$this->value1[$this->counter]	= $value1;
		$this->value2[$this->counter]	= $value2;
		$this->value3[$this->counter]	= $value3;

		$this->counter++;
	}

	function show_table()
	{
		global $FORM;
		
		$ausgabe = "<table width=".$this->width." cellpadding=3 cellspacing=1 border=0 bgcolor=#ffffff>
			<form ENCTYPE='multipart/form-data' action='".$this->action."' method='".$this->method."' name='".$this->formname."'>
			<input type=hidden name=FORM[submit] value=1>";

		if ($FORM[errmsg]!="") $ausgabe .= "<tr><td colspan=3 class=red>".$FORM[errmsg]."</td></tr>";

		for ($i=0;$i<$this->counter;$i++)
		{

			switch($this->type[$i])
			{
			case("file"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=top class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td class=grey colspan=2><input name='".$this->field[$i]."' TYPE='file' size=4></td>";
				$ausgabe .= "</tr>";
				break;
			case("hidden"):
				$ausgabe .= "<input type=hidden name=".$this->field[$i]." value=\"".$this->value1[$i]."\">";			
				break;
			case("headred"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<th valign=top colspan=3 class=red>".$this->name[$i]."</th>";
				$ausgabe .= "</tr>";
				break;
			case("head"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<th valign=top colspan=3>".$this->name[$i]."</th>";
				$ausgabe .= "</tr>";
				break;
			case("text"):
				if ($this->value2[$i]>29) $style = " style='width:100%'";
				else $style = "";
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=middle class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td colspan=2 class=grey><input type=text name=".$this->field[$i]." value=\"".$this->value1[$i]."\" size=".$this->value2[$i]."$style></td>";
				$ausgabe .= "</tr>";
				break;
			case("textarea"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=top class=grey colspan=3>".$this->name[$i]."</td></tr>";
				$ausgabe .= "<tr><td colspan=3 class=grey><textarea name=".$this->field[$i]." cols=\"".$this->value2[$i]."\" rows=".$this->value3[$i]." style='width:100%'>".htmlentities($this->value1[$i])."</textarea></td>";
				$ausgabe .= "</tr>";
				break;
			case("content"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=top class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td class=grey colspan=2>".$this->field[$i]."</td>";
				$ausgabe .= "</tr>";
				break;
			case("content2"):
				$ausgabe .= "<tr><td valign=top class=grey colspan=3>".$this->name[$i]."</td></tr>";
				$ausgabe .= "<tr><td class=lgrey colspan=3>".$this->field[$i]."</td></tr>";
				break;
			case("date"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=middle class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td class=grey width=70>";
				$ausgabe .= "<a href=javascript:getMyDate('".$this->formname."','".$this->field[$i]."','".$this->value1[$i]."','".$this->value2[$i]."');><img src=/pics/p_objects/but_date.gif border=0></a>";
				$ausgabe .= "</td>";
				$ausgabe .= "<td class=grey><input type=text name=".$this->field[$i]." value='".$this->value1[$i]."' size=10 ".$this->style[$i]." onfocus=this.blur();></td>";
				$ausgabe .= "</tr>";
				break;
			case("password"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=middle class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td colspan=2 class=grey><input type=password name=".$this->field[$i]." value=\"".$this->value1[$i]."\" style='width:200'></td>";
				$ausgabe .= "</tr>";
				break;
			case("nothing"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=top class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td colspan=2 class=grey>".$this->field[$i]."</td>";
				$ausgabe .= "</tr>";
				break;
			case("time"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=middle class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td class=grey colspan=2><select size=1 name=".$this->field[$i].">";
				for ($j=7;$j<24;$j=$j+0.25)
				{
					$ij = intval($j);
					$cj = $j-$ij;
					if ($cj==0.25)
					{
						$sj = "$ij:15 h";
						$hj = $ij."15";
					}elseif ($cj==0.50)
					{
						$sj = "$ij:30 h";
						$hj = $ij."30";
					}elseif ($cj==0.75)
					{
						$sj = "$ij:45 h";
						$hj = $ij."45";
					}else
					{
						$sj = "$j:00 h";
						$hj = $ij."00";
					}					

					if ($j<10) $sj = "0$sj";
					if ($j<10) $hj = "0$hj";
					
					if ($this->value1[$i] == "$hj") $ausgabe .= "<option value=$hj selected>$sj</option>";
					else $ausgabe .= "<option value=$hj>$sj</option>";
				}
				$ausgabe .= "</select></td>";
				$ausgabe .= "</tr>";
				$ausgabe .= "<tr><td class=grey>&nbsp;</td><td colspan=2 class=grey><img src=/pics/p_objects/timetable.gif usemap=#thismap$i border=0><map name=thismap$i>";
				for ($j=7;$j<22;$j++)
				{
					$x0 = ($j-7)*(10+3);
					$y0 = 0;
					$x1 = $x0+10;
					$y1 = 13;
					$value = $j."00";
					if ($j<10) $value = "0$value";
					$ausgabe .= "<area href=# onclick=document.".$this->formname.".elements['".$this->field[$i]."'].value='$value'; shape=rect coords=$x0,$y0,$x1,$y1>";
				}				
				$ausgabe .= "</map></td></tr>";
				break;	
				
			
			case("hours"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=middle class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td class=grey colspan=2><select size=1 name=".$this->field[$i].">";
				for ($j=0;$j<$this->value2[$i];$j++)
				{
					if ($this->value1[$i] == $j) $ausgabe .= "<option value=$j selected>$j</option>";
					else $ausgabe .= "<option value=$j>$j</option>";	
					
				}
				$ausgabe .= "</select> h</td>";
				$ausgabe .= "</tr>";
				break;
			case("status"):
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=middle class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td class=grey colspan=2><select size=1 name=".$this->field[$i].">";
				
				
				
				if ($this->value2[$i] == "") $this->value2[$i] = "inactive";
				if ($this->value3[$i] == "") $this->value3[$i] = "active";
				
				if ($this->value1[$i] == 0)
				{
					$ausgabe .= "<option value=0 selected>".$this->value2[$i]."</option>";
					$ausgabe .= "<option value=1>".$this->value3[$i]."</option>";
				}else
				{
					$ausgabe .= "<option value=0>".$this->value2[$i]."</option>";
					$ausgabe .= "<option value=1 selected>".$this->value3[$i]."</option>";
				}				
				$ausgabe .= "</select></td>";
				$ausgabe .= "</tr>";			
				break;
			default:
				$ausgabe .= "<tr>";
				$ausgabe .= "<td valign=top class=grey>".$this->name[$i]."</td>";
				$ausgabe .= "<td colspan=2 class=grey><input type=text name=".$this->field[$i]." value=\"".htmlentities($this->value1[$i])."\" size=20 style='width:100%'></td>";
				$ausgabe .= "</tr>";					
			}
		}

		if (!$this->nosubmit)
		{
			$ausgabe .= "<tr>";
			$ausgabe .= "<td class=dgrey width=110>&nbsp;</td>";
			$ausgabe .= "<td align=left class=dgrey>";
			
			if ($this->confirm != "") $ausgabe .= "<input type=image src=/pics/p_objects/but_submit.gif onClick=\"return confirm('".$this->confirm."');\">";
			else $ausgabe .= "<input type=image src=/pics/p_objects/but_submit.gif>";
			
			$ausgabe .= "</td>";
			$ausgabe .= "<td class=dgrey>&nbsp;</td>";
			$ausgabe .= "</tr>";
		}

		$ausgabe .= "</form></table>";

		if (($FORM[errmsg]!="" && $FORM[submit]==1) || $FORM[submit]=="") return $ausgabe;
		else return "";
	}
}

?>