<?php
/**
 * Funktionen zur Ausgabe der Titel Leiste und Subnavigation
 * @package redaxo3
 * @version $Id$
 */ 
 
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
      $state = $I18N->msg("setup_012", rex_absPath($item));
    }
  }
  // Fehler unterdrücken, falls keine Berechtigung
  elseif (@is_file($item))
  {
    if (!@ is_writable($item))
    {
      $state = $I18N->msg("setup_014", rex_absPath($item));
    }
  }
  else
  {
    $state = $I18N->msg("setup_015", rex_absPath($item));
  }
  
  return $state;
}

?>