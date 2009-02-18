<?php


/**
 * URL-Rewrite Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id: class.rewrite_fullnames.inc.php,v 1.16 2007/11/21 14:46:05 kills Exp $
 */

/**
 * Update
 * vscope update 18.09.07 (www.vscope.at)
 * Params werden in der Syntax /+/var/value/ angehängt und automatisch ausgelesen
 */

/**
 * URL Fullnames Rewrite Anleitung:
 *
 *   1) .htaccess file in das root verzeichnis:
 *     RewriteEngine On
 *     RewriteBase /
 *     RewriteCond %{REQUEST_URI}  !redaxo.*
 *     RewriteCond %{REQUEST_URI}  !files.*
 *     RewriteRule ^(.*)$ index.php?%{QUERY_STRING} [L]
 *
 *   2) .htaccess file in das redaxo/ verzeichnis:
 *     RewriteEngine Off
 *
 *   3) im Template folgende Zeile AM ANFANG des <head> ergänzen:
 *   <base href="htttp://www.meine_domain.de/pfad/zum/frontend" />
 *
 *   4) Specials->Regenerate All starten
 *
 * @author office[at]vscope[dot]at Wolfgang Huttegger
 * @author <a href="http://www.vscope.at/">vscope new media</a>
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 */

class myUrlRewriter extends rexUrlRewriter
{
  var $use_levenshtein;
  var $use_params_rewrite;

  // Konstruktor
  function myUrlRewriter($use_levenshtein = true, $use_params_rewrite = true)
  {
    $this->use_levenshtein = $use_levenshtein;
    $this->use_params_rewrite = $use_params_rewrite;

    // Parent Konstruktor aufrufen
    parent::rexUrlRewriter();
  }

  // Parameter aus der URL für das Script verarbeiten
  function prepare()
  {
    global $article_id, $clang, $REX, $REXPATH;

    if (!$REX['REDAXO'])
    {
      // article_id wurde in den super-globals übergeben
      if(rex_request('article_id', 'int'))
        $article_id = rex_request('article_id', 'int');

      // clang wurde in den super-globals übergeben
      if(rex_request('clang', 'int'))
        $clang = rex_request('clang', 'int');

      if($article_id)
        return true;

      $pathlist = $REX['INCLUDE_PATH'].'/generated/files/pathlist.php';
      include_once ($pathlist);

      $script_path = dirname($_SERVER['PHP_SELF']);
      $length = strlen($script_path);
      $path = substr($_SERVER['REQUEST_URI'], $length);

      // Parameter zählen nicht zum Pfad -> abschneiden
      if(($pos = strpos($path, '?')) !== false)
         $path = substr($path, 0, $pos);

      // Anker zählen nicht zum Pfad -> abschneiden
      if(($pos = strpos($path, '#')) !== false)
         $path = substr($path, 0, $pos);

      if ($path == '')
      {
        $article_id = $REX['START_ARTICLE_ID'];
        return true;
      }

			// Auch Urls die nicht auf "/" enden, sollen gefunden werden
      if(substr($path, -1) != '/')
        $path .= '/';

      // konvertiert params zu GET/REQUEST Variablen
      if($this->use_params_rewrite)
      {
        if(strstr($path,'/+/')){
          $tmp = explode('/+/',$path);
          $path = $tmp[0].'/';
           $vars = explode('/',$tmp[1]);
           for($c=0;$c<count($vars);$c+=2){
               if($vars[$c]!=''){
                 $_GET[$vars[$c]] = $vars[$c+1];
                 $_REQUEST[$vars[$c]] = $vars[$c+1];
               }
           }
        }
      }

      foreach ($REXPATH as $key => $var)
      {
        foreach ($var as $k => $v)
        {
          if ($path == $v)
          {
            $article_id = $key;
            $clang = $k;
          }
        }
      }

      // Check Clang StartArtikel
      if (!$article_id)
      {
        if(!isset($REX['CLANG']))
        {
          include($REX['INCLUDE_PATH'].'/clang.inc.php');
        }

        if (is_array($REX['CLANG']))
        {
          foreach ($REX['CLANG'] as $key => $var)
          {
            if ($var.'/' == $path)
            {
              $clang = $key;
            }
          }
        }
      }

      // Check levenshtein
      if ($this->use_levenshtein && !$article_id)
      {
        foreach ($REXPATH as $key => $var)
        {
          foreach ($var as $k => $v)
          {
            $levenshtein[levenshtein($path, $v)] = $key.'#'.$k;
          }
        }
        ksort($levenshtein);
        $best = explode('#', array_shift($levenshtein));
        $article_id = $best[0];
        $clang = $best[1];
      }

      if (!$article_id)
        $article_id = $REX['NOTFOUND_ARTICLE_ID'];

      // clang auch im REX speichern
      $REX['CUR_CLANG'] = $clang;
    }
  }

  // Url neu schreiben
  function rewrite($params)
  {
  	// Url wurde von einer anderen Extension bereits gesetzt
  	if($params['subject'] != '')
  		return $params['subject'];

    global $REX, $REXPATH;

    if (!$REXPATH)
    {
      include_once ($REX['INCLUDE_PATH'].'/generated/files/pathlist.php');
    }

    $id = $params['id'];
    $name = $params['name'];
    $clang = $params['clang'];
    $params = $params['params'];
    $divider = $params['divider'];

    // params umformatieren neue Syntax suchmaschienen freundlich
    if($this->use_params_rewrite)
    {
      $params = str_replace($divider,'/',$params);
      $params = str_replace('=','/',$params);
      $params = $params == '' ? '' : '+'.$params.'/';
    }
    else
    {
      $params = $params == '' ? '' : '?'.$params;
    }

    $url = $REXPATH[$id][$clang].$params;
    return $url;
  }
}

if ($REX['REDAXO'])
{
  // Die Pathnames bei folgenden Extension Points aktualisieren
  $extension = 'rex_rewriter_generate_pathnames';
  $extensionPoints = array(
    'CAT_ADDED',   'CAT_UPDATED',   'CAT_DELETED',
    'ART_ADDED',   'ART_UPDATED',   'ART_DELETED',
    'CLANG_ADDED', 'CLANG_UPDATED', 'CLANG_DELETED',
    'ALL_GENERATED');

  foreach($extensionPoints as $extensionPoint)
    rex_register_extension($extensionPoint, $extension);
}

function rex_rewriter_generate_pathnames($params = array ())
{
  global $REX;

  $db = new rex_sql();
  $result = $db->getArray('SELECT id,name,clang,path FROM rex_article');
  if (is_array($result))
  {
    foreach ($result as $var)
    {
      $article_names[$var['id']][$var['clang']]['name'] = rex_parse_article_name($var['name']);
    }
  }

  $fcontent = '<?php'."\n";
  if (is_array($result))
  {
    foreach ($result as $var)
    {
      $clang = $var['clang'];
      if (count($REX['CLANG']) > 1)
      {
        $pathname = $REX['CLANG'][$clang].'/';
      }
      else
      {
        $pathname = '';
      }
      $path = explode('|', $var['path']);
      $path[] = $var['id'];
      foreach ($path as $p)
      {
        if ($p != '')
        {
          $curname = $article_names[$p][$clang]['name'];
          if ($curname != '')
          {
            $pathname .= $curname.'/';
          }
        }
      }
      $fcontent .= '$REXPATH[\''.$var['id'].'\'][\''.$var['clang'].'\'] = "'.mysql_escape_string($pathname).'";'."\n";
    }
  }
  $fcontent .= '?>';

  $handle = fopen($REX['INCLUDE_PATH'].'/generated/files/pathlist.php', 'w');
  fwrite($handle, $fcontent);
  fclose($handle);
}
?>