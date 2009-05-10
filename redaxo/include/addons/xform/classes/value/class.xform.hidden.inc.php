<?php

class rex_xform_hidden extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		if (isset($this->elements[3]) && $this->elements[3]=="REQUEST" && isset($_REQUEST[$this->elements[1]]))
		{
			$this->value = $_REQUEST[$this->elements[1]];
			$form_output[] = '<p style="display:hidden;"><input type="hidden" name="'.$this->elements[1].'" value="'.$this->value.'" /></p>';
		}else
		{
			$this->value = $this->elements[2];
			$email_elements[$this->elements[1]] = $this->value;
		}

		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db") 
			$sql_elements[$this->elements[1]] = $this->value;
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
		Hiermit kšnnen Werte fest als Wert zum Formular eingetragen werden z.B. 
		
		hidden|status|abgeschickt
		
		Dieser Wert kann wie alle anderen Werte Ÿbernommen und in der Datenbank gepeichert, oder auch
		im E-Mail Formular anzeigt werden.
		
		Weiterhin gibt es mit "REQUEST" auch die Mšglichkeit, Werte auf der Url oder einem
		vorherigen Formular zu Ÿbernehmen.
		
		hidden -> Beispiel: hidden|job_id|default_value|REQUEST|
		
		Hier wird die job_id Ÿbernommen und direkt wieder Ÿber das Formular mitversendet.
		
		mit "no_db" wird definiert, dass bei einer eventuellen Datenbankspeicherung, dieser
		Wert nicht Ÿbernommen wird.
		';	
	}

}