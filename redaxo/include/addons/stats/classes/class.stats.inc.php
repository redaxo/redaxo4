<?php
class stats
{
  //
  // creating def arrays, sorted by visits  // TODO: how big can an array be? max index? 65536 ???
  var $MAIN;
  var $BROWSER;
  var $REFERER;
  var $SEARCH;
  var $visitcount = 0;
  var $path = "include/addons/stats/logs/";
  var $outtemplate = "include/addons/stats/classes/template.inc.php";
  var $evalshows;
  var $evalsnipps;
  var $debugpv = 0;
  var $ART;

  function stats()
  {
  	global $REX;

    $this->MAIN['stamp'] = Array ();
    $this->MAIN['ip'] = Array ();
    $this->MAIN['pageviews'] = Array (); // which pages in an array under this one
    $this->MAIN['useragent'] = Array ();
    $this->MAIN['hostname'] = Array ();
    $this->MAIN['referer'] = Array ();

    $this->BROWSER['type'] = Array ();
    $this->BROWSER['os'] = Array ();

    $this->REFERER = Array ();
    $this->SEARCH['engine'] = Array ();
    $this->SEARCH['words'] = Array ();

    $this->evalshows = array (
      "REX_EVAL_DAY",
      "REX_EVAL_MONTH",
      "REX_EVAL_ALLARTICLE",
      "REX_EVAL_TOP10ARTICLE",
      "REX_EVAL_WORST10ARTICLE",
      "REX_EVAL_LAENDER",
      "REX_EVAL_SUCHMASCHINEN",
      "REX_EVAL_REFERER",
      "REX_EVAL_BROWSER",
      "REX_EVAL_OPERATINGSYSTEM",
      "REX_EVAL_SEARCHWORDS"
    );
    $this->evalsnipps = array ();

    $statartikel = new sql;
    $statartikel->setQuery('SELECT id,name FROM '.$REX['TABLE_PREFIX'].'article');

    for ($i = 0; $i < $statartikel->getRows(); $i++)
    {
      $this->ART[$statartikel->getValue("id")] = $statartikel->getValue("name");
      $statartikel->next();
    }
  }

  #private:
  // get right newline char(s) for OS running
  function getnewline()
  {
    if (strpos($_SERVER["SERVER_SOFTWARE"], "Unix") === FALSE)
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
    if ($a[1] == "")
      return false;

    $this->debugpv++;

    // ( fuer alle vorhanden gleichen ips )
    // suche key von IP wenn vorhanden $this->MAIN[''] = ;
    $keys = array_keys($this->MAIN['ip'], $a[2]);

    foreach ($keys as $key)
    {
      // fuer alle gleichen useragents
      if ($a[4] == $this->MAIN['useragent'][$key])
      {
        // check ob eintrag nicht mindestens einen tag alt ( 24h ) 86400 sekunden sind ein tag
        $calc = $a[1] - $this->MAIN['stamp'][$key];
        if ($calc < 86400 && $calc >= 0)
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
    $this->MAIN['pageviews'][$this->visitcount] = Array (
      $aid
    );
    $this->MAIN['hostname'][$this->visitcount] = $a[3];
    if (Trim($a[5]) != "")
      $this->MAIN['referer'][$this->visitcount] = Array (
        $a[5]
      );

    $this->visitcount++;

  }

  // hier werden die Artikelseiten generiert und returned
  function createArticle($from = 0, $to = -1)
  {
    global $I18N_STATS;

    //  rekursivly count article visits
    $artcounter = array ();
    foreach ($this->MAIN['pageviews'] as $upper)
      foreach ($upper as $val)
        $artcounter[$val]++;

    $all = 0;
    foreach ($artcounter as $v)
      $all += $v;

    // handle it right
    if ($to == -1)
    {
      $to = 9999; // dirty dirty

    }
    else
    {
      asort($artcounter);
      if ($to > 0)
        $artcounter = array_reverse($artcounter, TRUE);
      else
        $to = - $to;

    }

    // fill table
    $out = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
    $out .= "<tr><th>".$I18N_STATS->msg("article_name")."</th><th>".$I18N_STATS->msg("article_id")."<th>".$I18N_STATS->msg("page_views")."</th><th>".$I18N_STATS->msg("share")."</th><th>&nbsp;</th></tr>";
    $i = 1;
    foreach ($artcounter as $k => $v)
    {
      if (!$this->ART[$k])
        $name = "[".$I18N_STATS->msg("stats_article_delete")."]";
      else
        $name = $this->ART[$k];
      $out .= "<tr>
      					<td class=grey align=right><a href=../index.php?article_id=$k target=_blank>".htmlspecialchars($name, ENT_QUOTES)."</a></td>
      					<td class=grey align=right><a href=../index.php?article_id=$k target=_blank>$k</td></td>
      					<td class=grey align=right>$v</td>
      					<td class=grey align=right>".round(($v / $all * 100))."%</td>
      					<td class=grey align=left><img src=pics/white.gif width=". (1 + 2 * round(($v / $all * 100)))." height=10></td>
      				 </tr>";
      $i++;
      // break if top 10 or worst 10
      if ($i >= $to)
        break;
    }

    $out .= "</table>";

    return $out;
  }

  // generiere tagesauswertung
  function CreateDay($month, $year)
  {
    global $I18N_STATS;

    $days = Array ();

    $maxvisits = 0;
    $maxpageviews = 0;

    for ($i = 0; $i < count($this->MAIN['stamp']); $i++)
    {
      $days[date("d", $this->MAIN['stamp'][$i])]['visits']++;
      if ($maxvisits < $days[date("d", $this->MAIN['stamp'][$i])]['visits'])
        $maxvisits = $days[date("d", $this->MAIN['stamp'][$i])]['visits'];

      $days[date("d", $this->MAIN['stamp'][$i])]['pageviews'] += count($this->MAIN['pageviews'][$i]);
      if ($maxpageviews < $days[date("d", $this->MAIN['stamp'][$i])]['pageviews'])
        $maxpageviews = $days[date("d", $this->MAIN['stamp'][$i])]['pageviews'];

    }

    $daysinmonth = date("t", $this->MAIN['stamp'][0]);

    $out = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
    $out .= "<tr><th>".$I18N_STATS->msg("date")."</th><th>".$I18N_STATS->msg("page_views")."</th><th>&nbsp;</th><th>".$I18N_STATS->msg("visits")."</th><th>&nbsp;</th><th>".$I18N_STATS->msg("pageviews_per_visit")."</th></tr>";
    for ($i = 0; $i <= $daysinmonth; $i++) // f�r jeden tag
    {

      $day = $i +1;
      if ($day < 10)
        $day = "0".$day;

      $daytime = mktime(0, 0, 0, $month, $day, $year);
      $date = date("D", $daytime)." ".$day.".".$month.".".$year;

      if ($days[$day]['visits'] > 0)
        $pvpv = round(($days[$day]['pageviews'] / $days[$day]['visits']));
      else
        $pvpv = 0;

      if ($pvpv != 0)
      {
        if (date("w", $daytime) == 0 or date("w", $daytime) == 6)
          $iclass = "dgrey";
        else
          $iclass = "grey";

        $pprozent = round(($days[$day]['pageviews'] / $maxpageviews * 50));
        $vprozent = round(($days[$day]['visits'] / $maxvisits * 50));

        $out .= "<tr>
        						<td class=$iclass align=right>$date</td>
        						<td class=$iclass align=right>".$days[$day]['pageviews']."</td>
        						<td class=$iclass align=left><img src=pics/white.gif width=". (1 + $pprozent)." height=10></td>
        						<td class=$iclass align=right>".$days[$day]['visits']."</td>
        						<td class=$iclass align=left><img src=pics/white.gif width=". (1 + $vprozent)." height=10></td>
        						<td class=$iclass align=right>$pvpv</td>
        					</tr>";
      }
    }
    $out .= "</table>";

    $this->evalsnipps[0] = $out;

  }

  // generiere alle browserrelevanten sachen
  // browser und betriebsystem
  function createBrowser() // TODO: noch nicht getestet für safari, ie on mac, netscape und opera!?
  {
    foreach ($this->MAIN['useragent'] as $v)
    {

      // hier wieder einmal IE und moz diffs augleichen

        if (preg_match("/(.*?) \((.*)\) (.*)/", $v, $res = array ()))
        $jo = TRUE;
      else
        preg_match("/(.*?) \((.*)\)/", $v, $res);

        if (preg_match("/(.*);(.*);(.*);(.*);(.*)/", $res[2], $os = array ()))
        $jo = TRUE;
      else
        preg_match("/(.*);(.*);(.*)/", $res[2], $os);

      // dito IE und moz

      if (substr(Trim($os[2]), 0, 4) == "MSIE") // eigentlich unnötig hier das zeug in arrays zu haun
      {
        $this->BROWSER['type'][base64_encode(Trim($os[2]))]++;
        $this->BROWSER['os'][base64_encode(Trim($os[3]))]++;
      }
      else
      {
        $this->BROWSER['type'][base64_encode(Trim($res[1])." ".Trim($res[3]))]++;
        $this->BROWSER['os'][base64_encode(Trim($os[3]))]++;
      }
    }

    asort($this->BROWSER['type']);
    asort($this->BROWSER['os']);

    $alltype = 0;
    foreach ($this->BROWSER['type'] as $v)
      $alltype += $v;

    $allos = 0;
    foreach ($this->BROWSER['os'] as $v)
      $allos += $v;

    $browserout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
    $browserout .= "<tr><th>Browser</th><th>Anzahl</th><th>Anteil</th><th>&nbsp;</th></tr>";

    $osout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
    $osout .= "<tr><th>Betriebsystem</th><th>Anzahl</th><th>Anteil</th><th>&nbsp;</th></tr>";

    foreach (array_reverse($this->BROWSER['type']) as $k => $v)
    {
      $k = base64_decode($k);
      $k = str_replace('"', '', $k);
      $browserout .= "<tr>
      						<td class=grey align=right>".$k."</td>
      						<td class=grey align=right>$v</td>
      						<td class=grey align=right>".round(($v / $alltype * 100))."%</td>
      						<td class=grey align=left><img src=pics/white.gif width=". (1 + 2 * round(($v / $alltype * 100)))." height=10></td>
      					 </tr>";
    }

    foreach (array_reverse($this->BROWSER['os']) as $k => $v)
    {
      $k = base64_decode($k);
      $k = str_replace('"', '', $k);
      $osout .= "<tr>
      						<td class=grey align=right>".$k."</td>
      						<td class=grey align=right>$v</td>
      						<td class=grey align=right>".round(($v / $allos * 100))."%</td>
      						<td class=grey align=left><img src=pics/white.gif width=". (1 + 2 * round(($v / $allos * 100)))." height=10></td>
      					 </tr>";
    }

    $browserout .= "</table>";
    $osout .= "</table>";

    $this->evalsnipps[8] = $browserout;
    $this->evalsnipps[9] = $osout;

  }

  // referer generiung
  // dabei mit einem wisch auch noch die suchbegriffe und suchmaschinen
  function createReferer()
  {

    foreach ($this->MAIN['referer'] as $v)
    {
      foreach ($v as $var)
      {
        if ($var != "")
        {
          preg_match("/^(http?:\/\/)?([^\/]+)/i", $var, $treffer = array ());
          $host = $treffer[2];
          // die letzten beiden Segmente aus Hostnamen holen
          // preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $treffer);
          // echo "<br>$var | ".$treffer[0];
          $this->REF[$treffer[0]][$var]++;
          $var = $treffer[0];
        }

        $this->REFERER[base64_encode($var)]++;
      }
    }

    asort($this->REFERER);

    $refout = "<table class=rex border=0 cellpadding=5 cellspacing=1 width=100% style='width:100%;'>";
    $refout .= "<tr><th class=icon>&nbsp;</th><th>Referer</th><th width=100>Anzahl</th></tr>";
    $refout .= "</table>";

    $refout .= "<script language=Javascript>
    		<!--
    		
    		function showRest(id)
    		{
    			obj = document.getElementById(id).style;
    			
    			if (obj.display == 'none')
    			{
    				obj.display = '';
    			}else
    			{
    				obj.display = 'none';
    			}
    			
    		}
    		
    		-->
    		</script>
    		";

    // searchengine array: $ser = enginename and domainsearchpattern,
    //					   $pat = pattern to determine searchwords

    $ser = array ();
    $pat = array ();

    $ser[] = "google";
    $pat[] = "/q=(.*?)\&/";
    $ser[] = "yahoo";
    $pat[] = "/p=(.*)\&/";
    $ser[] = "lycos";
    $pat[] = "/query=(.*?)\&/";
    $ser[] = "fireball";
    $pat[] = "/q=(.*?)\&/";
    $ser[] = "msn";
    $pat[] = "/q=(.*?)\&/";
    $ser[] = "aol";
    $pat[] = "/q=(.*?)\&/";
    $ser[] = "alltheweb";
    $pat[] = "/q=(.*?)\&/";
    $ser[] = "arcor";
    $pat[] = "/Keywords=(.*?)\&/";

    $id_k = 0;

    // generiere Refererliste und gleichzeitig check
    foreach (array_reverse($this->REFERER) as $k => $v)
    {
      $id_k++; // javascript layer id
      if (base64_decode($k) == "")
        $k = base64_encode("(direkt)");
      if (base64_decode($k) != "(direkt)")
        $link = base64_decode($k);
      else
        $link = "#";

      if (strlen(base64_decode($k)) > 60)
        $das = substr(base64_decode($k), 0, 60)." ...";
      else
        $das = base64_decode($k);

      $refout .= "<a name=astats$id_k></a>";
      $refout .= "<table class=rex border=0 cellpadding=5 cellspacing=1 width=100% style='width:100%;'>";
      // <th class=icon><a href=#astats$id_k onclick=showRest('stats$id_k');><img src=pics/folder.gif width=16 height=16 border=0></a></th>
      $refout .= "<tr>
      							<th class=icon><a href=javascript:showRest('stats$id_k');><img src=pics/folder.gif width=16 height=16 border=0></a></th>
      							<th align=left><a href=$link target=_blank>$das</a></th>
      							<th align=right width=100>$v</th>
      						 </tr>";
      $refout .= "</table>";

      $refout .= "<table class=rex border=0 cellpadding=5 cellspacing=1 width=100% id=stats$id_k style='width:100%;display:none'>";
      if ($das != "(direkt)")
      {
        foreach ($this->REF[$das] as $o => $p)
        {
          if (strlen($o) > 60)
            $q = substr($o, 0, 60)." ...";
          else
            $q = $o;
            
          $q = htmlspecialchars($q);
          $o = htmlspecialchars($o);

          $refout .= "<tr><td class=icon>&nbsp;</td><td class=lgrey><a href=$o target=_blank>$q</a></td><td width=100 align=right>$p</td></tr>";

          for ($i = 0; $i < count($ser); $i++)
          {
            if (preg_match("/".$ser[$i]."/", ($o)))
            {
              $this->SEARCH['engine'][base64_encode($ser[$i])] += $p;
                if (preg_match($pat[$i], ($o), $res = array ()))
                $this->SEARCH['words'][base64_encode($res[1])] += $p;
            }
          }

        }
      }
      $refout .= "</table>";
    }

    $searchout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
    $searchout .= "<tr><th>Suchmaschine</th><th>Anzahl</th></tr>";

    $wordsout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
    $wordsout .= "<tr><th>Suchwort</th><th>Anzahl</th></tr>";

    arsort($this->SEARCH['engine']);
    arsort($this->SEARCH['words']);

    // generiere searchout und wordsout

    foreach ($this->SEARCH['engine'] as $k => $v)
      $searchout .= "<tr>
      							<td class=grey align=right>".base64_decode($k)."</td>
      							<td class=grey align=right>$v</td>
      						 </tr>";

    foreach ($this->SEARCH['words'] as $k => $v)
      $wordsout .= '<tr>
      							<td class=grey align=right>'.htmlspecialchars(urldecode(base64_decode($k)), ENT_QUOTES).'</td>
      							<td class=grey align=right>'.$v.'</td>
      						 </tr>';

    $wordsout .= "</table>";
    $searchout .= "</table>";

    $this->evalsnipps[7] = $refout;
    $this->evalsnipps[6] = $searchout;
    $this->evalsnipps[10] = $wordsout;

  }

  // approximiere die länder anhand von den hostnames
  function createLaender()
  {
    $myar = array ();
    foreach ($this->MAIN['hostname'] as $v)
    {
      if (!is_numeric(substr($v, strrpos($v, ".") + 1)))
      {
        $myar[base64_encode(substr($v, strrpos($v, ".")))]++; // finde letzten "." im string und parse rest raus
      }
    }

    asort($myar);

    $landout = "<table border=0 cellpadding=5 cellspacing=1 width=100%>";
    $landout .= "<tr><th>Land</th><th>Anzahl</th><th>Anteil</th><th>&nbsp;</th></tr>";

    $all = 0;
    foreach ($myar as $v)
      $all += $v;

    foreach (array_reverse($myar) as $k => $v)
      $landout .= "<tr>
      						<td class=grey align=right>".base64_decode($k)."</td>
      						<td class=grey align=right>$v</td>
      						<td class=grey align=right>".round(($v / $all * 100))."%</td>
      						<td class=grey align=left><img src=pics/white.gif width=". (1 + 2 * round(($v / $all * 100)))." height=10></td>
      					 </tr>";
    $landout .= "</table>";

    $this->evalsnipps[5] = $landout;

  }

  // generiere Monatssnippet und speicher den spaß
  function createMonth($year, $month)
  {
    $visits = count($this->MAIN['ip']);

    // pageviews
    $pv = 0;
    // pageviews per visit
    $pvpv = 0;
    foreach ($this->MAIN['pageviews'] as $v)
    {
      $pv += count($v);
    }
    
    if($visits != 0)
    {
      $pvpv = round($pv / $visits);
    }

    $oldmonth = $month;

    $month = $month.".".$year;

    $line = "<?php
    				\$maincontent .= \"<tr>
    					<td class=grey align=right>$month</td>
    					<td class=grey align=right>$pv</td>
    					<td class=grey align=right>$visits</td>
    					<td class=grey align=right>$pvpv</td>
    				</tr>\";
    				?>";

    $h = fopen($this->path.$year."_".$oldmonth."_mon.php", "w+");
    fwrite($h, $line);
    fclose($h);

    #echo $this->path.$year."_".$oldmonth."_mon.php";

  }

  // hier werden die ganzen REX_EVAL_ Variable in das Template geschrieben
  function writeFile($year, $month)
  {
    $h = fopen($this->outtemplate, "r");
    $temp = fread($h, filesize($this->outtemplate));
    fclose($h);

    for ($i = 0; $i < count($this->evalshows); $i++)
    {

      if ($this->evalsnipps[$i] != "")
        $temp = str_replace($this->evalshows[$i], $this->evalsnipps[$i], $temp);
    }

    $temp = str_replace("REX_EVAL_YEAR", $year, $temp);
    $temp = str_replace("REX_EVAL_DATE", $month." - ".$year, $temp);
    $temp = str_replace("REX_EVAL_LOGPATH", $this->path, $temp);

    $h = fopen($this->path.$year."_".$month.".php", "w+");
    fwrite($h, $temp);
    fclose($h);

  }
  #public:

  //
  // das ist die funktion zum erstellen eines neuen log eintrags
  function writeLog($aid)
  {
    global $_SERVER;

    $len = strlen("http://".$_SERVER["SERVER_NAME"]);

    if (substr($_SERVER['HTTP_REFERER'], 0, $len) != "http://".$_SERVER["SERVER_NAME"])
      $ref = $_SERVER['HTTP_REFERER'];
    else
      $ref = "";

    #echo substr($_SERVER['HTTP_REFERER'],0,$len) . " -- http://".$_SERVER["SERVER_NAME"];

    $filename = "redaxo/".$this->path.date("Y\_m\_").$aid.".log";

    // unix timestamp , remote ip, hostname, user_agent, referrer
    $filestring = date("U")."\t".$_SERVER['REMOTE_ADDR']."\t".$this->gethost($_SERVER['REMOTE_ADDR'])."\t".$_SERVER["HTTP_USER_AGENT"]."\t".$ref.$this->getnewline();

    if ($h = @ fopen($filename, "a"))
    {
      fwrite($h, $filestring);
      fclose($h);
    }

  }

  //
  // auswertung hier ( die funktion wird gestartet und die auswertung zu starten )
  function evaluate($year, $month)
  {
    $time['start'] = microtime();
    //
    // grab target files
    $tfiles = Array ();
    if (is_dir($this->path))
    {
      if ($dh = opendir($this->path))
        while (($file = readdir($dh)) !== false)
        {
          if (substr($file, 0, 7) == $year."_".$month)
            if (strstr($file, ".log") == ".log")
              $tfiles[] = $file;
        }
      closedir($dh);
    }
    else
      echo "error: ".$this->path." is no dir";

    $time['afterCollectFiles'] = microtime();

    // going through all files and write to Arrays
    foreach ($tfiles as $val)
    {
      $article_id = substr($val, 8, strpos($val, ".") - 8);

      if ($h = fopen($this->path.$val, "r"))
      {
        while (!feof($h))
        {
          $buffer = fgets($h, 1024); // careck 07.06.04 to make it work with php4.1 !
          preg_match("/(.*?)\t(.*?)\t(.*?)\t(.*?)\t(.*?)\n/", $buffer, $res = array ());
          // $res[1] == stamp // $res[2] == ip // $res[3] == hostname // $res[4] == useragent // $res[5] == referer
          $this->computeLine($res, $article_id);

        }
        fclose($h);

      }
      else
      {
        echo "error: cannot open ".$this->path."$val for evaluation";
      }
    } // foreach end

    $time['parsedFiles'] = microtime();

    $this->createDay($month, $year);

    $time['afterDays'] = microtime();

    $this->evalsnipps[2] = $this->createArticle();
    $this->evalsnipps[3] = $this->createArticle(0, 11);
    $this->evalsnipps[4] = $this->createArticle(0, -11);

    $time['afterArticles'] = microtime();

    $this->createBrowser();

    $time['afterBrowser'] = microtime();

    $this->createReferer();

    $time['afterReferer'] = microtime();

    $this->createLaender();

    $time['afterLaender'] = microtime();

    $this->createMonth($year, $month);

    $time['afterMonth'] = microtime();

    $this->writeFile($year, $month);

    $time['afterWriteFile'] = microtime();

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