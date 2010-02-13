<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_a630_cronjob_urlrequest extends rex_a630_cronjob
{ 
  /*protected*/ function _execute($content)
  {
    if($fh = fopen($content,"r")){ 
      while (!feof($fh)){ 
         fgets($fh); 
      } 
      fclose($fh);
      return true; 
    }
    return false;
  }
}