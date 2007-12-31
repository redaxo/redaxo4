<?php

/**
 * Backend Search Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo4
 * @version $Id$
 */

rex_register_extension('PAGE_HEADER', 'rex_a256_insertCss');

/**
 * Fügt den nötigen JS-Code ein
 */
function rex_a256_insertCss($params)
{
	global $REX;

	$content = $params['subject'];

	$cssfile = $REX['INCLUDE_PATH'] .'/addons/be_search/css/be_search.css';
  $cssContent = rex_get_file_contents($cssfile);

  $css ='
    <!-- Backend Search CSS //-->
	  <style type="text/css">
	  <!--
	  '. $cssContent .'
	  //-->
	  </style>
    <!-- End Backend Search CSS //-->
  ';

  return $content . $css;
}

?>