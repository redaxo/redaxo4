<?php
/**
 * Branded ein Bild mit einem Wasserzeichen
 *
 * Der Filter sucht im Verzeichnis addons/image_resize/media/
 * nach einem Bild mit dem Dateinamen "brand.*" und verwendet den 1. Treffer
 */
function image_resize_brand(& $src_im)
{
  global $REX;

  $files = glob($REX['INCLUDE_PATH'] . '/addons/image_resize/media/brand.*');
  $brandImage = $files[0];
  $brand = new rex_thumbnail($brandImage);

  $paddX = 10;
  $paddY = 10;

  imagealphablending($src_im, true);
  imagecopy($src_im, $brand->getImage(), imagesx($src_im) - $brand->getImageWidth() - $paddX,  imagesy($src_im) - $brand->getImageHeight() - $paddY, 0, 0, $brand->getImageWidth(), $brand->getImageHeight());

  $brand->destroyImage();
}
?>