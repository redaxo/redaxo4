<?php

class rex_xform_select extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		$SEL = new rex_select();
		$SEL->setName("FORM[" . $this->params["form_name"] . "][el_" . $this->id . "]");
		$SEL->setId("el_" . $this->id);
		$SEL->setSize(1);

		if ($this->value == "" && !$send)
		{
			if (isset($this->elements[5])) $SEL->setSelected($this->elements[5]);
		}else
		{
			$SEL->setSelected($this->value);
		}

		foreach (explode(";", $this->elements[3]) as $v)
		{
			$teile = explode("=", $v);
			$wert = $teile[0];
			if (is_array($teile) && isset ($teile[1]))
			{
				$bezeichnung = $teile[1];
			}else
			{
				$bezeichnung = $teile[0];
			}
			$SEL->addOption($wert, $bezeichnung);
		}

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) $wc = $warning["el_" . $this->getId()];

		$SEL->setStyle(' class="select ' . $wc . '"');

		$form_output[] = ' 
			<p class="formselect">
			<label class="select ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>' . 
			$SEL->get() . '
			</p>';

		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db") $sql_elements[$this->elements[1]] = $this->value;

	}
	
	function getDescription()
	{
		return "select -> Beispiel: select|gender|Geschlecht *|Frau=w;Herr=m|[no_db]|defaultwert";
	}
}

?>