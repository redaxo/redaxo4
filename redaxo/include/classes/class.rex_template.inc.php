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
    // Generated Datei erzeugen
    if($this->generate())
      return $this->getFilePath();

    return FALSE;
  }

  function getFilePath()
  {
    global $REX;

    if($this->getId()<1) return FALSE;

    return $REX['INCLUDE_PATH'] . '/generated/templates/' . $this->getId() . '.template';
  }

  function getTemplate()
  {
		if($this->getId()<1) return FALSE;

    $file = $this->getFilePath();
    if ($handle = @fopen($file, 'r'))
    {
      $content = '';
	    $fs = filesize($file);
	    if ($fs>0) $content = fread($handle, filesize($file));
	    fclose($handle);
	    return $content;
    }else
    {
    	if($this->generate())
      {
        // rekursiv aufrufen, nach dem erfolgreichen generate
        return $this->getTemplate();
    	}
    }
		return FALSE;
  }

  function generate()
  {
    global $REX;

    if($this->getId()<1) return FALSE;

    include_once ($REX['INCLUDE_PATH'].'/functions/function_rex_generate.inc.php');
    return rex_generateTemplate($this->getId());
  }

  function deleteCache()
  {
  	global $REX;

		if($this->id<1) return FALSE;

		$file = $this->getFilePath();
    if (@unlink($file)) return TRUE;
    else return FALSE;
  }
}
?>