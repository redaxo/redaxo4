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
?>
<pre>
################################################################################
#
# imageResize Addon 0.2
# code by vscope new media - www.vscope.at - office@vscope.at
#
################################################################################
#
# Features:
#
#   Makes resize of images on the fly, with extra cache of resized images so
#   performance loss is extremly small.
#
#
# Usage:
#
#   call an image that way index.php?rex_resize=100w__imagefile
#   = to resize the imagefile to width = 100
#
#   Methods: 
#      w = width       (max width)
#      h = height      (max height)
#      c = crop        (cut image part to certain length and height)
#      a = automatic   (longest side will be used)
#
#
# Examples:
#
#   resize image to a length of 100px and calculate heigt to match ratio
#   index.php?rex_resize=100w__imagefile
#
#   resize image to a height of 150px and calculate width to match ratio
#   index.php?rex_resize=150h__imagefile
#
#   resize image on the longest side to 200px and calculate the other side to match ratio
#   index.php?rex_resize=200a__imagefile
#
#   resize image to a width of 100px and a heigt of 200px
#   index.php?rex_resize=100w__200h__imagefile
#
#   resize inner image part to a width of 100px and a heigt of 200px
#   index.php?rex_resize=100c__200h__imagefile
#
# Changelog:
#
# version 0.3 added cropping, UnsharpMask (kills)
# version 0.2 made addon (vscope)
# version 0.1 plugin first release (vscope)
#
################################################################################
</pre>