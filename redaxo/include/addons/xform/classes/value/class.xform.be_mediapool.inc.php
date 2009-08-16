<?php

class rex_xform_be_mediapool extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		global $REX;
		
		if(!isset($REX["xform_classes_be_mediapool"]))
			$REX["xform_classes_be_mediapool"] = 0;
		
		$REX["xform_classes_be_mediapool"]++;
		
		$i = $REX["xform_classes_be_mediapool"];
		
		$this->label = $this->elements[1];

		if ($this->value == "" && !$send)
			if (isset($this->elements[3])) 
				$this->value = $this->elements[3];

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) 
			$wc = $warning["el_" . $this->getId()];

		$form_output[] = '
			<div class="xform-element formbe_mediapool formlabel-'.$this->label.'">
				<label class="text ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>
				<div class="rex-widget">
					<div class="rex-widget-media">
						<p class="rex-widget-field">
							<input type="text" class="text '.$wc.'" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" id="REX_MEDIA_'.$i.'" readonly="readonly" value="'.htmlspecialchars(stripslashes($this->value)) . '" />
						</p>
						<p class="rex-widget-icons">
							<a onclick="openREXMedia('.$i.',\'\');return false;" class="rex-icon-file-open" href="#"><img width="16" height="16" alt="Medium auswählen" title="Medium auswählen" src="media/file_open.gif"/></a>
							<a onclick="addREXMedia('.$i.');return false;" class="rex-icon-file-add" href="#"><img width="16" height="16" alt="Neues Medium hinzufügen" title="Neues Medium hinzufügen" src="media/file_add.gif"/></a>
							<a onclick="deleteREXMedia('.$i.');return false;" class="rex-icon-file-delete" href="#"><img width="16" height="16" alt="Ausgewähltes Medium löschen" title="Ausgewähltes Medium löschen" src="media/file_del.gif"/></a>
						</p>
					</div>
				</div>
			</div>';
			
		$email_elements[$this->elements[1]] = stripslashes($this->value);

		if (!isset($this->elements[4]) || $this->elements[4] != "no_db") 
			$sql_elements[$this->elements[1]] = $this->value;
	}
	
	function getDescription()
	{
		return "be_mediapool -> Beispiel: be_mediapool|label|Bezeichnung|defaultwert|no_db";
	}
}

?>