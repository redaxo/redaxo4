<?

// --------------------------- globals

unset($REX);

// Übernahme von FormularImagePosition

$REX[x] = $x;
$REX[y] = $y;

// Flag ob Inhalte mit Redaxo aufgerufen oder
// von der Webseite aus
// Kann wichtig für die Darstellung sein
// Sollte immer false bleiben

$REX[REDAXO] = false;

// B CONTENT Seiten wenn $REX[BC] = true;
// Weiterhin um diese Seiten zu erstellen muss
// in der redaxo/include/master.inc.php die
// $REX[BCONTENT] = true; gesetzt werden.
//

$REX[BC] = false;

// Wenn $REX[GG] = true; dann wird der
// Content aus den redaxo/include/generated/
// genommen

$REX[GG] = true;

// setzte pfad und includiere klassen und funktionen

$REX[HTDOCS_PATH] = "./";
include "./redaxo/include/master.inc.php";

// Online / Offline Check
if($REX[ONOFFCHECK]!=false){
	include_once "./redaxo/include/classes/class.sql.inc.php";
	include_once "./redaxo/include/functions/function_onoff.inc.php";
	CHECKONOFFSTATUS();
}

// Starte einen neuen Artikel und setzte die aktuelle
// artikel id. wenn nicht vorhanden, nimm einen
// speziellen artikel. z.b. fehler seite oder home seite

if ($category_id != "")
{
	// categoryarticle holen

	$category_id = str_replace("\\","",$category_id);
	$category_id = str_replace("\/","",$category_id);

	if (@include $REX[INCLUDE_PATH]."/generated/categories/$category_id.category")
	{
		$article_id = $REX[CAT][$category_id][article_id];
	}
}

if ($article_id == "") $article_id = $REX[STARTARTIKEL_ID];

$SHOWARTICLE = true;

// If Caching is true start engine
if($REX[CACHING]){


	//////////////////////////////////////////////
	// advanced caching
	//////////////////////////////////////////////

	$Cache = new Cache($article_id);
	if($Cache->isCacheConf())
	{
		if($Cache->isCacheFile())
		{
			$Cache->printCacheFile();
			if($REX[CACHING_DEBUG]) print "<br>CachedVersion<br>";
			if($REX[CACHING_DEBUG]) print "Script time: ".showScripttime();
			$SHOWARTICLE = false;
		} else {
			// start caching
			$Cache->startCacheFile();
		}
	}

	//////////////////////////////////////////////

}

if ($SHOWARTICLE)
{
	$REX_ARTICLE = new article;
	if ($REX_ARTICLE->setArticleId($article_id))
	{
		$REX_ARTICLE->getArticleTemplate();
	}elseif($REX_ARTICLE->setArticleId($REX[STARTARTIKEL_ID]))
	{
		$REX_ARTICLE->getArticleTemplate();
	}else
	{
		echo "Kein Startartikel selektiert";
		#$REX[STATS] = 0;
	}
	//////////////////////////////////////////////
	// advanced caching
	//////////////////////////////////////////////

	if($Cache->makeCacheFile){
		$Cache->writeCacheFile();
		if($REX[CACHING_DEBUG]) print "<br>MadeCache<br>";
	} else {
		if($REX[CACHING_DEBUG]) print "<br>Live<br>";
	}

	if($REX[CACHING_DEBUG]) print "Script time: ".showScripttime();

	//////////////////////////////////////////////
}

// ------------------------------------------------------------ STATISTIK

if ($REX[STATS]==1)
{
	$log = new stat;
	$log->writeLog(($article_id+0));
}


?>
