<?

// class board 1.0
//
// erstellt 01.12.2003
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

// changed 02.04.04 Carsten Eckelman <careck@circle42.com>
//   * added the english translation
//   * maybe we should think about converting the board to a general
//     i18n concept like the one I introduced in the other classes

class board
{

	// aktualisierung am 14.10.2002
	// von jan
	// braucht sql class

	// neue variablen
	var $boardname;
	var $realboardname;
	var $DB;
	var $table;
	var $userjoin_query;
	var $userjoin_field;
	var $user_id;
	var $username;
	var $admin;
	var $filename;
	var $linkname;
	var $linkvalue;
	var $linkcounter;
	var $linkuser;
	var $linkuser_field_id;
	var $docname;
	var $errmsg;
	var $text;
	var $layout;
	var $anonymous = false;

	function board()
	{
		$this->admin = false;
		$this->DB = 1;
		$this->table = "board";
		$this->linkcounter = 0;
		$this->boardname = "standard";
		$this->realboardname = "Standard";
		$this->linkuser = "";
		$this->linkuser_field_id = 0;
		$this->setLang("de");
		$this->setLayout();
	}

	function setLang($lang)
	{
		if ($lang == "en")
		{
			// --- en
			$this->text[10] = "Forum name: ";
			$this->text[20] = "Topics found"; // 10 Themen gefunden
			$this->text[22] = "One topic found"; // 1 Thema gefunden
			$this->text[23] = "No topics found"; // Kein Thema gefunden
			$this->text[30] = "Add new topic";
			$this->text[40] = "Topic";
			$this->text[50] = "Author";
			$this->text[60] = "Replies";
			$this->text[70] = "Created";
			$this->text[80] = "Last entry";
			$this->text[90] = "[ No title entered ]";
			$this->text[100]= "New";
			$this->text[110]= "Today";
			$this->text[120]= "Yesterday";
			$this->text[130]= "No topics found"; // ! doppelt, siehe text[23]
			$this->text[140]= "Message";
			$this->text[150]= "d M Y H:i";
			$this->text[155]= "h";
			$this->text[160]= "Add topic";
			$this->text[170]= "No replies";
			$this->text[180]= "Your reply";
			$this->text[190]= "Add reply";
			$this->text[200]= "Please enter a reply!";
			$this->text[210]= "Reply added";
			$this->text[220]= "No such topic.";
			$this->text[230]= "Topic added";
			$this->text[240]= "You forgot to enter a title for your topic. The topic was not added!";
			$this->text[250]= "Topic and replies deleted!";
			$this->text[260]= "No such topic!";
			$this->text[270]= "[ delete topic and messages ]";
			$this->text[280]= "[ delete message ]";
			$this->text[290]= "Name";
			$this->text[300]= "Please enter your name";
		}else
		{
			// --- de
			$this->text[10] = "Forumname: ";
			$this->text[20] = "Themen gefunden"; // 10 Themen gefunden
			$this->text[22] = "Ein Thema gefunden"; // 1 Thema gefunden
			$this->text[23] = "Keine Themen gefunden"; // Kein Thema gefunden
			$this->text[30] = "Neues Thema hinzufügen";
			$this->text[40] = "Thema";
			$this->text[50] = "Ersteller";
			$this->text[60] = "Antworten";
			$this->text[70] = "Erstellt";
			$this->text[80] = "Letzter Eintrag";
			$this->text[90] = "[ Kein Titel eingeben ]";
			$this->text[100]= "Neu";
			$this->text[110]= "Heute";
			$this->text[120]= "Gestern";
			$this->text[130]= "Keine Themen gefunden";
			$this->text[140]= "Nachricht";
			$this->text[150]= "d M Y H:i";
			$this->text[155]= "h";
			$this->text[160]= "Thema hinzufügen";
			$this->text[170]= "Keine Antworten";
			$this->text[180]= "Deine Antwort";
			$this->text[190]= "Antwort hinzufügen";
			$this->text[200]= "Bitte gib eine Antwort ein !";
			$this->text[210]= "Antwort wurde hinzugefügt";
			$this->text[220]= "Dieses Thema existiert nicht.";
			$this->text[230]= "Thema wurde hinzugefügt";
			$this->text[240]= "Du hast keine Themaüberschrift eingegeben. Thema wurde nicht hinzugefügt !";
			$this->text[250]= "Thema und Antworten wurden gelöscht !";
			$this->text[260]= "Dieses Thema existiert nicht !";
			$this->text[270]= "[ delete topic and messages ]";
			$this->text[280]= "[ delete message ]";
			$this->text[290]= "Name";
			$this->text[300]= "Bitte gib einen Namen ein";
		}
	}

	function setLayout()
	{
		// --- td
		$this->layout[10] = "class=dgrey";
		$this->layout[20] = "class=grey";
		$this->layout[30] = "class=lgrey";
		$this->layout[40] = "class=warning";
		// --- table
		$this->layout[50] = "border=0 cellpadding=5 cellspacing=1 width=100%";
		// --- links
		$this->layout[60] = " ";
		// --- neu.gestern.heute
		$this->layout[70] = " color=#ff0000";
	}

	function setDB($db)
	{
		$this->DB = $db;
	}

	function setTable($table)
	{
		$this->table = $table;
	}

	function setAdmin()
	{
		$this->admin = true;
	}

	function setLinkUser($link,$id)
	{
		// $link = "index.php?article_id=1&user_id=";
		$this->linkuser = $link;
		$this->linkuser_field_id = $id;
	}

	function setDocname($name)
	{
		$this->docname = $name;
	}

	function setBoardname($boardname)
	{
		$this->boardname = $boardname;
	}

	function setRealBoardname($realboardname)
	{
		$this->realboardname = $realboardname;
	}

	function setUser($user_id,$username)
	{
		$this->user_id = $user_id;
		$this->username = htmlentities($username);
	}

	function addLink($name,$value)
	{
		$this->linkcounter++;
		$this->linkname[$this->linkcounter] = $name;
		$this->linkvalue[$this->linkcounter] = $value;
	}

	function getLink($type = "get")
	{
		// ---------------------------------- generate link
		$return = "";
		for($i=1;$i<=$this->linkcounter;$i++)
		{
			if ($type=="get") $return .= "&".$this->linkname[$i]."=".urlencode($this->linkvalue[$i]);
			else $return .= "<input type=hidden name=\"".$this->linkname[$i]."\" value=\"".$this->linkvalue[$i]."\">";
		}

		if ($type=="get") $return .= "&FORM[boardname]=".urlencode($this->boardname);
		else $return .= "<input type=hidden name=\"FORM[boardname]\" value=\"".$this->boardname."\">";

		return $return;
	}

	function checkVars()
	{
		$return = true;

		if ($this->username == "") $return = false;
		if ($this->user_id == "") $return = false;

		// vscope anonymous hack
		if ($this->anonymous == true){
			$return = true;
	        }

		// $return = "Vars are not correct ! Please check the parameters !";

		return $return;
	}

	function setUserjoin($userjoin,$userfield)
	{
		$this->userjoin_query = "left join $userjoin";
		$this->userjoin_field = $userfield;
	}

	function showUser($id,$name)
	{
		if ($this->linkuser != "")
		{
			// vscope anonymous hack
			if($this->anonymous == false){
				return "<a href=".$this->linkuser."$id ".$this->layout[60].">".htmlentities($name)."</a>";
			} else {
				return htmlentities($name);
			}
		}else
		{
			return htmlentities($name);
		}
	}

	function showBoard()
	{
		global $FORM;

			if ($FORM[func]== "deleteMessage" && $this->admin) $return = $this->deleteMessage($FORM[message_id]);
			elseif ($FORM[func]== "reply" && $this->checkVars())$return = $this->saveMessage($FORM[subject],$FORM[message],$FORM[message_id],$FORM[anonymous_user]);
			elseif ($FORM[func]== "addtopic" && $this->checkVars()) $return = $this->saveMessage($FORM[subject],$FORM[message],$FORM[message_id],$FORM[anonymous_user]);
			elseif ($FORM[func]== "showMessage") $return = $this->showMessage();
			elseif ($FORM[func]== "showAddMessage" && $this->checkVars()) $return = $this->showAddMessage();
		        elseif ($FORM[func]== "showAddTopic" && $this->checkVars()) $return = $this->showAddTopic();
			else $return = $this->showMessages();

		return "$return<br>";
	}

	function showMessages()
	{
		// ---------------------------------- messages select
		$msql = new sql($this->DB);
		// $msql->debugsql = 1;
		$msql->setQuery("select * from $this->table $this->userjoin_query where ".$this->table.".re_message_id='0' and ".$this->table.".board_id='".$this->boardname."' and ".$this->table.".status='1' order by last_entry desc");

		$mout = "<table ".$this->layout[50].">
			<tr>
				<td colspan=5 ".$this->layout[30]."><b>".$this->text[10]."<a href=".$this->docname."?".$this->getLink()." ".$this->layout[60].">".$this->realboardname."</a></b></td>
			</tr>
			<tr>
				<td colspan=5 ".$this->layout[30].">"." ";

		if ($msql->getRows()==0) $mout .= $this->text[23];
		elseif ($msql->getRows()==1) $mout .= $this->text[22];
		else $mout .= $msql->getRows()." ".$this->text[20];

		if ( $this->checkVars()) $mout .= " - <a href=".$this->docname."?".$this->getLink()."&FORM[func]=showAddTopic ".$this->layout[60].">".$this->text[30]."</a>";

		$mout .= "</td>
			</tr>
			<tr>
				<td ".$this->layout[10]."><b>".$this->text[40]."</b></td>
				<td ".$this->layout[10]."><b>".$this->text[50]."</b></td>
				<td ".$this->layout[10]."><b>".$this->text[60]."</b></td>
				<td ".$this->layout[10]."><b>".$this->text[70]."</b></td>
				<td ".$this->layout[10]."><b>".$this->text[80]."</b></td>
			</tr>";

		if ($this->errmsg != "") $mout .= "<tr><td colspan=5 ".$this->layout[40]."><b>".$this->errmsg."</b></td></tr>";

		for ($i=0;$i<$msql->getRows();$i++)
		{
			$mout .= "<tr>
					<td ".$this->layout[20]."><a href=".$this->docname."?".$this->getLink()."&FORM[func]=showMessage&FORM[message_id]=".$msql->getValue($this->table.".message_id")." ".$this->layout[60].">";

			if ($msql->getValue("subject")== "") $mout .= $this->text[90];
			else $mout .= $msql->getValue("subject");

			$datenow = date("YmdHis");

			/*
			if ($datenow-10000 <  $msql->getValue($this->table.".last_entry")) $add_marker =  "<font ".$this->layout[70].">".$this->text[100]."</font>";
			elseif (substr($datenow,6,2) ==  substr($msql->getValue($this->table.".last_entry"),6,2)) $add_marker =  "<font ".$this->layout[70].">".$this->text[110]."</font>";
			elseif ((substr($datenow,6,2)-1) ==  substr($msql->getValue($this->table.".last_entry"),6,2)) $add_marker =  "<font ".$this->layout[70].">".$this->text[120]."</font>";
			else $add_marker =  "<font color=#ff0000>&nbsp;</font>";
			*/

			// vscope anomyous hack
			$mout .= "</a></td>";
			if($msql->getValue('anonymous_user')!=''){
				$mout .= "<td ".$this->layout[20].">".$msql->getValue('anonymous_user')."</td>";
			} else {
				$mout .= "<td ".$this->layout[20].">".$this->showUser($msql->getValue($this->linkuser_field_id),$msql->getValue($this->userjoin_field))."</td>";
			}
			$mout .= "
				<td ".$this->layout[20].">".$msql->getValue($this->table.".replies")."</td>
				<td ".$this->layout[20].">".$this->date_from_mydate($msql->getValue($this->table.".stamp"),$this->text[150]).$this->text[155]."</td>
				<td ".$this->layout[20].">".$this->date_from_mydate($msql->getValue($this->table.".last_entry"),$this->text[150]).$this->text[155]." $add_marker</td>
				</tr>";
			$msql->next();
		}

		if ($msql->getRows()==0) $mout .= "<tr><td colspan=5 ".$this->layout[20].">".$this->text[130]."</td></tr>";

		$mout .= "</table>";
		return $mout;
	}


	function showAddTopic()
	{
		global $FORM;

		$mout = "<table ".$this->layout[50].">
			<form action=".$this->docname." method=post>
			".$this->getLink("post")."
			<input type=hidden name=FORM[func] value=addtopic>
			<tr>
				<td colspan=2 ".$this->layout[30]."><b>".$this->text[10]."<a href=".$this->docname."?".$this->getLink()." ".$this->layout[60].">".$this->realboardname."</a></b></td>
			</tr>
			<tr>
				<td colspan=2 ".$this->layout[10]."><b>".$this->text[30]."</b></td>
			</tr>
			".$this->warning();
		// vscope anonymous hack
		if(($this->anonymous != false) && ($this->username=='')){
			$mout.= "
			<tr>
				<td width=200 ".$this->layout[10].">".$this->text[290]."</td>
				<td width=500 ".$this->layout[20]."><input type=text name=FORM[anonymous_user] maxlength=15 style='width: 100%;'></td>
			</tr>
			";
		}

		$mout.= "
			<tr>
				<td width=200 ".$this->layout[10].">".$this->text[40]."</td>
				<td width=500 ".$this->layout[20]."><input type=text name=FORM[subject] style='width: 100%;' value='".htmlentities($FORM[subject])."'></td>
			</tr>
			<tr>
				<td ".$this->layout[10]." valign=top>".$this->text[140]."<br>".$this->username."<br>".$this->date_from_mydate(date("YmdHis"),$this->text[150]).$this->text[155]."</td>
				<td ".$this->layout[20]." valign=top><textarea cols=60 rows=5 name=FORM[message] style='width: 100%;'>".stripslashes(htmlentities($FORM[message]))."</textarea></td>
			</tr>
			<tr>
				<td ".$this->layout[20]." valign=top>&nbsp;</td>
				<td ".$this->layout[20]." valign=top><input type=submit value='".$this->text[160]."'></td>
			</tr>
			</form>
			</table>";
		return $mout;
	}

	function showMessage()
	{
		global $FORM;

		$msql = new sql($this->DB);
		$msql->setQuery("select * from $this->table $this->userjoin_query where ".$this->table.".re_message_id='0' and ".$this->table.".board_id='".$this->boardname."' and ".$this->table.".message_id='".$FORM[message_id]."' and ".$this->table.".status='1'");

		if ($msql->getRows() == 1)
		{
			$mout = "<table ".$this->layout[50].">
				<tr>
					<td colspan=2 ".$this->layout[30]."><b>".$this->text[10]." <a href=".$this->docname."?".$this->getLink()." ".$this->layout[60].">".$this->realboardname."</a></b></td>
				</tr>
				<tr>
					<td width=200 ".$this->layout[10]."><b>".$this->text[40]."</b></td>
					<td width=500 ".$this->layout[20]."><b>".$msql->getValue($this->table.".subject")."</b></td>
				</tr>
				<tr>
					<td ".$this->layout[10]." valign=top>".$this->showUser($msql->getValue($this->linkuser_field_id),$msql->getValue($this->userjoin_field))."<br>".$this->date_from_mydate($msql->getValue($this->table.".stamp"),$this->text[150]).$this->text[155]."</td>
					<td ".$this->layout[20]." valign=top>".nl2br(htmlentities($msql->getValue($this->table.".message")));

			if ($this->admin) $mout .= "<br><br><a href=".$this->docname."?".$this->getLink()."&FORM[func]=deleteMessage&FORM[message_id]=".$msql->getValue($this->table.".message_id")." ".$this->layout[60].">".$this->text[270]."</a>";

			$mout .= "</td>
					</tr>";

			$mrsql = new sql($this->DB);
			$mrsql->setQuery("select * from $this->table $this->userjoin_query where ".$this->table.".re_message_id='".$FORM[message_id]."' and ".$this->table.".status=1");

			if ($mrsql->getRows()>0)
			{
				$mout .= "<tr>
						<td ".$this->layout[30]." colspan=2><b>".$this->text[60]."</b></td>
					</tr>".$this->warning(2);

				for ($i=0;$i<$mrsql->getRows();$i++)
				{
					$mout .= "<tr>";
					if($mrsql->getValue('anonymous_user')!=''){
						$mout .= "<td ".$this->layout[10]." valign=top><font color=#bbbbbb>".sprintf ("%03d",($i+1))."</font><br>".$mrsql->getValue('anonymous_user')."<br>";
					} else {
						$mout .= "<td ".$this->layout[10]." valign=top><font color=#bbbbbb>".sprintf ("%03d",($i+1))."</font><br>".$this->showUser($mrsql->getValue($this->linkuser_field_id),$mrsql->getValue($this->userjoin_field))."<br>";
					}
					$mout .= $this->date_from_mydate($mrsql->getValue($this->table.".stamp"),$this->text[150]).$this->text[155]."</td>
					<td ".$this->layout[20]." valign=top>".nl2br(htmlentities($mrsql->getValue($this->table.".message")));

					if ($this->admin) $mout .= "<br><br><a href=".$this->docname."?".$this->getLink()."&FORM[func]=deleteMessage&FORM[message_id]=".$mrsql->getValue($this->table.".message_id")." ".$this->layout[60].">".$this->text[280]."</a>";

					$mout .= "</td>
						</tr>";
					$mrsql->next();
				}
			}else
			{
				$mout .= "<tr><td colspan=2 ".$this->layout[20].">".$this->text[170]."</td></tr>";
			}


			if ( $this->checkVars())
			{
				$mout .= "
					<form action=".$this->docname." method=post>
					".$this->getLink("post")."
					<input type=hidden name=FORM[message_id] value=".$msql->getValue($this->table.".message_id").">
					<input type=hidden name=FORM[func] value=reply>
				      	<tr>
						<td colspan=2 ".$this->layout[30]."><b>".$this->text[180]."</b></td>
					</tr>
					";
	                        // vscope anonymous hack
	                        if(($this->anonymous != false) && ($this->username=='')){
	                        	$mout.= "
	                                	<tr>
	                                        	<td ".$this->layout[10].">".$this->text[290]."</td>
	                                        	<td ".$this->layout[20]."><input type=text name=FORM[anonymous_user] maxlength=15 style='width: 100%;' value='".htmlentities($FORM[subject])."'></td>
	                                  	</tr>
	                                  	";
	                        }
				$mout .= "
					<tr>
						<td ".$this->layout[10]." valign=top><font color=#bbbbbb>".sprintf ("%03d",($i+1))."</font><br>".$this->username."<br>".$this->date_from_mydate(date("YmdHis"),$this->text[150]).$this->text[155]."</td>
						<td ".$this->layout[20]." valign=top><textarea cols=60 rows=5 name=FORM[message] style='width: 100%;'>".$FORM[message]."</textarea></td>
					</tr>
					
					<tr>
						<td ".$this->layout[20]." valign=top>&nbsp;</td>
						<td ".$this->layout[20]." valign=top><input type=submit value='".$this->text[190]."'></td>
					</tr>
					</form>";
			}
			$mout .= "</table>";
		}
		return $mout;
	}

	function saveMessage($subject,$message,$message_id,$anonymous_user='')
	{
		global $FORM;

	        if(($this->anonymous==true) &&($anonymous_user == '')){
			$this->errmsg = $this->text[300];
			if($message_id>0){
				return $this->showMessage();
			} else {
				return $this->showAddTopic();
			}
	        }

		if ($message_id>0)
		{
			// reply
			$r_sql = new sql($this->DB);
			$r_sql->setQuery("select * from $this->table where message_id='$message_id' and board_id='".$this->boardname."' and status='1'");

			if (trim($message) == "" && $r_sql->getRows() == 1)
			{
				$this->errmsg = $this->text[200];

			}elseif ($r_sql->getRows() == 1)
			{
				// insert reply
				$r_sql = new sql($this->DB);
				$r_sql->setTable($this->table);
				$r_sql->setValue("user_id",$this->user_id);
				$r_sql->setValue("message",$message);
				$r_sql->setValue("re_message_id",$message_id);
				$r_sql->setValue("stamp",date("YmdHis"));
				$r_sql->setValue("board_id",$this->boardname);
	                        // vscope anonymous hack
	                        if($anonymous_user != ''){
					$r_sql->setValue("anonymous_user",$anonymous_user);
	                        }
				$r_sql->insert();

				// update message
				$u_sql = new sql($this->DB);
				$u_sql->setQuery("select * from $this->table where re_message_id='$message_id' and status='1'");

				$u_sql->setTable($this->table);
				$u_sql->where("message_id='$message_id'");
				$u_sql->setValue("last_entry",date("YmdHis"));
				$u_sql->setValue("replies",$u_sql->getRows());
				$u_sql->update();

				$this->errmsg = $this->text[210];
			}else
			{
				$this->errmsg = $this->text[220];
			}
			$return = $this->showMessage();
		}else
		{
			// new topic

			if ($subject!="")
			{
				$r_sql = new sql($this->DB);
				//$r_sql->debugsql = 1;
				$r_sql->setTable($this->table);
				$r_sql->setValue("user_id",$this->user_id);
				$r_sql->setValue("subject",$subject);
				$r_sql->setValue("message",$message);
				$r_sql->setValue("re_message_id",0);
				$r_sql->setValue("stamp",date("YmdHis"));
				$r_sql->setValue("last_entry",date("YmdHis"));
				$r_sql->setValue("board_id",$this->boardname);
				$r_sql->setValue("replies",0);
	                        // vscope anonymous hack
	                        if($anonymous_user != ''){
					$r_sql->setValue("anonymous_user",$anonymous_user);
	                        }
				$r_sql->insert();

				$this->errmsg = $this->text[230];
				$return = $this->showMessages();
			}else
			{
				$this->errmsg = $this->text[240];
				$return = $this->showAddTopic();
			}
		}

		return $return;

	}

	function deleteMessage($message_id)
	{
		global $FORM;


		// reply
		$r_sql = new sql($this->DB);
		$r_sql->setQuery("select * from $this->table where message_id='$message_id' and board_id='".$this->boardname."'");


		if ($r_sql->getRows() == 1)
		{
			if ($r_sql->getValue("re_message_id")!=0)
			{

				// reply
				$ur_sql = new sql($this->DB);
				$ur_sql->setTable($this->table);
				$ur_sql->where("message_id='$message_id'");
				$ur_sql->setValue("status",0);
				$ur_sql->update();

				$message_id = $r_sql->getValue("re_message_id");

				// update topic
				$u_sql = new sql($this->DB);
				$u_sql->setQuery("select * from $this->table where re_message_id='$message_id' and status='1'");

				$u_sql->setTable($this->table);
				$u_sql->where("message_id='$message_id'");
				$u_sql->setValue("replies",$u_sql->getRows());
				$u_sql->update();

				$FORM[message_id] = $r_sql->getValue("re_message_id");

				$return = $this->showMessage();
			}else
			{
				// topic
				$u_sql = new sql($this->DB);
				$u_sql->setTable($this->table);
				$u_sql->where("message_id='$message_id' or re_message_id='$message_id'");
				$u_sql->setValue("status",0);
				$u_sql->update();

				$this->errmsg = $this->text[250];
				$return = $this->showMessages();
			}
		}else
		{
			$this->errmsg = $this->text[260];
			$return = $this->showMessages();
		}


		return $return;
	}

	function warning($colspan=2)
	{
		if ($this->errmsg != "") return "<tr><td ".$this->layout[40]." colspan=$colspan><b>".$this->errmsg."</b></td></tr>";
		else return "";
	}

	function date_from_mydate($date,$format){

	if ($format=="" or $format=="date"){ $format="d M Y"; }
	if ($format=="time"){ $format="H:i:s"; }
	if ($format=="datetime"){ $format="d M Y H:i\h"; }

	$new_date = date($format,mktime(
			substr($date,8,2),
			substr($date,10,2),
			substr($date,12,2),
			substr($date,4,2),
			substr($date,6,2),
			substr($date,0,4)
			));

	return $new_date;

}



}

?>
