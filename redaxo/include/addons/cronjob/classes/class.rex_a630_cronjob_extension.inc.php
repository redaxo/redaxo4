<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_a630_cronjob_extension extends rex_a630_cronjob
{ 
  /*protected*/ function _execute($content)
  {
    global $REX;
    $extension = $content;
    $extensions = rex_register_extension_point('REX_CRONJOB_EXTENSIONS', array());
    if(!isset($extensions[$extension]) || !is_array($extensions[$extension]) || !isset($extensions[$extension][1]))
      return false;
    $callback = $extensions[$extension][1];
    if(!is_callable($callback))
      return false;
    $params = array();
    if(isset($extensions[$extension][2]))
      $params = (array)$extensions[$extension][2];
    return call_user_func_array($callback,$params) !== false;
  }
}