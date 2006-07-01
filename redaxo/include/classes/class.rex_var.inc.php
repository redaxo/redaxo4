<?php

/**
 * Abtrackte Basisklasse für REX_VARS innerhalb der Module
 * @package redaxo3
 * @version $Id$
 */

class rex_var
{
	
	// FE = Frontend
	// Ausgabe eines Modules fürs Frontend
	// sql Objekt mit der passenden Slice
	function getFEOutput(&$sql,$content)
	{	
		return $this->getBEOutput($sql,$content);
	}
	
	// BE = Backend
	// Ausgabe eines Modules im Backend bei der Ausgabe
	// sql Objekt mit der passenden Slice
	function getBEOutput(&$sql,$content)
	{
		return $content;
	}
	
	// BE = Backend
	// Ausgabe eines Modules im Backend bei der Eingabe
	// sql Objekt mit der passenden Slice
	function getBEInput(&$sql,$content)
	{
		return $this->getBEOutput($sql,$content);
	}

	function stripPHP($content)
	{
		$content = str_replace("<?","&lt;?",$content);
		$content = str_replace("?>","?&gt;",$content);
		return $content;
	}

}

?>