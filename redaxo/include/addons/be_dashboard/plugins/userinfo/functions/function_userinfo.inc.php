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


function rex_a659_statistics()
{
  global $REX;
  
  $stats = array();
  $stats['last_update'] = 0;
  
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'article WHERE clang=0 ORDER BY updatedate desc');
  $stats['total_articles'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'article_slice ORDER BY updatedate DESC');
  $stats['total_slices'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count FROM '. $REX['TABLE_PREFIX'] .'clang');
  $stats['total_clangs'] = $result[0]['count'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'template ORDER BY updatedate DESC');
  $stats['total_templates'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'module ORDER BY updatedate DESC');
  $stats['total_modules'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'action ORDER BY updatedate DESC');
  $stats['total_actions'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'user ORDER BY updatedate DESC');
  $stats['total_users'] = $result[0]['count'];
  $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  
  return $stats;
}

function rex_a659_latest_articles($limit = A659_DEFAULT_LIMIT)
{
  global $REX;
  
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $articles = $sql->getArray('SELECT id, clang, startpage, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'article GROUP BY id ORDER BY updatedate DESC LIMIT '.$limit);
  return $articles;
}

function rex_a659_latest_templates($limit = A659_DEFAULT_LIMIT)
{
  global $REX;
  
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $templates = $sql->getArray('SELECT id, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'template ORDER BY updatedate DESC LIMIT '.$limit);
  return $templates;
}

function rex_a659_latest_modules($limit = A659_DEFAULT_LIMIT)
{
  global $REX;
  
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $modules = $sql->getArray('SELECT id, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'module ORDER BY updatedate DESC LIMIT '.$limit);
  return $modules;
}

function rex_a659_latest_actions($limit = A659_DEFAULT_LIMIT)
{
  global $REX;
  
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $actions = $sql->getArray('SELECT id, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'action ORDER BY updatedate DESC LIMIT '.$limit);
  return $actions;
}

function rex_a659_latest_users($limit = A659_DEFAULT_LIMIT)
{
  global $REX;
  
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $users = $sql->getArray('SELECT user_id, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'user ORDER BY updatedate DESC LIMIT '.$limit);
  return $users;
}

function rex_a659_latest_media($limit = A659_DEFAULT_LIMIT)
{
  global $REX;
  
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $users = $sql->getArray('SELECT file_id, filename, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'file ORDER BY updatedate DESC LIMIT '.$limit);
  return $users;
}
