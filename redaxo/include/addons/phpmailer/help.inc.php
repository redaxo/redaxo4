<?php

/**
 * PHPMailer Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 *
 * @package redaxo4
 * @version $Id$
 */

?>
<p>
PHPMailer Addon

<br /><br />

<?php
  $file = dirname( __FILE__) .'/_changelog.txt';
  if(is_readable($file))
    echo str_replace( '+', '&nbsp;&nbsp;+', nl2br(file_get_contents($file)));
?>
</p>