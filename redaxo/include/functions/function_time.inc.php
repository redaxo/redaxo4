<?


// Scripttime for debugging

function showScripttime()
{
	global $scriptTimeStart;
	$scriptTimeEnd = getCurrentTime();
	$scriptTimeDiv = intval(($scriptTimeEnd - $scriptTimeStart)*1000)/1000;
	return $scriptTimeDiv;
}

function getCurrentTime()
{ 
	$time = explode(" ",microtime()); 
	return ($time[0]+$time[1]);
} 

function startScripttime()
{
	global $scriptTimeStart;
	$scriptTimeStart = getCurrentTime();
}

startScripttime();

?>