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
?>
<h3>Version: 0.3</h1>

<h3>Features:</h3>

<p>Makes resize of images on the fly, with extra cache of resized images so performance loss is extremly small.</p>

<h3>Usage:</h3>
<p>call an image that way <b>index.php?rex_resize=100w__imagefile</b>
= to resize the imagefile to width = 100</p>

<h3>Methods:</h3>
<p>
w = width       (max width)<br />
h = height      (max height)<br />
c = crop        (cut image part to certain length and height)<br />
a = automatic   (longest side will be used)
</p>

<h3>Default-Filters:</h3>
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
resize inner image part to a width of 100px and a heigt of 200px<br />
<b>index.php?rex_resize=100c__200h__imagefile</b>

<br /><br />
add filter/s: here blur and sepia<br />
<b>index.php?rex_resize=200a__imagefile&rex_filter[]=blur&rex_filter[]=sepia</b>

</p>