<?php

/*abstract*/ class rex_dashboard_component
{
  var $title;
  var $content;
  
  function rex_dashboard_component($title, $content = '')
  {
    $this->title = $title;
    $this->content = $content;
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
  
  /*public*/ function get()
  {
    return '<div class="rex-dashboard-component">
              <h3>'. $this->getTitle() .'</h3>
              '. $this->getContent() .'
            </div>';
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