<?php

/**
 * Cronjob Addon - Plugin article_status
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */
 
class rex_cronjob_article_status extends rex_cronjob
{
  /*protected*/ function _execute()
  {
    global $REX;
    $config = OOPlugin::getProperty('cronjob', 'article_status', 'config');
    $from = $config['from'];
    $to   = $config['to'];
    $from['before'] = (array) $from['before'];
    $to['before']   = (array) $to['before'];
    
    $sql = rex_sql::factory();
    // $sql->debugsql = true;
    $sql->setQuery('
      SELECT  field_id 
      FROM    '. $REX['TABLE_PREFIX'] .'62_params 
      WHERE   name="'. $from['field'] .'" OR name="'. $to['field'] .'"
    ');
    if ($sql->getRows() != 2)
      return false;
    
    $time = time();
    $sql->setQuery('
      SELECT  id, clang, status 
      FROM    '. $REX['TABLE_PREFIX'] .'article 
      WHERE 
        (     '. $from['field'] .' > 0 
        AND   '. $from['field'] .' < '. $time .' 
        AND   status IN ('. implode(',', $from['before']) .')
        AND   ('. $to['field'] .' > '. $time .' OR '. $to['field'] .' = 0 OR '. $to['field'] .' = "")
        )
      OR 
        (     '. $to['field'] .' > 0 
        AND   '. $to['field'] .' < '. $time .' 
        AND   status IN ('. implode(',', $to['before']) .')
        )
    ');
    $rows = $sql->getRows();
    for($i = 0; $i < $rows; $i++)
    {
      if (in_array($sql->getValue('status'), $from['status']))
        $status = $from['after'];
      else
        $status = $to['after'];
      
      rex_articleStatus($sql->getValue('id'), $sql->getValue('clang'), $status);
      $sql->next();
    }
    return true;
  }
}