<?php

/**
 * URL-Rewrite Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version svn:$Id$
 */

class rexUrlRewriter
{
  // Konstruktor
  function rexUrlRewriter()
  {
    // nichts tun
  }

  // Parameter aus der URL für das Script verarbeiten
  function prepare()
  {
    // nichts tun
  }

  // Url neu schreiben
  function rewrite($params)
  {
    $id = $params['id'];
    $name = $params['name'];
    $clang = $params['clang'];
    $params = $params['params'];
    return rex_no_rewrite($id, $name, $clang, $params);
  }
}
?>