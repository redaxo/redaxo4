<?php

/**
 * HTTP1.1 Client Cache Features
 *
 * @package redaxo3
 * @version $Id$
 */

// Prüft, ob sich dateien geändert haben
function rex_send_last_modified($REX_ARTICLE)
{
  $lastModified = date('r', $REX_ARTICLE->getValue('updatedate'));

  // Last-Modified Timestamp gefunden
  // => den Browser anweisen, den Cache zu verwenden
  if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastModified)
  {
    while(@ob_end_clean());

    header('HTTP/1.1 304 Not Modified');
    die();
  }

  // Sende Last-Modification time
  header('Last-Modified: ' . $lastModified);
}

// Prüft ob sich der Inhalt einer Seite geändert hat
function rex_send_etag($CONTENT)
{
  $cacheKey = md5($CONTENT);

  // CacheKey gefunden
  // => den Browser anweisen, den Cache zu verwenden
  if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $cacheKey)
  {
    while(@ob_end_clean());

    header('HTTP/1.1 304 Not Modified');
    die();
  }

  // Sende CacheKey als ETag
  header('ETag: ' . $cacheKey);
}

function rex_send_gzip($CONTENT)
{
  // Check if it supports gzip
  if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
    $encodings = explode(',', strtolower(preg_replace('/\s+/', '', $_SERVER['HTTP_ACCEPT_ENCODING'])));

  if ((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')) {
    $enc = in_array('x-gzip', $encodings) ? 'x-gzip' : 'gzip';
    $supportsGzip = true;
  }

  if($supportsGzip)
  {
    header('Content-Encoding: '. $enc);
    $CONTENT = gzencode($CONTENT, 9, FORCE_GZIP);
  }

  return $CONTENT;
}

?>