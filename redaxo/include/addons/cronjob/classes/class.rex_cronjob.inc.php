<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/*abstract*/ class rex_cronjob
{
  /*private*/ var $name;
  /*private*/ var $content;
  /*private*/ var $success;
  
  /*protected*/ function rex_cronjob($name, $content = null) 
  {
    if (empty($name))
      $name = '[no name]';
    
    $this->name = $name;
    $this->content = $content;
  }
  
  /*public*/ function factory($type, $name, $content) 
  {
    if ($type < 1 || $type > 4)
      return null;
    
    $class = null;
    if ($type != 4)
    {
      $classes = array(
        1 => 'rex_cronjob_phpcode',
        2 => 'rex_cronjob_phpcallback',
        3 => 'rex_cronjob_urlrequest'
      );
      $class = $classes[$type];
    }
    else
      $class = $content;
    
    if (!class_exists($class))
      return null;
    
    return new $class($name, $content);
  }
  
  /*public*/ function getContent()
  {
    return $this->content;
  }
  
  /*public*/ function getName() 
  {
    return $this->name;
  }
   
  /*public*/ function execute()
  {
    $this->success = $this->_execute();
    $this->log();
    return $this->success;
  }
  
  /*abstract protected*/ function _execute() 
  {
    trigger_error('The _execute method has to be overridden by a subclass!', E_USER_ERROR);
  }
  
  /*private*/ function log()
  {   
    return rex_cronjob_log::save($this->getName(), $this->success);
  }
  
  /*public static*/ function isValid($cronjob)
  {
    return is_object($cronjob) && is_subclass_of($cronjob, 'rex_cronjob');
  }
}