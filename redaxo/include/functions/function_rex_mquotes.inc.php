<?

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

addSlashesOnArray($_GET);

if (is_array($_GET))
{
	while(list($Akey,$AVal)=each($_GET))
	{
		$$Akey = $AVal;
	}
}


addSlashesOnArray($_HTTP);

if (is_array($_HTTP))
{
	while(list($Akey,$AVal)=each($_HTTP))
	{
		$$Akey = $AVal;
	}
}

?>