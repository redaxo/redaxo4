<?php
/**
 *
 * @package redaxo4
 * @version $Id: medienpool.inc.php,v 1.19 2008/03/26 16:19:38 kills Exp $
 */

// TODOS
// - wysiwyg image pfade anschauen und kontrollieren
// - import checken
// - mehrere ebenen in kategorienedit  einbauen

// -------------- Defaults
$subpage      = rex_request('subpage', 'string');
$func         = rex_request('func', 'string');
$media_method = rex_request('media_method', 'string');
$info         = rex_request('info', 'string');
$warning      = rex_request('warning', 'string');

// -------------- Additional Args
$arg_url = '';
$arg_fields = '';
foreach(rex_request('args', 'array') as $arg_name => $arg_value)
{
  $arg_url .= '&amp;args['. urlencode($arg_name) .']='. urlencode($arg_value);
  $arg_fields .= '<input type="hidden" name="args['. $arg_name .']" value="'. $arg_value .'" />'. "\n";
}

// -------------- CatId in Session speichern
$file_id = rex_request('file_id', 'int');
$file_name = rex_request('file_name', 'string');
$rex_file_category = rex_request('rex_file_category', 'int', -1);

if ($file_name != "")
{
  $sql = new rex_sql();
  $sql->setQuery("select * from ".$REX['TABLE_PREFIX']."file where filename='$file_name'");
  if ($sql->getRows()==1)
  {
    $file_id = $sql->getValue("file_id");
    $rex_file_category = $sql->getValue("category_id");
  }
}

if($rex_file_category == -1)
{
  $rex_file_category = rex_session('media[rex_file_category]', 'int');
}


$gc = new rex_sql;
$gc->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'file_category WHERE id='. $rex_file_category);
if ($gc->getRows() != 1)
{
  $rex_file_category = 0;
  $rex_file_category_name = $I18N->msg('pool_kats_no');
}else
{
  $rex_file_category_name = $gc->getValue('name');
}

rex_set_session('media[rex_file_category]', $rex_file_category);

// -------------- PERMS
$PERMALL = false;
if ($REX_USER->hasPerm('admin[]') or $REX_USER->hasPerm('media[0]')) $PERMALL = true;

// -------------- Header
$subline = array(
  array('', $I18N->msg('pool_file_list')),
  array('add_file', $I18N->msg('pool_file_insert')),
);

if($PERMALL)
{
  $subline[] = array('categories', $I18N->msg('pool_cat_list'));
  $subline[] = array('sync', $I18N->msg('pool_sync_files'));
}

// Arg Url an Menulinks anhaengen
foreach($subline as $key => $item)
{
  $subline[$key][2] = '';
  $subline[$key][3] = $arg_url;
}

// ----- EXTENSION POINT
$subline = rex_register_extension_point('PAGE_MEDIENPOOL_MENU', $subline,
  array(
    'subpage' => $subpage,
  )
);

$title = $I18N->msg('pool_media');
rex_title($title, $subline);

// -------------- Messages
if ($info != '')
{
  echo rex_info($info);
  $info = '';
}
if ($warning != '')
{
  echo rex_warning($warning);
  $warning = '';
}

// -------------- Include Page
switch($subpage)
{
  case 'add_file'  : $file = 'medienpool.upload.inc.php'; break;
  case 'categories': $file = 'medienpool.structure.inc.php'; break;
  case 'sync'      : $file = 'medienpool.sync.inc.php'; break;
  default          : $file = 'medienpool.medien.inc.php'; break;
}

require $REX['INCLUDE_PATH'].'/pages/'.$file;