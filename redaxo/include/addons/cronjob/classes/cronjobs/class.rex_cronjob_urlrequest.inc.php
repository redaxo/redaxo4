<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_cronjob_urlrequest extends rex_cronjob
{ 
  /*protected*/ function _execute()
  {
    if($fh = fopen($this->getContent(), "r")){ 
      while (!feof($fh)){ 
        fgets($fh); 
      } 
      fclose($fh);
      return true; 
    }
    return false;
  }
}