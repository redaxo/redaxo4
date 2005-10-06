<?php

// ----- caching start für output filter

ob_start();

// --------------------------- ini settings

// Setzten des arg_separators, falls Sessions verwendet werden,
// um XHTML valide Links zu produzieren
@ini_set( 'arg_separator.input', '&amp;');
@ini_set( 'arg_separator.output', '&amp;');

// --------------------------- globals

unset($REX);

// Übernahme von FormularImagePosition

if (!isset($x)) $x = '';
if (!isset($y)) $y = '';

$REX['x'] = $x;
$REX['y'] = $y;

// Flag ob Inhalte mit Redaxo aufgerufen oder
// von der Webseite aus
// Kann wichtig für die Darstellung sein
// Sollte immer false bleiben

$REX['REDAXO'] = false;

// Wenn $REX[GG] = true; dann wird der
// Content aus den redaxo/include/generated/
// genommen

$REX['GG'] = true;

// setzte pfad und includiere klassen und funktionen

$REX['HTDOCS_PATH'] = "./";
include "./redaxo/include/master.inc.php";

// Starte einen neuen Artikel und setzte die aktuelle
// artikel id. wenn nicht vorhanden, nimm einen
// speziellen artikel. z.b. fehler seite oder home seite

if (!isset($article_id) or $article_id == '') $article_id = $REX['STARTARTIKEL_ID'];

$SHOWARTICLE = true;

// If Caching is true start engine
if($REX['CACHING'])
{

  $Cache = new Cache($article_id);
  if($Cache->isCacheConf())
  {
    if($Cache->isCacheFile())
    {
      $Cache->printCacheFile();
      if ($REX['CACHING_DEBUG']) print "<br>CachedVersion<br>";
      if ($REX['CACHING_DEBUG']) print "Script time: ".showScripttime();
      $SHOWARTICLE = false;
    } else {
      // start caching
      $Cache->startCacheFile();
    }
  }

}

if ($SHOWARTICLE)
{
  $REX_ARTICLE = new article;
  $REX_ARTICLE->setCLang($clang);
  if ($REX_ARTICLE->setArticleId($article_id))
  {
    $REX_ARTICLE->getArticleTemplate();
  }elseif($REX_ARTICLE->setArticleId($REX['STARTARTIKEL_ID']))
  {   
    $REX_ARTICLE->getArticleTemplate();
  }else
  {
    echo "Kein Startartikel selektiert / No starting Article selected. Please click here to enter <a href=redaxo/index.php>redaxo</a>";
    $REX['STATS'] = 0;
  }
  //////////////////////////////////////////////
  // advanced caching
  //////////////////////////////////////////////
  
  if(isset($Cache->makeCacheFile) and $Cache->makeCacheFile){
    $Cache->writeCacheFile();
    if($REX['CACHING_DEBUG']) print "<br>MadeCache<br>";
  } else {
    if($REX['CACHING_DEBUG']) print "<br>Live<br>";
  }
  
  if($REX['CACHING_DEBUG']) print "Script time: ".showScripttime();
  
  //////////////////////////////////////////////  
}


// ----- caching end für output filter

$CONTENT = ob_get_contents();
ob_end_clean();


// ---- user functions vorhanden ? wenn ja ausführen
if (is_array($REX['OUTPUT_FILTER']))
{
   foreach ($REX['OUTPUT_FILTER'] as $output_filter) {
      $CONTENT = call_user_func($output_filter, $CONTENT);
   }
}


// ---- caching functions vorhanden ? wenn ja ausführen
if (is_array($REX['OUTPUT_FILTER_CACHE']))
{
   foreach ($REX['OUTPUT_FILTER_CACHE'] as $output_cache) {
      call_user_func($output_cache, $CONTENT);
   }
}


// ----- inhalt endgueltig ausgeben

echo $CONTENT;

?>
