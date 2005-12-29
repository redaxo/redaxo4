<?php

/**
 * Setup Funktionen
 * @package redaxo3
 * @version $Id$
 */

/**
 * Prüfen ob ein/e Datei/Ordner beschreibbar ist 
 */
function rex_is_writable($item)
{
  global $I18N;
  
  $state = true;
  
  // Fehler unterdrücken, falls keine Berechtigung
  if (@is_dir($item))
  {
    if (!@ is_writable($item."/."))
    {
      $state = $I18N->msg("setup_012", absPath($item));
    }
  }
  // Fehler unterdrücken, falls keine Berechtigung
  elseif (@is_file($item))
  {
    if (!@ is_writable($item))
    {
      $state = $I18N->msg("setup_014", absPath($item));
    }
  }
  else
  {
    $state = $I18N->msg("setup_015", absPath($item));
  }
  
  return $state;
}

/**
 * Ausgabe des Setup spezifischen Titels 
 */
function rex_setuptitle($title)
{
  title($title,"");

  echo "
  <table border=0 cellpadding=5 cellspacing=1 width=770>
  <tr><td class=lgrey><font class=content>";

}

/**
 * Berechnet aus einem Relativen Pfad einen Absoluten 
 */
function rex_absPath( $rel_path) 
{
    $path = realpath( '.');
    $stack = explode(DIRECTORY_SEPARATOR, $path);
    
    foreach( explode( '/',$rel_path) as $dir) 
    {
        if ( $dir == '.') {
            continue;
        }
        
        if ( $dir == '..') 
        {
            array_pop( $stack);
        } 
        else
        {
            array_push( $stack, $dir);
        }
    }
    
    
    return implode('/',$stack);
}

?>