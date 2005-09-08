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

/*
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
 * 
 * 
 * Since rexdaxo > 3.0 you can set $REX['MOD_REWRITE'] to any function name, 
 * which is called for rewriting the url.
 * The function is called with the following parameters:
 *   param1: $id - the article id (int)
 *   param2: $name - the article name (string)
 *   param3: $clang - the content language (int)
 *   param4: $params - the params to append for the url
 * 
 * You can set $REX['MOD_REWRITE'] also to an object.
 * If the object owns a method called "rewrite",
 * the method will be used as rewrite method.
 * 
 * The variable $REX['MOD_REWRITE'] can also be set for a static class call.
 * e.g. 'myClass::myMethod'
 */
function rex_getUrl($id, $clang = "", $params = "")
{
   global $REX;

   // ----- get clang
   if ($clang == '')
   {
      $clang = $REX['CUR_CLANG'];
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

   if ($name == '')
   {
      $name = 'NoName';
   }

   // ----- get rewrite function
   $rewrite_fn = 'rexrewrite_no_rewrite';

   if ($REX['MOD_REWRITE'] === true || $REX['MOD_REWRITE'] == 'true')
   {
      $rewrite_fn = 'rexrewrite_apache_rewrite';
   }

   if (is_string($REX['MOD_REWRITE']) && $REX['MOD_REWRITE'] != '')
   {
      if (strpos($REX['MOD_REWRITE'], '::') !== false)
      {
         // static method
         preg_match('!(\w+)::(\w+)!', $REX['MOD_REWRITE'], $_match);
         $_object_name = $_match[1];
         $_method_name = $_match[2];

         if (is_callable(array ($_object_name, $_method_name)))
         {
            $rewrite_fn = array ($_object_name, $_method_name);
         }
      }
      elseif (function_exists($REX['MOD_REWRITE']))
      {
         // function call
         $rewrite_fn = $REX['MOD_REWRITE'];
      }

   }
   elseif (is_object($REX['MOD_REWRITE']))
   {
      // object call
      $_method_name = 'rewrite';
      if (method_exists($REX['MOD_REWRITE'], $_method_name))
      {
         $rewrite_fn = array ($REX['MOD_REWRITE'], $_method_name);
      }
   }

   return call_user_func($rewrite_fn, $id, $name, $clang, $params);
}

// ----------------------------------------- Rewrite functions

// Kein Rewrite wird durchgeführt
function rexrewrite_no_rewrite($id, $name, $clang, $params)
{
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

   return $REX['WWW_PATH'].'index.php?article_id='.$id.'&amp;clang='.$clang.$param_string;
}

// Rewrite für mod_rewrite
function rexrewrite_apache_rewrite($id, $name, $clang, $params)
{
   // ----- get params
   $param_string = '';
   if (is_array($params))
   {
      $first = true;
      foreach ($params as $key => $value)
      {
         if ($first)
         {
            $first = false;
         }
         else
         {
            $param_string .= '&amp;';
         }
         $param_string .= $key.'='.$value;
      }
   }
   elseif ($params != '')
   {
      $param_string = str_replace('&', '&amp;', $params);
   }

   if ($param_string != '')
   {
      $param_string = '?'.$param_string;
   }

   return $REX['WWW_PATH'].$id.'-'.$clang.'-'.$name.'.html'.$param_string;
}
?>