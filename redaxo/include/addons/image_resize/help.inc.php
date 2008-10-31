<?php
/**
 * Image-Resize Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author <a href="http://www.vscope.at">www.vscope.at</a>
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 *
 * @package redaxo4
 * @version $Id: help.inc.php,v 1.12 2008/04/12 08:04:07 kills Exp $
 */
?>
<h3>Features:</h3>

<p>Makes resize of images on the fly, with extra cache of resized images so performance loss is extremly small.</p>

<h3>Usage:</h3>
<p>call an image that way <b>index.php?rex_resize=100w__imagefile</b>
 to resize the imagefile to a width of 100px</p>

<h3>Methods:</h3>
<p>
w = width       (max width)<br />
h = height      (max height)<br />
c = crop        (cut image part to certain length and height)<br />
a = automatic   (longest side will be used)
</p>

<h3>Filters:</h3>
<p>
blur<br />
brand<br />
sepia<br />
sharpen
</p>

<h3>Examples:</h3>
<p>
resize image to a length of 100px and calculate heigt to match ratio<br />
<b>index.php?rex_resize=100w__imagefile</b>

<br /><br />
resize image to a height of 150px and calculate width to match ratio<br />
<b>index.php?rex_resize=150h__imagefile</b>

<br /><br />
resize image on the longest side to 200px and calculate the other side to match ratio<br />
<b>index.php?rex_resize=200a__imagefile</b>

<br /><br />
resize image to a width of 100px and a heigt of 200px<br />
<b>index.php?rex_resize=100w__200h__imagefile</b>

<br /><br />
resize inner image part to a width of 100px and a height of 200px<br />
<b>index.php?rex_resize=100c__200h__imagefile</b>

<br /><br />
resize inner image part to a width of 100px and a height of 200px with an offset of 50px<br />
<b>index.php?rex_resize=100c__200h__50o__imagefile</b>

<br /><br />
resize inner image part to a width of 100px and a height of 200px with an offset of -150px<br />
<b>index.php?rex_resize=100c__200h__-150o__imagefile</b>

<br /><br />
add filter/s: here blur and sepia<br />
<b>index.php?rex_resize=200a__imagefile&amp;rex_filter[]=blur&amp;rex_filter[]=sepia</b>

</p>