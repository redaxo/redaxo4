<?php

/**
 * Cronjob Addon - Plugin art_on_off
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */
 
class rex_a630_cronjob_art_on_off extends rex_a630_cronjob
{
  /*protected*/ function _execute()
  {
    global $REX;
    $sql = rex_sql::factory();
    // $sql->debugsql = true;
    $sql->setQuery('
      SELECT field_id 
      FROM '.$REX['TABLE_PREFIX'].'62_params 
      WHERE name="art_online_from" OR name="art_online_to"
    ');
    if ($sql->getRows() != 2)
      return false;
    
    $sql->setQuery('
      SELECT id, clang, status 
      FROM '.$REX['TABLE_PREFIX'].'article 
      WHERE 
        (art_online_from > 0 
        AND art_online_from < '.time().' 
        AND status = 0 
        AND (art_online_to > '.time().' OR art_online_to = 0 OR art_online_to = ""))
      OR 
        (art_online_to > 0 
        AND art_online_to < '.time().' AND status = 1)
    ');
    $rows = $sql->getRows();
    for($i = 0; $i < $rows; $i++)
    {
      $status = ($sql->getValue('status') + 1) % 2;
      rex_articleStatus($sql->getValue('id'), $sql->getValue('clang'), $status);
      $sql->next();
    }
    return true;
  }
}