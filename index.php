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

// Barrierefreie Seite wenn $REX[BF] = true;
// Weiterhin um diese Seiten zu erstellen muss
// in der redaxo/include/master.inc.php die
// $REX[BARRIEREFREI] = true; gesetzt werden.
// 

$REX[BF] = false;

// Wenn $REX[GG] = true; dann wird der
// Content aus den redaxo/include/generated/
// genommen

$REX[GG] = true;

// setzte pfad und includiere klassen und funktionen

$REX[HTDOCS_PATH] = "./";
include "./redaxo/include/master.inc.php";

// Starte einen neuen Artikel und setzte die aktuelle
// artikel id. wenn nicht vorhanden, nimm einen
// speziellen artikel. z.b. fehler seite oder home seite

if ($article_id == "") $article_id = $REX[STARTARTIKEL_ID];

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

// ------------------------------------------------------------ scriptzeit
// echo showmicrotime();

if ($REX[STATS]==1)
{
	$log = new stat;
	$log->writeLog($article_id);
}

?>
