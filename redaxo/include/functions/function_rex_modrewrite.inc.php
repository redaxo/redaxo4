<?php

##########################################################
# Generate Mod Rewrite Name for Article
# Get Url for an Article ID
# Replace redaxo:// REX_LINK_INTERN[] in content
##########################################################

function ModRewriteName($article_name) {
    $url = str_replace(" ","_",$article_name);
    $url = str_replace("ä","ae",$url);
    $url = str_replace("ö","oe",$url);
    $url = str_replace("ü","ue",$url);
    $url = str_replace("Ä","Ae",$url);
    $url = str_replace("Ö","Oe",$url);
    $url = str_replace("Ü","Ue",$url);
    $url = str_replace("/","-",$url);
    $url = str_replace("&","-",$url);
    $url = urlencode($url);
    return $url;
}

function getURLbyID($ArticleID){
	global $REX;
	if($REX[MOD_REWRITE]){
		$db = new sql;
		$sql = "SELECT name FROM rex_article WHERE id='$ArticleID'";
		$res = $db->get_array($sql);
		$url = $ArticleID."-".ModRewriteName($res[0][name]);
	} else {
		$url = '?article_id='.$ArticleID;
	}
	return $url;
}

function replaceLinks($content){

        // -- preg match REX_LINK_INTERN[ARTICLEID] --
        preg_match_all("/REX_LINK_INTERN\[([0-9]*)\]/im",$content,$matches);
        if($matches[0][0]!=''){
            for($m=0;$m<count($matches[0]);$m++){
                $url = getURLbyID($matches[1][$m]);
                $content = str_replace($matches[0][$m],$url,$content);
            }
        }

        // -- preg match redaxo://[ARTICLEID] --
        preg_match_all("/redaxo:\/\/([0-9]*)/im",$content,$matches);
        if($matches[0][0]!=''){
            for($m=0;$m<count($matches[0]);$m++){
                $url = getURLbyID($matches[1][$m]);
                $content = str_replace($matches[0][$m],$url,$content);
            }
        }

        return $content;
}

?>