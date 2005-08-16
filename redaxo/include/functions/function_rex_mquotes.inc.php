<?php

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

?>