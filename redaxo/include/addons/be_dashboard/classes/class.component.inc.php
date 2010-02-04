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

/*abstract*/ class rex_dashboard_component extends rex_dashboard_component_base
{
  var $title;
  var $titleUrl;
  var $content;
  
  function rex_dashboard_component($id, $cache_options = array())
  {
    if(!isset($cache_options['lifetime']))
    {
      // default cache lifetime in seconds
      $cache_options['lifetime'] = 60;
    }
    
    $this->title = '';
    $this->titleUrl = '';
    $this->content = '';
    
    parent::rex_dashboard_component_base($id, $cache_options);
  }

  
  /*public*/ function setTitle($title)
  {
    $this->title = $title;
  }
  
  /*public*/ function getTitle()
  {
    return $this->title;
  }
  
  /*public*/ function setTitleUrl($titleUrl)
  {
    $this->titleUrl = $titleUrl;
  }
  
  /*public*/ function getTitleUrl()
  {
    return $this->titleUrl;
  }
  
  /*public*/ function setContent($content)
  {
    $this->content = $content;
  }
  
  /*public*/ function getContent()
  {
    return $this->content;
  }
   
  /*public*/ function _get()
  {
    global $I18N;
    
    $this->prepare();
    $content = $this->content;
    
    if($content)
    {
      $title = htmlspecialchars($this->title);
      
      if($this->titleUrl != '')
      {
        $title = '<a href="'. $this->titleUrl .'">'. $title .'</a>';
      }
      
    	return '<div class="rex-dashboard-component" id="'. $this->getId() .'">
                <h3>'. $title .'%%actionbar%%</h3>
                %%config%%
                <div class="rex-dashboard-component-content">
                  '. $content .'
                  <p class="rex-dashboard-component-updatedate">
                    '. $I18N->msg('dashboard_component_lastupdate') .'
                    %%cachetime%%
                  </p>
                </div>
              </div>
              <script type="text/javascript">componentInit("'. $this->getId() .'")</script>';
    }
    
    return '';
  }
  
  /*
   * Static Method: Returns boolean if is notification
   */
  /*public static*/ function isValid($component)
  {
    return is_object($component) && is_a($component, 'rex_dashboard_component');
  }
}