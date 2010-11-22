<?php

class rex_xform_select_sql extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		$multiple = (int) $this->getElement(8);
		if($multiple != 1)
			$multiple = 0; 

		$size = (int) $this->getElement(9);
		if($size < 1)
			$size = 1; 

		$SEL = new rex_select();
		$SEL->setId($this->getHTMLId().'-s');
		
		if($multiple)
		{
			$SEL->setName($this->getFormFieldname().'[]');
			$SEL->setMultiple();
			$SEL->setSize($size);
		}else
		{
			$SEL->setName($this->getFormFieldname());
			$SEL->setSize(1);
		}
		

		$sql = $this->elements[3];

		$teams = rex_sql::factory();
		$teams->debugsql = $this->params["debug"];
		$teams->setQuery($sql);

		$sqlnames = array();

		// mit --- keine auswahl ---
		if (!$multiple && $this->elements[6] == 1)
			$SEL->addOption($this->elements[7], "0");

		foreach($teams->getArray() as $t)
		{
			$v = $t['name'];
			$k = $t['id'];
			$SEL->addOption($v, $k);
			$sqlnames[$k] = $t['name'];
		}

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) 
			$wc = $warning["el_" . $this->getId()];

		$SEL->setStyle(' class="select ' . $wc . '"');

		if ($this->value=="" && isset($this->elements[4]) && $this->elements[4] != "") 
			$this->value = $this->elements[4];

		if(!is_array($this->value))
		{
			$this->value = explode(",",$this->value);
		}

		foreach($this->value as $v)
		{
			$SEL->setSelected($v);
		}
		
		$form_class = '';
		if ($multiple)
			$form_class = ' formselect-multiple-'.$size;
		
		$form_output[] = '
			<p class="formselect'.$form_class.'"  id="'.$this->getHTMLId().'">
				<label class="select ' . $wc . '" for="' . $this->getHTMLId() . '-s" >' . $this->elements[2] . '</label>
				' . $SEL->get() . '
			</p>';

		/*
		if (isset($sqlnames[$this->value])) 
			$email_elements[$this->elements[1].'_SQLNAME'] = stripslashes($sqlnames[$this->value]);
		*/

		$this->value = implode(",",$this->value);
		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[5]) || $this->elements[5] != "no_db") 
			$sql_elements[$this->elements[1]] = $this->value;
		
	}
	
	function getDescription()
	{
		return "select_sql -> Beispiel: select_sql|label|Bezeichnung:|select id,name from table order by name|[defaultvalue]|[no_db]|1/0 Leeroption|Leeroptionstext|1/0 Multiple Feld";
	}
	
	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'select_sql',
						'values' => array(
							array( 'type' => 'name',		'label' => 'Name' ),
							array( 'type' => 'text',		'label' => 'Bezeichnung'),
							array( 'type' => 'text',		'label' => 'Query mit "select id, name from .."'),
					   		array( 'type' => 'text',		'label' => 'Defaultwert (opt.)'),
					   		array( 'type' => 'no_db',   	'label' => 'Datenbank',  'default' => 1),
					   		array( 'type' => 'boolean',		'label' => 'Leeroption'),
					   		array( 'type' => 'text',		'label' => 'Text bei Leeroption (Bitte auswählen)'),
					   		array( 'type' => 'boolean',		'label' => 'Mehrere Felder möglich'),
					   		array( 'type' => 'text',		'label' => 'Höhe der Auswahlbox'),
						),
						'description' => 'Hiermit kann man SQL Abfragen als Selectbox nutzen',
						'dbtype' => 'text'
					);
	}
	
}

?>