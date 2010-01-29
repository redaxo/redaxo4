<?php

/*abstract*/ class rex_dashboard_component
{
  var $title;
  var $content;
  
  function rex_dashboard_component($title = '', $content = '')
  {
    $this->title = $title;
    $this->content = $content;
  }
  
  /*protected*/ function prepare()
  {
    // override in subclasses to retrieve and set content/title
  }
  
  /*public*/ function setTitle($title)
  {
    $this->title = $title;
  }
  
  /*public*/ function getTitle()
  {
    return $this->title;
  }
  
  /*public*/ function setContent($content)
  {
    $this->content = $content;
  }
  
  /*public*/ function getContent()
  {
    return $this->content;
  }
   
  /*public*/ function getLastUpdate()
  {
    // todo
    return mktime();
  } 
  
  /*public*/ function get()
  {
    global $I18N;
    
    $this->prepare();
    
    $content = $this->getContent();
    
    if($content)
    {
      $update = $I18N->msg('dashboard_component_lastupdate');
      $update .= ' '. rex_formatter::format($this->getLastUpdate(), 'strftime', 'datetime');
      
      return '<div class="rex-dashboard-component">
                <h3>'. $this->getTitle() .'</h3>
                '. $content .'
                <span class="rex-dashboard-component-updatedate">'. $update .'</span>
              </div>';
    }
    
    return '';
  }
  
  /*public*/ function registerAsExtension($params)
  {
    $params['subject'][] = $this;
    return $params['subject'];
  }
  
  /*
   * Static Method: Returns boolean if is notification
   */
  /*public static*/ function isValid($component)
  {
    return is_object($component) && is_a($component, 'rex_dashboard_component');
  }
}