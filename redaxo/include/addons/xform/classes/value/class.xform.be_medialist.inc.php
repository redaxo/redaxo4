<?php

class rex_xform_be_medialist extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		

		$this->label = $this->elements[1];
		
		
		if (!isset($tmp_medialist)) $tmp_medialist = 0;
		$tmp_medialist++;
			
		$ausgabe = '
		<div class="rex-wdgt" style="margin-left:160px;">
		  <div class="rex-wdgt-mdlst">
			<input type="hidden" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" id="REX_MEDIALIST_'.$tmp_medialist.'" value="'.htmlspecialchars(stripslashes($this->value)) . '" />
			<p class="rex-wdgt-fld">
			  <select name="MEDIALIST_SELECT[1]" id="REX_MEDIALIST_SELECT_'.$tmp_medialist.'" size="8" tabindex="29" style="width:250px;">';
				$medialistarray = explode(",",$this->value);
				if (is_array($medialistarray))
				{
					for($j=0;$j<count($medialistarray);$j++)
					{
						if (current($medialistarray)!="") $ausgabe .= "<option value='".current($medialistarray)."'>".current($medialistarray)."</option>\n";
						next($medialistarray);
					}
				}
		$ausgabe .='
			  </select>
			</p>
			<p class="rex-wdgt-icons">
			  <a href="#" onclick="moveREXMedialist('.$tmp_medialist.',\'top\');return false;" tabindex="30"><img src="media/file_top.gif" width="16" height="16" title="Ausgewähltes Medium an den Anfang verschieben" alt="Ausgewähltes Medium an den Anfang verschieben" /></a>
			  <a href="#" onclick="openREXMedialist('.$tmp_medialist.');return false;" tabindex="31"><img src="media/file_open.gif" width="16" height="16" title="Medium auswählen" alt="Medium auswählen" /></a><br />
			  <a href="#" onclick="moveREXMedialist('.$tmp_medialist.',\'up\');return false;" tabindex="32"><img src="media/file_up.gif" width="16" height="16" title="Ausgewähltes Medium nach oben verschieben" alt="Ausgewähltes Medium an den Anfang verschieben" /></a>
			  <a href="#" onclick="addREXMedialist('.$tmp_medialist.');return false;" tabindex="33"><img src="media/file_add.gif" width="16" height="16" title="Neues Medium hinzufügen" alt="Neues Medium hinzufügen" /></a><br />
			  <a href="#" onclick="moveREXMedialist('.$tmp_medialist.',\'down\');return false;" tabindex="34"><img src="media/file_down.gif" width="16" height="16" title="Ausgewähltes Medium nach unten verschieben" alt="Ausgewähltes Medium nach unten verschieben" /></a>
			  <a href="#" onclick="deleteREXMedialist('.$tmp_medialist.');return false;" tabindex="35"><img src="media/file_del.gif" width="16" height="16" title="Ausgewähltes Medium löschen" alt="Ausgewähltes Medium löschen" /></a><br />
			  <a href="#" onclick="moveREXMedialist('.$tmp_medialist.',\'bottom\');return false;" tabindex="36"><img src="media/file_bottom.gif" width="16" height="16" title="Ausgewähltes Medium an das Ende verschieben" alt="Ausgewähltes Medium an das Ende verschieben" /></a>
			</p>
			<div class="rex-clearer"></div>
		  </div>
		</div>';
		
		
		
		
		
		
		
		
		
		
		
		
		
		

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) $wc = $warning["el_" . $this->getId()];
		
		
		
		$form_output[] = '
			<p class="formbe_medialist formlabel-'.$this->label.'">
			
				<label class="text ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>
				
				'.$ausgabe.'
				
			</p>';





		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[3]) || $this->elements[3] != "no_db") $sql_elements[$this->elements[1]] = $this->value;

	}
	
	function getDescription()
	{
		return "be_medialist -> Beispiel: be_medialist|label|Bezeichnung|no_db";
	}
}

?>