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

addSlashesOnArray($HTTP_GET_VARS);

while(list($Akey,$AVal)=each($HTTP_GET_VARS))
{
	$$Akey = $AVal;
}

addSlashesOnArray($HTTP_POST_VARS);

while(list($Akey,$AVal)=each($HTTP_POST_VARS))
{
	$$Akey = $AVal;
}

?>