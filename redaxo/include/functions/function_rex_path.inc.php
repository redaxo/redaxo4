<?php
/** 
 * Pfad Funktionen  
 * @package redaxo3 
 * @version $Id$ 
 */ 
 
/**
 * Berechnet aus einem Relativen Pfad einen Absoluten 
 */
function absPath( $rel_path) 
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