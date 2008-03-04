<?php

/**
 * MetaForm Addon
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo4
 * @version $Id$
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
    $metaTable = $REX['TABLE_PREFIX'] .'file';
    break;
  }

  case 'categories' :
  {
    $prefix = 'cat_';
    $metaTable = $REX['TABLE_PREFIX'] .'article';
    break;
  }

  default:
  {
	  $prefix = 'art_';
    $metaTable = $REX['TABLE_PREFIX'] .'article';
  }
}
require $Basedir .'/field.inc.php';

// Include Footer
require $REX['INCLUDE_PATH'].'/layout/bottom.php';
?>