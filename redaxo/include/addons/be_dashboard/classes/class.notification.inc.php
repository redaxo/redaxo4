<?php

/*abstract*/ class rex_dashboard_notification
{
  var $message;
  var $funcCache;
  var $cacheBackend;
  
  function rex_dashboard_notification($cache_options = array())
  {
    if(!isset($cache_options['lifetime']))
    {
      // default cache lifetime in seconds
      $cache_options['lifetime'] = 60;
    }
    
    $this->message = '';
    $this->cacheBackend = new rex_file_cache($cache_options);
    $this->funcCache = new rex_function_cache($this->cacheBackend);
  }
  
  /*protected*/ function prepare()
  {
    // override in subclasses to retrieve and set message
  }
  
  /*public*/ function setMessage($message)
  {
    $this->message = $message;
  }
  
  /*public*/ function getMessage()
  {
    return $this->message;
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
    $this->prepare();
    
    $message = $this->getMessage();
    
    if($message)
    {
      return '<li>'. $message .'</li>';
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
  /*public static*/ function isValid($notification)
  {
    return is_object($notification) && is_a($notification, 'rex_dashboard_notification');
  }
}