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
        $temp = rex_call_func($ext, $params);
        // RÜckgabewert nur auswerten wenn auch einer vorhanden ist
        // damit $params['subject'] nicht verfälscht wird 
        if($temp !== null)
        {
          $result = $temp; 
          $params['subject'] = $result;
        }
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
 * Prüft ob eine extension für den angegebenen Extension point definiert ist
 *
 * @param $extension Name der Extension
 */
function rex_extension_is_registered($extension)
{
  global $REX;
  return !empty ($REX['EXTENSIONS'][$extension]);
}

/**
 * Aufruf einer Funtion (Class-Member oder statische Funktion)
 *
 * @param $function Name der Callback-Funktion
 * @param $params Parameter für die Funktion
 * 
 * @example 
 *   rex_call_func( 'myFunction', array( 'Param1' => 'ab', 'Param2' => 12))
 * @example 
 *   rex_call_func( 'myObject::myMethod', array( 'Param1' => 'ab', 'Param2' => 12))
 * @example 
 *   rex_call_func( array('myObject', 'myMethod'), array( 'Param1' => 'ab', 'Param2' => 12))
 * @example 
 *   $myObject = new myObject();
 *   rex_call_func( array($myObject, 'myMethod'), array( 'Param1' => 'ab', 'Param2' => 12))
 */
function rex_call_func($function, $params)
{
  $func = '';

  if (is_string($function) && strlen($function) > 0)
  {
    // static class method
    if (strpos($function, '::') !== false)
    {
      preg_match('!(\w+)::(\w+)!', $function, $_match = array ());
      $_class_name = $_match[1];
      $_method_name = $_match[2];

      rex_check_callable($func = array ($_class_name, $_method_name));
    }
    // function call
    elseif (function_exists($function))
    {
      $func = $function;
    }
    else
    {
      trigger_error('rexExtension: Function "'.$function.'" not found!');
    }
  }
  // object method call
  elseif (is_array($function))
  {
    $_object = $function[0];
    $_method_name = $function[1];

    rex_check_callable($func = array ($_object, $_method_name));
  }
  else
  {
    trigger_error('rexExtension: Using of an unexpected function var "'.$function.'"!');
  }

  return call_user_func($func, $params);
}

function rex_check_callable($_callable)
{
  if (is_callable($_callable))
  {
    return true;
  }
  else
  {
    if (!is_array($_callable))
    {
      trigger_error('rexExtension: Unexpected vartype for $_callable given! Expecting Array!', E_USER_ERROR);
    }
    $_object = $_callable[0];
    $_method_name = $_callable[1];

    if (!is_object($_object))
    {
      $_class_name = $_object;
      if (!class_exists($_class_name))
      {
        trigger_error('rexExtension: Class "'.$_class_name.'" not found!', E_USER_ERROR);
      }
    }
    trigger_error('rexExtension: No such method "'.$_method_name.'" in class "'.get_class($_object).'"!', E_USER_ERROR);
  }
}
?>