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
 * @package redaxo3
 * @version $Id$
 */

class thumbnail
{

  var $img;
  var $gifsupport;
  var $imgfile;
  var $filters;

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
        $this->img['src'] = ImageCreateFromJPEG($imgfile);

      }
      elseif ($this->img['format'] == 'PNG')
      {
        // --- PNG
        $this->img['format'] = 'PNG';
        $this->img['src'] = ImageCreateFromPNG($imgfile);

      }
      elseif ($this->img['format'] == 'GIF')
      {
        // --- GIF
        $this->img['format'] = 'GIF';
        if ($this->gifsupport)
          $this->img['src'] = ImageCreateFromGIF($imgfile);

      }
      elseif ($this->img['format'] == 'WBMP')
      {
        // --- WBMP
        $this->img['format'] = 'WBMP';
        $this->img['src'] = ImageCreateFromWBMP($imgfile);

      }
      else
      {
        // --- DEFAULT
        echo 'Not Supported File';
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
      //      $this->img['width_thumb'] = $size;
      //      $this->img['height_thumb'] = ($this->img['width_thumb'] / $this->img['width']) * $this->img['height'];
    }
    else
    {
      $this->size_height($size);
      //      $this->img['height_thumb'] = $size;
      //      $this->img['width_thumb'] = ($this->img['height_thumb'] / $this->img['height']) * $this->img['width'];
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
      //      $_DST['offset_w'] = round(($this->img['width']-$this->img['width_thumb']*$height_ratio)/2);
      $this->img['width_offset_thumb'] = round(($this->img['width'] - $this->img['width_thumb'] * $height_ratio) / 2);
      $this->img['width'] = round($this->img['width_thumb'] * $height_ratio);
    }
    // es muss an der Höhe beschnitten werden
    elseif ($width_ratio < $height_ratio)
    {
      //      $_DST['offset_h'] = round(($this->img['height']-$this->img['height_thumb']*$width_ratio)/2);
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
    $this->filters[] = $filter;
  }

  function applyFilters()
  {
    if(in_array('blur', $this->filters))
    {
       $this->UnsharpMask($this->img['des'], 80, .5, 3);
    }
  }

  // Übernommen von cerdmann.com
  // Unsharp mask algorithm by Torstein Hønsi 2003 (thoensi_at_netcom_dot_no)
  // Christoph Erdmann: changed it a little, cause i could not reproduce the darker blurred image, now it is up to 15% faster with same results
  function UnsharpMask($img, $amount, $radius, $threshold)
  {
    // Attempt to calibrate the parameters to Photoshop:
    if ($amount > 500)
      $amount = 500;
    $amount = $amount * 0.016;
    if ($radius > 50)
      $radius = 50;
    $radius = $radius * 2;
    if ($threshold > 255)
      $threshold = 255;

    $radius = abs(round($radius)); // Only integers make sense.
    if ($radius == 0)
    {
      return $img;
      imagedestroy($img);
      break;
    }
    $w = imagesx($img);
    $h = imagesy($img);
    $imgCanvas = $img;
    $imgCanvas2 = $img;
    $imgBlur = imagecreatetruecolor($w, $h);

    // Gaussian blur matrix:
    //  1 2 1
    //  2 4 2
    //  1 2 1

    // Move copies of the image around one pixel at the time and merge them with weight
    // according to the matrix. The same matrix is simply repeated for higher radii.
    for ($i = 0; $i < $radius; $i++)
    {
      imagecopy($imgBlur, $imgCanvas, 0, 0, 1, 1, $w -1, $h -1); // up left
      imagecopymerge($imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50); // down right
      imagecopymerge($imgBlur, $imgCanvas, 0, 1, 1, 0, $w -1, $h, 33.33333); // down left
      imagecopymerge($imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h -1, 25); // up right
      imagecopymerge($imgBlur, $imgCanvas, 0, 0, 1, 0, $w -1, $h, 33.33333); // left
      imagecopymerge($imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25); // right
      imagecopymerge($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h -1, 20); // up
      imagecopymerge($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667); // down
      imagecopymerge($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50); // center
    }
    $imgCanvas = $imgBlur;

    // Calculate the difference between the blurred pixels and the original
    // and set the pixels
    for ($x = 0; $x < $w; $x++)
    { // each row
      for ($y = 0; $y < $h; $y++)
      { // each pixel
        $rgbOrig = ImageColorAt($imgCanvas2, $x, $y);
        $rOrig = (($rgbOrig >> 16) & 0xFF);
        $gOrig = (($rgbOrig >> 8) & 0xFF);
        $bOrig = ($rgbOrig & 0xFF);
        $rgbBlur = ImageColorAt($imgCanvas, $x, $y);
        $rBlur = (($rgbBlur >> 16) & 0xFF);
        $gBlur = (($rgbBlur >> 8) & 0xFF);
        $bBlur = ($rgbBlur & 0xFF);

        // When the masked pixels differ less from the original
        // than the threshold specifies, they are set to their original value.
        $rNew = (abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig;
        $gNew = (abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig;
        $bNew = (abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig;

        if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew))
        {
          $pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
          ImageSetPixel($img, $x, $y, $pixCol);
        }
      }
    }
    return $img;
  }
}
?>