<?php

$mypage = "glossar"; 				// only for this file

$I18N_GLOSSAR = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/addons/$mypage/lang/"); 	// CREATE LANG OBJ FOR THIS ADDON

$REX['ADDON']['page'][$mypage] = "$mypage";
$REX['ADDON']['name'][$mypage] = "Glossar";
$REX['ADDON']['perm'][$mypage] = "glossar[]";

$REX['PERM'][] = "glossar[]";

// Glossar Ersetzungsfunktion für das Frontend
if($REX[REDAXO] === false){
    
    function glossar_replace( $string) {
        global $REX, $mypage;
        $I18N_GLOSSAR = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/addons/$mypage/lang/");    // CREATE LANG OBJ FOR THIS ADDON
    
        $sql = new sql;
        $sql->setQuery("select * from rex__glossar order by shortcut");
    
        for($i=0;$i<$sql->getRows();$i++)
        {
            $language = $sql->getValue("language");
            if ( $language == "0") {
                $language = $I18N_GLOSSAR->msg('lang_de_short');
            } elseif ( $language == "1") {
                $language = $I18N_GLOSSAR->msg('lang_en_short');
            } else {
                $language = $I18N_GLOSSAR->msg('lang_fr_short');
            }
    
            $id = $sql->getValue("short_id");
            $shortcut = htmlentities($sql->getValue("shortcut"));
            $escapedshortcut = str_replace( '.', '\.', $shortcut);
            $description = htmlentities($sql->getValue("description"));
            $language = trim( $language);
    
            $casesense = $sql->getValue("casesense");
    
            $search = "/((<[^>]*)|$escapedshortcut)/e";
            $replace = '"\2"=="\1"? "\1":"<span lang=\"'. $language .'\" xml:lang=\"'. $language .'\" title=\"'. $language .': '. $description .'\" class=\"shortcut\">'. $shortcut .'</span>"';
            $subject = $string;
    
            if ( $casesense == 0) {
                $search .= 'i';
            }
    
            $string = preg_replace( $search, $replace, $subject);
    
            $sql->counter++;
        }
    
        return $string;
    }
    
}


?>