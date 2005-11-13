<?php


// ----------------------------------------- Redaxo 2.* functions

function getUrlByid($id, $clang = "", $params = "")
{
  return rex_getUrl($id, $clang, $params);
}

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
 * e.g.:
 *   $param = array("order" => "123", "name" => "horst");
 *   $article->getUrl($param);
 * will return:
 *   index.php?article_id=1&order=123&name=horst
 * or if mod_rewrite support is activated:
 *   /1-The_Article_Name?order=123&name=horst
 */
function rex_getUrl($id = '', $clang = '', $params = '')
{
  global $REX, $article_id;

  // ----- get id
  if (strlen($id) == 0 || $id == 0)
  {
    $id = $article_id;
  }

  // ----- get clang
  if (strlen($clang) == 0 && count($REX['CLANG']) > 1)
  {
    $clang = $REX['CUR_CLANG'];
  }

  // ----- get params
  $param_string = '';
  if (is_array($params))
  {
    foreach ($params as $key => $value)
    {
      $param_string .= '&amp;'.$key.'='.$value;
    }
  }
  elseif ($params != '')
  {
    $param_string = str_replace('&', '&amp;', $params);
  }

  // ----- get article name
  $id = (int) $id;

  if ($id != 0)
  {
    $ooa = OOArticle :: getArticleById($id);
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
      $rewrite_fn = 'rexrewrite_apache_rewrite';
    }
    else
    {
      $rewrite_fn = 'rexrewrite_no_rewrite';
    }

    $url = call_user_func($rewrite_fn, $id, $name, $clang, $param_string);
  }

  return $url;

}

// ----------------------------------------- Rewrite functions

// Kein Rewrite wird durchgeführt
function rexrewrite_no_rewrite($id, $name, $clang, $param_string)
{
  global $REX;
  $url = 'index.php?article_id='.$id;

  if (count($REX['CLANG']) > 1)
  {
    $url .= '&amp;clang='.$clang;
  }

  return $REX['WWW_PATH'].$url.$param_string;
}

// Rewrite für mod_rewrite
function rexrewrite_apache_rewrite($id, $name, $clang, $params)
{
  global $REX;
  if ($params != '')
  {
    // strip first "&amp;"
    $params = '?'.substr($params, strpos($params, '&amp;') + 5);
  }

  return $REX['WWW_PATH'].$id.'-'.$clang.'-'.$name.'.html'.$params;
}
?>