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
// zuletzt bearbeitete editMe Datenmodelle
// zuletzt gelaufene cronjobs

class rex_stats_component extends rex_dashboard_component
{
  function rex_stats_component()
  {
    global $I18N;
    
    // default cache lifetime in seconds
    $cache_options['lifetime'] = 1800;
    
    parent::rex_dashboard_component('userinfo_stats', $cache_options);
    $this->setTitle($I18N->msg('userinfo_component_stats_title'));
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $stats = rex_a659_statistics();
    
    $content = '';
    $content .= '<tr>';
    $content .= '<th>';
    $content .= $I18N->msg('userinfo_component_stats_articles');
    $content .= '</th>';
    $content .= '<td>';
    $content .= $stats['total_articles'];
    $content .= '</td>';
    $content .= '</tr>';
    
    $content .= '<tr>';
    $content .= '<th>';
    $content .= $I18N->msg('userinfo_component_stats_slices');
    $content .= '</th>';
    $content .= '<td>';
    $content .= $stats['total_slices'];
    $content .= '</td>';
    $content .= '</tr>';
    
    $content .= '<tr>';
    $content .= '<th>';
    $content .= $I18N->msg('userinfo_component_stats_clangs');
    $content .= '</th>';
    $content .= '<td>';
    $content .= $stats['total_clangs'];
    $content .= '</td>';
    $content .= '</tr>';
    
    $content .= '<tr>';
    $content .= '<th>';
    $content .= $I18N->msg('userinfo_component_stats_templates');
    $content .= '</th>';
    $content .= '<td>';
    $content .= $stats['total_templates'];
    $content .= '</td>';
    $content .= '</tr>';
    
    $content .= '<tr>';
    $content .= '<th>';
    $content .= $I18N->msg('userinfo_component_stats_modules');
    $content .= '</th>';
    $content .= '<td>';
    $content .= $stats['total_modules'];
    $content .= '</td>';
    $content .= '</tr>';
    
    $content .= '<tr>';
    $content .= '<th>';
    $content .= $I18N->msg('userinfo_component_stats_actions');
    $content .= '</th>';
    $content .= '<td>';
    $content .= $stats['total_actions'];
    $content .= '</td>';
    $content .= '</tr>';
    
    $content .= '<tr>';
    $content .= '<th>';
    $content .= $I18N->msg('userinfo_component_stats_users');
    $content .= '</th>';
    $content .= '<td>';
    $content .= $stats['total_users'];
    $content .= '</td>';
    $content .= '</tr>';
    
    $this->setContent('<table class="rex-table rex-dashboard-table"><colgroup><col width="120" /><col width="*" /></colgroup><tbody>'.$content.'</tbody></table>');
  }
}

class rex_articles_component extends rex_dashboard_component
{
  function rex_articles_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component('userinfo_articles');
    $this->setTitle($I18N->msg('userinfo_component_articles_title'));
    $this->setTitleUrl('index.php?page=structure');
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $articles = rex_a659_latest_articles();
    
    $content = '';
    
    if(count($articles) > 0)
    {
      $content .= '<table class="rex-table rex-dashboard-table">
      							<colgroup>
      								<col width="*" />
      								<col width="120" />
      								<col width="150" />
      							</colgroup>
      							
      							<thead>
      								<tr>
      									<th>'.$I18N->msg('userinfo_component_stats_article').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_user').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_date').'</th>
      								</tr>
      							</thead>
      							<tbody>';
      							
      foreach($articles as $article)
      {
        $updatedate = rex_formatter::format($article['updatedate'], 'strftime', 'datetime');
        
        $content .= '<tr>';
        $content .= '<td><a href="index.php?page=content&article_id='. $article['id'] .'&mode=edit&clang='. $article['clang'] .'">'. htmlspecialchars($article['name']) .'</a></td>';
        $content .= '<td>'. htmlspecialchars($article['updateuser']).'</td>';
        $content .= '<td>'.$updatedate.'</td>';
        $content .= '</tr>';
      }
      $content .= '</tbody>';
      $content .= '</table>';
    }
    
    $this->setContent($content);
  }
}

class rex_templates_component extends rex_dashboard_component
{
  function rex_templates_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component('userinfo_templates');
    $this->setTitle($I18N->msg('userinfo_component_templates_title'));
    $this->setTitleUrl('index.php?page=template');
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }

  /*protected*/ function prepare()
  {
    global $I18N;
    
    $templates = rex_a659_latest_templates();
    
    $content = '';
    if(count($templates) > 0)
    {
      $content .= '<table class="rex-table rex-dashboard-table">
      							<colgroup>
      								<col width="*" />
      								<col width="120" />
      								<col width="150" />
      							</colgroup>
      							
      							<thead>
      								<tr>
      									<th>'.$I18N->msg('userinfo_component_stats_template').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_user').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_date').'</th>
      								</tr>
      							</thead>
      							<tbody>';
      							
      foreach($templates as $template)
      {
        $updatedate = rex_formatter::format($template['updatedate'], 'strftime', 'datetime');
        
        $content .= '<tr>';
        $content .= '<td><a href="index.php?page=template&function=edit&template_id='. $template['id'] .'">'. htmlspecialchars($template['name']) .'</a></td>';
        $content .= '<td>'. htmlspecialchars($template['updateuser']).'</td>';
        $content .= '<td>'.$updatedate.'</td>';
        $content .= '</tr>';
      }
      $content .= '</tbody>';
      $content .= '</table>';
    }
          
    $this->setContent($content);
  }
}

class rex_modules_component extends rex_dashboard_component
{
  function rex_modules_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component('userinfo_modules');
    $this->setTitle($I18N->msg('userinfo_component_modules_title'));
    $this->setTitleUrl('index.php?page=module');
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $modules = rex_a659_latest_modules();
    
    $content = '';
    if(count($modules) > 0)
    {
      $content .= '<table class="rex-table rex-dashboard-table">
      							<colgroup>
      								<col width="*" />
      								<col width="120" />
      								<col width="150" />
      							</colgroup>
      							
      							<thead>
      								<tr>
      									<th>'.$I18N->msg('userinfo_component_stats_module').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_user').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_date').'</th>
      								</tr>
      							</thead>
      							<tbody>';
      							
      foreach($modules as $module)
      {
        $updatedate = rex_formatter::format($module['updatedate'], 'strftime', 'datetime');
        
        $content .= '<tr>';
        $content .= '<td><a href="index.php?page=module&function=edit&modul_id='. $module['id'] .'">'. htmlspecialchars($module['name']) .'</a></td>';
        $content .= '<td>'. htmlspecialchars($module['updateuser']).'</td>';
        $content .= '<td>'.$updatedate.'</td>';
        $content .= '</tr>';
      }
      $content .= '</tbody>';
      $content .= '</table>';
    }
          
    $this->setContent($content);
  }
}

class rex_actions_component extends rex_dashboard_component
{
  function rex_actions_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component('userinfo_actions');
    $this->setTitle($I18N->msg('userinfo_component_actions_title'));
    $this->setTitleUrl('index.php?page=module&amp;subpage=actions');
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $actions = rex_a659_latest_actions();
    
    $content = '';
    if(count($actions) > 0)
    {
      $content .= '<table class="rex-table rex-dashboard-table">
      							<colgroup>
      								<col width="*" />
      								<col width="120" />
      								<col width="150" />
      							</colgroup>
      							
      							<thead>
      								<tr>
      									<th>'.$I18N->msg('userinfo_component_stats_action').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_user').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_date').'</th>
      								</tr>
      							</thead>
      							<tbody>';
      							
      foreach($actions as $action)
      {
        $updatedate = rex_formatter::format($action['updatedate'], 'strftime', 'datetime');
        
        $content .= '<tr>';
        $content .= '<td><a href="index.php?page=module&subpage=actions&function=edit&action_id='. $action['id'] .'">'. htmlspecialchars($action['name']) .'</a></td>';
        $content .= '<td>'. htmlspecialchars($action['updateuser']).'</td>';
        $content .= '<td>'.$updatedate.'</td>';
        $content .= '</tr>';
      }
      $content .= '</tbody>';
      $content .= '</table>';
    }
          
    $this->setContent($content);
  }
}

class rex_users_component extends rex_dashboard_component
{
  function rex_users_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component('userinfo_users');
    $this->setTitle($I18N->msg('userinfo_component_users_title'));
    $this->setTitleUrl('index.php?page=user');
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
    
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $users = rex_a659_latest_users();
    
    $content = '';
    if(count($users) > 0)
    {
      $content .= '<table class="rex-table rex-dashboard-table">
      							<colgroup>
      								<col width="*" />
      								<col width="120" />
      								<col width="150" />
      							</colgroup>
      							
      							<thead>
      								<tr>
      									<th>'.$I18N->msg('userinfo_component_stats_user').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_user').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_date').'</th>
      								</tr>
      							</thead>
      							<tbody>';
      							
      foreach($users as $user)
      {
        $updatedate = rex_formatter::format($user['updatedate'], 'strftime', 'datetime');
        
        $content .= '<tr>';
        $content .= '<td><a href="index.php?page=user&user_id='. $user['user_id'] .'">'. htmlspecialchars($user['name']) .'</a></td>';
        $content .= '<td>'. htmlspecialchars($user['updateuser']).'</td>';
        $content .= '<td>'.$updatedate.'</td>';
        $content .= '</tr>';
      }
      $content .= '</tbody>';
      $content .= '</table>';
    }
          
    $this->setContent($content);
  }
}

class rex_media_component extends rex_dashboard_component
{
  function rex_media_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component('userinfo_media');
    $this->setTitle($I18N->msg('userinfo_component_media_title'));
    $this->setTitleUrl('javascript:openMediaPool();');
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->hasMediaPerm();
  }

  /*protected*/ function prepare()
  {
    global $I18N;
    
    $media = rex_a659_latest_media();
    
    $content = '';
    if(count($media) > 0)
    {
      $content .= '<table class="rex-table rex-dashboard-table">
      							<colgroup>
      								<col width="*" />
      								<col width="120" />
      								<col width="150" />
      							</colgroup>
      							
      							<thead>
      								<tr>
      									<th>'.$I18N->msg('userinfo_component_stats_medium').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_user').'</th>
      									<th>'.$I18N->msg('userinfo_component_stats_date').'</th>
      								</tr>
      							</thead>
      							<tbody>';
      foreach($media as $medium)
      {
        $url = 'index.php?page=mediapool&subpage=detail&file_id='. $medium['file_id'];
        $updatedate = rex_formatter::format($medium['updatedate'], 'strftime', 'datetime');
        
        $content .= '<tr>';
        $content .= '<td><a href="'. $url .'" onclick="newPoolWindow(this.href); return false;">'. htmlspecialchars($medium['filename']) .'</a></td>';
        $content .= '<td>'. htmlspecialchars($medium['updateuser']).'</td>';
        $content .= '<td>'.$updatedate.'</td>';
        $content .= '</tr>';
      }
      $content .= '</tbody>';
      $content .= '</table>';
    }
          
    $this->setContent($content);
  }
}
