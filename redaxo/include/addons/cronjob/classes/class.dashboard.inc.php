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
    $messages = rex_cronjob_log::getNewestMessages(10);
    
    $content = '';
    if(count($messages) > 0)
    {
      $content .= '<ul>';
      foreach($messages as $message)
      {
        $style = '';
        if (strpos($message, ' ERROR ') !== false)
          $style = ' style="font-weight:bold; color:red;"';
        $content .= '<li><pre'.$style.'>'. $message .'</pre></li>';
      }
      $content .= '</ul>';
    }
    
    $this->setContent($content);
  }
}