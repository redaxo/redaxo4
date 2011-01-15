<?php

class rex_xform_validate_unique extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
		
			$table = $this->params["main_table"];
			if($this->getElement(4) != "")
				$table = $this->getElement(4);
				
			foreach($this->obj_array as $Object)
			{
			
				$sql = 'select '.$this->getElement(2).' from '.$table.' WHERE '.$this->getElement(2).'="'.$Object->getValue().'" LIMIT 1';
				if($this->params["main_where"] != "")
					$sql = 'select '.$this->getElement(2).' from '.$table.' WHERE '.$this->getElement(2).'="'.$Object->getValue().'" AND !('.$this->params["main_where"].') LIMIT 1';

				$cd = rex_sql::factory();
				// $cd->debugsql = 1;
				$cd->setQuery($sql);
				if ($cd->getRows()>0)
				{
					$warning[$Object->getId()] = $this->params["error_class"];
					$warning_messages[$Object->getId()] = $this->getElement(3);
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
             				array( 'type' => 'select_name',   	'label' => 'Name' ),
              				array( 'type' => 'text',    	'label' => 'Fehlermeldung'),
              				array( 'type' => 'text',    	'label' => 'Tabelle [opt]'),
						),
						'description' => 'Hiermit geprüft, ob ein Wert bereits vorhanden ist.',
			);
	
	}
}

?>