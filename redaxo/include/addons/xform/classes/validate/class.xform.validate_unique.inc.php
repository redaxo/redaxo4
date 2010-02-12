<?php

class rex_xform_validate_unique extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
		
			$table = $this->params["main_table"];
			if(isset($this->xaElements[4]) && $this->xaElements[4] != "")
				$table = $this->xaElements[4];
				
			foreach($this->xaObjects as $xoObject)
			{
			
				$sql = 'select '.$this->xaElements[2].' from '.$table.' WHERE '.$this->xaElements[2].'="'.$xoObject->getValue().'" LIMIT 1';
				if($this->params["main_where"] != "")
					$sql = 'select '.$this->xaElements[2].' from '.$table.' WHERE '.$this->xaElements[2].'="'.$xoObject->getValue().'" AND !('.$this->params["main_where"].') LIMIT 1';

				$cd = rex_sql::factory();
				// $cd->debugsql = 1;
				$cd->setQuery($sql);
				if ($cd->getRows()>0)
				{
					$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
					$warning_messages[] = $this->xaElements[3];
				}
			}
		}
	}
	
	function getDescription()
	{
		return "unique -> prüft ob unique, beispiel: validate|unique|dbfeldname|Dieser Name existiert schon|[table]";
	}
	
  function getDefinitions()
  {
    return array(
            'type' => 'validate',
            'name' => 'unique',
            'values' => array(
                    array( 'type' => 'getlabel',    'name' => 'Label' ),
                      array( 'type' => 'text',      'name' => 'Fehlermeldung'),
                      array( 'type' => 'text',      'name' => 'Tabelle [opt]'),
            ),
            'description' => 'Hiermit geprŸft, ob ein Wert bereits vorhanden ist.',
      );
  
  }
	
}

?>