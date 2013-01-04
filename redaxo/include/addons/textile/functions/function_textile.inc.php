<?php
/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

function rex_a79_textile($code, $restricted=false, $doctype='xhtml')
{
  $textile = rex_a79_textile_instance($doctype);
  return $restricted==false
       ? $textile->TextileThis($code)
       : $textile->TextileRestricted($code);
}

function rex_a79_textile_instance($doctype='xhtml')
{
  static $instance = array();

  if(!isset($instance[$doctype]))
  {
    $instance[$doctype] = new Textile($doctype);
    $instance[$doctype]->unrestricted_url_schemes[] = 'redaxo';

    if (!rex_lang_is_utf8())
    {
      $instance[$doctype]->regex_snippets = array(
        'acr' => 'A-Z0-9',
        'abr' => 'A-Z',
        'nab' => 'a-z',
        'wrd' => '\w',
        'mod' => '',
        'cur' => '',
      );
      $instance[$doctype]->urlch = '[\w"$\-_.+!*\'(),";\/?:@=&%#{}|\\^~\[\]`]';
    }
  }

  return $instance[$doctype];
}
