<?php


/**
 * REX_VALUE[1],
 * REX_HTML_VALUE[1],
 * REX_PHP_VALUE[1],
 * REX_PHP,
 * REX_HTML,
 * REX_IS_VALUE
 *
 * @package redaxo4
 * @version $Id: class.rex_var_value.inc.php,v 1.4 2008/03/19 12:54:08 kristinus Exp $
 */

class rex_var_value extends rex_var
{
  // --------------------------------- Actions

  function getACRequestValues($REX_ACTION)
  {
    $values = rex_request('VALUE', 'array');
    for ($i = 1; $i < 21; $i++)
    {
      // Nur Werte die urspruenglich gepostet wurden auch uebernehmen
      // siehe http://forum.redaxo.de/ftopic8174.html
      if (isset ($values[$i]))
      {
        $REX_ACTION['VALUE'][$i] = stripslashes($values[$i]);
      }
    }
    $REX_ACTION['PHP'] = stripslashes(rex_request('INPUT_PHP', 'string'));
    $REX_ACTION['HTML'] = $this->stripPHP(stripslashes(rex_request('INPUT_HTML', 'string')));

    return $REX_ACTION;
  }

  function getACDatabaseValues($REX_ACTION, & $sql)
  {
    for ($i = 1; $i < 21; $i++)
    {
      $REX_ACTION['VALUE'][$i] = $this->getValue($sql, 'value'. $i);
    }
    $REX_ACTION['PHP'] = $this->getValue($sql, 'php');
    $REX_ACTION['HTML'] = $this->getValue($sql, 'html');

    return $REX_ACTION;
  }

  function setACValues(& $sql, $REX_ACTION, $escape = false)
  {
    global $REX;

    for ($i = 1; $i < 21; $i++)
    {
      // Nur Werte die urspruenglich gepostet wurden auch uebernehmen
      // siehe http://forum.redaxo.de/ftopic8174.html
      if(isset($REX_ACTION['VALUE'][$i]))
      {
        $this->setValue($sql, 'value' . $i, $REX_ACTION['VALUE'][$i], $escape);
      }
    }

    $this->setValue($sql, 'php', $REX_ACTION['PHP'], $escape);
    $this->setValue($sql, 'html', $REX_ACTION['HTML'], $escape);
  }

  // --------------------------------- Output

  function getBEOutput(& $sql, $content)
  {
    $content = $this->getOutput($sql, $content, true);

    $php_content = $this->getValue($sql, 'php');
    $php_content = highlight_string($php_content, true);

    $content = str_replace('REX_PHP', $this->stripPHP($php_content), $content);
    return $content;
  }

  function getBEInput(& $sql, $content)
  {
    $content = $this->getOutput($sql, $content);
    $content = str_replace('REX_PHP', htmlspecialchars($this->getValue($sql, 'php'),ENT_QUOTES), $content);
    return $content;
  }

  function getFEOutput(& $sql, $content)
  {
    $content = $this->getOutput($sql, $content, true);
    $content = str_replace('REX_PHP', $this->getValue($sql, 'php'), $content);
    return $content;
  }

  function getOutput(& $sql, $content, $nl2br = false)
  {
    $content = $this->matchValue($sql, $content, $nl2br);
    $content = $this->matchHtmlValue($sql, $content);
    $content = $this->matchIsValue($sql, $content);
    $content = $this->matchPhpValue($sql, $content);
    $content = str_replace('REX_HTML', $this->getValue($sql, 'html'), $content);

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
        $replace = $this->getValue($sql, 'value' . $id);
        if ($booleanize)
        {
          $replace = $replace == '' ? 'false' : 'true';
        }
        else
        {
          if ($escape)
          {
            $replace = htmlspecialchars($replace,ENT_QUOTES);
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

        $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
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