<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_a630_cronjob_phpcode extends rex_a630_cronjob
{ 
  /*protected*/ function _execute()
  {
    $code = preg_replace('/^\<\?(?:php)?/','',$this->getContent());
    $success = eval($code) !== false;
    return $success;
  }
}