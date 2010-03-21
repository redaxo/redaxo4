<?php

/**
 * editme
 *
 * @author jan@kristinus.de
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$error = '';

$tables = rex_em_getTables();

$sql = rex_sql::factory();
foreach($tables as $table)
{
  $sql->setQuery('DROP TABLE IF EXISTS `'. $REX['TABLE_PREFIX'].'em_data_'. $table['name'] .'`;');
  
  if($sql->hasError())
  {
    $error .= 'MySQL Error '. $sql->getErrno() .': '. $sql->getError();
    break;
  }
}

if($error == '')
{
  $REX['ADDON']['install']['editme'] = 0;
}
else
{
   $REX['ADDON']['installmsg']['editme'] = $error;
}
