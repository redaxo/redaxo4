<?php

/**
 * REDAXO Version Checker Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

$error = '';

if($error == '')
{
  if(!ini_get('allow_url_fopen'))
  {
    $error = 'PHP Configuration "allow_url_fopen" have to be enabled in php.ini';
  }
}

if ($error != '')
  $REX['ADDON']['installmsg']['version_checker'] = $error;
else
  $REX['ADDON']['install']['version_checker'] = true;