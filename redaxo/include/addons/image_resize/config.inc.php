<?php

################################################################################
#
# imageResize Addon 0.2
# code by vscope new media - www.vscope.at - office@vscope.at
#
################################################################################
#
# Features:
#
# Makes resize of images on the fly, with extra cache of resized images so
# performance loss is extremly small.
#
# Usage:
#
# call an image that way index.php?rex_resize=100w__imagefile
# = to resize the imagefile to width = 100
# other methods: w = width h=height a=automatic
# important: gif files are cached as jpegs
#
# Changelog:
#
# version 0.2 made addon
# version 0.1 plugin first release
#
################################################################################

$mypage = "image_resize";

$REX['ADDON']['rxid'][$mypage] = "REX_IMAGE_RESIZE";
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = "Image Resize Addon";
$REX['ADDON']['perm'][$mypage] = "image_resize[]";
$REX['ADDON']['max_size'][$mypage] = 1000;
$REX['ADDON']['jpeg_quality'][$mypage] = 75;

$REX['PERM'][] = "image_resize[]";

if ($REX['GG'])
{
  rex_register_extension('OUTPUT_FILTER', 'rex_resize_wysiwyg_output');

  // Resize WYSIWYG Editor Images
  function rex_resize_wysiwyg_output($params)
  {
    global $REX;

    $content = $params['subject'];

    preg_match_all('/<img[^>]*ismap="rex_resize"[^>]*>/imsU', $content, $matches);

    if (is_array($matches[0]))
    {
      foreach ($matches[0] as $var)
      {
        preg_match('/width="(.*)"/imsU', $var, $width);
        if (!$width)
        {
          preg_match('/width: (.*)px/imsU', $var, $width);
        }
        preg_match('/height="(.*)"/imsU', $var, $height);
        if (!$height)
        {
          preg_match('/height: (.*)px/imsU', $var, $height);
        }
        if ($width)
        {
          preg_match('/src="(.*files\/(.*))"/imsU', $var, $src);
          if (file_exists($REX['HTDOCS_PATH'].'files/'.$src[2]))
          {
            $realsize = getimagesize($REX['HTDOCS_PATH'].'files/'.$src[2]);
            if (($realsize[0] != $width[1]) or ($realsize[1] != $height[1]))
            {
              $newsrc = "index.php?rex_resize=".$width[1]."w__".$height[1]."h__".$src[2];
              $newimage = str_replace($src[1], $newsrc, $var);
              $content = str_replace($var, $newimage, $content);
            }
          }
        }
      }
    }
    return $content;
  }

}

// Resize Script für das Frontend
if ((isset ($REX['REDAXO']) and $REX['REDAXO'] === false) && (isset ($_GET['rex_resize']) and $_GET['rex_resize'] != ''))
{
  $rex_resize = $_GET['rex_resize'];

  // get params
  ereg("^([0-9]*)([awh])__(([0-9]*)h__)?(.*)", $rex_resize, $resize);

  $size = $resize[1];
  $mode = $resize[2];
  $hmode = $resize[4];
  $imagefile = $resize[5];

  $cachepath = $REX['HTDOCS_PATH'].'files/cache_resize___'.$rex_resize;
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
      print "Error: Imagefile does not exist - $imagefile";
      exit;
    }

    // cache is newer? - show cache
    if ($cachetime > $filetime)
    {
      include ($REX['HTDOCS_PATH']."redaxo/include/addons/image_resize/class.thumbnail.inc.php");
      $thumb = new thumbnail($cachepath);
      @ header("Content-Type: image/".$thumb->img["format"]);
      readfile($cachepath);
      exit;
    }

  }

  // check params
  if (!file_exists($imagepath))
  {
    print "Error: Imagefile does not exist - $imagefile";
    exit;
  }

  if (($mode != 'w') and ($mode != 'h') and ($mode != 'a'))
  {
    print "Error wrong mode - only h,w,a";
    exit;
  }
  if ($size == '')
  {
    print "Error size is no INTEGER";
    exit;
  }
  if ($size > $REX['ADDON']['max_size'][$mypage])
  {
    print "Error size to big: max ".$REX['ADDON']['max_size'][$mypage]." px";
    exit;
  }

  include ($REX['HTDOCS_PATH']."redaxo/include/addons/image_resize/class.thumbnail.inc.php");

  // start thumb class
  $thumb = new thumbnail($imagepath);

  // check method
  if ($mode == "w")
  {
    $thumb->size_width($size);
  }
  if ($mode == "h")
  {
    $thumb->size_height($size);
  }
  if ($hmode != '')
  {
    $thumb->size_height($hmode);
  }
  if ($mode == "a")
  {
    $thumb->size_auto($size);
  }

  // jpeg quality
  $thumb->jpeg_quality($REX['ADDON']['jpeg_quality'][$mypage]);

  // save cache
  $thumb->generateImage($cachepath);
  exit ();
}
?>