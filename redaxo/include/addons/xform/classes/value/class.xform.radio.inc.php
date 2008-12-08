<?php

// Dateiname: class.xform.radio.inc.php

class rex_xform_radio extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		if ($this->value == "" && !$send)
		{
			if (isset($this->elements[5])) 
				$this->value = $this->elements[5];
		}else
		{

		}

		$out = "";
		foreach (explode(";", $this->elements[3]) as $v)
		{
			$teile = explode("=", $v);
			$bezeichnung = $teile[0];
			if (is_array($teile) && isset ($teile[1]))
			{
				$wert = $teile[1];
			}else
			{
				$wert = $teile[0];
			}
			
			$out .= '<span>'.$bezeichnung.'</span>';
			$out .= '<input type="radio" name="FORM[' . $this->params["form_name"] . '][el_' . $this->id . ']" value="'.$wert.'" ';
			if ($this->value == $wert) $out .= ' checked="checked"';
			$out .= ' />';
			
		}

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) $wc = $warning["el_" . $this->getId()];


		$form_output[] = ' 
			<p class="formradio">
				<label class="radio ' . $wc . '" >' . $this->elements[2] . '</label>
				<div class="radio '.$wc.'">' .$out . '</div>
			</p>';

		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db") $sql_elements[$this->elements[1]] = $this->value;

	}
	
	function getDescription()
	{
		return "radio -> Beispiel: radio|gender|Geschlecht *|Frau=w;Herr=m|[no_db]|defaultwert";
	}
}

?>