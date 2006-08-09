<?php
include $REX['INCLUDE_PATH']."/layout/top.php";
?>
<style type="text/css">
pre {padding:10px;font-size:12px;overflow:hidden;border:1px solid #CCCCCC;margin:5px;background-color:#FFFFFF;}
</style>
<table border="0" cellpadding="5" cellspacing="1" width="770">
  <tbody>
  <tr>
    <th colspan="2" align="left">TinyMCE2</th>
  </tr>
  
  <tr>
<td class="grey">
TinyMCE 2.0.6.1 installiert
<br />
<h2>Moduleingabe Einfach</h2>
<pre>
&lt;?php
//TinyMCE for Redaxo- dh@gn2-netwerk.de v0.03
include_once $REX['INCLUDE_PATH'].'/addons/tinymce/classes/class.tiny.inc.php';

$TINY2[0]=new tiny2editor();
$TINY2[0]->content="REX_VALUE[1]";
$TINY2[0]->show();
<?php echo "?>";?>
</pre>

<h2>Moduleingabe Erweitert</h2>
<pre>
&lt;?php
//TinyMCE for Redaxo- dh@gn2-netwerk.de v0.03
include_once $REX['INCLUDE_PATH'].'/addons/tinymce/classes/class.tiny.inc.php';

$TINY2[0]=new tiny2editor();
$TINY2[0]->content="REX_VALUE[1]";
$TINY2[0]->disable="link,image,advimage,justifyleft,justifycenter,justifyright,justifyfull,indent,outdent,sub,sup,separator,help,visualaid,anchor";
$TINY2[0]->plugins="emotions,iespell,table,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen";
$TINY2[0]->validhtml="marquee[border|class|style|width]b[border|class|style]img[class|style|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]";
$TINY2[0]->show();
<?php echo "?>";?>
</pre>

<h2>Modulausgabe (Alle)</h2>
<pre>
&lt;div class="section"&gt;
&lt;?php
$data =&lt;&lt;&lt;EOD
REX_HTML_VALUE[1]
EOD;
<?php echo "?>";?>
&lt;?php
$content=$data;
if ($REX['REDAXO']) {
	$content=str_replace('src="files/','src="../files/',$content);
	echo '&lt;link rel="stylesheet" type="text/css" href="../files/tinymce/content.css" /&gt;';
}
echo $content;
<?php echo "?>\n";?>
&lt;/div&gt;
</pre>



<a href="http://www.gn2-netwerk.de">GN2-Netwerk</a>
</td>
  </tr>
</tbody>
</table>

<?php

include $REX['INCLUDE_PATH']."/layout/bottom.php";
ob_end_flush();
?>