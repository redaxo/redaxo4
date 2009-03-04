<?php

/**
 * URL-Rewrite Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version svn:$Id$
 */

/**
 * URL Mod Rewrite Anleitung:
 * 
 *   .htaccess file in das root verzeichnis:
 *     RewriteEngine On
 *     # RewriteBase /
 *     RewriteRule ^([0-9]*)-([0-9]*)- index.php?article_id=$1&clang=$2&%{QUERY_STRING}
 */
class myUrlRewriter extends rexUrlRewriter
{
  // Url neu schreiben
  function rewrite($params)
  {
  	// Url wurde von einer anderen Extension bereits gesetzt
  	if($params['subject'] != '')
  	{
  		return $params['subject'];
  	}
  	
    $params['params'] = $params['params'] == '' ? '' : '?'. $params['params'];
    return $params['id'].'-'.$params['clang'].'-'.$params['name'].'.htm'.$params['params'];
  }
}
?>