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

function getURLbyID($ArticleID,$Nameonly = false){
    	global $REX;
        if(!$ArticleID) return '';
        if($GLOBALS[REX][MOD_REWRITE]){
                @include("redaxo/include/generated/articles/$ArticleID.article");
                $name = $REX[ART][$ArticleID][name];
                $path = $REX[ART][$ArticleID][path];
                $tmp = explode("-",$path);
                foreach($tmp as $var){
                    if($var != ""){
                        @include("redaxo/include/generated/categories/$var.category");
                        if($REX[CAT][$var][name]!=$name){
                            $linkpath .= $REX[CAT][$var][name]."/";
                        }
                    }
                }
                $name = $linkpath.$name;
                if($Nameonly){
                    return str_replace('/',$Nameonly,$name);
                }
                $url = $ArticleID."-".ModRewriteName($name);
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
                $url = getURLbyID($matches[1][$m]);
                $content = str_replace($matches[0][$m],$url,$content);
            }
        }

        // -- preg match redaxo://[ARTICLEID] --
        preg_match_all("/redaxo:\/\/([0-9]*)\/?/im",$content,$matches);
        if($matches[0][0]!=''){
            for($m=0;$m<count($matches[0]);$m++){
                $url = getURLbyID($matches[1][$m]);
                $content = str_replace($matches[0][$m],$url,$content);
            }
        }

        return $content;
}

?>
