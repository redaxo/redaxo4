<?php

/**
 * TinyMCE Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 *
 * @package redaxo4
 * @version $Id: help.inc.php,v 1.3 2008/03/11 16:04:53 kills Exp $
 */

?>
<p>
Erweitert REDAXO um den WYSIWYG-Editor, TinyMCE
<br /><br />

<?php
  $file = dirname( __FILE__) .'/_changelog.txt';
  if(is_readable($file))
    echo str_replace( '+', '&nbsp;&nbsp;+', nl2br(file_get_contents($file)));
?>
</p>