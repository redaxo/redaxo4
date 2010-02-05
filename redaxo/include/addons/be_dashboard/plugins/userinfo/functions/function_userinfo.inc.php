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
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'article WHERE clang=0 GROUP BY clang ORDER BY updatedate DESC');
  if(count($result) > 0)
  {
    $stats['total_articles'] = $result[0]['count'];
    $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  }
  else
  {
    $stats['total_articles'] = 0;
  }
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'article_slice GROUP BY revision ORDER BY updatedate DESC LIMIT 1');
  if(count($result) > 0)
  {
    $stats['total_slices'] = $result[0]['count'];
    $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  }
  else
  {
    $stats['total_slices'] = 0;
  }
  
  $result = $sql->getArray('SELECT COUNT(*) as count FROM '. $REX['TABLE_PREFIX'] .'clang');
  if(count($result) > 0)
  {
    $stats['total_clangs'] = $result[0]['count'];
  }
  else
  {
    $stats['total_clangs'] = 0;
  }
  
  $result = $sql->getArray('SELECT COUNT(*) as count FROM '. $REX['TABLE_PREFIX'] .'template');
  if(count($result) > 0)
  {
    $stats['total_templates'] = $result[0]['count'];
	  $result = $sql->getArray('SELECT updatedate FROM '. $REX['TABLE_PREFIX'] .'template ORDER BY updatedate DESC LIMIT 1');
    $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  }
  else
  {
    $stats['total_templates'] = 0;
  }
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'module GROUP BY revision ORDER BY updatedate DESC LIMIT 1');
  if(count($result) > 0)
  {
    $stats['total_modules'] = $result[0]['count'];
    $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  }
  else
  {
    $stats['total_modules'] = 0;
  }
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'action GROUP BY revision ORDER BY updatedate DESC LIMIT 1');
  if(count($result) > 0)
  {
    $stats['total_actions'] = $result[0]['count'];
    $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  }
  else
  {
    $stats['total_actions'] = 0;
  }
  
  $result = $sql->getArray('SELECT COUNT(*) as count, updatedate FROM '. $REX['TABLE_PREFIX'] .'user GROUP BY revision ORDER BY updatedate DESC LIMIT 1');
  if(count($result) > 0)
  {
    $stats['total_users'] = $result[0]['count'];
    $stats['last_update'] = $result[0]['updatedate'] > $stats['last_update'] ? $result[0]['updatedate'] : $stats['last_update'];
  }
  else
  {
    $stats['total_users'] = 0;
  }
  
  return $stats;
}

function rex_a659_latest_articles($limit = A659_DEFAULT_LIMIT)
{
  global $REX;
  
  $sql = rex_sql::factory();
//  $sql->debugsql = true;
  $sql->setQuery('SELECT id, re_id, clang, startpage, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'article GROUP BY id ORDER BY updatedate DESC LIMIT 50');
  
  $i = 0;
  $articles = array();
  while($sql->hasNext())
  {
    $article = $sql->getRow();
    $catId = $article['startpage'] == 1 ? $article['id'] : $article['re_id'];
    
    if($REX['USER']->hasCategoryPerm($catId))
    {
      $i++;
      $articles[] = $article;
    }
    
    if($i >= $limit)
    {
      break;
    }
    
    $sql->next();
  }
  $sql->freeResult();
  
  
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
