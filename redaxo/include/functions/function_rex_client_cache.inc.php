<?php

/**
 * HTTP1.1 Client Cache Features
 *
 * @package redaxo4
 * @version $Id: function_rex_client_cache.inc.php,v 1.8 2008/03/07 18:36:16 kills Exp $
 */

/**
 * Sendet eine Datei zum Client
 *
 * @param $file string Pfad zur Datei
 * @param $contentType ContentType der Datei
 * @param $environment string Die Umgebung aus der der Inhalt gesendet wird
 * (frontend/backend)
 */
function rex_send_file($file, $contentType, $environment = 'backend')
{
  global $REX;

  // Cachen für Dateien aktivieren
  $temp = $REX['USE_LAST_MODIFIED'];
  $REX['USE_LAST_MODIFIED'] = true;

  header('Content-Type: '. $contentType);
  header('Content-Disposition: inline; filename="'.$file.'"');

  $content = rex_get_file_contents($file);
  $cacheKey = md5($content . $file . $contentType . $environment);

  rex_send_content(
    $content,
    filemtime($file),
    $cacheKey,
    $environment);

  // Setting zurücksetzen
  $REX['USE_LAST_MODIFIED'] = $temp;
}

/**
 * Sendet einen rex_article zum Client,
 * fügt ggf. HTTP1.1 cache headers hinzu
 *
 * @param $REX_ARTICLE rex_article Den zu sendenen Artikel
 * @param $content string Inhalt des Artikels
 * @param $environment string Die Umgebung aus der der Inhalt gesendet wird
 * (frontend/backend)
 */
function rex_send_article($REX_ARTICLE, $content, $environment)
{
  global $REX;
  
  // ----- EXTENSION POINT
  $content = rex_register_extension_point( 'OUTPUT_FILTER', $content);

  // ----- EXTENSION POINT - keine Manipulation der Ausgaben ab hier (read only)
  rex_register_extension_point( 'OUTPUT_FILTER_CACHE', $content, '', true);

  $etag = md5(preg_replace('@<!--DYN-->.*<!--/DYN-->@','', $content));

  if($REX_ARTICLE)
  {
    $lastModified = $REX_ARTICLE->getValue('updatedate');
    $etag .= $REX_ARTICLE->getValue('pid');
    
    if($REX_ARTICLE->getArticleId() == $REX['NOTFOUND_ARTICLE_ID'])
      header("HTTP/1.0 404 Not Found");
  }
  else
  {
    $lastModified = time();
  }

  rex_send_content(
    $content,
    $lastModified,
    $etag,
    $environment);
}

/**
 * Sendet den Content zum Client,
 * fügt ggf. HTTP1.1 cache headers hinzu
 *
 * @param $content string Inhalt des Artikels
 * @param $lastModified integer Last-Modified Timestamp
 * @param $cacheKey string Cachekey zur identifizierung des Caches
 * @param $environment string Die Umgebung aus der der Inhalt gesendet wird
 * (frontend/backend)
 */
function rex_send_content($content, $lastModified, $etag, $environment)
{
  global $REX;

  // Cachen erlauben, nach revalidierung
  header('Cache-Control: must-revalidate, proxy-revalidate, private');
    
  if($environment == 'backend')
  {
    global $I18N;
    header('Content-Type: text/html; charset='.$I18N->msg('htmlcharset'));
  }

  // ----- Last-Modified
  if($REX['USE_LAST_MODIFIED'] === 'true' || $REX['USE_LAST_MODIFIED'] == $environment)
    rex_send_last_modified($lastModified);

  // ----- ETAG
  if($REX['USE_ETAG'] === 'true' || $REX['USE_ETAG'] == $environment)
    rex_send_etag($etag);

  // ----- GZIP
  if($REX['USE_GZIP'] === 'true' || $REX['USE_GZIP'] == $environment)
    $content = rex_send_gzip($content);

  // ----- MD5 Checksum
  if($REX['USE_MD5'] === 'true' || $REX['USE_MD5'] == $environment)
    rex_send_checksum(md5(preg_replace('@<!--DYN-->.*<!--/DYN-->@','', $content)));

  // Evtl offene Db Verbindungen schließen
  rex_sql::disconnect(null);

  echo $content;
}

/**
 * Prüft, ob sich dateien geändert haben
 *
 * XHTML 1.1: HTTP_IF_MODIFIED_SINCE feature
 *
 * @param $lastModified integer Last-Modified Timestamp
 */
function rex_send_last_modified($lastModified = null)
{
  if(!$lastModified)
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
 * Prüft ob sich der Inhalt einer Seite im Cache des Browsers befindet und
 * verweisst ggf. auf den Cache
 *
 * XHTML 1.1: HTTP_IF_NONE_MATCH feature
 *
 * @param $cacheKey string Cachekey zur identifizierung des Caches
 */
function rex_send_etag($cacheKey)
{
  // Laut HTTP Spec muss der Etag in " sein
  $cacheKey = '"'. $cacheKey .'"';

  // Sende CacheKey als ETag
  header('ETag: '. $cacheKey);

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
 * Kodiert den Inhalt des Artikels in GZIP/X-GZIP, wenn der Browser eines der
 * Formate unterstützt
 *
 * XHTML 1.1: HTTP_ACCEPT_ENCODING feature
 *
 * @param $content string Inhalt des Artikels
 */
function rex_send_gzip($content)
{
  $enc = '';
  $encodings = array();
  $supportsGzip = false;

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

/**
 * Sendet eine MD5 Checksumme als HTTP Header, damit der Browser validieren
 * kann, ob Übertragungsfehler aufgetreten sind
 *
 * XHTML 1.1: HTTP_CONTENT_MD5 feature
 *
 * @param $md5 string MD5 Summe des Inhalts
 */
function rex_send_checksum($md5)
{
  header('Content-MD5: '. $md5);
}