<?php

// TODO paginierung entfernen
// TODO fehlende lang strings ergaenzen

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
    $this->setBlock($I18N->msg('userinfo_block_stats'));
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
    $this->setBlock($I18N->msg('userinfo_block_latest_infos'));
  }
  
  /*protected*/ function prepare()
  {
    global $REX, $I18N;
    
    $limit = A659_DEFAULT_LIMIT;
    
    // TODO Permcheck im SQL
    $list = rex_list::factory('SELECT id, re_id, clang, startpage, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'article GROUP BY id ORDER BY updatedate DESC', $limit);
    $list->setCaption($I18N->msg('structure_articles_caption'));
    $list->addTableAttribute('summary', $I18N->msg('structure_articles_summary'));
    $list->addTableColumnGroup(array(40, '*', 120, 150));
    
    $list->removeColumn('id');
    $list->removeColumn('re_id');
    $list->removeColumn('clang');
    $list->removeColumn('startpage');
    $editParams = array('page' => 'content', 'mode' => 'edit', 'article_id' => '###id###', 'clang' => '###clang###');
  
    $thIcon = '';
    $tdIcon = '<span class="rex-i-element rex-i-article"><span class="rex-i-element-text">###name###</span></span>';
    $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
    $list->setColumnParams($thIcon, $editParams);
  
    $list->setColumnLabel('name', $I18N->msg('header_article_name'));
    $list->setColumnParams('name', $editParams);
  
    $list->setColumnLabel('updateuser', $I18N->msg('userinfo_component_stats_user'));
    $list->setColumnLabel('updatedate', $I18N->msg('userinfo_component_stats_date'));
    $list->setColumnFormat('updatedate', 'strftime', 'datetime');
    
    $this->setContent($list->get());
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
    $this->setBlock($I18N->msg('userinfo_block_latest_infos'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }

  /*protected*/ function prepare()
  {
    global $REX, $I18N;
    
    $limit = A659_DEFAULT_LIMIT;
      
    $list = rex_list::factory('SELECT id, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'template ORDER BY updatedate DESC', $limit);
    $list->setCaption($I18N->msg('header_template_caption'));
    $list->addTableAttribute('summary', $I18N->msg('header_template_summary'));
    $list->addTableColumnGroup(array(40, '*', 120, 150));
    
    $addParams  = array('page' => 'template', 'function' => 'add');
    $editParams = array('page' => 'template', 'function' => 'edit', 'template_id' => '###id###');
  
    $tdIcon = '<span class="rex-i-element rex-i-template"><span class="rex-i-element-text">###name###</span></span>';
    $thIcon = '<a class="rex-i-element rex-i-template-add" href="'. $list->getUrl($addParams) .'"><span class="rex-i-element-text">'.$I18N->msg('create_template').'</span></a>';
    $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
    $list->setColumnParams($thIcon, $editParams);
  
    $list->removeColumn('id');
  
    $list->setColumnLabel('name', $I18N->msg('header_template_description'));
    $list->setColumnParams('name', $editParams);
    
    $list->setColumnLabel('updateuser', $I18N->msg('userinfo_component_stats_user'));
    $list->setColumnLabel('updatedate', $I18N->msg('userinfo_component_stats_date'));
    $list->setColumnFormat('updatedate', 'strftime', 'datetime');
  
    $list->setNoRowsMessage($I18N->msg('templates_not_found'));

    $this->setContent($list->get());
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
    $this->setBlock($I18N->msg('userinfo_block_latest_infos'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
  
  /*protected*/ function prepare()
  {
    global $REX, $I18N;
    
    $limit = A659_DEFAULT_LIMIT;
    
    $list = rex_list::factory('SELECT id, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'module ORDER BY updatedate DESC', $limit);
    $list->setCaption($I18N->msg('module_caption'));
    $list->addTableAttribute('summary', $I18N->msg('module_summary'));
    $list->addTableColumnGroup(array(40, '*', 120, 150));
    
    $list->removeColumn('id');
    $addParams  = array('page' => 'module', 'function' => 'add');
    $editParams = array('page' => 'module', 'function' => 'edit', 'modul_id' => '###id###');
  
    $tdIcon = '<span class="rex-i-element rex-i-module"><span class="rex-i-element-text">###name###</span></span>';
    $thIcon = '<a class="rex-i-element rex-i-module-add" href="'. $list->getUrl($addParams) .'"><span class="rex-i-element-text">'.$I18N->msg('create_module').'</span></a>';
    $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
    $list->setColumnParams($thIcon, $editParams);
  
    $list->setColumnLabel('name', $I18N->msg('module_description'));
    $list->setColumnParams('name', $editParams);
  
    $list->setColumnLabel('updateuser', $I18N->msg('userinfo_component_stats_user'));
    $list->setColumnLabel('updatedate', $I18N->msg('userinfo_component_stats_date'));
    $list->setColumnFormat('updatedate', 'strftime', 'datetime');
    
    $list->setNoRowsMessage($I18N->msg('modules_not_found'));

    $this->setContent($list->get());
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
    $this->setBlock($I18N->msg('userinfo_block_latest_infos'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
  
  /*protected*/ function prepare()
  {
    global $REX, $I18N;
    
    $limit = A659_DEFAULT_LIMIT;
    
    $list = rex_list::factory('SELECT id, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'action ORDER BY updatedate DESC', $limit);
    $list->setCaption($I18N->msg('action_caption'));
    $list->addTableAttribute('summary', $I18N->msg('action_summary'));
    $list->addTableColumnGroup(array(40, '*', 120, 150));
    
    $list->removeColumn('id');
    $addParams  = array('page' => 'module', 'subpage' => 'actions', 'function' => 'add');
    $editParams = array('page' => 'module', 'subpage' => 'actions', 'function' => 'edit', 'action_id' => '###id###');
    
    $tdIcon = '<span class="rex-i-element rex-i-action"><span class="rex-i-element-text">###name###</span></span>';
    $thIcon = '<a class="rex-i-element rex-i-action-add" href="'. $list->getUrl($addParams) .'"><span class="rex-i-element-text">'.$I18N->msg('action_create').'</span></a>';
    $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
    $list->setColumnParams($thIcon, $editParams);
  
    $list->setColumnLabel('name', $I18N->msg('action_name'));
    $list->setColumnParams('name', $editParams);
  
    $list->setColumnLabel('updateuser', $I18N->msg('userinfo_component_stats_user'));
    $list->setColumnLabel('updatedate', $I18N->msg('userinfo_component_stats_date'));
    $list->setColumnFormat('updatedate', 'strftime', 'datetime');
    
    $list->setNoRowsMessage($I18N->msg('actions_not_found'));

    $this->setContent($list->get());
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
    $this->setBlock($I18N->msg('userinfo_block_latest_infos'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->isAdmin();
  }
    
  /*protected*/ function prepare()
  {
    global $REX, $I18N;
    
    $limit = A659_DEFAULT_LIMIT;
    
    $list = rex_list::factory('SELECT user_id, name, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'user ORDER BY updatedate DESC', $limit);
    $list->setCaption($I18N->msg('user_caption'));
    $list->addTableAttribute('summary', $I18N->msg('user_summary'));
    $list->addTableColumnGroup(array(40, '*', 120, 150));
    
    $list->removeColumn('user_id');
    $addParams  = array('page' => 'user', 'FUNC_ADD' => '1');
    $editParams = array('page' => 'user', 'function' => 'edit', 'user_id' => '###user_id###');
    
    $tdIcon = '<span class="rex-i-element rex-i-user"><span class="rex-i-element-text">###name###</span></span>';
    $thIcon = '<a class="rex-i-element rex-i-user-add" href="'. $list->getUrl($addParams) .'"><span class="rex-i-element-text">'.$I18N->msg('create_user').'</span></a>';
    $list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
    $list->setColumnParams($thIcon, $editParams);
  
    $list->setColumnLabel('name', $I18N->msg('name'));
    $list->setColumnParams('name', $editParams);
  
    $list->setColumnLabel('updateuser', $I18N->msg('userinfo_component_stats_user'));
    $list->setColumnLabel('updatedate', $I18N->msg('userinfo_component_stats_date'));
    $list->setColumnFormat('updatedate', 'strftime', 'datetime');
    
    $this->setContent($list->get());
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
    $this->setBlock($I18N->msg('userinfo_block_latest_infos'));
  }
  
  /*public*/ function checkPermission()
  {
    global $REX;
    
    return $REX['USER']->hasMediaPerm();
  }

  /*protected*/ function prepare()
  {
    global $REX, $I18N;
    
    $limit = A659_DEFAULT_LIMIT;
      
    $list = rex_list::factory('SELECT category_id, file_id, filename, updateuser, updatedate FROM '. $REX['TABLE_PREFIX'] .'file ORDER BY updatedate DESC', $limit);
    $list->setCaption($I18N->msg('pool_file_caption'));
    $list->addTableAttribute('summary', $I18N->msg('pool_file_summary'));
    $list->addTableColumnGroup(array('*', 120, 150));
  
    $list->removeColumn('category_id');
    $list->removeColumn('file_id');
    $editParams = array('page' => 'mediapool', 'subpage' => 'detail', 'rex_file_category' => '###category_id###', 'file_id' => '###file_id###');
    
    $list->setColumnLabel('filename', $I18N->msg('pool_file_info'));
    $list->setColumnParams('filename', $editParams);
    $list->addLinkAttribute('filename','onclick', 'newPoolWindow(this.href); return false;');
    
    $list->setColumnLabel('updateuser', $I18N->msg('userinfo_component_stats_user'));
    $list->setColumnLabel('updatedate', $I18N->msg('userinfo_component_stats_date'));
    $list->setColumnFormat('updatedate', 'strftime', 'datetime');
  
    $list->setNoRowsMessage($I18N->msg('templates_not_found'));

    $this->setContent($list->get());
  }
}
