<?php

/**
 * Userinfo Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

// zuletzt bearbeitete module
// zuletzt bearbeitete templates
// zuletzt bearbeitete artikel (metainfos, content, status, version-addon)
// zuletzt bearbeitete medien
// zuletzt bearbeitete editMe Datenmodelle
// zuletzt gelaufene cronjobs
// statistik

function rex_a659_statistics()
{
  global $REX;
  
  $stats = array();
  $stats['last_update'] = 0;
  
  $sql = rex_sql::factory();
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'article WHERE clang=0 ORDER BY updatedate desc');
  $stats['total_articles'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'article_slice');
  $stats['total_slices'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count FROM '. $REX['TABLE_PREFIX'] .'clang');
  $stats['total_clangs'] = $result[0]['count'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'template');
  $stats['total_templates'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'module');
  $stats['total_modules'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'action');
  $stats['total_actions'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'user');
  $stats['total_users'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  return $stats;
}
