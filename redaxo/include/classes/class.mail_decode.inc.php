<?

// class mail_decode 1.0 [prozer]
// 
// erstellt 01.12.2003
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: m.meissner,j.kristinus

class mail_decode
{

	var $showtype; // html oder text
	var $path_mail; // pfad zur mail
	var $path_decode; // decodierte teile der mail

	var $err_msg = array();			// Error Message Array
	var $headers = array();			// Multipart Headers - $headers[owner][which_part][what_headeritem]
	var $bodies = array();			// Multipart Bodies - $bodies[owner][which_part]
	var $depth = array();			// Multipart Depth - $depth[owner]
	var $depthcounter = 0;			// 
	var $done = array();			// which items were added
	var $order = array();			// in which order are the parts
	var $treeprefs = array();		// the preferences for the un_plain showtree
	var $temp_path ;			// temp path for html files
	var $html_count = 0;			// wieviele htmls in der mail??
	var $html = TRUE;			// soll HTML oder plain angezeigt werden
	var $tree = "";				// whole tree string
	var $downloadlink = "";
	var $idlink = "";
	var $innerhtmllink = "";		// innerhtmllink fuer html mails
	var $innerhtmllinkname = "";		// z.b. FORM[content-id]
	var $Innerhtmlcontenttype = "";		// z.b. FORM[content-type]
	var $Innerhtmlcontenttypename = "";	// z.b. FORM[content-name]
	var $innerhtmldl = 0;			// z.b. FORM[content-dl]
	var $clipboardlink = "";		// z.b. FORM[addtype]
	
	
	# private
	var $wroteplain = FALSE;		// intern für tree
	var $first = 0;
	var $isplain = FALSE;			// intern für tree
	var $ishtml = FALSE;			// intern für tree

	function mail_decode()
	{
		$this->setInnerHTMLVars();
	}

	# ---------------------------------------------------------------
	# private
	# ---------------------------------------------------------------

	function __loadMail()
	{

		if(file_exists($this->path_mail))
		{
			$handle = fopen($this->path_mail,"r");
			$content = fread($handle,filesize($this->path_mail));

			# hier beginnt das eigentlich auseinandernehmen der mail

			$this->__splitMail($content,0,0);
			fclose($handle);			
		}else
		{
			$this->err_msg[] = "The file doesn't exist, please select another one.";
		}
	}

	function __splitMail($s, $owner, $depth = 0)
	{

		# teilen der mail/part in header und body
		# der preg. pattern sucht nach body+header, wenn die mail mime konform ist
		# sollte dies problemlos funktionieren

		if (preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $s, $teil))
		{

			# next ist im prinzip nur eine unique id innerhalb der mail f?r ein part

			$next = count($this->bodies, COUNT_RECURSIVE)+1;

			# body des parts wird gespeichert $teil[2] = body

			$this->bodies[$owner][$next] = substr($teil[2],0,-2);
			
			# zeilenweise abarbeiten des headers $teil[1] = header

			$token = strtok($teil[1],"\n\r");
			$this->__splitHeader($token, $owner, $next);
			while ($token)
			{
				$token = strtok("\n\r");
				$this->__splitHeader($token, $owner, $next);
			}
			
			$this->depth[$next] = $depth;
			
			# sonderregelung fuer embedded emails die muessen 2 mal geteilt werden
			# d.h. __split_mail wird grad wieder mit dem body aufgerufen
			# und is_parent wird auf true gesetzt damit der part im tree anders erscheint

			if($this->headers[$owner][$next][content_type] == "message/rfc822")
			{
				$depth++;
				$this->__splitMail($this->bodies[$owner][$next], $next, $depth);
				// $this->headers[$owner][$next][is_parent] = FALSE;
			}
			
			# wenn es eine boundary deklaration gibt, gehen wir davon aus dass es multipart ist und teilen 

			if(isset($this->headers[$owner][$next][boundary]))
			{
				$depth++;				
				$this->__splitParts($this->bodies[$owner][$next], $owner, $next, $depth);
			}
		}
		#else $this->err_msg = 'Could not split header and body.';
	}

	function __splitHeader($b, $owner, $next)
	{

		# an diese funktion wird jede zeile des headers uebergeben
		# und eventuelle regular matches werden benutzt um bestimmte 
		# header dann zu setzten ( es werden _nur_ vorhandene header auch gesetzt )

		if (eregi ("^to:(.*)$", $b, $regs)) { $this->headers[$owner][$next][to] = Trim($regs[1]); } 
   		if (eregi ("^from:(.*)$", $b, $regs)) { $this->headers[$owner][$next][from] = Trim($regs[1]); }
   		if (eregi ("^subject:(.*)$", $b, $regs)) { $this->headers[$owner][$next][subject] = Trim($regs[1]); }
   		if (eregi ("^date:(.*)$ ", $b, $regs)) { $this->headers[$owner][$next][date] = Trim($regs[1]); }
   		if (eregi ("^content-type: ([^;\ ]*)", $b, $regs))
   		{
   			#if(strpos(str_replace(";","",Trim($regs[1]))," ") === FALSE) $this->headers[$owner][$next][content_type] = str_replace(";","",Trim($regs[1]));
   			#else $this->headers[$owner][$next][content_type] = substr(str_replace(";","",Trim($regs[1])),0,strpos(str_replace(";","",Trim($regs[1]))," "));
   			
   			$this->headers[$owner][$next][content_type] = $regs[1];
   			if($this->headers[$owner][$next][content_type] == 'text/html')
   			{	// hier wird für jede html ein name eingesetzt
   				$this->headers[$owner][$next][name] = "index".$this->html_count.".html";	// weil oft keiner vorhanden ist
   				$this->html_count++;								// falls doch steht der name in der regel nach dem content_type
   			}											// also wird der hier gesetzte name ?berschrieben
   		}
   		if (eregi ("boundary=([^;\ ]*)", $b, $regs))
   		{
   			$this->headers[$owner][$next][boundary] = Trim($regs[1],"\"");
   			$this->headers[$owner][$next][is_parent] = TRUE;
   		}
   		if (eregi ("^x-mailer:(.*)$", $b, $regs)) { $this->headers[$owner][$next][x_mailer] = Trim($regs[1]); }
   		if (eregi ("^content-transfer-encoding:(.*)$", $b, $regs)) { $this->headers[$owner][$next][content_transfer_encoding] = Trim($regs[1]); }
   		if (eregi ("^charset=\"(.*)\"", $b, $regs)) { $this->headers[$owner][$next][charset] = $regs[1]; }
   		if (eregi ("name=\"(.*)\"", $b, $regs)) { $this->headers[$owner][$next][name] = str_replace(" ","",Trim($regs[1])); }
   		if (eregi ("Content-ID:(.*)", $b, $regs)) { $this->headers[$owner][$next][content_id] = Trim(str_replace(array(">","<"),"",$regs[1])); }
   		if (eregi ("Message-ID:(.*)", $b, $regs)) { $this->headers[$owner][$next][message_id] = Trim(str_replace(array(">","<"),"",$regs[1])); }
   		if (eregi ("X-Priority:(.*)", $b, $regs)) { $this->headers[$owner][$next][x_priority] = Trim($regs[1]); }
   		if (eregi ("Mime-Version:(.*)", $b, $regs)) { $this->headers[$owner][$next][mime_version] = Trim(str_replace(array(">","<"),"",$regs[1])); }
   		if (eregi ("Content-Disposition:(.*)", $b, $regs)) { $this->headers[$owner][$next][content_disposition] = Trim($regs[1]); }
  	}

	function __splitParts($body,$owner, $next, $depth=0)
	{
		if(isset($this->headers[$owner][$next][boundary]))
		{

			# das teilen mit den boundaries ist ziemlich leicht, einfach den boundary
			# explode ?bergeben und schon haben wir ein array mit allen teilen
			# echo $this->headers[$owner][$next][boundary];

			$parts = explode($this->headers[$owner][$next][boundary], $body);
			// echo $this->headers[$owner][$next][boundary];
			for($i=0;$i<count($parts);$i++)
			{
				$das = count($this->bodies, COUNT_RECURSIVE)+1;
				
				# mit jedem der teile wird jetzt wieder gesplited, 
				# d.h. header und body werden getrennt

				$this->__splitMail($parts[$i],$next,$depth);
			}
		}
	}
	
	function __getItem($id, $what = "content_type")
	{

			# holen der parent ids

			$wo = array_keys($this->headers);

			# ausgeben wenn [$parent][$id] vorhanden

			for($k=0;$k<count($this->headers);$k++)
			{

				# what entscheidet was herauskommt
				# entweder body, alle header, die groesse oder ein spezieller header

				if($what == "body"){ if(isset($this->bodies[$wo[$k]][$id])) return $this->bodies[$wo[$k]][$id];
				}elseif($what == 'header'){ if(isset($this->headers[$wo[$k]][$id])) return $this->headers[$wo[$k]][$id];	
				}elseif($what == 'size'){ if(isset($this->bodies[$wo[$k]][$id])) return round(strlen($this->bodies[$wo[$k]][$id])/1024,1);
				}else { if(isset($this->headers[$wo[$k]][$id])) return $this->headers[$wo[$k]][$id][$what];
				}
			}
			return FALSE;
	}
	
	function __getIDs()
	{

		# hier werden alle ids aus $this->headers ausgelesen
		# und ausgegeben

		$he = array();
		$wo = array_keys($this->headers);
		for($i=0;$i<count($wo);$i++)
			foreach($this->headers[$wo[$i]] as $key => $value)
				$he[] = $key;
		return $he;
	}
	
	function __decodeString($s,$de)
	{
		if($de == 'base64') return base64_decode($s);
		elseif($de == 'quoted-printable') return quoted_printable_decode($s);
		else return $s;
	}

	function __parseHTML($id)
	{
	
		# suche nach cid:[[:alnum:]] Eintraegen in der html datei
		# preg_match_all("/cid:([[:alnum:]]+)/",$this->__decode_string($this->__getitem($id, "body"),$this->__getitem($id,"content_transfer_encoding")), $regs);

		preg_match_all("/cid:([a-zA-Z\@\$0-9]*)/",$this->__decodeString($this->__getItem($id, "body"),$this->__getItem($id,"content_transfer_encoding")),$regs);

		# print_r($regs);
		# mal den ganzen inhalt der html datei an eine variable uebergeben

		$contents = $this->__decodeString($this->__getItem($id, "body"),$this->__getItem($id,"content_transfer_encoding"));

		# f?r jede gefundene cid:[[:alnum:]] wird ein element mit der passenden content_id gesucht
		# und direkt extrahiert, daraufhin wird die verlinkung noch hergestellt in der html datei
		
		
		
		foreach($regs[1] as $key => $value)
		{
			foreach($this->__getIDs() as $ding)
			{
				if($this->__getItem($ding,"content_id") == $value)
				{
					# dateiname wird in $item gespeichert

					$item = $this->innerhtmllink."&".$this->innerhtmllinkname."=".$ding;

					# die cid:[[:alnum:]] wird durch $item also den dateinamen ersetzt

					$contents = str_replace("cid:".$value,$item,$contents);					
				}
			}
		}
		
		return $contents;
	}

	function __decodeCharset($value)
	{
		$elements = imap_mime_header_decode($value);
		$value = "";
		for($k=0;$k<count($elements);$k++)
		{
			// echo "Charset: {$elements[$i]->charset}\n";
			$value .= $elements[$k]->text;
		}
		return $value;
	}

	# ---------------------------------------------------------------
	# public
	# ---------------------------------------------------------------
	
	function setDefaultType($type)
	{
		if ($type == "html") $this->showtype = "html";
		else $this->showtype = "text";
	}

	function setPath($path)
	{
		$this->path_decode = $path;
	}
	
	function setMailpath($path)
	{
		$this->path_mail = $path;
	}
	
	function generateParts()
	{
		$this->__loadMail();
		
		$ids = $this->__getIDs();
		
		for ($i=0;$i<count($ids);$i++,next($ids))
		{
			// if (!$this->__getItem(current($ids),"is_parent"))
			// {
				if (
					$this->first==0 && 
					(($this->__getItem(current($ids),"content_type")=="text/html") || 
					($this->__getItem(current($ids),"content_type")=="text/plain"))
					) $this->first = current($ids);
				$handle = fopen($this->path_decode."/".current($ids),"w+");
				if ($this->__getItem(current($ids),"content_type")=="text/html")
				{
					$body_decoded = $this->__parseHTML(current($ids));
				}else
				{
					$body_decoded = $this->__decodeString($this->__getItem(current($ids),"body"),
							$this->__getItem(current($ids),"content_transfer_encoding"));
				}
				fwrite($handle, $body_decoded, strlen($body_decoded));
				fclose($handle);
			// }
		}
	}
	
	function setInnerHTMLVars($link = "index.php?article_id=125&FORM[mail_id]=1",
	$id = "FORM[content_id]",
	$type = "FORM[content_type]",
	$name = "FORM[content_name]",
	$dl = "FORM[dl]",
	$clipboard = "FORM[addtype]")
	{
		$this->innerhtmllink = $link;
		$this->innerhtmllinkname = $id;
		$this->innerhtmlcontenttype = $type;
		$this->innerhtmlcontenttypename = $name;
		$this->innerhtmldl = $dl;
		$this->clipboardlink = $clipboard;
	}
	
	function getMain($id=0)
	{

		$ids = $this->__getIDs();
		
		$first_html = 0;
		$first_plain = 0;
		$first_other = 0;	
		for ($i=0;$i<count($ids);$i++,next($ids))
		{
			if (!$this->__getItem(current($ids),"is_parent"))
			{
				if ($first_html == 0 && $this->__getItem(current($ids),"content_type")=="text/html") $first_html = current($ids);
				elseif ($first_plain == 0 && $this->__getItem(current($ids),"content_type")=="text/plain") $first_plain = current($ids);
				elseif ($first_other == 0) $first_other = current($ids);
			}
		}

		if($this->showtype=="html" && $first_html>0) $id = $first_html;
		elseif($first_plain>0) $id = $first_plain;
		// else $id = $first_other;
		
		$return[id] = $id;
		$return[type] = $this->__getItem($id,"content_type");
		
		return $return;

	}

	function getMailTree()
	{
		global $I18N;
	
		$ids = $this->__getIDs();
		
		sort($ids);
		
		for ($i=0;$i<count($ids);$i++,next($ids))
		{
			$id = current($ids);
			$echo .= "<table width=100% cellpadding=1 cellspacing=0 border=0><tr><td width=".(($this->depth[current($ids)]*30)+1).">&nbsp;</td><td>";
			if (!$this->__getItem(current($ids),"is_parent"))
			{
				$link = $this->innerhtmllink."&".$this->innerhtmllinkname."=".current($ids)."&".$this->innerhtmlcontenttype."=".urlencode($this->__getItem(current($ids),"content_type"));
				$clipboardlink = $this->clipboardlink."&".$this->innerhtmllinkname."=".current($ids)."&".$this->innerhtmlcontenttype."=".urlencode($this->__getItem(current($ids),"content_type"));
				$type = $this->__getItem(current($ids),"content_type");
				$size = $this->__getItem(current($ids),"size");
				$name = $this->__decodeCharset($this->__getItem(current($ids),"name"));
				$showlink = true;

				if ($name == "" && $type == "text/plain") $name = "text";
				if ($type == "text/html") $name = "html";
				elseif($name == "") $name = "-";
				
				if ($type == "application/applefile")
				{
					$name = "\"$name\" [apple] ";
					$showlink = FALSE;
				}

				if ($showlink)
				{
					$echo .= " \"<a target=mailframe href=$link><b>$name</b></a>\"";
					$echo .= " [$size kb] ";
	
	$echo .= " <a href=$link"."&".$this->innerhtmldl."=1&".$this->innerhtmlcontenttypename."=".urlencode($name).">".$I18N->msg('download')."</a>";
	
					// EXTRA CLIPBOARD / NUR FUER PROZER
					
					if ($this->__getItem(current($ids),"name") != "") $echo .= " | <a href=$clipboardlink&".$this->innerhtmlcontenttypename."=".urlencode($name)." target=extra>->".$I18N->msg('clipboard')."</a>";
				}else
				{
					$echo .= $name;	
				}

				// CONTENT TYPE ICON ZEIGEN

				// $echo .= " \"<b>".$this->__decodeCharset($this->__getItem(current($ids),"name"))."</b>\" ";
				// $echo .= " [".$this->__getItem(current($ids),"content_type")."] ";
				// $echo .= "depth:".$this->depth[current($ids)]."";
				
			}else
			{
				$echo .= " [".$this->__getItem(current($ids),"content_type")."] ";
			}
			$echo .= "</td></tr></table>";
		}
	
		return $echo;
		
	}
	

}

?>