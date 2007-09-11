<?php

/**
 * Template Objekt.
 * Zuständig für die Verarbeitung eines Templates
 * 
 * @package redaxo3
 * @version $Id$
 */

class rex_template
{
  var $id;

  function rex_template($template_id = 0)
  {
    $this->setId($template_id);
  }

  function getId()
  {
    return $this->id;
  }

  function setId($id)
  {
    $this->id = (int) $id;
  }

  function getTemplate()
  {
    global $REX;

		if($this->getId()<1) return FALSE;

    $file = $REX['INCLUDE_PATH'] . '/generated/templates/' . $this->getId() . '.template';
    if ($handle = @fopen($file, 'r'))
    {
	    $fs = filesize($file);
	    if ($fs>0) $content = fread($handle, filesize($file));
	    fclose($handle);
	    return $content;
    }else
    {
    	include_once ($REX["INCLUDE_PATH"]."/functions/function_rex_generate.inc.php");
    	rex_generateTemplate($this->getId());
	    if ($handle = @fopen($file, 'r'))
	    {
		    $fs = filesize($file);
		    if ($fs>0) $content = fread($handle, filesize($file));
		    fclose($handle);
		    return $content;
	    }else
	    {
    		return FALSE;
    	}
    }
  }
  
  function deleteCache()
  {
  	global $REX;

		if($this->id<1) return FALSE;
		
		$file = $REX['INCLUDE_PATH'] . '/generated/templates/' . $this->getId() . '.template';
    if (@unlink($file)) return TRUE;
    else return FALSE;
  }
}
?>