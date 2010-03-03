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

if($error == '')
{
  require_once dirname(__FILE__) .'/functions/function_version_check.inc.php';
  
  $url = 'www.redaxo.de';
  if(!rex_a657_check_connectivity($url))
  {
    $error = 'The server was unable to connect to "'. $url .'". Make sure the server has access to the internet.';
  }
}

if ($error != '')
  $REX['ADDON']['installmsg']['version_checker'] = $error;
else
  $REX['ADDON']['install']['version_checker'] = true;