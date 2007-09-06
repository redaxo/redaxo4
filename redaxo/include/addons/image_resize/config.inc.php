<?php

/**
 * Image-Resize Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author <a href="http://www.vscope.at">www.vscope.at</a>
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 *
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 *
 * @package redaxo3
 * @version $Id$
 */

$mypage = 'image_resize';

$REX['ADDON']['rxid'][$mypage] = 'REX_IMAGE_RESIZE';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'Image Resize';
$REX['ADDON']['perm'][$mypage] = 'image_resize[]';
$REX['ADDON']['max_size'][$mypage] = 1000;
$REX['ADDON']['jpeg_quality'][$mypage] = 75;
$REX['ADDON']['system'][$mypage] = TRUE;
$REX['ADDON']['version'][$mypage] = "1.0";
$REX['ADDON']['author'][$mypage] = "Wolfgang Hutteger, Markus Staab, Jan Kristinus";
// $REX['ADDON']['supportpage'][$mypage] = "";

$REX['PERM'][] = 'image_resize[]';

if ($REX['GG'])
{
  require $REX['INCLUDE_PATH'].'/addons/image_resize/extensions/extension_wysiwyg.inc.php';
}

// Resize Script
$rex_resize = rex_get('rex_resize', 'string');
if ($rex_resize != '')
{
	// Lösche alle Ausgaben zuvor
	while(ob_get_level())
	  ob_end_clean();

  // get params
  ereg('^([0-9]*)([awhc])__(([0-9]*)h__)?(.*)', $rex_resize, $resize);

  $size = $resize[1];
  $mode = $resize[2];
  $hmode = $resize[4];
  $imagefile = $resize[5];
  $rex_filter = rex_get('rex_filter', 'array');
  $filters = "";
	foreach($rex_filter as $filter)
	{
		$filters .= $filter;
	}


  $cachepath = $REX['INCLUDE_PATH'].'/generated/files/'. $REX['TEMP_PREFIX'] .'cache_resize___'.$filters.$rex_resize;
  $imagepath = $REX['HTDOCS_PATH'].'files/'.$imagefile;

  // check for cache file
  if (file_exists($cachepath))
  {
    // time of cache
    $cachetime = filectime($cachepath);

    // file exists?
    if (file_exists($imagepath))
    {
      $filetime = filectime($imagepath);
    }
    else
    {
      // image file not exists
      print 'Error: Imagefile does not exist - '. $imagefile;
      exit;
    }

    // cache is newer? - show cache
    if ($cachetime > $filetime)
    {
      include ($REX['HTDOCS_PATH'].'redaxo/include/addons/image_resize/classes/class.thumbnail.inc.php');
      $thumb = new thumbnail($cachepath);
      $thumb->send($cachepath, $cachetime);
      exit;
    }

  }

  // check params
  if (!file_exists($imagepath))
  {
    print 'Error: Imagefile does not exist - '. $imagefile;
    exit;
  }

  if (($mode != 'w') and ($mode != 'h') and ($mode != 'a')and ($mode != 'c'))
  {
    print 'Error wrong mode - only h,w,a,c';
    exit;
  }
  if ($size == '')
  {
    print 'Error size is no INTEGER';
    exit;
  }
  if ($size > $REX['ADDON']['max_size'][$mypage])
  {
    print 'Error size to big: max '.$REX['ADDON']['max_size'][$mypage].' px';
    exit;
  }

  include ($REX['INCLUDE_PATH'].'/addons/image_resize/classes/class.thumbnail.inc.php');

  // start thumb class
  $thumb = new thumbnail($imagepath);

  // check method
  if ($mode == 'w')
  {
    $thumb->size_width($size);
  }
  if ($mode == 'h')
  {
    $thumb->size_height($size);
  }
  
  if ($mode == 'c')
  {
    $thumb->size_crop($size, $hmode);
  }
  elseif ($hmode != '')
  {
    $thumb->size_height($hmode);
  }
  
  if ($mode == 'a')
  {
    $thumb->size_auto($size);
  }
  
  foreach($rex_filter as $filter)
  {
    $thumb->addFilter($filter);
  }

  // jpeg quality
  $thumb->jpeg_quality($REX['ADDON']['jpeg_quality'][$mypage]);

  // save cache
  $thumb->generateImage($cachepath);
  exit ();
}
?>