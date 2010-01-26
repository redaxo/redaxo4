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

/*abstract*/ class rex_user_info_component extends rex_dashboard_component
{
  function rex_user_info_component($title)
  {
    parent::rex_dashboard_component($title);
  }
}

/*abstract*/ class rex_admin_info_component extends rex_dashboard_component
{
  function rex_admin_info_component($title)
  {
    parent::rex_dashboard_component($title);
  }
}

class rex_admin_stats_component extends rex_admin_info_component
{
  function rex_admin_stats_component()
  {
    parent::rex_admin_info_component('stats');
    
    $stats = rex_a659_statistics();
    
    $content = '';
    $content .= 'Artikel '. $stats['total_articles'] .'<br />';
    $content .= 'Blöcke '. $stats['total_slices'].'<br />';
    $content .= 'Sprachen '. $stats['total_clangs'].'<br />';
    $content .= 'Templates '. $stats['total_templates'].'<br />';
    $content .= 'Module '. $stats['total_modules'].'<br />';
    $content .= 'Aktionen '. $stats['total_actions'].'<br />';
    $content .= 'Benutzer '. $stats['total_users'].'<br />';
    $content .= '<br />';
    $content .= 'letzte Änderung '. $stats['last_update'];
    
    $this->setContent($content);
  }
}