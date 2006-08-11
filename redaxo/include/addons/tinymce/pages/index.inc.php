<?php
include $REX['INCLUDE_PATH']."/layout/top.php";
?>
<style type="text/css">
pre {padding:10px;font-size:12px;overflow:hidden;border:1px solid #CCCCCC;margin:5px;background-color:#FFFFFF;}
</style>
<table border="0" cellpadding="5" cellspacing="1" width="770">
  <tbody>
  <tr>
    <th colspan="2" align="left"><?=$FL['title'];?></th>
  </tr>

  <tr>
<td class="grey">
<a href="http://tinymce.moxiecode.com/tinymce/docs/index.html">TinyMCE 2.0.6.1 Dokumentation</a>
<br />
<h2>Moduleingabe Einfach</h2>
<pre>
&lt;?php
$editor=new tiny2editor();
$editor->id=1;
$editor->content="REX_VALUE[1]";
$editor->show();
<?php echo "?>";?>
</pre>

<h2>Moduleingabe Erweitert (mehrere Editoren in einem Modul)</h2>
<a href="http://tinymce.moxiecode.com/tinymce/docs/reference_plugins.html" target="_blank">TinyMCE Plugin Liste</a>
<pre>
&lt;?php
$editor1=new tiny2editor();
$editor1->id=1;
$editor1->content="REX_VALUE[1]";
$editor1->editorCSS = "../files/tinymce/content.css";
$editor1->disable="justifyleft,justifycenter,justifyright,justifyfull";
$editor1->buttons3="tablecontrols,separator,search,replace,separator,print";
$editor1->add_validhtml="img[myspecialtag]";
$editor1->show();

$editor2=new tiny2editor();
$editor2->id=2;
$editor2->content="REX_VALUE[2]";
$editor2->show();
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