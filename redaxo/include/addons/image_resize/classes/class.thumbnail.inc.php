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
 * @package redaxo4
 * @version $Id$
 */

class thumbnail
{

  var $img;
  var $gifsupport;
  var $imgfile;
  var $filters;
  var $warning_image;

  function thumbnail($imgfile)
  {
    // ----- imagepfad speichern
    $this->imgfile = $imgfile;

    // ----- gif support ?
    $this->gifsupport = false;
    if (function_exists('imageGIF'))
      $this->gifsupport = true;

    // ----- detect image format
    $this->img['format'] = ereg_replace('.*\.(.*)$', '\\1', $imgfile);
    $this->img['format'] = strtoupper($this->img['format']);
    if (!eregi('cache/', $imgfile))
    {
      if ($this->img['format'] == 'JPG' || $this->img['format'] == 'JPEG')
      {
        // --- JPEG
        $this->img['format'] = 'JPEG';
        $this->img['src'] = @ImageCreateFromJPEG($imgfile);
      }elseif ($this->img['format'] == 'PNG')
      {
        // --- PNG
        $this->img['src'] = @ImageCreateFromPNG($imgfile);
      }elseif ($this->img['format'] == 'GIF')
      {
        // --- GIF
        if ($this->gifsupport)
          $this->img['src'] = @ImageCreateFromGIF($imgfile);
      }elseif ($this->img['format'] == 'WBMP')
      {
        // --- WBMP
        $this->img['src'] = @ImageCreateFromWBMP($imgfile);
      }

      // ggf error image senden
      if (!$this->img['src'])
      {
        global $REX;
				$file = $REX['INCLUDE_PATH'].'/addons/image_resize/media/warning.jpg';
        header('Content-Type: image/JPG');
		    // error image nicht cachen
		    header('Cache-Control: false');
		    readfile($file);
        exit ();
      }

      $this->img['width'] = imagesx($this->img['src']);
      $this->img['height'] = imagesy($this->img['src']);
      $this->img['width_offset_thumb'] = 0;
      $this->img['height_offset_thumb'] = 0;

      // --- default quality jpeg
      $this->img['quality'] = 75;
      $this->filters = array();
    }
  }

  function showWarning()
  {

  }

  function size_height($size)
  {
    // --- height
    $this->img['height_thumb'] = $size;
    //if ($this->img['width_thumb'] == 0)
    //{
      $this->img['width_thumb'] = ($this->img['height_thumb'] / $this->img['height']) * $this->img['width'];
    //}
  }

  function size_width($size)
  {
    // --- width
    $this->img['width_thumb'] = $size;
    $this->img['height_thumb'] = ($this->img['width_thumb'] / $this->img['width']) * $this->img['height'];
  }

  function size_auto($size)
  {
    // --- size
    if ($this->img['width'] >= $this->img['height'])
    {
      $this->size_width($size);
      // $this->img['width_thumb'] = $size;
      // $this->img['height_thumb'] = ($this->img['width_thumb'] / $this->img['width']) * $this->img['height'];
    }
    else
    {
      $this->size_height($size);
      // $this->img['height_thumb'] = $size;
      // $this->img['width_thumb'] = ($this->img['height_thumb'] / $this->img['height']) * $this->img['width'];
    }
  }

  // Ausschnitt aus dem Bild auf bestimmte größe zuschneiden
  function size_crop($width, $height)
  {
    $this->img['width_thumb'] = $width;
    $this->img['height_thumb'] = $height;

    $width_ratio = $this->img['width'] / $this->img['width_thumb'];
    $height_ratio = $this->img['height'] / $this->img['height_thumb'];

    // Es muss an der Breite beschnitten werden
    if ($width_ratio > $height_ratio)
    {
      // $_DST['offset_w'] = round(($this->img['width']-$this->img['width_thumb']*$height_ratio)/2);
      $this->img['width_offset_thumb'] = round(($this->img['width'] - $this->img['width_thumb'] * $height_ratio) / 2);
      $this->img['width'] = round($this->img['width_thumb'] * $height_ratio);
    }
    // es muss an der Höhe beschnitten werden
    elseif ($width_ratio < $height_ratio)
    {
      // $_DST['offset_h'] = round(($this->img['height']-$this->img['height_thumb']*$width_ratio)/2);
      $this->img['height_offset_thumb'] = round(($this->img['height'] - $this->img['height_thumb'] * $width_ratio) / 2);
      $this->img['height'] = round($this->img['height_thumb'] * $width_ratio);
    }

  }

  function jpeg_quality($quality = 85)
  {
    // --- jpeg quality
    $this->img['quality'] = $quality;
  }

  function resampleImage()
  {
    if (function_exists('ImageCreateTrueColor'))
    {
      $this->img['des'] = ImageCreateTrueColor($this->img['width_thumb'], $this->img['height_thumb']);
    }
    else
    {
      $this->img['des'] = ImageCreate($this->img['width_thumb'], $this->img['height_thumb']);
    }
    // Transparenz erhalten
    if ($this->img['format'] == 'PNG')
    {
      imagealphablending($this->img['des'], false);
      imagesavealpha($this->img['des'], true);
    }
    imagecopyresampled($this->img['des'], $this->img['src'], 0, 0, $this->img['width_offset_thumb'], $this->img['height_offset_thumb'], $this->img['width_thumb'], $this->img['height_thumb'], $this->img['width'], $this->img['height']);
  }

  function generateImage($save = '', $show = true)
  {
    if ($this->img['format'] == 'GIF' && !$this->gifsupport)
    {
      // --- kein caching -> gif ausgeben
      $this->send();
    }

    $this->resampleImage();
    $this->applyFilters();

    if ($this->img['format'] == 'JPG' || $this->img['format'] == 'JPEG')
    {
      imageJPEG($this->img['des'], $save, $this->img['quality']);
    }
    elseif ($this->img['format'] == 'PNG')
    {
      imagePNG($this->img['des'], $save);
    }
    elseif ($this->img['format'] == 'GIF')
    {
      imageGIF($this->img['des'], $save);
    }
    elseif ($this->img['format'] == 'WBMP')
    {
      imageWBMP($this->img['des'], $save);
    }

    if ($show)
    {
      $this->send($save);
    }
  }

  function send($file = null, $lastModified = null)
  {
    if (!$file)
      $file = $this->imgfile;
    if (!$lastModified)
      $lastModified = time();

    $lastModified = gmdate('r', $lastModified);

    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastModified)
    {
      header('HTTP/1.1 304 Not Modified');
      exit();
    }

    header('Content-Type: image/' . $this->img['format']);
    header('Last-Modified: ' . $lastModified);
    // caching clientseitig/proxieseitig erlauben
    header('Cache-Control: public');
    readfile($file);
  }

  function addFilter($filter)
  {
  	if ($filter == "") return;
    $this->filters[] = $filter;
  }

  function applyFilters()
  {
  	global $REX;
  	foreach($this->filters as $filter)
  	{
  		$file = $REX['INCLUDE_PATH'].'/addons/image_resize/filters/filter.'.$filter.'.inc.php';
  		if (file_exists($file)) require_once($file);
  		$fname = 'image_resize_'.$filter;
  		if (function_exists($fname))
  		{
  			$fname($this->img['des']);
  		}
  	}
  }

  // deleteCache
  function deleteCache($filename = '')
  {
  	global $REX;

  	require_once $REX['INCLUDE_PATH'] . '/addons/image_resize/functions/function_folder.inc.php';

	  $folders = array();
    $folders[] = $REX['INCLUDE_PATH'] . '/generated/files/';
    $folders[] = $REX['HTDOCS_PATH'] . 'files/';

  	$c = 0;
    foreach($folders as $folder)
    {
  	  $files = readFolderFiles($folder);
  	  if (is_array($files))
  	  {
  	    foreach ($files as $var)
  	    {
  	      if (eregi('^image_resize__', $var))
  	      {
  	      	if ($filename == '' || $filename != '' && $filename == substr($var,strlen($filename) * -1))
  	      	{
  	      		unlink($folder . $var);
  	      		$c++;
  	      	}
  	      }
  	    }
  	  }
    }

	  return $c;
  }

}
?>