<?php

/**
 * URL Funktionen
 * @package redaxo3
 * @version $Id$
 */

// ----------------------------------------- Parse Article Name for Url

function rex_parseArticleName($name)
{
  $name = strtolower($name);
  $name = str_replace(' - ', '-', $name);
  $name = str_replace(' ', '-', $name);
  $name = str_replace('.', '-', $name);
  $name = str_replace('Ä', 'Ae', $name);
  $name = str_replace('Ö', 'Oe', $name);
  $name = str_replace('Ü', 'Ue', $name);
  $name = str_replace('ä', 'ae', $name);
  $name = str_replace('ö', 'oe', $name);
  $name = str_replace('ü', 'ue', $name);
  $name = str_replace('ß', 'ss', $name);
  $name = preg_replace("/[^a-zA-Z\-0-9]/", "", $name);
  return $name;
}

// ----------------------------------------- URL

/**
 * Object Helper Function:
 * Returns a url for linking to this article
 * This url respects the setting for mod_rewrite
 * support!
 *
 * If you pass an associative array for $params,
 * then these parameters will be attached to the URL.
 *
 *
 * USAGE:
 *   $param = array("order" => "123", "name" => "horst");
 *   $url = $article->getUrl($param);
 *
 *   - OR -
 *
 *   $url = $article->getUrl("order=123&name=horst");
 *
 * RETURNS:
 *   index.php?article_id=1&order=123&name=horst
 * or if mod_rewrite support is activated:
 *   /1-The_Article_Name?order=123&name=horst
 */
function rex_getUrl($id = '', $clang = '', $params = '')
{
  global $REX, $article_id;

  $id = (int) $id;
  $clang = (int) $clang;

  // ----- get id
  if (strlen($id) == 0 || $id == 0)
  {
    $id = $article_id;
  }

  // ----- get clang
  // Wenn eine rexExtension vorhanden ist, immer die clang mitgeben!
  // Die rexExtension muss selbst entscheiden was sie damit macht
  if (strlen($clang) == 0 && (count($REX['CLANG']) > 1 || rex_extension_is_registered( 'URL_REWRITE')))
  {
    $clang = $REX['CUR_CLANG'];
  }

  // ----- get params
  $param_string = '';
  if (is_array($params))
  {
    foreach ($params as $key => $value)
    {
      $param_string .= '&'.$key.'='.$value;
    }
  }
  elseif ($params != '')
  {
    $param_string = $params;
  }

  // ----- get article name
  $id = (int) $id;

  if ($id != 0)
  {
    $ooa = OOArticle :: getArticleById($id, $clang);
    if ($ooa)
    {
      $name = rex_parseArticleName($ooa->getName());
    }
  }

  if (!isset ($name) or $name == '')
  {
    $name = 'NoName';
  }

  // ----- EXTENSION POINT
  $url = rex_register_extension_point('URL_REWRITE', '', array ('id' => $id, 'name' => $name, 'clang' => $clang, 'params' => $param_string));

  if ($url == '')
  {
    // ----- get rewrite function
    if ($REX['MOD_REWRITE'] === true || $REX['MOD_REWRITE'] == 'true')
    {
      $rewrite_fn = 'rex_apache_rewrite';
    }
    else
    {
      $rewrite_fn = 'rex_no_rewrite';
    }

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

  return 'index.php?article_id='.$id .urlencode($url.$param_string);
}

// Rewrite für mod_rewrite
function rex_apache_rewrite($id, $name, $clang, $params)
{
  if ($params != '')
  {
    // strip first "&amp;"
    $params = '?'.urlencode(substr($params, strpos($params, '&') + 1));
  }

  return $id.'-'.$clang.'-'.$name.'.htm'.$params;
}
?>