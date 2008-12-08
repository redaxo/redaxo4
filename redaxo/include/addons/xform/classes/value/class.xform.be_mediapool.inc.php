<?php

class rex_xform_be_mediapool extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		
		$this->label = $this->elements[1];

		if ($this->value == "" && !$send)
		{
			if (isset($this->elements[3])) $this->value = $this->elements[3];
		}

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) $wc = $warning["el_" . $this->getId()];
		
		$form_output[] = '
			<p class="formbe_mediapool formlabel-'.$this->label.'">
			
				<label class="text ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>
				
				<input type="text" class="text '.$wc.'" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" id="REX_MEDIA_1" readonly="readonly" value="'.htmlspecialchars(stripslashes($this->value)) . '" />
				
				<span><a href="#" onclick="openREXMedia(1,\'\');return false;" tabindex="27"><img src="media/file_open.gif" width="16" height="16" title="Medium auswählen" alt="Medium auswählen" /></a>
				<a href="#" onclick="addREXMedia(1);return false;" tabindex="28"><img src="media/file_add.gif" width="16" height="16" title="Neues Medium hinzufügen" alt="Neues Medium hinzufügen" /></a>
				<a href="#" onclick="deleteREXMedia(1);return false;" tabindex="29"><img src="media/file_del.gif" width="16" height="16" title="Ausgewähltes Medium löschen" alt="Ausgewähltes Medium löschen" /></a>
				</span>
				
			</p>';
		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db") $sql_elements[$this->elements[1]] = $this->value;
	}
	
	function getDescription()
	{
		return "text -> Beispiel: be_mediapool|label|Bezeichnung|defaultwert|no_db";
	}
}

?>