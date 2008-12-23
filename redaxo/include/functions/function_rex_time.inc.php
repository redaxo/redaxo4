<?php

/**
 * Zeit Funktionen
 * @package redaxo4
 * @version $Id: function_rex_time.inc.php,v 1.1 2007/12/28 10:45:10 kills Exp $
 */

function rex_showScriptTime()
{
	global $scriptTimeStart;
	$scriptTimeEnd = rex_getCurrentTime();
	$scriptTimeDiv = intval(($scriptTimeEnd - $scriptTimeStart)*1000)/1000;
	return $scriptTimeDiv;
}

function rex_getCurrentTime()
{
	$time = explode(" ",microtime());
	return ($time[0]+$time[1]);
}

function rex_startScriptTime()
{
	global $scriptTimeStart;
	$scriptTimeStart = rex_getCurrentTime();
}

rex_startScripttime();