<?php

/**
 * Funktionen zur Ausgabe der Titel Leiste und Subnavigation
 * @package redaxo4
 * @version $Id$
 */

/**
 * Berechnet aus einem Relativen Pfad einen Absoluten
 */
function rex_absPath($rel_path, $rel_to_current = false)
{
  $stack = array();
  // Pfad relativ zum aktuellen Verzeichnis?
  // z.b. ../../files
  if($rel_to_current)
  {
    $path = realpath('.');
    $stack = explode(DIRECTORY_SEPARATOR, $path);
  }

  foreach (explode('/', $rel_path) as $dir)
  {
    // Aktuelles Verzeichnis, oder Ordner ohne Namen
    if ($dir == '.' || $dir == '')
      continue;

    // Zum Parent
    if ($dir == '..')
      array_pop($stack);
    // Normaler Ordner
    else
      array_push($stack, $dir);
  }

  return implode('/', $stack);
}

/**
 * Prüfen ob ein/e Datei/Ordner beschreibbar ist
 */
function rex_is_writable($item)
{
  return _rex_is_writable_info(_rex_is_writable($item), $item);
}

function _rex_is_writable_info($is_writable, $item = '')
{
  global $I18N;

  $state = true;
  $key = '';
  switch($is_writable)
  {
    case 1:
    {
      $key = 'setup_012';
      break;
    }
    case 2:
    {
      $key = 'setup_014';
      break;
    }
    case 3:
    {
      $key = 'setup_015';
      break;
    }
  }

  if($key != '')
  {
    $file = '';
    if($item != '')
      $file = '<b>'. $item .'</b>';

    $state = $I18N->msg($key, '<span class="rex-error">', '</span>', rex_absPath($file));
  }

  return $state;
}

function _rex_is_writable($item)
{
  // Fehler unterdrücken, falls keine Berechtigung
  if (@ is_dir($item))
  {
    if (!@ is_writable($item . '/.'))
    {
      return 1;
    }
  }
  // Fehler unterdrücken, falls keine Berechtigung
  elseif (@ is_file($item))
  {
    if (!@ is_writable($item))
    {
      return 2;
    }
  }
  else
  {
    return 3;
  }

  return 0;
}

function rex_getAttributes($name,$content,$default = null)
{
	$prop = unserialize($content);
	if (isset($prop[$name])) return $prop[$name];
	return $default;
}

function rex_setAttributes($name,$value,$content)
{
	$prop = unserialize($content);
	$prop[$name] = $value;
	return serialize($prop);
}

/**
 * Gibt den nächsten freien Tabindex zurück.
 * Der Tabindex ist eine stetig fortlaufende Zahl,
 * welche die Priorität der Tabulatorsprünge des Browsers regelt.
 *
 * @return integer nächster freier Tabindex
 */
function rex_tabindex($html = true)
{
  global $REX;

  if (empty($REX['TABINDEX']))
  {
    $REX['TABINDEX'] = 0;
  }

  if($html === true)
  {
	  return ' tabindex="'. ++$REX['TABINDEX'] .'"';
  }
  return ++$REX['TABINDEX'];
}


function array_insert($array, $index, $value)
{
	// In PHP5 akzeptiert array_merge nur arrays. Deshalb hier $value als Array verpacken
  return array_merge(array_slice($array, 0, $index), array($value), array_slice($array, $index));
}

function rex_warning($message, $cssClass = 'rex-warning')
{
  return '<p class="'. $cssClass .'"><span>'. $message .'</span></p>';
}

function rex_accesskey($title, $key)
{
  global $REX_USER;

  if($REX_USER->hasPerm('accesskeys[]'))
    return ' accesskey="'. $key .'" title="'. $title .' ['. $key .']"';

  return ' title="'. $title .'"';
}

function rex_ini_get($val)
{
  $val = trim(ini_get($val));
  if ($val != '') {
    $last = strtolower($val{strlen($val)-1});
  } else {
    $last = '';
  }
  switch($last) {
      // The 'G' modifier is available since PHP 5.1.0
      case 'g':
          $val *= 1024;
      case 'm':
          $val *= 1024;
      case 'k':
          $val *= 1024;
  }

  return $val;
}

/**
 * Übersetzt den text $text, falls dieser mit dem prefix "translate:" beginnt.
 */
function rex_translate($text, $I18N_Catalogue = null)
{
  if(!$I18N_Catalogue)
  {
    global $I18N;

    return rex_translate($text, $I18N);
  }

  $tranKey = 'translate:';
  $transKeyLen = strlen($tranKey);
  if(substr($text, 0, $transKeyLen) == $tranKey)
  {
    return htmlspecialchars($I18N_Catalogue->msg(substr($text, $transKeyLen)));
  }

  return htmlspecialchars($text);
}

/**
 * Leitet auf einen anderen Artikel weiter
 */
function rex_redirect($article_id, $clang, $params = array())
{
  global $REX;

  // Alle OBs schließen
  while(@ob_end_clean());

  $url = rex_no_rewrite($article_id, $clang, '', rex_param_string($params));

  // Redirects nur im Frontend folgen
  // Und nur wenn FOLLOW_REDIRECT auf true steht
  // Somit können Addons wie search_index die Seite indizieren
  // ohne dass der Indizierungsprozess durch weiterleitungen unterbrochen wird
  if(!$REX['REDAXO'] && $REX['FOLLOW_REDIRECTS'])
    header('Location: '. $url);
  else
    echo 'Disabled redirect to '. $url;

  exit();
}

/**
 * Trennt einen String an Leerzeichen auf.
 * Dabei wird beachtet, dass Strings in " zusammengehören
 */
function rex_split_string($string)
{
  $spacer = '@@@REX_SPACER@@@';
  $result = array ();

  // TODO mehrfachspaces hintereinander durch einfachen ersetzen
  $string = ' ' . trim($string) . ' ';

  // Strings mit Quotes heraussuchen
  $pattern = '!(["\'])(.*)\\1!U';
  preg_match_all($pattern, $string, $matches);
  $quoted = isset ($matches[2]) ? $matches[2] : array ();

  // Strings mit Quotes maskieren
  $string = preg_replace($pattern, $spacer, $string);

  // ----------- z.b. 4 "av c" 'de f' ghi
  if (strpos($string, '=') === false)
  {
    $parts = explode(' ', $string);
    foreach ($parts as $part)
    {
      if (empty ($part))
        continue;

      if ($part == $spacer)
      {
        $result[] = array_shift($quoted);
      }
      else
      {
        $result[] = $part;
      }
    }
  }
  // ------------ z.b. a=4 b="av c" y='de f' z=ghi
  else
  {
    $parts = explode(' ', $string);
    foreach ($parts as $part)
    {
      if(empty($part))
        continue;

      $variable = explode('=', $part);
      $var_name = $variable[0];
      $var_value = $variable[1];

      if (empty ($var_name))
        continue;

      if ($var_value == $spacer)
      {
        $var_value = array_shift($quoted);
      }

      $result[$var_name] = $var_value;
    }
  }
  return $result;
}
?>