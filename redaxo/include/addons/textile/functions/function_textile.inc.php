<?php
/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

function rex_a79_textile($code)
{
  $textile = rex_a79_textile_instance();

  if(rex_lang_is_utf8())
  {
    return $textile->TextileThis($code);
  }
  else
  {
    // TEXITLE LIB 2.2 WON'T WORK WITH ISO INPUT
    $code = utf8_encode($code);
    $code = $textile->TextileThis($code);
    return utf8_decode($code);
  }
}

function rex_a79_textile_instance()
{
  static $instance = null;

  if($instance === null)
  {
    $instance = new Textile();
  }

  return $instance;
}