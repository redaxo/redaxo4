<?php

/**
 * Template Objekt.
 * Zuständig für die Verarbeitung eines Templates
 *
 * @package redaxo4
 * @version $Id: class.rex_template.inc.php,v 1.6 2008/03/20 15:34:42 kills Exp $
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
      return $this->getFilePath($this->getId());

    trigger_error('Unable to generate rexTemplate with id "'. $this->getId() . '"', E_USER_ERROR);

    return FALSE;
  }

  function getFilePath($template_id)
  {
    if($template_id<1) return FALSE;

    return rex_template::getTemplatesDir() .'/' . $template_id . '.template';
  }

  function getTemplatesDir()
  {
    global $REX;

    return $REX['INCLUDE_PATH'] . '/generated/templates';
  }

  function getTemplate()
  {
		if($this->getId()<1) return FALSE;

    $file = $this->getFilePath($this->getId());
    if (file_exists($file))
    {
      return rex_get_file_contents($file);
    }
    else
    {
    	if($this->generate())
        return rex_get_file_contents($file);
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

		$file = $this->getFilePath($this->getId());
    return @unlink($file);
  }
}
?>