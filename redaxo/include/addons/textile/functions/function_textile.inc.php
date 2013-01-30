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
  }

  return $instance[$doctype];
}
