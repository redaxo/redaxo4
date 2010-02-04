<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_cronjob_component extends rex_dashboard_component
{
  function rex_cronjob_component()
  {
    global $I18N;
    
    parent::rex_dashboard_component('cronjob');
    $this->setTitle($I18N->msg('cronjob_dashboard_component_title'));
    $this->setTitleUrl('index.php?page=cronjob');
  }
  
  /*protected*/ function prepare()
  {
    global $I18N;
    
    $folder = REX_LOG_FOLDER;
    $years = rex_a630_log_years($folder);
    
    if(count($years) > 0)
    {
      $messages = rex_a630_log_messages($folder, $years[0], 10);
      
      $content = '';
      if(count($messages) > 0)
      {
        $content .= '<ul>';
        foreach($messages as $message)
        {
          $content .= '<li>'. $message .'</li>';
        }
        $content .= '</ul>';
      }
      
      $this->setContent($content);
    }
  }
}