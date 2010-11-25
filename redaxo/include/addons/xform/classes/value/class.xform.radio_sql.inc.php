<?php

class rex_xform_radio_sql extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		$SEL = new rex_radio();
		$SEL->setId($this->getHTMLId());
		
		$SEL->setName($this->getFormFieldname());

		$sql = $this->elements[3];

		$teams = rex_sql::factory();
		$teams->debugsql = $this->params["debug"];
		$teams->setQuery($sql);

		$sqlnames = array();

		foreach($teams->getArray() as $t)
		{
			$v = $t['name'];
			$k = $t['id'];
			$SEL->addOption($v, $k);
			$sqlnames[$k] = $t['name'];
		}

		$wc = "";
		if (isset($warning[$this->getId()])) 
			$wc = $warning[$this->getId()];

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
		
		$form_output[] = '
			<p class="formradio formlabel-'.$this->getName().'"  id="'.$this->getHTMLId().'">
				<label class="radio ' . $wc . '" for="' . $this->getHTMLId() . '" >' . $this->elements[2] . '</label>
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
		return "radio_sql -> Beispiel: select_sql|label|Bezeichnung:|select id,name from table order by name|[defaultvalue]|[no_db]|";
	}
	

	
}

?>