<?php

/**
 * MetaForm Addon
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: index.inc.php,v 1.5 2008/03/26 18:54:34 kills Exp $
 */

// Parameter
$Basedir = dirname(__FILE__);

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');


// Include Header and Navigation
require $REX['INCLUDE_PATH'].'/layout/top.php';

// Build Subnavigation
$subpages = array(
  array('','Artikel'),
  array('categories','Kategorien'),
  array('media','Medien'),
);

rex_title('Metainformationen erweitern', $subpages);

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

// Include Footer
require $REX['INCLUDE_PATH'].'/layout/bottom.php';