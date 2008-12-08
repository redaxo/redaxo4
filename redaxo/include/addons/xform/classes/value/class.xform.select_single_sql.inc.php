<?php

class rex_xform_select_single_sql extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		$SEL = new rex_select();
		$SEL->setName('FORM[' . $this->params["form_name"] . '][el_' . $this->id . ']');
		$SEL->setId("el_" . $this->id);
		$SEL->setSize(1);

		$sql = $this->elements[4];
		$teams = new rex_sql;
		$teams->debugsql = $this->params["debug"];
		$teams->setQuery($sql);
		$sqlnames = array();

		if ($this->elements[3] != 1)
		{
			// mit --- keine auswahl ---
			$SEL->addOption($this->elements[3], "0");
		}

		for ($t = 0; $t < $teams->getRows(); $t++)
		{
			$SEL->addOption($teams->getValue($this->elements[6]), $teams->getValue($this->elements[5]));
			if (isset($this->elements[7])) $sqlnames[$teams->getValue($this->elements[5])] = $teams->getValue($this->elements[7]);
			$teams->next();
		}

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) $wc = $warning["el_" . $this->getId()];

		$SEL->setStyle(' class="select ' . $wc . '"');

		if ($this->value=="" && isset($this->elements[7]) && $this->elements[7] != "") $this->value = $this->elements[7];
		$SEL->setSelected($this->value);

		$form_output[] = '
			<p class="formselect">
			<label class="select ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>
			' . $SEL->get() . '
			</p>';

		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (isset($sqlnames[$this->value])) $email_elements[$this->elements[1].'_SQLNAME'] = stripslashes($sqlnames[$this->value]);
		if (!isset($this->elements[8]) || $this->elements[8] != "no_db") $sql_elements[$this->elements[1]] = $this->value;
		
	}
	
	function getDescription()
	{
		return "select_single_sql -> Beispiel: select_single_sql|stadt_id|BASE *:|1|select * from branding_rex_staedte order by name|id|name|default|[no_db]";
	}
}

?>