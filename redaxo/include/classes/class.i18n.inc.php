<?php


/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */

// class.i18n.inc.php
// 
// created 03.04.04 by Carsten Eckelmann, <careck@circle42.com>
// 
// Mission: to supply messages for different language environments

class i18n
{

  var $locale;
  var $locales;
  var $text;
  var $searchpath;
  var $filename;

  var $fallback_locale;
  var $fallback;
  /*
   * Constructor
   * the locale must of the common form, eg. de_DE, en_US or just plain en, de.
   * the searchpath is where the language files are located
   */
  function i18n($locale, $searchpath, $fallback_local = 'de_de')
  {
    global $REX;
    $this->text = array ();
    $this->locale = $locale;
    $this->searchpath = $searchpath;
    $this->filename = $searchpath."/".$locale.".lang";
    $this->fallback_locale = $fallback_local;
    $this->fallback = null;
    $this->loadTexts();
    // Locale Cache
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
    global $I18N;
    
    if (is_readable($this->filename))
    {
      $f = fopen($this->filename, "r");
      while (!feof($f))
      {
        $buffer = fgets($f, 4096);
        if (preg_match("/^(\w*)\s*=\s*(.*)$/", $buffer, $matches))
        {
          $this->text[$matches[1]] = trim($matches[2]);
        }
      }
      fclose($f);
    }
    elseif(file_exists( $this->filename))
    {
      trigger_error( $I18N->msg('lang_file_not_readable', $this->filename), E_USER_ERROR);
    }
    else
    {
      trigger_error( $I18N->msg('lang_file_not_found', $this->filename), E_USER_ERROR);
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
    }
    else
    {
      $msg = '';
    }

    // falls der key nicht gefunden wurde, auf die fallbacksprache switchen
    if ($msg == '')
    {
      if (!$this->isFallback())
      {
        // fallbackobjekt ggf anlegen
        if (!$this->isFallback() && $this->fallback == null)
        {
          $this->fallback = rex_create_lang($this->fallback_locale, $this->searchpath);
        }

        // suchen des keys in der fallbacksprache
        return $this->fallback->msg($key, $p0, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9);
      }
      else
      {
        trigger_error('Schlüssel "'.$key.'" konnte weder in der ausgewählten noch in der Fallbacksprache gefunden werden!', E_USER_ERROR);
      }
    }

    $patterns = array ('/\{0\}/', '/\{1\}/', '/\{2\}/', '/\{3\}/', '/\{4\}/', '/\{5\}/', '/\{6\}/', '/\{7\}/', '/\{8\}/', '/\{9\}/');
    $replacements = array ($p0, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9);
    return preg_replace($patterns, $replacements, $msg);
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

  function isFallback()
  {
    return $this->locale == $this->fallback_locale;
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

  /*
  if ($use_as_fallback)
  {
     $REX['LANG_FALLBACK_OBJ'] = 
  }
  else
  {
     $REX['LANG_OBJ'] = $I18N = new i18n($locale, $searchpath);
  }
  */
}
?>