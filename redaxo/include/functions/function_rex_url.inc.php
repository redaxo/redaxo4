<?php

/**
 * URL Funktionen
 * @package redaxo4
 * @version $Id: function_rex_url.inc.php,v 1.4 2008/04/15 15:44:50 kills Exp $
 */

function rex_parse_article_name($name)
{
  static $firstCall = true;
  static $search, $replace;

  if($firstCall)
  {
    global $REX, $I18N;

    // Im Frontend gibts kein I18N
    if(!$I18N)
      $I18N = rex_create_lang($REX['LANG']);

    // Sprachspezifische Sonderzeichen Filtern
    $search = explode('|', $I18N->msg('special_chars'));
    $replace = explode('|', $I18N->msg('special_chars_rewrite'));

    $firstCall = false;
  }

  return preg_replace('/[^a-zA-Z\-0-9]/', '', str_replace($search, $replace, $name));
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
 * @param [$_id] ArtikelId des Artikels
 * @param [$_clang] SprachId des Artikels
 * @param [$_params] Array von Parametern
 * @param [$_divider] Trennzeichen für Parameter
 * (z.B. &amp; für HTML, & für Javascript)
 */
function rex_getUrl($_id = '', $_clang = '', $_params = '', $_divider = '&amp;')
{
  global $REX, $article_id;

  $id = (int) $_id;
  $clang = (int) $_clang;

  // ----- get id
  if ($id == 0)
    $id = $article_id;

  // ----- get clang
  // Wenn eine rexExtension vorhanden ist, immer die clang mitgeben!
  // Die rexExtension muss selbst entscheiden was sie damit macht
  if ($_clang === '' && (count($REX['CLANG']) > 1 || rex_extension_is_registered( 'URL_REWRITE')))
    $clang = $REX['CUR_CLANG'];

  // ----- get params
  $param_string = rex_param_string($_params, $_divider);

  $name = 'NoName';
  if ($id != 0)
  {
    $ooa = OOArticle :: getArticleById($id, $clang);
    if ($ooa)
      $name = rex_parse_article_name($ooa->getName());
  }

  // ----- EXTENSION POINT
  $url = rex_register_extension_point('URL_REWRITE', '', array ('id' => $id, 'name' => $name, 'clang' => $clang, 'params' => $param_string, 'divider' => $_divider));

  if ($url == '')
  {
    // ----- get rewrite function
    if ($REX['MOD_REWRITE'] === true || $REX['MOD_REWRITE'] == 'true')
      $rewrite_fn = 'rex_apache_rewrite';
    else
      $rewrite_fn = 'rex_no_rewrite';

    $url = call_user_func($rewrite_fn, $id, $name, $clang, $param_string, $_divider);
  }

  return $url;
}

// ----------------------------------------- Rewrite functions

/**
 * Standard Rewriter, gibt normale Urls zurück
 */
function rex_no_rewrite($id, $name, $clang, $param_string, $divider)
{
  global $REX;
  $url = '';

  if (count($REX['CLANG']) > 1)
  {
    $url .= $divider.'clang='.$clang;
  }

  return $REX["FRONTEND_FILE"].'?article_id='.$id .$url.$param_string;
}

/**
 * Standard Rewriter, gibt umschrieben Urls im Format
 *
 * <id>-<clang>-<name>.html[?<params>]
 */
function rex_apache_rewrite($id, $name, $clang, $params, $divider)
{
  if ($params != '')
  {
    // strip first "&"
    $params = '?'.substr($params, strpos($params, $divider) + strlen($divider));
  }

  return $id.'-'.$clang.'-'.$name.'.html'.$params;
}