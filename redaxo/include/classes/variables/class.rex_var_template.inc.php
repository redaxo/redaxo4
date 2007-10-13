<?php

/**
 * REX_TEMPLATE[2]
 * 
 * @package redaxo4
 * @version $Id$
 */

class rex_var_template extends rex_var
{
  // --------------------------------- Output
  
  function getBEOutput(& $sql, $content)
  {
    return $this->matchTemplate($content);
  }
  
  function getTemplate($content)
  {
    return $this->matchTemplate($content);
  }

  function getInputParams($content, $varname)
  {
    $matches = array ();
    $id = '';

    $match = $this->matchVar($content, $varname);
    foreach ($match as $param_str)
    {
      $params = $this->splitString($param_str);

      foreach ($params as $name => $value)
      {
        switch ($name)
        {
          case '0' :
          case 'id' :
            $id = (int) $value;
            break;
        }
      }

      $matches[] = array (
        $param_str,
        $id,
      );
    }

    return $matches;
  }

  /**
   * Wert für die Ausgabe
   */
  function matchTemplate($content)
  {
    $var = 'REX_TEMPLATE';
    $matches = $this->getInputParams($content, $var);

    foreach ($matches as $match)
    {
      list ($param_str, $template_id) = $match;
      
      $template = new rex_template($template_id);
      $content = str_replace($var . '[' . $param_str . ']', $template->getTemplate(), $content);
    }

    return $content;
  }
}
?>