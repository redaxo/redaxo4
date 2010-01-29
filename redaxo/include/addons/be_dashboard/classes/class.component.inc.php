<?php

/*abstract*/ class rex_dashboard_component
{
  var $title;
  var $content;
  var $funcCache;
  var $cacheBackend;
  
  function rex_dashboard_component($title = '', $content = '', $cache_options = array())
  {
    if(!isset($cache_options['lifetime']))
    {
      // default cache lifetime in seconds
      $cache_options['lifetime'] = 60;
    }
    
    $this->title = $title;
    $this->content = $content;
    $this->cacheBackend = new rex_file_cache($cache_options);
    $this->funcCache = new rex_function_cache($this->cacheBackend);
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
   
  /*public*/ function get()
  {
    $callable = array($this, '_get');
    $content = $this->funcCache->call($callable);
    $cachekey = $this->funcCache->computeCacheKey($callable);
    $cachestamp = $this->cacheBackend->getLastModified($cachekey);
    $cachetime = rex_formatter::format($cachestamp, 'strftime', 'datetime');
    return strtr($content, array('%%cachetime%%' => $cachetime));
  }
  
  /*public*/ function _get()
  {
    global $I18N;
    
    $this->prepare();
    $content = $this->getContent();
    
    if($content)
    {
      return '<div class="rex-dashboard-component">
                <h3>'. $this->getTitle() .'</h3>
                '. $content .'
                <span class="rex-dashboard-component-updatedate">
                  '. $I18N->msg('dashboard_component_lastupdate') .'
                  %%cachetime%%
                </span>
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