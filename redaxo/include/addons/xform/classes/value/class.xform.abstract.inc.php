<?php

class rex_xform_abstract
{

	var $id;
	var $params = array(); // allgemeine parameter der
	var $elements = array(); //
	var $value;

	// Position im Formular. Unique ID
	function setId($id)
	{
		$this->id = $id;
	}

	function setArticleId($aid)
	{
		$this->aid = $aid;
	}
	
	function setValue($value)
	{
		// der Wert wird übergeben
		$this->value=$value;
	}

	//Wert zurückgeben
	function getValue()
	{
		// echo '<p>Wert wird geholt: '.$this->value.'</p>';
		return $this->value;
	}

	// FormularParameter ins Objekt legen
	function loadParams(&$params,$elements = array(), $obj = "")
	{
		// parameter des Formuarmoduls werden Ã¼bergeben
		$this->params = &$params;
		// die entsprechende passende Zeile wird als array | Ã¼bergeben
		$this->elements = &$elements;
	}

	// Aufruf des Objektes mit den verschiedenen Zeigern
	function enterObject($email_elements,$sql_elements,$warning,$form_output,$send = 0)
	{
		
		// fuer email verschicken
		// $email_elements["feldname"] = "feldwert";
		
		// Zum Schreiben oder Aktualisieren des Eintrages
		// $sql_elements["feldname"] = "feldwert";

		// alle formulareintraeg
		// $form_elements
		
		// $warning["el_".$this->id] = "Warenkorb ist nicht vorhanden";

		// Formular ausgabe
		// $form_output[] = "<p>hallo</p>";

		// $send == 1 - formular wurde schonmal abgeschickt
	}
	
	function preAction()
	{
	
	}
	
	
	// Aufruf nachdem E-Mail oder Datenbankeintrag vorgenommen wurde
	function postAction($email_elements,$sql_elements)
	{
		/*
		unset($_SESSION["wk"]);
		ob_end_flush();
		ob_end_flush();
		header("Location:".rex_getUrl(28));
		exit;
		*/
	}

	// nachdem update oder insert sql ausgefÃ¼hrt wurde
	function postSQLAction($sql,$flag="insert")
	{
		// Zeiger auf sql Objekt
		if ($flag=="insert")
		{
			// $id = $sql->getLastId();
			
		}
	}
	
	// DB-feld Bezeichnung zurückgeben
	function getDatabasefieldname()
	{
		if (isset($this->elements[1])) return $this->elements[1];
	}
	

	
	//Id zurückgeben
	function getId()
	{
		return $this->id;
	}
	
	//Element mit Nummer $nr zurückgeben
	function getElement($nr)
	{
		return $this->elements[$nr];
	}
	
	function getDescription()
	{
		return "Es existiert keine Klassenbeschreibung";
	}


	function longDescription()
	{
		return "Es existiert keine auswührliche Klassenbeschreibung";
	}

}

?>