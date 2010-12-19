<?php

/*
$objparams["actions"][] = "db"; // z.b. email, datenbank, als datei speichern etc.
$objparams["action_params"][] = array(
	"table" => "REX_VALUE[8]",
	"where" => "REX_VALUE[8]",
	);
*/


class rex_xform_action_nestedset extends rex_xform_action_abstract
{
	
	function execute()
	{
		// echo "DB EXECUTE";
		// return;
		
		$table = $this->action["elements"][2];

		$parent = rex_request($this->params['manager_type'].'_parent', 'int', 1);
    
    $sql = new rex_sql;
    $sql->setTable($table);
    
    // select lft and rgt
    $sql->setWhere('id = '.$parent);
    
    if(!$sql->select('id,'.$this->params['manager_type'].'_rgt,'.$this->params['manager_type'].'_level'))
      return false; // error
    
    $id = $sql->getValue('id');
    $rgt = $sql->getValue($this->params['manager_type'].'_rgt');
    $level = $sql->getValue($this->params['manager_type'].'_level');
    
    // update rgt
    $sql->setQuery(
      sprintf('
        UPDATE `%s`
        SET '.$this->params['manager_type'].'_rgt = '.$this->params['manager_type'].'_rgt + 2
        WHERE '.$this->params['manager_type'].'_rgt >= %d',
        $table,
        $rgt
      )
    );
    
    // update lft
    $sql->setQuery(
      sprintf('
        UPDATE `%s`
        SET '.$this->params['manager_type'].'_lft = '.$this->params['manager_type'].'_lft + 2
        WHERE '.$this->params['manager_type'].'_lft > %d',
        $table,
        $rgt
      )
    );
    
    // new data
    $this->elements_sql[$this->params['manager_type'].'_lft'] = $rgt;
    $this->elements_sql[$this->params['manager_type'].'_rgt'] = $rgt + 1;
    $this->elements_sql[$this->params['manager_type'].'_parent'] = $parent;
    $this->elements_sql[$this->params['manager_type'].'_level'] = $level + 1;
	}

	function getDescription()
	{
		return "action|nestedset|tblname";
	}

}

?>