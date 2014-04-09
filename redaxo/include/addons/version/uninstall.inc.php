<?php

/**
 * Version
 *
 * @author jan@kristinus.de
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$REX['ADDON']['install']['version'] = 0;

$d = rex_sql::factory();
$d->setQuery('delete from ' . $REX['TABLE_PREFIX'] . 'article_slice where revision=1');

// ERRMSG IN CASE: $REX['ADDON']['installmsg']['version'] = "Leider konnte nichts installiert werden da.";
