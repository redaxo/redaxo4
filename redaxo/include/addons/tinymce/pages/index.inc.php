<?php

include $REX['INCLUDE_PATH']."/layout/top.php";

$subline = '
<ul>
  <li><a href="http://tinymce.moxiecode.com/tinymce/docs/index.html" target="_blank">Zur Dokumentation</a></li>
  <li>&nbsp;<a href="http://tinymce.moxiecode.com/tinymce/docs/reference_plugins.html" target="_blank">Zur Plugin Liste</a></li>
</ul>
';

rex_title('TinyMCE', $subline);
?>

<p>

<h2>Moduleingabe Einfach</h2>

<pre>
&lt;?php
$editor=new tiny2editor();
$editor->id=1;
$editor->content="REX_VALUE[1]";
$editor->show();
?&gt;
</pre>

<br />
<h2>Moduleingabe Erweitert (mehrere Editoren in einem Modul)</h2>

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
?&gt;
</pre>

<br />
<h2>Modulausgabe (Alle)</h2>

<pre>
&lt;div class="section"&gt;
&lt;?php
$content =&lt;&lt;&lt;EOD
REX_HTML_VALUE[1]
EOD;

if ($REX['REDAXO']) {
  $content=str_replace('src="files/','src="../files/',$content);
  echo '&lt;link rel="stylesheet" type="text/css" href="../files/tinymce/content.css" /&gt;';
}
echo $content;
?&gt;
&lt;/div&gt;
</pre>

<br />

<a href="http://www.gn2-netwerk.de">GN2-Netwerk</a>

</p>

<?php

include $REX['INCLUDE_PATH']."/layout/bottom.php";

?>