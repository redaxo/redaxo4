<?php

/**
 * COM - Plugin - guestbook
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$sql = new rex_sql();
$sql->setQuery("DROP TABLE `rex_com_guestbook`");
$sql->setQuery('SHOW TABLE STATUS LIKE "rex_com_guestbook"');

if ($sql->getRows() == 1)
{
	$REX['ADDON']['plugins']['community']['guestbook']['install'] = 1;
	$REX['ADDON']['plugins']['community']['guestbook']['installmsg'] = 'Tabelle "rex_com_guestbook" konnte nicht gelöscht werden';
}else
{
	$REX['ADDON']['plugins']['community']['guestbook']['install'] = 0;
}

?>