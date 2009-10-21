<?php

/*
 * ŸberprŸft, ob Feld vorhanden ist.
 * 
 * 
 */

function rex_em_checkField($l,$v,$p)
{
  $q = 'select * from rex_em_field where table_id='.$p.' and '.$l.'="'.$v.'" LIMIT 1';
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

function rex_em_checkLabelInTable($l,$v,$p)
{
  $q = 'select * from rex_em_table where '.$l.'="'.$v.'" LIMIT 1';
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



function rex_em_generateAll()
{
	$tables = rex_em_getTables();
	foreach($tables as $table)
	{
    $name = $table['name'];
    $id = $table['id'];
		
		
		echo "<br />*** ".$table['name']." / ".$table['id'];
		
		
	}
}


function rex_em_getTables()
{
  $tb = rex_sql::factory();
  $tb->setQuery('select * from rex_em_table');
	return $tb->getArray();
}





?>