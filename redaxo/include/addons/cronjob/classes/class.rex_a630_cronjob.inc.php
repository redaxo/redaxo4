<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/*abstract*/ class rex_a630_cronjob
{
  /*private*/ var $name;
  /*private*/ var $success;
  
  /*protected*/ function rex_a630_cronjob($name) {
    $this->name = $name;
  }
  
  /*public*/ function factory($type, $name) {
    if (!in_array($type,range(1,4)))
      return null;
    
    $classes = array(
      1 => 'rex_a630_cronjob_phpcode',
      2 => 'rex_a630_cronjob_phpcallback',
      3 => 'rex_a630_cronjob_urlrequest',
      4 => 'rex_a630_cronjob_extension',
    );
    return new $classes[$type]($name);
  }
   
  /*public*/ function execute($content)
  {
    $this->success = $this->_execute($content);
    $this->log();
    return $this->success;
  }
  
  /*abstract protected*/ function _execute($content) {}
  
  /*private*/ function log()
  {
    global $REX;
    $name = $this->name;
    $success = $this->success;
    $year = date('Y');
    $month = date('m');
    
    $dir = $REX['INCLUDE_PATH'].'/addons/cronjob/logs/'.$year;
    if (!is_dir($dir))
    {
      mkdir($dir);
      chmod($dir, $REX['DIRPERM']);
    }
    
    $content = '';
    $file = $dir.'/'.$year.'-'.$month.'.log';
    if (file_exists($file))
      $content = rex_get_file_contents($file);
    
    // Im Frontend ist die Klasse rex_formatter nicht verfuegbar.
    // Falls die Klasse hier manuell eingebunden wird,
    // als Format nicht 'datetime' verwenden, da im Frontend kein I18N-Objekt verfuegbar ist
    $newline = date('Y-m-d H:i');
    if ($success)
      $newline .= '  SUCCESS  ';
    else
      $newline .= '   ERROR   ';
      
    $newline .= $name;
    $content = $newline."\n".$content;
    
    rex_put_file_contents($file, $content);
  }
}