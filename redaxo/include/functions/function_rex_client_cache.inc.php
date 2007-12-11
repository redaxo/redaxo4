<?php

/**
 * HTTP1.1 Client Cache Features
 *
 * @package redaxo4
 * @version $Id$
 */

/**
 * Sendet den Content zum Client,
 * fügt ggf. HTTP1.1 cache headers hinzu
 *
 * @param $REX_ARTICLE rex_article Den zu sendenen Artikel
 * @param $content string Inhalt des Artikels
 * @param $environment string Die Umgebung aus der der Inhalt gesendet wird
 * (frontend/backend)
 */
function rex_send_content($REX_ARTICLE, $content, $environment)
{
  global $REX;

  // ----- EXTENSION POINT
  $content = rex_register_extension_point( 'OUTPUT_FILTER', $content);

  // ----- EXTENSION POINT - keine Manipulation der Ausgaben ab hier (read only)
  rex_register_extension_point( 'OUTPUT_FILTER_CACHE', $content, '', true);

  // ----- Last-Modified
  if($REX['USE_LAST_MODIFIED'] === 'true' || $REX['USE_LAST_MODIFIED'] == $environment)
    rex_send_last_modified($REX_ARTICLE);

  // ----- ETAG
  if($REX['USE_ETAG'] === 'true' || $REX['USE_ETAG'] == $environment)
    rex_send_etag($REX_ARTICLE, $content);

  // ----- GZIP
  if($REX['USE_GZIP'] === 'true' || $REX['USE_GZIP'] == $environment)
    $content = rex_send_gzip($content);

  // Evtl offene Db Verbindungen schließen
  rex_sql::disconnect(null);

  echo $content;
}

/**
 * Prüft, ob sich dateien geändert haben
 *
 * XHTML 1.1: HTTP_IF_MODIFIED_SINCE feature
 *
 * @param $REX_ARTICLE rex_article Den zu sendenen Artikel
 */
function rex_send_last_modified($REX_ARTICLE)
{
  if($REX_ARTICLE)
    $lastModified = $REX_ARTICLE->getValue('updatedate');
  else
    $lastModified = time();

  $lastModified = date('r', $lastModified);

  // Sende Last-Modification time
  header('Last-Modified: ' . $lastModified);

  // Last-Modified Timestamp gefunden
  // => den Browser anweisen, den Cache zu verwenden
  if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastModified)
  {
    while(@ob_end_clean());

    header('HTTP/1.1 304 Not Modified');
    exit();
  }
}

/**
 * Prüft ob sich der Inhalt einer Seite geändert hat
 *
 * XHTML 1.1: HTTP_IF_NONE_MATCH feature
 *
 * @param $REX_ARTICLE rex_article Den zu sendenen Artikel
 * @param $content string Inhalt des Artikels
 */
function rex_send_etag($REX_ARTICLE, $content)
{
  $cacheKey = md5($content);

  // Concat rex_article primary key to cache key in frontend
  if($REX_ARTICLE)
    $cacheKey .= $REX_ARTICLE->getValue('pid');

  // Sende CacheKey als ETag
  header('ETag: "' . $cacheKey .'"');

  // CacheKey gefunden
  // => den Browser anweisen, den Cache zu verwenden
  if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $cacheKey)
  {
    while(@ob_end_clean());

    header('HTTP/1.1 304 Not Modified');
    exit();
  }
}

/**
 * Kodiert den Inhalt des Artikels in GZIP/X-GZIP
 *
 * XHTML 1.1: HTTP_ACCEPT_ENCODING feature
 *
 * @param $content string Inhalt des Artikels
 */
function rex_send_gzip($content)
{
  // Check if it supports gzip
  if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
    $encodings = explode(',', strtolower(preg_replace('/\s+/', '', $_SERVER['HTTP_ACCEPT_ENCODING'])));

  if ((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression'))
  {
    $enc = in_array('x-gzip', $encodings) ? 'x-gzip' : 'gzip';
    $supportsGzip = true;
  }

  if($supportsGzip)
  {
    header('Content-Encoding: '. $enc);
    $content = gzencode($content, 9, FORCE_GZIP);
  }

  return $content;
}

?>