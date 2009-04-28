<?php

class rex_xform_validate_abstract
{
	var $xaObjects = array();
	var $params = array();
	var $xaElements = array();
	var $Objects; // die verschiedenen Value Objekte
	
	function loadParams(&$params, &$elements)
	{
		$this->params = $params;
		$this->xaElements=$elements;
	}
	
	function setObjects($Objects)
	{
		$this->Objects = $Objects; 
	
		$xatmpObjects = explode(",", $this->xaElements[2]);
		
		foreach($xatmpObjects as $xatmpObject)
		{
			$xbFoundObject=false;
			foreach($Objects as $Object)
			{
				if(strcmp($Object->getDatabasefieldname(),trim($xatmpObject))==0)
				{
					$this->xaObjects[] = &$Object;
					$xbFoundObject = true;
					break;
				}
			}
			if(!$xbFoundObject)
				echo "FEHLER: Object ".$xatmpObject." nicht gefunden!";
		}
	}
	
	function enterObject()
	{
		
	}
	
	function getDescription()
	{
		return "Für dieses Objekt fehlt die Beschreibung";
	}

	function getLongDescription()
	{
		return "Für dieses Objekt fehlt die Beschreibung";
	}
	
}