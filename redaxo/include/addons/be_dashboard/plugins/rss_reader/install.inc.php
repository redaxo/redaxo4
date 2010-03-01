<?php

/**
 * RSS Reader Addon
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
  if (version_compare(PHP_VERSION, '4.3.0', '<'))
  {
    $error = 'This plugin requires at least PHP Version 4.3.0';
  }
}

if($error == '')
{
  if (!extension_loaded('xml'))
  {
    $error = 'Missing required PHP-Extension "xml"';
  }
  elseif (!extension_loaded('xmlreader'))
  {
    $error = 'Missing required PHP-Extension "xmlreader"';
  }
}

if($error == '')
{
  require_once dirname(__FILE__) .'/functions/function_reader.inc.php';
  
  $url = 'www.redaxo.de';
  if(!rex_a656_http_health_check($url))
  {
    $error = 'The server was unable to connect to "'. $url .'". Make sure the server has access to the internet.';
  }
}

if ($error != '')
  $REX['ADDON']['installmsg']['rss_reader'] = $error;
else
  $REX['ADDON']['install']['rss_reader'] = true;