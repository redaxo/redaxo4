<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
rex_register_extension('ALL_GENERATED', 'rex_a62_metainfo_regenerate_all');
rex_register_extension('OUTPUT_FILTER', 'rex_a62_insertJs');

/**
 * Führt das nötige Cleanup nach einem "regenerate all" her. 	
 */
function rex_a62_metainfo_regenerate_all($params)
{
	rex_set_session('A62_MESSAGE', '');
}

/**
 * Fügt den nötigen JS-Code ein
 */
function rex_a62_insertJs($params)
{
	global $REX;
	
	$content = $params['subject'];

	$jsfile = $REX['INCLUDE_PATH'] .'/addons/metainfo/js/metainfo.js';
	$hdl = fopen($jsfile, 'r');
	$jscontent = fread($hdl, filesize($jsfile));
	fclose($hdl);
  
  $js ='
	  <script type="text/javascript">
	  <!--
	  '. $jscontent .'
	  //-->
	  </script>
  ';

  return str_replace('</head>', $js . '</head>', $content);
}