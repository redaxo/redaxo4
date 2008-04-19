<?php

/** 
 * Zeit Funktionen  
 * @package redaxo4 
 * @version $Id: function_rex_time.inc.php,v 1.1 2007/12/28 10:45:10 kills Exp $ 
 */ 

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