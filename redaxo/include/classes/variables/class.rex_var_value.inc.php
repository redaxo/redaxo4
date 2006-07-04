<?php


/**
 * REX_VALUE[1], REX_HTML_VALUE[1], REX_PHP, REX_PHP_VALUE[1], REX_HTML, REX_IS_VALUE
 * @package redaxo3
 * @version $Id$
 */

class rex_var_value extends rex_var
{
  function getBEOutput(& $sql, $content)
  {
    $content = $this->getOutput($sql, $content, true);
    
    // hightlight_string funktioniert erst seit PHP 4.2.0 so wie wir es brauchen
    $php_content = $sql->getValue('php');
    if(version_compare(phpversion(), '4.2.0', '>='))
    {
      $php_content = highlight_string($php_content, true);
    }
    
    $content = str_replace('REX_PHP', $this->stripPHP($php_content), $content);
    return $content;
  }

  function getFEOutput(& $sql, $content)
  {
    $content = $this->getOutput($sql, $content, true);
    $content = str_replace('REX_PHP', $sql->getValue('php'), $content);
    return $content;
  }

  function getBEInput(& $sql, $content)
  {
    $content = $this->getOutput($sql, $content);
    $content = str_replace('REX_PHP', $sql->getValue('php'), $content);
    return $content;
  }

  function getOutput(& $sql, $content, $nl2br = false)
  {
    $content = $this->matchValue($sql, $content, $nl2br);
    $content = $this->matchHtmlValue($sql, $content);
    $content = $this->matchIsValue($sql, $content);
    $content = $this->matchPhpValue($sql, $content);

    return $content;
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
        $id
      );
    }

    return $matches;
  }

  /**
   * Wert für die Ausgabe
   */
  function _matchValue(& $sql, $content, $var, $escape = false, $nl2br = false, $stripPHP = false, $booleanize = false)
  {
    $matches = $this->getInputParams($content, $var);

    foreach ($matches as $match)
    {
      list ($param_str, $id) = $match;

      if ($id > 0 && $id < 21)
      {
        $replace = $this->getValue($sql, 'value'.$id);
        if ($booleanize)
        {
          $replace = $replace == '' ? 'false' : 'true';
        }
        else
        {
          if ($escape)
          {
            $replace = htmlspecialchars($replace);
          }

          if ($nl2br)
          {
            $replace = nl2br($replace);
          }

          if ($stripPHP)
          {
            $replace = $this->stripPHP($replace);
          }
        }

        $content = str_replace($var.'['.$param_str.']', $replace, $content);
      }
    }

    return $content;
  }

  function matchValue(& $sql, $content, $nl2br = false)
  {
    return $this->_matchValue($sql, $content, 'REX_VALUE', true, $nl2br);
  }

  function matchHtmlValue(& $sql, $content)
  {
    return $this->_matchValue($sql, $content, 'REX_HTML_VALUE', false, false, true);
  }

  function matchPhpValue(& $sql, $content)
  {
    return $this->_matchValue($sql, $content, 'REX_PHP_VALUE', false, false, false);
  }

  function matchIsValue(& $sql, $content)
  {
    return $this->_matchValue($sql, $content, 'REX_IS_VALUE', false, false, false, true);
  }
}
?>