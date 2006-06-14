<?php

/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */

// class.i18n.inc.php
// 
// created 03.04.04 by Carsten Eckelmann, <careck@circle42.com>
// updated 08.02.06 by Jan Kristinus
// -- System vereinfacht und Fehlermeldungen vermeiden. 
//    lang file fehlt -> fallback
//    msg nicht da -> [translate:key]

class i18n
{

  var $locales;
  var $searchpath;

  var $locale;
  var $text;
  var $filename;

  var $fallback_locale;
  var $fallback_text;
  var $fallback_filename;
  
  /*
   * Constructor
   * the locale must of the common form, eg. de_DE, en_US or just plain en, de.
   * the searchpath is where the language files are located
   */
  function i18n($locale, $searchpath, $fallback_locale = 'de_de')
  {
    $this->searchpath = $searchpath;
	
    $this->text = array ();
    $this->locale = $locale;
    $this->filename = $searchpath."/".$locale.".lang";
    
	$this->fallback_text = array();
    $this->fallback_locale = $fallback_locale;
    $this->fallback_filename = $searchpath."/".$fallback_locale.".lang";

	$this->loadTexts();

    $this->locales = array ();
  }

  /* 
   * load texts from file.
   * The filename must be of the form:
   *
   * <locale>.lang
   * eg: de_DE.lang or en_US.lang or en_GB.lang
   *
   * The file must be in the common property format:
   *
   * key = value
   * # comments must be on one line
   * 
   * values may contain placeholders for replacement of variables, e.g.
   * file_not_found = The file {0} could not be found.
   * there can be only 10 placeholders, {0} to {9}.
   */
  function loadTexts()
  {

	// sprache nicht vorhanden -> fallback einsetzen
	$filename = $this->filename;
    if (!is_readable($filename))
    {
    	$filename = $this->fallback_filename;
    }

    if (is_readable($filename))
    {
      $f = fopen($filename, "r");
      while (!feof($f))
      {
        $buffer = fgets($f, 4096);
        if (preg_match("/^(\w*)\s*=\s*(.*)$/", $buffer, $matches))
        {
          $this->text[$matches[1]] = trim($matches[2]);
        }
      }
      fclose($f);
    }else
    {
		// sprache und fallbacksprache nicht gefunden

    }
  }

  /*
   * return a message according to a key from the current locale
   * you can give up to 10 parameters for substitution.
   */
  function msg($key, $p0 = '', $p1 = '', $p2 = '', $p3 = '', $p4 = '', $p5 = '', $p6 = '', $p7 = '', $p8 = '', $p9 = '')
  {
    global $REX;

    if (isset ($this->text[$key]))
    {
      $msg = $this->text[$key];
    }else
    {
      $msg = "[translate:$key]";
    }

    $patterns = array ('/\{0\}/', '/\{1\}/', '/\{2\}/', '/\{3\}/', '/\{4\}/', '/\{5\}/', '/\{6\}/', '/\{7\}/', '/\{8\}/', '/\{9\}/');
    $replacements = array ($p0, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9);
    return htmlspecialchars(preg_replace($patterns, $replacements, $msg));
  }

  /* 
   * find all defined locales in a searchpath
   * the language files must be of the form: <locale>.lang
   * e.g. de_de.lang or en_gb.lang
   */
  function getLocales($searchpath)
  {
    if (empty ($this->locales) && is_readable($searchpath))
    {
      $this->locales = array ();

      $handle = opendir($searchpath);
      while ($file = readdir($handle))
      {
        if ($file != "." && $file != "..")
        {
          if (preg_match("/^(\w+)\.lang$/", $file, $matches))
          {
            $this->locales[] = $matches[1];
          }
        }
      }
      closedir($handle);

    }

    return $this->locales;
  }

}

// Funktion zum Anlegen eines Sprache-Objekts
function rex_create_lang($locale, $searchpath = '')
{
  global $REX;

  $_searchpath = $searchpath;

  if ($searchpath == '')
  {
    $searchpath = $REX['INCLUDE_PATH']."/lang";
  }

  $lang_object = new i18n($locale, $searchpath);

  if ($_searchpath == '')
  {
    $REX['LOCALES'] = $lang_object->getLocales($searchpath);
  }

  return $lang_object;

}

?>