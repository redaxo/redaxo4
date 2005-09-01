<?php

// ----------------------------------------- Redaxo 2.* Function
function getUrlByid($id,$clang = "",$params = ""){
	return rex_getUrl($id,$clang,$params);
}

// ----------------------------------------- Parse Article Name for Url
function rex_parseArticleName($name){
    $name = strtolower($name);
    $name = str_replace(' ','-',$name);
    $name = str_replace('ä','ae',$name);
    $name = str_replace('ö','oe',$name);
    $name = str_replace('ü','ue',$name);
    $name = preg_replace("/[^a-zA-Z\-]/","",$name);
    return $name;
}

// ----------------------------------------- URL

function rex_getUrl($id,$clang = "",$params = "") {

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

	// ----- definiere sprache
	if ($clang == "") $clang = $REX[CUR_CLANG];

	// ----- get article Name
	$id = $id+0;
	if ($id==0)
	{
		$name = "NoName";
	}else {
		$ooa = OOArticle::getArticleById($id);
		if ($ooa) $name = rex_parseArticleName($ooa->getName());
	}

	// ----- get params
	$param_string = "";
    if ( is_array( $params))
    {
        $first = true;
        foreach ( $params as $key => $value)
        {
            // Nur Wenn MOD_REWRITE aktiv ist, das erste "&amp;" entfernen.
            if ( $first && $REX['MOD_REWRITE'])
            {
                $first = false;
            }
            else
            {
                $param_string .= '&amp;';
            }
            $param_string .= $key . '=' . $value;
        }
    }else if ( $params != "")
    {
        $param_string = str_replace( '&', '&amp;', $params);
    }

	if ($REX['MOD_REWRITE'] && $param_string != "") $param_string = "?".$param_string;

	// ----- create url
	$url = $REX['MOD_REWRITE'] ? "$id-$clang-$name.html"  : "index.php?article_id=$id&amp;clang=$clang";

	return $REX['WWW_PATH']."$url"."$param_string";
}

?>