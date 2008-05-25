<?php

/**
 * REX_TEMPLATE[2]
 *
 * @package redaxo4
 * @version $Id: class.rex_var_template.inc.php,v 1.3 2008/02/25 09:51:11 kills Exp $
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

  /**
   * Wert für die Ausgabe
   */
  function matchTemplate($content)
  {
    $var = 'REX_TEMPLATE';
    $matches = $this->getVarParams($content, $var);

    foreach ($matches as $match)
    {
      list ($param_str, $template_id, $args) = $match;

      $template = new rex_template($template_id);
      $replace = $template->getTemplate();
      $replace = $this->handleGlobalVarParams($var, $args, $replace);
      $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
    }

    return $content;
  }
}
?>