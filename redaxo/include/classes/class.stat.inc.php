<?

class stat
{
	//
	// creating def arrays, sorted by visits  // TODO: how big can an array be? max index? 65536 ???
	var $MAIN;
	var $BROWSER;
	var $REFERER;
	var $SEARCH;
	var $visitcount = 0;	
	var $path = "include/generated/logs/";
	var $outtemplate = "include/pages/stats/stat_template.inc.php";
	var $evalshows;
	var $evalsnipps;
	
	
	var $debugpv = 0;
	
	function stat()
	{
	
		$this->MAIN['stamp'] = Array();
		$this->MAIN['ip'] = Array();
		$this->MAIN['pageviews'] = Array();		// which pages in an array under this one 
		$this->MAIN['useragent'] = Array();
		$this->MAIN['hostname'] = Array();
		$this->MAIN['referer'] = Array();
		
		$this->BROWSER['type'] = Array();
		$this->BROWSER['os'] = Array();
		
		$this->REFERER = Array();
		$this->SEARCH['engine'] = Array();
		$this->SEARCH['words'] = Array();
		
		$this->evalshows = Array("REX_EVAL_DAY","REX_EVAL_MONTH","REX_EVAL_ALLARTICLE","REX_EVAL_TOP10ARTICLE","REX_EVAL_WORST10ARTICLE","REX_EVAL_LAENDER","REX_EVAL_SUCHMASCHINEN","REX_EVAL_REFERER","REX_EVAL_BROWSER","REX_EVAL_OPERATINGSYSTEM","REX_EVAL_SEARCHWORDS");
		$this->evalsnipps = Array();
	}
		
	
  #private:
  	// get right newline char(s) for OS running
	function getnewline()
	{
		if ( strpos($_SERVER["SERVER_SOFTWARE"], "Unix" ) === FALSE )
			return "\n\r";
		else 
			return "\n";
	}
	
	// better gethostbyaddr implentation
	function gethost($ip) 
	{
		/*
		if ( strpos($_SERVER["SERVER_SOFTWARE"], "Unix" ) === FALSE ) 
		{
 			$host = `host $ip`;
 			return (($host ? end ( explode (' ', $host)) : $ip));
		} else 
		{
 			$host = split('Name:',`nslookup $ip`);
 			return ( trim (isset($host[1]) ? str_replace ("\n".'Address:  '.$ip, '', $host[1]) : $ip));
		}*/
		return gethostbyaddr($ip);
	}
	
	// hier wird jede zeile in einer log datei "abgearbeitet"	
	function computeLine($a, $aid)
	{	
	
		/*
			Wie optimiere ich dieses Ding ???
			GANZ EINFACH: man nehme einen string aus ip+useragent als key vom array
				so kann man ganz einfach gezielt auf den arraypunkt zugreifen und die stampzeit mit der
				geparsten zeit vergleichen und gegebenenfalls das ding adden oder neu erstellen...
		*/
	
	
		// bei leerem input gleich wieder raus
		if ( $a[1] == "" ) return false;	
	
		$this->debugpv++;
	
		// ( für alle vorhanden gleichen ips ) 
		// suche key von IP wenn vorhanden $this->MAIN[''] = ;
		$keys = array_keys($this->MAIN['ip'], $a[2]);
		
		foreach ( $keys as $key )
		{
			// für alle gleichen useragents
			if ( $a[4] == $this->MAIN['useragent'][$key] ) 
			{
				// check ob eintrag nicht mindestens einen tag alt ( 24h ) 86400 sekunden sind ein tag 
				$calc = $a[1] - $this->MAIN['stamp'][$key];
				if ( $calc < 86400 && $calc >= 0 )
				{
					// hier ist der visit als nicht unique anzusehen
					// also adden wir den artikel zu pageviews und returnen
					$this->MAIN['pageviews'][$key][] = $aid;
					$this->MAIN['referer'][$key][] = $a[5];
					return true;
				}
			}
		}
		
		// an diesem punkt kommt das script nur an wenn es einen unique ermittelt
		$this->MAIN['stamp'][$this->visitcount] = $a[1];
		$this->MAIN['ip'][$this->visitcount] = $a[2];
		$this->MAIN['useragent'][$this->visitcount] = $a[4];
		$this->MAIN['pageviews'][$this->visitcount] = Array($aid);
		$this->MAIN['hostname'][$this->visitcount] = $a[3];
		if ( Trim($a[5]) != "" ) $this->MAIN['referer'][$this->visitcount] = Array($a[5]);
		
		$this->visitcount++;
		
	}
	
	// hier werden die Artikelseiten generiert und returned
	function createArticle($from = 0, $to = -1)
	{
		//  rekursivly count article visits
		$artcounter = Array();
		foreach( $this->MAIN['pageviews'] as $upper )
			foreach ( $upper as $val ) 
				$artcounter[$val]++;		
		
		$all = 0;
		foreach ( $artcounter as $v ) 
			$all += $v;
		
		// handle it right
		if ( $to == -1 ) 
		{
			$to = 9999;			// dirty dirty
			
		} else
		{		
			asort($artcounter);
			if ( $to > 0 ) $artcounter = array_reverse($artcounter, TRUE);
			else $to = - $to;
			
		} 
		
		
		
		
		// fill table
		$out  = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
		$out .= "<tr><th>Artikelname</th><th>ArtikelID<th>PageViews</th><th>Anteil</th><th>&nbsp;</th></tr>"; 
		$i = 1;
		foreach ( $artcounter as $k => $v )
		{
			$name = new sql;
			$name->setQuery("SELECT * FROM rex_article WHERE id=$k");
			
			$out .= "<tr>
						<td class=grey align=right><a href=../index.php?article_id=$k target=_blank>".$name->getValue("name")."</a></td>	
						<td class=grey align=right><a href=../index.php?article_id=$k target=_blank>$k</td></td>
						<td class=grey align=right>$v</td>
						<td class=grey align=right>".round(($v/$all*100))."%</td>
						<td class=grey align=left><img src=pics/white.gif width=".(1+2*round(($v/$all*100)))." height=10></td>
					 </tr>";
			$i++;
			// break if top 10 or worst 10
			if ( $i >= $to ) break;
		}
		
		$out .= "</table>";	
			
		return $out;
	}
	
	// generiere tagesauswertung
	function CreateDay($month,$year)
	{
		$days = Array();
	
		$maxvisits = 0;
		$maxpageviews = 0;
	
		for  ( $i=0 ; $i < count($this->MAIN['stamp']) ; $i++ )
		{
			$days[date("d",$this->MAIN['stamp'][$i])][visits]++;
			if ($maxvisits<$days[date("d",$this->MAIN['stamp'][$i])][visits]) $maxvisits = $days[date("d",$this->MAIN['stamp'][$i])][visits];
			
			$days[date("d",$this->MAIN['stamp'][$i])][pageviews] += count( $this->MAIN['pageviews'][$i] );
			if ($maxpageviews<$days[date("d",$this->MAIN['stamp'][$i])][pageviews]) $maxpageviews = $days[date("d",$this->MAIN['stamp'][$i])][pageviews];
				
		}
		
		$daysinmonth = date("t", $this->MAIN['stamp'][0]);

		$out  = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
		$out .= "<tr><th>Datum</th><th>PageViews</th><th>&nbsp;</th><th>Visits</th><th>&nbsp;</th><th>PageViews per Visit</th></tr>"; 
		for ($i=0; $i<=$daysinmonth ; $i++ ) 	// f�r jeden tag
		{

			$day = $i+1;
			if ( $day < 10 ) $day = "0".$day;
			
			$daytime = mktime(0, 0, 0, $month, $day, $year);
			$date = date("D",$daytime)." ".$day.".".$month.".".$year;
			
			if ( $days[$day][visits] > 0 ) $pvpv = round(($days[$day][pageviews]/$days[$day][visits]));
			else $pvpv = 0;
			
			if ( $pvpv != 0 )
			{
				if (date("w",$daytime)==0 or date("w",$daytime)==6) $iclass = "dgrey";
				else $iclass = "grey";
				
				$pprozent = round(($days[$day][pageviews]/$maxpageviews*50));
				$vprozent = round(($days[$day][visits]/$maxvisits*50));
				
				$out .= "<tr>
						<td class=$iclass align=right>$date</td>
						<td class=$iclass align=right>".$days[$day][pageviews]."</td>
						<td class=$iclass align=left><img src=pics/white.gif width=".(1+$pprozent)." height=10></td>
						<td class=$iclass align=right>".$days[$day][visits]."</td>
						<td class=$iclass align=left><img src=pics/white.gif width=".(1+$vprozent)." height=10></td>
						<td class=$iclass align=right>$pvpv</td>
					</tr>";
			}
		 }
		 $out .= "</table>";
		
		 $this->evalsnipps[0] = $out;
	
	}
	
	
	
	// generiere alle browserrelevanten sachen
	// browser und betriebsystem
	function createBrowser()		// TODO: noch nicht getestet für safari, ie on mac, netscape und opera!?		
	{
		foreach ($this->MAIN['useragent'] as $v)
		{
			
			// hier wieder einmal IE und moz diffs augleichen
			
			if ( preg_match("/(.*?) \((.*)\) (.*)/", $v, $res) ) $jo = TRUE;
			else
				preg_match("/(.*?) \((.*)\)/", $v, $res);			
				
			if ( preg_match("/(.*);(.*);(.*);(.*);(.*)/", $res[2], $os ) ) $jo = TRUE;
			else 
				preg_match("/(.*);(.*);(.*)/", $res[2], $os );
			
			// dito IE und moz
			
			if ( substr(Trim($os[2]),0,4) == "MSIE" )		// eigentlich unnötig hier das zeug in arrays zu haun
			{
				$this->BROWSER['type'][base64_encode(Trim($os[2]))]++;
				$this->BROWSER['os'][base64_encode(Trim($os[3]))]++ ;
			} else
			{
				$this->BROWSER['type'][base64_encode(Trim($res[1]) . " " .Trim($res[3]))]++;
				$this->BROWSER['os'][base64_encode(Trim($os[3]))]++;			
			}			
		}
		
		asort($this->BROWSER['type']);
		asort($this->BROWSER['os']);
		
		$alltype = 0;
		foreach ( $this->BROWSER['type'] as $v )
			$alltype += $v;
		
		$allos = 0;
		foreach ( $this->BROWSER['os'] as $v )
			$allos += $v;
		
		
		$browserout  = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
		$browserout .= "<tr><th>Browser</th><th>Anzahl</th><th>Anteil</th><th>&nbsp;</th></tr>"; 
		
		$osout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
		$osout .= "<tr><th>Betriebsystem</th><th>Anzahl</th><th>Anteil</th><th>&nbsp;</th></tr>"; 
		
		
		
		foreach ( array_reverse($this->BROWSER['type']) as $k => $v ) 
			$browserout .= "<tr>
							<td class=grey align=right>".base64_decode($k)."</td>	
							<td class=grey align=right>$v</td>
							<td class=grey align=right>".round(($v/$alltype*100))."%</td>
							<td class=grey align=left><img src=pics/white.gif width=".(1+2*round(($v/$alltype*100)))." height=10></td>
						 </tr>";				
		
		foreach ( array_reverse($this->BROWSER['os']) as $k => $v ) 
			$osout .= "<tr>
							<td class=grey align=right>".base64_decode($k)."</td>	
							<td class=grey align=right>$v</td>
							<td class=grey align=right>".round(($v/$allos*100))."%</td>
							<td class=grey align=left><img src=pics/white.gif width=".(1+2*round(($v/$allos*100)))." height=10></td>
						 </tr>";		
		
		$browserout .= "</table>";
		$osout .= "</table>";
		
		$this->evalsnipps[8] = $browserout;
		$this->evalsnipps[9] = $osout;
		
		
	}
	
	// referer generiung
	// dabei mit einem wisch auch noch die suchbegriffe und suchmaschinen
	function createReferer()
	{
	
		foreach ( $this->MAIN['referer'] as $v ) 
			foreach ( $v as $var )
				$this->REFERER[base64_encode($var)]++;
		
		asort( $this->REFERER );
		
		$refout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
		$refout .= "<tr><th>Referer</th><th>Anzahl</th></tr>"; 
		
		// searchengine array: $ser = enginename and domainsearchpattern, 
		//					   $pat = pattern to determine searchwords
		
		$ser[0] = "google";
		$pat[0] = "/q=(.*?)\&/";
		$ser[1] = "yahoo";
		$pat[1] = "/p=(.*)\&/";
		$ser[2] = "lycos";
		$pat[2] = "/query=(.*?)\&/";		
		$ser[3] = "fireball";
		$pat[3] = "/q=(.*?)\&/";
		
		// generiere Refererliste und gleichzeitig check 
		foreach( array_reverse($this->REFERER) as $k => $v)
		{	
			for ( $i = 0; $i < count($ser) ; $i++ )
			{
				if ( preg_match("/".$ser[$i]."/",base64_decode($k) ) )
				{
					$this->SEARCH['engine'][base64_encode($ser[$i])] += $v;
					if ( preg_match($pat[$i],base64_decode($k),$res ) )
						$this->SEARCH['words'][base64_encode($res[1])] += $v;
				}
			}			
		
			if ( base64_decode($k) == "" ) $k = base64_encode("(direkt)");
			
			if ( base64_decode($k) != "(direkt)" ) $link = base64_decode($k);
			else $link = "#";
			
			if ( strlen(base64_decode($k)) > 60 ) $das = substr(base64_decode($k),0,60)."...";
			else $das = base64_decode($k);
			
			$refout .= "<tr>
							<td class=grey align=right><a href=$link target=_blank>$das</a></td>	
							<td class=grey align=right>$v</td>
						 </tr>";		
		}
		
		$refout .= "</table>";
		
		$searchout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
		$searchout .= "<tr><th>Suchmaschine</th><th>Anzahl</th></tr>"; 
		
		$wordsout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
		$wordsout .= "<tr><th>Suchwort</th><th>Anzahl</th></tr>"; 
		
		asort($this->SEARCH['engine']);
		asort($this->SEARCH['words']);
		
		// generiere searchout und wordsout
		
		foreach ( $this->SEARCH['engine'] as $k => $v )
			$searchout .= "<tr>
							<td class=grey align=right>".base64_decode($k)."</td>	
							<td class=grey align=right>$v</td>
						 </tr>";	
		
		foreach ( $this->SEARCH['words'] as $k => $v )
			$wordsout .= "<tr>
							<td class=grey align=right>".addslashes(urldecode(base64_decode($k)))."</td>	
							<td class=grey align=right>$v</td>
						 </tr>";	
						 
		$wordsout .= "</table>";
		$searchout .= "</table>";
		
		$this->evalsnipps[7] = $refout;
		$this->evalsnipps[6] = $searchout;
		$this->evalsnipps[10] = $wordsout;
	
	}
	
	// approximiere die länder anhand von den hostnames
	function createLaender()
	{
		foreach ( $this->MAIN['hostname'] as $v )
			 if ( !is_numeric(substr($v,strrpos($v,".")+1)))
			 	$myar[base64_encode(substr($v,strrpos($v,".")))]++;			// finde letzten "." im string und parse rest raus
		
		asort($myar);
		
		$landout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
		$landout .= "<tr><th>Land</th><th>Anzahl</th><th>Anteil</th><th>&nbsp;</th></tr>"; 
		
		
		$all = 0;
		foreach($myar as $v)
			$all += $v;
		
		foreach ( array_reverse($myar) as $k => $v )
			$landout .= "<tr>
							<td class=grey align=right>".base64_decode($k)."</td>	
							<td class=grey align=right>$v</td>
							<td class=grey align=right>".round(($v/$all*100))."%</td>
							<td class=grey align=left><img src=pics/white.gif width=".(1+2*round(($v/$all*100)))." height=10></td>
						 </tr>";	
		$landout .= "</table>";
	
		$this->evalsnipps[5] = $landout;
	
	
	}
	
	// generiere Monatssnippet und speicher den spaß
	function createMonth($year,$month)
	{
		$visits = count($this->MAIN['ip']);
		
		$pv = 0;
		foreach( $this->MAIN['pageviews'] as $v)
			$pv += count($v);			
	
		$pvpv = round($pv / $visits);
		
		$oldmonth = $month;
		
		$month = $month.".".$year;
		
		$line = "<? 
				\$maincontent .= \"<tr>
					<td class=grey align=right>$month</td>
					<td class=grey align=right>$pv</td>
					<td class=grey align=right>$visits</td>
					<td class=grey align=right>$pvpv</td>
				</tr>\"; 
				?>";
		
		$h = fopen($this->path.$year."_".$oldmonth."_mon.php","w+");
		fwrite($h, $line);
		fclose($h);
	
		#echo $this->path.$year."_".$oldmonth."_mon.php";
	
	}
	
	// hier werden die ganzen REX_EVAL_ Variable in das Template geschrieben	
	function writeFile($year,$month)
	{
		$h = fopen($this->outtemplate,"r");
		$temp = fread($h,filesize($this->outtemplate));
		fclose($h);
		
		for($i=0; $i<count($this->evalshows); $i++)
			if ( $this->evalsnipps[$i] != "" ) 
				$temp = str_replace($this->evalshows[$i],$this->evalsnipps[$i],$temp);
		
		$temp = str_replace("REX_EVAL_YEAR",$year,$temp);
		$temp = str_replace("REX_EVAL_DATE",$month." - ".$year,$temp);
		$temp = str_replace("REX_EVAL_LOGPATH",$this->path,$temp);
		
		$h = fopen($this->path.$year."_".$month.".php","w+");
		fwrite($h,$temp);
		fclose($h);	
	
	}
  #public:
	
    //
	// das ist die funktion zum erstellen eines neuen log eintrags
	function writeLog($aid)
	{
		global $_SERVER;
		
		$len = strlen( "http://".$_SERVER["SERVER_NAME"] );
		
		if ( substr($_SERVER['HTTP_REFERER'],0,$len) != "http://".$_SERVER["SERVER_NAME"] ) $ref = $_SERVER['HTTP_REFERER'];
		else $ref = "";
		
		#echo substr($_SERVER['HTTP_REFERER'],0,$len) . " -- http://".$_SERVER["SERVER_NAME"];
		
		$filename = "redaxo/". $this->path . date("Y\_m\_").$aid.".log";

		
		// unix timestamp , remote ip, hostname, user_agent, referrer
		$filestring = date("U") . "\t" .$_SERVER['REMOTE_ADDR'] . "\t" . $this->gethost($_SERVER['REMOTE_ADDR']) . "\t" . $_SERVER["HTTP_USER_AGENT"] . "\t" . $ref . $this->getnewline();
		
		$h = fopen($filename,"a"); 
		fwrite($h, $filestring);
		fclose($h);
				
	}
	
	//
	// auswertung hier ( die funktion wird gestartet und die auswertung zu starten )
	function evaluate($year, $month)
	{
		$time[start] = microtime();
		//
		// grab target files
		$tfiles = Array();
		if (is_dir($this->path)) 
		{
			if ($dh = opendir($this->path)) 
				while (($file = readdir($dh)) !== false)
				{
					if ( substr($file, 0, 7) == $year."_".$month ) 
						if ( strstr($file,".log") == ".log" ) 
							$tfiles[] = $file;
				}  	
			closedir($dh);
		} else 
			echo "error: ".$this->path." is no dir";
		
		$time[afterCollectFiles] = microtime();
		
		//
		// going through all files and write to Arrays
		foreach($tfiles as $val)
		{	
			$article_id = substr($val,8,strpos($val,".")-8);
		   
			if ( $h = fopen($this->path . $val,"r") )
			{
				while (!feof($h)) 
				{
					$buffer = fgets($h, 1024); // careck 07.06.04 to make it work with php4.1 !
					preg_match("/(.*?)\t(.*?)\t(.*?)\t(.*?)\t(.*?)\n/",$buffer,$res);
					// $res[1] == stamp // $res[2] == ip // $res[3] == hostname // $res[4] == useragent // $res[5] == referer
					$this->computeLine($res,$article_id);	
					
				}
				fclose($h);	
				
			} else
			{
				echo "error: cannot open ".$this->path."$val for evaluation";
			}
		}	// foreach end
		
		$time[parsedFiles] = microtime();
		
		$this->createDay($month,$year);
		
		$time[afterDays] = microtime();
		
		$this->evalsnipps[2] = $this->createArticle();
		$this->evalsnipps[3] = $this->createArticle(0,11);
		$this->evalsnipps[4] = $this->createArticle(0,-11);
		
		$time[afterArticles] = microtime();
		
		$this->createBrowser();
		
		$time[afterBrowser] = microtime();
		
		$this->createReferer();
		
		$time[afterReferer] = microtime();
		
		$this->createLaender();
		
		$time[afterLaender] = microtime();
		
		$this->createMonth($year,$month);
		
		$time[afterMonth] = microtime();
		
		$this->writeFile($year, $month);
		
		$time[afterWriteFile] = microtime();
		
		#echo "visitcount : ".$this->visitcount."<br>";
		#echo "ppv count : ".$this->debugpv."<br>";
		
		/* TIMEDEBUG HERE
		foreach ( $time as $k => $v )
			echo $k." - ".$v . "<br> ";
		*/
		return TRUE;
		
		
	}
	
	
}

?>