<?php

class Doc {

	var $Doc = 'editor';
	var $Lang = 'de_DE';
	var $DocPath = '../source/';
	var $CodeStart;
	var $CodeEnd;



	function loadIndex(){
		$filename = $this->DocPath.$this->Doc.'/'.$this->Lang.'/index.inc';
	    $handle = @fopen($filename, "r") or die("Error !! Index no found: Path ".$filename);
	    $conf = fread($handle, filesize($filename));
	    fclose($handle);
	    preg_match_all('/^\[([0-9]*)\|\|(.*)\]/iUsm',$conf,$matches);
	    for($c=0;$c<count($matches[0]);$c++){
	    	$return[$matches[1][$c]] = $matches[2][$c];
	    }
	   	return $return;
	}

	function loadChapterTitles($ChapterID){
		$filename = $this->DocPath.$this->Doc.'/'.$this->Lang.'/text/'.$ChapterID.'.inc';
	    $handle = @fopen($filename, "r") or die("Error !! Chapter not found: Path ".$filename);
	    $conf = @fread($handle, filesize($filename));
	    @fclose($handle);
	    preg_match_all('/^\[([0-9]*)\|\|(.*)\]/iUsm',$conf,$matches);
	    for($c=0;$c<count($matches[0]);$c++){
	    	$return[$matches[1][$c]] = $matches[2][$c];
	    }
	   	return $return;
	}

	function loadChapter($ChapterID,$parseContent='html'){

		$filename = $this->DocPath.$this->Doc.'/'.$this->Lang.'/text/'.$ChapterID.'.inc';
	    $handle = @fopen($filename, "r") or die("Error !! Chapter not found: Path ".$filename);

	    $c=0;
	    while (!@feof($handle)) {
		  $content = @fgets($handle, 4096);
	      if(preg_match("/\[([0-9]*)\|\|(.*)\]/",$content,$match)){
	      	$c++;
	      	$Chapter[$c][id]=$match[1];
		  	$Chapter[$c][title]=$match[2];
	      } else {
	      	if($c!=0){
	      		$Chapter[$c][text].=$content;
	      	}
	      }

	    }

	    fclose($handle);

	    if($parseContent=='html'){
				for($x=1;$x<=$c;$x++){
					$Chapter[$x][text] = $this->parseContentHTML($Chapter[$x][text]);
				}
	    }

	    if($parseContent=='pdf'){
				for($x=1;$x<=$c;$x++){
					$Chapter[$x][text] = $this->parseContentPDF($Chapter[$x][text]);
				}
	    }

	    return $Chapter;
	}

	function parseContentHTML($content){

        if(eregi("{ilink",$content)){
            preg_match_all("/{ilink (.*)}(.*){\/ilink}/Uism",$content,$match);
            for($c=0;$c<count($match[0]);$c++){
            	$url = explode("_",$match[1][$c]);
                $content = str_replace($match[0][$c],"<a href=main.php?doc=".$_GET[doc]."&lang=".$this->Lang."&chapter=".$url[0]."#".$url[1]."><u>".$match[2][$c]."</u></a>",$content);
            }
        }
        if(eregi("{link",$content)){
            preg_match_all("/{link (.*)}(.*){\/link}/Uism",$content,$match);
            for($c=0;$c<count($match[0]);$c++){
                $content = str_replace($match[0][$c],"<a href=\"".$match[1][$c]."\" target=_blank>".$match[2][$c]."</a>",$content);
            }
        }

		$content = preg_replace("/{img (.*)}/Ums",'<img src='.$this->DocPath.$this->Doc.'/'.$this->Lang.'/img/\\1 border=1>',$content);
	    preg_match_all('/{code}(.*){\/code}/iUs',$content,$matches);
        for($c=0;$c<count($matches[0]);$c++){
            $clear[$c] = $matches[1][$c];
            $clear[$c] = htmlentities($clear[$c]);
            $clear[$c] = str_replace('%5B','[',$clear[$c]);
            $clear[$c] = str_replace('%5D',']',$clear[$c]);
            $clear[$c] = str_replace('%7B','{',$clear[$c]);
            $clear[$c] = str_replace('%7D','}',$clear[$c]);
            $content   = str_replace($matches[0][$c],$this->CodeStart.$clear[$c].$this->CodeEnd,$content);
        }
        $content = nl2br($content);
		return $content;
	}

	function parseContentPDF($content){

		$content = explode("\n",$content);
		foreach($content as $var){
            $var = str_replace('%5B','[',$var);
            $var = str_replace('%5D',']',$var);
            $var = str_replace('%7B','{',$var);
            $var = str_replace('%7D','}',$var);
            $return[] = $var;
        }
		return $return;

	}

}

?>