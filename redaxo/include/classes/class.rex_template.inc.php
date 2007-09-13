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

  function getFile()
  {
    global $REX;

    if($this->getId()<1) return FALSE;

    return $REX['INCLUDE_PATH'] . '/generated/templates/' . $this->getId() . '.template';
  }

  function getTemplate()
  {
    global $REX;

		if($this->getId()<1) return FALSE;

    $file = $this->getFile();
    if ($handle = @fopen($file, 'r'))
    {
	    $fs = filesize($file);
	    if ($fs>0) $content = fread($handle, filesize($file));
	    fclose($handle);
	    return '?>'. $content;
    }else
    {
    	include_once ($REX['INCLUDE_PATH'].'/functions/function_rex_generate.inc.php');
    	if(rex_generateTemplate($this->getId()))
      {
        // rekursiv aufrufen, nach dem erfolgreichen generate
        return $this->getTemplate();
    	}
    }
		return FALSE;
  }

  function deleteCache()
  {
  	global $REX;

		if($this->id<1) return FALSE;

		$file = $this->getFile();
    if (@unlink($file)) return TRUE;
    else return FALSE;
  }
}
?>