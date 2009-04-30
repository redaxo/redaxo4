<?php

/**
 * COM - Plugin - Newsletter
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

/*
	TODOS: Felder hinzufügen

	- newsletter
	- last_newsletter_id
	
	function zum abgleich einbauen


*/

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['newsletter'] = $error;
else
  $REX['ADDON']['install']['newsletter'] = true;

?>