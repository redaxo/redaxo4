<?php

##########################################################
# Generate Mod Rewrite Name for Article
# Get Url for an Article ID
# Replace redaxo:// REX_LINK_INTERN[] in content
##########################################################

function ModRewriteName($article_name) {
    $url = str_replace(" ","-",$article_name);
    $url = str_replace("_","-",$url);
    $url = str_replace("ä","ae",$url);
    $url = str_replace("ö","oe",$url);
    $url = str_replace("ü","ue",$url);
    $url = str_replace("Ä","Ae",$url);
    $url = str_replace("Ö","Oe",$url);
    $url = str_replace("Ü","Ue",$url);
    $url = str_replace("/","-",$url);
    $url = str_replace("&","-",$url);
    $url = urlencode($url).".html";
    return $url;
}

function getURLbyID($ArticleID){
	if(!$ArticleID) return '';
	global $REX;
	if($REX[MOD_REWRITE]){
		$db = new sql;
		$sql = "SELECT name FROM rex_article WHERE id='$ArticleID'";
		$res = $db->get_array($sql);
		$url = $ArticleID."-".ModRewriteName($res[0][name]);
	} else {
		$url = 'index.php?article_id='.$ArticleID;
	}
	return $url;
}

function replaceLinks($content){

        // -- preg match REX_LINK_INTERN[ARTICLEID] --
        preg_match_all("/REX_LINK_INTERN\[([0-9]*)\]/im",$content,$matches);
        if($matches[0][0]!=''){
            for($m=0;$m<count($matches[0]);$m++){
                $url = rex_getURL($matches[1][$m]);
                $content = str_replace($matches[0][$m],$url,$content);
            }
        }

        // -- preg match redaxo://[ARTICLEID] --
        preg_match_all("/redaxo:\/\/([0-9]*)\/?/im",$content,$matches);
        if($matches[0][0]!=''){
            for($m=0;$m<count($matches[0]);$m++){
                $url = rex_getURL($matches[1][$m]);
                $content = str_replace($matches[0][$m],$url,$content);
            }
        }

        return $content;
}



// ----------------------------------------- URL

function rex_getUrl($id,$clang = "",$params = null) {
	
	/*
	 * Object Helper Function:
	 * Returns a url for linking to this article
	 * This url respects the setting for mod_rewrite
	 * support!
	 *
	 * If you pass an associative array for $params,
	 * then these parameters will be attached to the URL.
	 * e.g.:
	 *   $param = array("order" => "123", "name" => "horst");
	 *   $article->getUrl($param);
	 * will return:
	 *   index.php?article_id=1&order=123&name=horst
	 * or if mod_rewrite support is activated:
	 *   /1-The_Article_Name?order=123&name=horst
	 */
	 
	global $REX;
	
	if ($clang == "") $clang = $REX[CUR_CLANG];
	
	$param_string = "";
	if ($params && sizeof($params) > 0) {
		$param_string = $REX['MOD_REWRITE'] ? "?" : "&amp;";
		foreach ($params as $key => $val) {
			$param_string .= "{$key}={$val}&amp;";
		}
	}
	$param_string = substr($param_string,0,strlen($param_string)-5); // cut off the last '&'
	$url = $REX['MOD_REWRITE'] ? "/$id-$clang-{$mr_name}"
	                           : "index.php?article_id=$id&clang=$clang";
	return $REX['WWW_PATH']."{$url}{$param_string}";
}



?>