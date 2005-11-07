<?php


/** 
 * Funktionen zur Registrierung von Schnittstellen 
 * @package redaxo3 
 * @version $Id$ 
 */

/**
* Definiert einen Extension Point
*
* @param $extension Name der Extension
* @param $subject Objekt/Variable die beeinflusst werden soll
* @param $params Parameter für die Callback-Funktion
*/
function rex_register_extension_point($extension, $subject = '', $params = array (), $read_only = false)
{
  global $REX;
  $result = $subject;

  if (!is_array($params))
  {
    $params = array ();
  }

  if (isset ($REX['EXTENSIONS'][$extension]) && is_array($REX['EXTENSIONS'][$extension]))
  {
    $params['subject'] = $subject;
    if ($read_only)
    {
      foreach ($REX['EXTENSIONS'][$extension] as $ext)
      {
        rex_call_func($ext, $params);
      }
    }
    else
    {
      foreach ($REX['EXTENSIONS'][$extension] as $ext)
      {
        $result = rex_call_func($ext, $params);
        $params['subject'] = $result;
      }
    }
  }
  return $result;
}

/**
 * Definiert eine Callback-Funktion, die an dem Extension Point $extension aufgerufen wird
 *
 * @param $extension Name der Extension
 * @param $function Name der Callback-Funktion
 */
function rex_register_extension($extension, $function)
{
  global $REX;
  $REX['EXTENSIONS'][$extension][] = $function;
}

/**
 * Aufruf einer Funtion (Class-Member oder statische Funktion)
 *
 * @param $function Name der Callback-Funktion
 * @param $params Parameter für die Funktion
 */
function rex_call_func($function, $params)
{
  $func = '';

  if (is_string($function) && strlen($function) > 0)
  {
    if (strpos($function, '::') !== false)
    {
      // static method
      preg_match('!(\w+)::(\w+)!', $function, $_match = array ());
      $_object_name = $_match[1];
      $_method_name = $_match[2];

      if (is_callable(array ($_object_name, $_method_name)))
      {
        $func = array ($_object_name, $_method_name);
      }
    }
    elseif (function_exists($function))
    {
      // function call
      $func = $function;
    }
  }
  elseif (is_array($function))
  {
    $func = $function;
  }
  else
  {
    trigger_error('rexExtension: Using of an unexpected function var "'.$function.'"');
  }

  return call_user_func($func, $params);
}
?>