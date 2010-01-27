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

// zuletzt bearbeitete artikel (metainfos, content, status, version-addon)
// zuletzt bearbeitete editMe datensätze

// zuletzt bearbeitete module
// zuletzt bearbeitete templates
// zuletzt bearbeitete artikel (metainfos, content, status, version-addon)
// zuletzt bearbeitete medien
// zuletzt bearbeitete editMe Datenmodelle
// zuletzt gelaufene cronjobs
// statistik

class rex_stats_component extends rex_dashboard_component
{
  function rex_stats_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_stats_title'));
    
    $stats = rex_a659_statistics();
    
    $content = '';
    $content .= '<span>';
    $content .= $stats['total_articles'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_articles');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_slices'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_slices');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_clangs'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_clangs');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_templates'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_templates');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_modules'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_modules');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_actions'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_actions');
    $content .= '<br />';
    
    $content .= '<span>';
    $content .= $stats['total_users'];
    $content .= '</span>';
    $content .= $I18N->msg('userinfo_component_stats_users');
    $content .= '<br />';
    
    $content .= '<br />';
    $content .= $I18N->msg('userinfo_component_stats_last_update');
    $content .= ' '. rex_formatter::format($stats['last_update'], 'strftime', 'datetime');
    
    $this->setContent($content);
  }
}

class rex_articles_component extends rex_dashboard_component
{
  function rex_articles_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_articles_title'));
    
    $articles = rex_a659_latest_articles();
    
    $content = '';
    $content .= '<ul>';
    foreach($articles as $article)
    {
      $content .= '<li>';
      $content .= '<a href="index.php?page=content&article_id='. $article['id'] .'&mode=edit&clang='. $article['clang'] .'">'. $article['name'] .'</a>';
      $content .= '</li>';
    }
    $content .= '</ul>';
    
    $this->setContent($content);
  }
}

class rex_templates_component extends rex_dashboard_component
{
  function rex_templates_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_templates_title'));
    
    $templates = rex_a659_latest_templates();
    
    $content = '';
    $content .= '<ul>';
    foreach($templates as $template)
    {
      $content .= '<li>';
      $content .= '<a href="index.php?page=template&function=edit&template_id='. $template['id'] .'">'. $template['name'] .'</a>';
      $content .= '</li>';
    }
    $content .= '</ul>';
          
    $this->setContent($content);
  }
}

class rex_modules_component extends rex_dashboard_component
{
  function rex_modules_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_modules_title'));
    
    $modules = rex_a659_latest_modules();
    
    $content = '';
    $content .= '<ul>';
    foreach($modules as $module)
    {
      $content .= '<li>';
      $content .= '<a href="index.php?page=module&function=edit&modul_id='. $module['id'] .'">'. $module['name'] .'</a>';
      $content .= '</li>';
    }
    $content .= '</ul>';
          
    $this->setContent($content);
  }
}

class rex_actions_component extends rex_dashboard_component
{
  function rex_actions_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_actions_title'));
    
    $actions = rex_a659_latest_actions();
    
    $content = '';
    $content .= '<ul>';
    foreach($actions as $action)
    {
      $content .= '<li>';
      $content .= '<a href="index.php?page=module&subpage=actions&function=edit&action_id='. $action['id'] .'">'. $action['name'] .'</a>';
      $content .= '</li>';
    }
    $content .= '</ul>';
          
    $this->setContent($content);
  }
}

class rex_users_component extends rex_dashboard_component
{
  function rex_users_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component($I18N->msg('userinfo_component_users_title'));
    
    $users = rex_a659_latest_users();
    
    $content = '';
    $content .= '<ul>';
    foreach($users as $user)
    {
      $content .= '<li>';
      $content .= '<a href="index.php?page=user&user_id='. $user['user_id'] .'">'. $user['name'] .'</a>';
      $content .= '</li>';
    }
    $content .= '</ul>';
          
    $this->setContent($content);
  }
}

