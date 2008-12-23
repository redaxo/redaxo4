<?php

/** 
 * Funktionen zum handeln von magic_quotes=off  
 * @package redaxo4 
 * @version $Id: function_rex_mquotes.inc.php,v 1.1 2007/12/28 10:45:10 kills Exp $ 
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
	
	if (is_array($_REQUEST))
	{
	    addSlashesOnArray($_REQUEST);
	    
		while(list($Akey,$AVal)=each($_REQUEST))
		{
			$$Akey = $AVal;
		}
	}
	
}
