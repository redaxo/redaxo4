<?php

/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$required_PHP = '5.0.0';

// CHECK PHP VERSION
////////////////////////////////////////////////////////////////////////////////
if(version_compare(PHP_VERSION, $required_PHP, '<'))
{
  $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/textile/lang/');
  $REX['ADDON']['installmsg']['textile'] = $I18N->msg('textile_install_phpversion', $required_PHP);
  $REX['ADDON']['install']['textile']    = 0;
  return;
}

$REX['ADDON']['install']['textile'] = 1;
