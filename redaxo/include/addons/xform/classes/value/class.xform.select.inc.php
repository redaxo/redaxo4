<?php

class rex_xform_select extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		$multiple = FALSE;
		if(isset($this->elements[6]) && $this->elements[6]==1)
		$multiple = TRUE;

		$size = (int) $this->getElement(7);
		if($size < 1)
		  $size = 1;

		$SEL = new rex_select();
		$SEL->setId("el_" . $this->getId());
		if($multiple)
		{
			if($size == 1)
			 $size = 2;
			$SEL->setName($this->getFormFieldname()."[]");
			$SEL->setSize($size);
			$SEL->setMultiple(1);
		}else
		{
			$SEL->setName($this->getFormFieldname());
			$SEL->setSize(1);
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

		if (!$send && $this->value=="" && isset($this->elements[5]) && $this->elements[5] != "")
		$this->value = $this->elements[5];

		if(!is_array($this->getValue()))
		{
			$this->value = explode(",",$this->getValue());
		}

		foreach($this->getValue() as $v)
		{
			$SEL->setSelected($v);
		}

		$this->value = implode(",",$this->getValue());

		$wc = "";
		if (isset($warning[$this->getId()]))
		  $wc = $warning[$this->getId()];

		$SEL->setStyle(' class="select '.$wc.'"');

		$form_output[$this->getId()] = '
      <p class="formselect formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
      <label class="select '.$wc.'" for="el_'.$this->getId().'" >'.$this->elements[2].'</label>'. 
		$SEL->get().
      '</p>';

		$email_elements[$this->elements[1]] = $this->getValue();
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db")
		  $sql_elements[$this->elements[1]] = $this->getValue();

	}

	function getDescription()
	{
		return "select -> Beispiel: select|gender|Geschlecht *|Frau=w;Herr=m|[no_db]|defaultwert|multiple=1";
	}

	function getDefinitions()
	{
		return array(
            'type' => 'value',
            'name' => 'select',
            'values' => array(
		array( 'type' => 'name',   'label' => 'Feld' ),
		array( 'type' => 'text',    'label' => 'Bezeichnung'),
		array( 'type' => 'text',    'label' => 'Selektdefinition',   'example' => 'Frau=w;Herr=m'),
		array( 'type' => 'no_db',   'label' => 'Datenbank',          'default' => 1),
		array( 'type' => 'text',    'label' => 'Defaultwert'),
		array( 'type' => 'boolean', 'label' => 'Mehrere Felder möglich'),
		array( 'type' => 'text',    'label' => 'Höhe der Auswahlbox'),
		),
            'description' => 'Ein Selektfeld mit festen Definitionen',
            'dbtype' => 'text'
            );

	}

}

?>