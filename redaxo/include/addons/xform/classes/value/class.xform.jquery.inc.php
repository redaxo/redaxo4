<?php

class rex_xform_jquery extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{


		$out = "";
		if (isset($this->elements[3])) 
			$out = $this->elements[3];

		// alle labels ersetzen..

		foreach($this->obj as $o)
		{
			// echo "  ".$o->getName()."  ";
			$out = str_replace("###".$o->getName()."###",'el_'.$o->getId(),$out);
		}


		$form_output[] = '
		
		<script type="text/javascript">
		
		'.$out.';

		</script>';

		return;


		if ($this->getValue() == "" && !$send)
		{
			if (isset($this->elements[3])) $this->setValue($this->elements[3]);
		}

	  $classes = "";
    if (isset($this->elements[5]))
    {
      $classes .= " ".$this->elements[5];
    }
		
		$wc = "";
		if (isset($warning["el_" . $this->getId()]))
		{
			$wc = " ".$warning["el_" . $this->getId()];
		}

		$form_output[] = '
			<p class="formtext formlabel-'.$this->getName().'">
				<label class="text' . $wc . '" for="el_' . $this->getId() . '" >' . $this->elements[2] . '</label>
				<input type="text" class="text' . $classes. $wc . '" name="FORM[' . 
		$this->params["form_name"] . '][el_' . $this->id . ']" id="el_' . $this->id . '" value="' .
		htmlspecialchars(stripslashes($this->getValue())) . '" />
			</p>';
		$email_elements[$this->elements[1]] = stripslashes($this->getValue());
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db")
		{
			$sql_elements[$this->elements[1]] = $this->getValue();
		}
	}

	function getDescription()
	{
		/*
		jquery - jq1 - hidelabels - label - labels,labels,labels
		jquery - jq2 - maxlength - label
		*/
		return "jquery -> Beispiel: text|label|Bezeichnung|defaultwert|[no_db]";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'jquery',
						'values' => array(
							array( 'type' => 'name',	'label' => 'Feld' ),
							array( 'type' => 'select',	'label' => 'Welche Funktion ?', 'default' => '', 'definition' => '-=0;hidelabel=1;maxlength=1' ),
							array( 'type' => 'textarea','label' => 'Javascriptcode'),
		        		),
						'description' => 'JQuery Hilfsfunktionen',
						'dbtype' => 'text'
						);

	}
}

?>