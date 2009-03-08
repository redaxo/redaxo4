<?php

/**
 * MetaForm Addon
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// Parameter
$Basedir = dirname(__FILE__);

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');

// Include Header and Navigation
require $REX['INCLUDE_PATH'].'/layout/top.php';

rex_title('Metainformationen erweitern', $REX['SUBPAGES']['metainfo']);

echo '<div class="rex-addon-output">';

// Include Current Page
switch($subpage)
{
  case 'media' :
  {
    $prefix = 'med_';
    break;
  }
  case 'categories' :
  {
    $prefix = 'cat_';
    break;
  }
  default:
  {
	  $prefix = 'art_';
  }
}

$metaTable = a62_meta_table($prefix);

require $Basedir .'/field.inc.php';

echo '</div>';

// Include Footer
require $REX['INCLUDE_PATH'].'/layout/bottom.php';