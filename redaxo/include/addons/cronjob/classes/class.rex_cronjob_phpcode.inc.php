<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_cronjob_phpcode extends rex_cronjob
{ 
  /*protected*/ function _execute()
  {
    $code = preg_replace('/^\<\?(?:php)?/','',$this->getContent());
    $success = eval($code) !== false;
    return $success;
  }
}