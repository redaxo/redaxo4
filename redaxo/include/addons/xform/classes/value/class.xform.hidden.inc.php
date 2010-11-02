<?php

class rex_xform_hidden extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		
		if (isset($this->elements[3]) && $this->elements[3]=="REQUEST" && isset($_REQUEST[$this->getName()]))
		{
			$this->setValue(stripslashes(rex_request($this->getName())));
			$form_output[$this->getId()] = "\n".'<p style="display:none;" id="'.$this->getHTMLId().'"><input type="hidden" name="'.$this->getName().'" value="'.htmlspecialchars($this->getValue()).'" /></p>';

		}else
		{
			$this->setValue($this->elements[2]);
			$email_elements[$this->getName()] = $this->getValue();
		}

		$email_elements[$this->getName()] = stripslashes($this->getValue());
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db") 
			$sql_elements[$this->getName()] = $this->getValue();
	}
	
	function getDescription()
	{
		return "
				hidden -> Beispiel: hidden|status|default_value||[no_db]
		<br />	hidden -> Beispiel: hidden|job_id|default_value|REQUEST|[no_db]
		";
	}

	function getLongDescription()
	{
		return '
		Hiermit können Werte fest als Wert zum Formular eingetragen werden z.B. 
		
		hidden|status|abgeschickt
		
		Dieser Wert kann wie alle anderen Werte bernommen und in der Datenbank gepeichert, oder auch
		im E-Mail Formular anzeigt werden.
		
		Weiterhin gibt es mit "REQUEST" auch die Mglichkeit, Werte auf der Url oder einem
		vorherigen Formular zu bernehmen.
		
		hidden -> Beispiel: hidden|job_id|default_value|REQUEST|
		
		Hier wird die job_id bernommen und direkt wieder ber das Formular mitversendet.
		
		mit "no_db" wird definiert, dass bei einer eventuellen Datenbankspeicherung, dieser
		Wert nicht bernommen wird.
		';	
	}

}