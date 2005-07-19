<?php

################################################################################
# vscope search function for redaxo
#
# function first tries to get article description
# then returns chars arround keyword + keyword itself
# results are positioned by count of occurrence
# max 3 words and 40 chars, min 2 chars
#
################################################################################

function REX_SEARCH($searchtxt,$surroundchars=20,$categories="",$surround_tag_start="<b>",$surround_tag_end="</b>"){

	global $REX;

    ###### CHECK WHICH PATHES SHOULD BE SEARCHED
    if(!is_array($categories)){
        $ADD_AREA.= "AND rex_article.path like '%-%'";
    } else {
    	$ADD_AREA = "AND (";
        foreach($categories as $var){
            $ADD_AREA.=" rex_article.path like '%-$var%' OR ";
        }
        $ADD_AREA = substr($ADD_AREA,0,-3).")";
    }

    ##### TRIM SEARCHTXT
    $searchtxt = trim($searchtxt," ");

    ##### CHECK IF SEARCH STRING IS LONG ENOUGH
    if (strlen($searchtxt)<40 AND strlen($searchtxt)>2){

    	##### EXPLODE SEARCH STRING
		$words = explode(" ",$searchtxt);
	    $words_count = 0;
	    if (count($words)>3){
			$words_count = 3;
			$RETURN[msg] = "Es wurden nur die ersten 3 Begriffe benutzt";
	    } else {
			$words_count = count($words);
	    }

        ##### START SQL CLASS
        $SUCHE = new sql;

        #### SEARCH FOR ALL KEYWORDS
        for($i=0;$i<$words_count;$i++){

	        $SUCHE->flush();

			$KEYWORD = current($words);

            #### SQL QUERY
	        $sql = "
	        SELECT

	        rex_article.id,rex_article.name,rex_article.description,

			rex_article_slice.value1,rex_article_slice.value2,rex_article_slice.value3,
			rex_article_slice.value4,rex_article_slice.value5,rex_article_slice.value6,
			rex_article_slice.value7,rex_article_slice.value8,rex_article_slice.value9,

	        (FIND_IN_SET('$KEYWORD',REPLACE(rex_article.name,' ',',')) * 10) +
	        (FIND_IN_SET('$KEYWORD',REPLACE(rex_article.description,' ',',')) * 5) +
	        (FIND_IN_SET('$KEYWORD',REPLACE(rex_article.keywords,' ',',')) * 5) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value1,' ',',')) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value2,' ',',')) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value3,' ',',')) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value4,' ',',')) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value5,' ',',')) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value6,' ',',')) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value7,' ',',')) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value8,' ',',')) +
	        FIND_IN_SET('$KEYWORD',REPLACE(rex_article_slice.value9,' ',','))
	        AS COUNTWORD

	        FROM rex_article_slice

	        LEFT JOIN rex_article ON rex_article.id=rex_article_slice.article_id

	        WHERE

	        (
	        rex_article.name LIKE ('%$KEYWORD%') OR
	        rex_article.description LIKE ('%$KEYWORD%') OR
	        rex_article.keywords LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value1 LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value2 LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value3 LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value4 LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value5 LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value6 LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value7 LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value8 LIKE ('%$KEYWORD%') OR
	        rex_article_slice.value9 LIKE ('%$KEYWORD%')
	        )

	        AND status = 1

			$ADD_AREA

	        GROUP BY id

	        ORDER BY COUNTWORD DESC

	        LIMIT 0,50

	        ";

	        $SUCHE->setQuery($sql);

			$count_limit = 0;

            ###### GO THROUGH RESULTS
	        for ($j=0;$j<$SUCHE->getRows();$j++){

	            $ART[$SUCHE->getValue("rex_article.id")][ID] = $SUCHE->getValue("rex_article.id");
	            $ART[$SUCHE->getValue("rex_article.id")][NAME] = $SUCHE->getValue("rex_article.name");
				$ART[$SUCHE->getValue("rex_article.id")][DESC] = $SUCHE->getValue("rex_article.description");
				$ART[$SUCHE->getValue("rex_article.id")][COUNTWORD] = $SUCHE->getValue("COUNTWORD");
				if ($REX['MOD_REWRITE']) $ART[$SUCHE->getValue("rex_article.id")][URL] = rex_getUrl($SUCHE->getValue("rex_article.id"));
				else $ART[$SUCHE->getValue("rex_article.id")][URL] = "index.php?article_id=".$SUCHE->getValue("rex_article.id")."&clang=".$REX["CUR_CLANG"];

                ###### CHECK OCURRENCE OF KEYWORD
                for($val=1;$val<10;$val++){
                    $regex = "/\b.{0,".$surroundchars."}".$KEYWORD.".{0,".$surroundchars."}\b/im";
                    preg_match_all($regex,strip_tags($SUCHE->getValue("rex_article_slice.value".$val)),$matches);
                    if($matches[0][0]!=''){
                        $ART_REGEX[$SUCHE->getValue("rex_article.id")].= "... ".implode($matches[0]," ... ");
                    }
                }

                $SUCHE->next();
			}

		$SEARCH_WORDS[]=$KEYWORD;

		next($words);

		}

        if(is_array($ART_REGEX)){
        	    $replace_string = implode("|",$SEARCH_WORDS);
				foreach($ART_REGEX as $key=>$var){
					$ART[$key][DESC_REGEX] = preg_replace("/(".$replace_string.")/im",$surround_tag_start."\\1".$surround_tag_end,$var)." ...";
				}
        }


    }

	return $ART;

}

?>