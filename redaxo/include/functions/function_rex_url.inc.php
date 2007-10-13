<?php

/**
 * URL Funktionen
 * @package redaxo4
 * @version $Id$
 */

function rex_parse_article_name($name)
{
  static $firstCall = true;
  static $search  = array('Ä' , 'Ö' , 'Ü' , 'ä' , 'ö' , 'ü' , 'ß' , ' - ', ' ', '.');
  static $replace = array('Ae', 'Oe', 'Ue', 'ae', 'oe', 'ue', 'ss', '-'  , '-', '-');

  if($firstCall)
  {
    global $REX;

    $firstCall = false;

    // Wenn die Seite auf UTF-8 läuft, müssen wir auch nach UTF-8 Umlauten suchen
    if(strpos($REX['LANG'], 'utf8') !== false)
      $search = array_map('utf8_encode', $search);
  }

  $name = str_replace($search, $replace, $name);
  return preg_replace('/[^a-zA-Z\-0-9]/', '', $name);
}

/**
 * Baut einen Parameter String anhand des array $params
 */
function rex_param_string($params, $divider = '&amp;')
{
  $param_string = '';

  if (is_array($params))
  {
    foreach ($params as $key => $value)
    {
      $param_string .= $divider.urlencode($key).'='.urlencode($value);
    }
  }
  elseif ($params != '')
  {
    $param_string = $params;
  }

  return $param_string;
}

/**
 * Gibt eine Url zu einem Artikel zurück
 *
 * @param [$id] ArtikelId des Artikels
 * @param [$clang] SprachId des Artikels
 * @param [$params] Array von Parametern
 * @param [$divider] Trennzeichen für Parameter
 * (z.B. &amp; für HTML, & für Javascript)
 */
function rex_getUrl($id = '', $clang = '', $params = '', $divider = '&amp;')
{
  global $REX, $article_id;

  $id = (int) $id;
  $clang = (int) $clang;

  // ----- get id
  if (strlen($id) == 0 || $id == 0)
    $id = $article_id;

  // ----- get clang
  // Wenn eine rexExtension vorhanden ist, immer die clang mitgeben!
  // Die rexExtension muss selbst entscheiden was sie damit macht
  if (strlen($clang) == 0 && (count($REX['CLANG']) > 1 || rex_extension_is_registered( 'URL_REWRITE')))
  {
    $clang = $REX['CUR_CLANG'];
  }

  // ----- get params
  $param_string = rex_param_string($params, $divider);

  // ----- get article name
  $id = (int) $id;

  if ($id != 0)
  {
    $ooa = OOArticle :: getArticleById($id, $clang);
    if ($ooa)
      $name = rex_parse_article_name($ooa->getName());
  }

  if (!isset ($name) or $name == '')
    $name = 'NoName';

  // ----- EXTENSION POINT
  $url = rex_register_extension_point('URL_REWRITE', '', array ('id' => $id, 'name' => $name, 'clang' => $clang, 'params' => $param_string));

  if ($url == '')
  {
    // ----- get rewrite function
    if ($REX['MOD_REWRITE'] === true || $REX['MOD_REWRITE'] == 'true')
      $rewrite_fn = 'rex_apache_rewrite';
    else
      $rewrite_fn = 'rex_no_rewrite';

    $url = call_user_func($rewrite_fn, $id, $name, $clang, $param_string);
  }

  return $url;
}

// ----------------------------------------- Rewrite functions

// Kein Rewrite wird durchgeführt
function rex_no_rewrite($id, $name, $clang, $param_string)
{
  global $REX;
  $url = '';

  if (count($REX['CLANG']) > 1)
  {
    $url .= '&clang='.$clang;
  }

  return 'index.php?article_id='.$id .$url.$param_string;
}

// Rewrite für mod_rewrite
function rex_apache_rewrite($id, $name, $clang, $params)
{
  if ($params != '')
  {
    // strip first "&"
    $params = '?'.substr($params, strpos($params, '&') + 1);
  }

  return $id.'-'.$clang.'-'.$name.'.htm'.$params;
}
?>