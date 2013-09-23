<?php
/**
 * image_manager Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author <a href="http://www.vscope.at">www.vscope.at</a>
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */
?>
<h3>Funktionen:</h3>

<p>Addon zum generieren von Grafiken anhand von Bildtypen.</p>

<h3>Benutzung:</h3>
<p>
Die Bildtypen werden in der Verwaltung des Addons erstellt und konfiguriert.
Jeder Bildtyp kann beliebig viele Effekte enthalten, die auf das aktuelle Bild angewendet werden.

Zum einbinden eines Bildes muss dazu der Bildtyp in der Url notiert werden.
</p>

<h3>Anwendungsbeispiele:</h3>
<p>
  <?php echo $REX["FRONTEND_FILE"]; ?>?rex_img_type=ImgTypeName&rex_img_file=ImageFileName
</p>


<h3>Version 1.0:</h3>
<p>
  Neue Filter eingebaut: mirror und workspace
  Anpassung von resize
</p>

<h3>Verwendung des HTTP_IF_MODIFIED_SINCE-Headers</h3>
<p>
Damit Bilder, die sich beim Nutzer seit dem letzten Seitenaufruf nicht geändert haben, nicht ständig neu gesendet werden, kann bei entsprechender Browserunterstützung ein "304 Not Modified"-Header gesendet werden.
Falls der HTTP_IF_MODIFIED_SINCE-Header nicht gesetzt sein sollte, folgende Zeilen ganz am Ende in die oberste htaccess-Datei eintragen:
<pre>RewriteRule .* - [E=HTTP_IF_MODIFIED_SINCE:%{HTTP:If-Modified-Since}]
RewriteRule .* - [E=HTTP_IF_NONE_MATCH:%{HTTP:If-None-Match}]</pre>