<?php


function rex_xform_manage_checkLabelInTable($l,$v,$table)
{
	global $REX;
	$q = 'select * from '.$table.' where '.$l.'="'.$v.'" LIMIT 1';
	$c = rex_sql::factory();
	// $c->debugsql = 1;
	$c->setQuery($q);
	if($c->getRows()>0)
	{
		// FALSE -> Warning = TRUE;
		return TRUE;
	}else
	{
		return FALSE;
	}
}



?>