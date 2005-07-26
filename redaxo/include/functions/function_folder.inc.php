<?php
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

/**
 * Returns the content of the given folder
 * 
 * @param $dir Path to the folder
 * @return Array Content of the folder or false on error
 * @author Markus Staab <kills@t-online.de>
 */
function readFolder( $dir) 
{
    if ( !is_dir( $dir)) 
    {
        trigger_error( 'Folder "'. $dir .'" is not available or not a directory');
        return false;
    }
    $hdl = opendir($dir);
    $folder = array();
    while (false !== ($file = readdir($hdl)))
    {
        $folder[] = $file;
    }
    
    return $folder;
}

/**
 * Returns the content of the given folder.
 * The content will be filtered with the given $fileprefix
 * 
 * @param $dir Path to the folder
 * @param $fileprefix Fileprefix to filter
 * @return Array Filtered-content of the folder or false on error
 * @author Markus Staab <kills@t-online.de>
 */
function readFilteredFolder( $dir, $fileprefix) 
{
    $filtered = array();
    $folder = readFolder( $dir);
    
    if ( !$folder) {
        return false;
    }
    
    foreach( $folder as $file) {
        if ( endsWith( $file, $fileprefix)) 
        {
            $filtered[] = $file;
        }
    }
    
    return $filtered;
}

/**
 * Returns the files of the given folder
 * 
 * @param $dir Path to the folder
 * @return Array Files of the folder or false on error
 * @author Markus Staab <kills@t-online.de>
 */
function readFolderFiles( $dir) 
{
    $folder = readFolder( $dir);
    $files = array();
    
    if ( !$folder) {
        return false;
    }
    
    foreach( $folder as $file) {
        if ( is_file( $dir .'/'. $file)) 
        {
            $files[] = $file;
        }
    }
    
    return $files;
}

/**
 * Returns the subfolders of the given folder
 * 
 * @param $dir Path to the folder
 * @param $ignore_dots True if the system-folders "." and ".." should be ignored
 * @return Array Subfolders of the folder or false on error
 * @author Markus Staab <kills@t-online.de>
 */
function readSubFolders( $dir, $ignore_dots = true) 
{
    $folder = readFolder( $dir);
    $folders = array();
    
    if ( !$folder) {
        return false;
    }
    
    foreach( $folder as $file) 
    {
        if ( $ignore_dots && ($file == '.' || $file == '..')) 
        {
            continue;
        }
        if ( is_dir( $dir .'/'. $file)) 
        {
            $folders[] = $file;
        }
    }
    
    return $folders;
}

?>