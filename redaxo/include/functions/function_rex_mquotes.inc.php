<?php

/** 
 * Funktionen zum handeln von magic_quotes=off  
 * @package redaxo3 
 * @version $Id$ 
 */

if (!get_magic_quotes_gpc())
{
	function addSlashesOnArray(&$theArray)
	{
		if (is_array($theArray))
		{
			reset($theArray);
			while(list($Akey,$AVal)=each($theArray))
			{
				if (is_array($AVal))
				{
					addSlashesOnArray($theArray[$Akey]);
				}else
				{
					$theArray[$Akey] = addslashes($AVal);
				}
			}
			reset($theArray);
		}
	}
	
	if (is_array($_GET))
	{
	    addSlashesOnArray($_GET);
	    
		while(list($Akey,$AVal)=each($_GET))
		{
			$$Akey = $AVal;
		}
	}
	
	if (is_array($_POST))
	{
	    addSlashesOnArray($_POST);
	    
		while(list($Akey,$AVal)=each($_POST))
		{
			$$Akey = $AVal;
		}
	}
}

// ----------------- REGISTER GLOBALS CHECK
if (!ini_get('register_globals'))
{
        // register_globals = off;
        
        if (isset($_COOKIE) and $_COOKIE) extract($_COOKIE);
        if (isset($_ENV) and $_ENV) extract($_ENV);
        if (isset($_FILES) and $_FILES) extract($_FILES);
        if (isset($_GET) and $_GET) extract($_GET);
        if (isset($_POST) and $_POST) extract($_POST);
        if (isset($_SERVER) and $_SERVER) extract($_SERVER);
        if (isset($_SESSION) and $_SESSION) extract($_SESSION);
}else
{
        // register_globals = on;
        
}



?>