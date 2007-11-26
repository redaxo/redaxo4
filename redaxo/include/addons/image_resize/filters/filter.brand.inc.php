<?php
function image_resize_brand(& $src_im)
{
  global $REX;

  $brandImage = $REX['INCLUDE_PATH'] . '/addons/image_resize/media/brand.gif';
  $brand = new thumbnail($brandImage);

  $paddX = 10;
  $paddY = 10;

  imagealphablending($src_im, true);
  imagecopy($src_im, $brand->getImage(), imagesx($src_im) - $brand->getImageWidth() - $paddX,  imagesy($src_im) - $brand->getImageHeight() - $paddY, 0, 0, $brand->getImageWidth(), $brand->getImageHeight());

  $brand->destroyImage();
}
?>