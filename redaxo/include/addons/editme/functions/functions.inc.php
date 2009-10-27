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
	$types = rex_xform::getTypeArray();

	
	$tables = rex_em_getTables();
	foreach($tables as $table)
	{
    $name = $table['name'];
    $id = $table['id'];
		
    $fields = rex_em_getFields($table['id']);
		
    echo "<h1>".$table['name']." / ".$table['id']."</h1>";
    
    // TODO: Table schon vorhanden ?, wenn nein, dann anlegen

    // TODO: Felder merken und eventuell loeschen
    
    echo '<ul>';
    foreach($fields as $field)
    {
    	$type_name = $field["type_name"];
    	$type_id = $field["type_id"];

    	echo '<li>'.$field["type_name"].$field["type_id"].'</li>';
    	echo '<pre>'; var_dump($types[$type_id][$type_name]); echo '</pre>';
    	
    }
		echo '</ul>';
		
		
		
	}
}


function rex_em_getTables()
{
  $tb = rex_sql::factory();
  $tb->setQuery('select * from rex_em_table');
	return $tb->getArray();
}

function rex_em_getFields($table_id)
{
  $tb = rex_sql::factory();
  $tb->setQuery('select * from rex_em_field where table_id='.$table_id.' order by prio');
	return $tb->getArray();
}



?>