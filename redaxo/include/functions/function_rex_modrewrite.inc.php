<?php

##########################################################
# Generate Mod Rewrite Name for Article
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
    $url = urlencode($url);
    return $url;
}

?>