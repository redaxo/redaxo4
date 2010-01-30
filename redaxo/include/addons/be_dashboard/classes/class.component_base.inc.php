<?php

/**
 * Backenddashboard Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

/*abstract*/ class rex_dashboard_component_base
{
  var $funcCache;
  var $cacheBackend;
  
  function rex_dashboard_component_base($cache_options = array())
  {
    $this->cacheBackend = new rex_file_cache($cache_options);
    $this->funcCache = new rex_function_cache($this->cacheBackend);
  }
  
  /*protected*/ function prepare()
  {
    // override in subclasses to prepare component
  }
  
  
  /*public*/ function checkPermission()
  {
    // no permission required by default
    return true;
  }

  /*public*/ function get()
  {
    if($this->checkPermission())
    {
      $callable = array($this, '_get');
      $content = $this->funcCache->call($callable);
      $cachekey = $this->funcCache->computeCacheKey($callable);
      $cachestamp = $this->cacheBackend->getLastModified($cachekey);
      $cachetime = rex_formatter::format($cachestamp, 'strftime', 'datetime');
      return strtr($content, array('%%cachetime%%' => $cachetime));
    }
    return '';
  }
  
  /*public abstract*/ function _get()
  {
    trigger_error('The _get method have to be overridden by a subclass!', E_USER_ERROR);
  }
  
  /*public*/ function registerAsExtension($params)
  {
    $params['subject'][] = $this;
    return $params['subject'];
  }
}