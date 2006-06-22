<?php

/**
 * 
 * @package redaxo3
 * @version $Id$
 */

// rex_replace_variables als Extension anfügen
rex_register_extension('GENERATE_FILTER', 'rex_replace_variables');

/**
 * Registriert für die Variable $variable die Callback-Funktion $function
 * @param $variable Name der zur ersetzenden Variable
 * @param $function Name der Callback-Funktion
 * @access public
 */
function rex_register_variable($variable, $function)
{
  global $REX;
  return $REX['VARIABLES'][$variable] = $function;
}

/**
 * Gibt alle registrierten Variablen als Array zurück
 * @access protected
 */
function rex_get_registered_variables()
{
  global $REX;
  if (isset($REX['VARIABLES']) && is_array($REX['VARIABLES']))
  {
    return $REX['VARIABLES'];
  }
  return array ();
}

/**
 * Sucht alle Variablen in der Ausgabe und ersetzt diese durch die Rückgabewerte der Callback-Funktionen
 * @access private
 */
function rex_replace_variables($params)
{
  $content = $params['subject'];
  $variables = rex_get_registered_variables();

  foreach ($variables as $variable => $callback)
  {
    if (preg_match_all('/'.$variable.'\[([^\]]*)\]/ms', $content, $matches = array ()))
    {
      if(!isset($matches[1][0])) 
      {
        continue;
      }
      $call_params = rex_split_variable_string($matches[1][0]);
      $content = str_replace($variable .'['.$matches[1][0].']', rex_call_func($callback, $call_params), $content);
    }
  }
  return $content;
}

/**
 * Trennt einen String an Leerzeichen auf.
 * Abschnitte die in "" oder '' stehen, werden als ganzes behandelt und
 * darin befindliche Leerzeichen nicht getrennt.
 * @access protected
 */
function rex_split_variable_string($string)
{
  $spacer = '@@@REX_SPACER@@@';
  $result = array ();
  
  // TODO mehrfachspaces hintereinander durch einfachen ersetzen
  $string = ' '. trim($string) .' ';

  // Strings mit Quotes heraussuchen
  preg_match_all('!(["\'])(.*)\\1!', $string, $matches = array ());
  $quoted = isset($matches[2]) ? $matches[2] : array();
  
  // Strings mit Quotes maskieren
  $string = preg_replace('!(["\'])(.*)\\1!', $spacer, $string);

  // ----------- z.b. 4 "av c" 'de f' ghi
  if (strpos($string, '=') === false)
  {
    $parts = explode(' ', $string);
    foreach ($parts as $part)
    {
      if (empty ($part))
      {
        continue;
      }
      
      if ($part == $spacer)
      {
        $result[] = array_shift($quoted);
      }
      else
      {
        $result[] = $part;
      }
    }
  }
  // ------------ z.b. a=4 b="av c" y='de f' z=ghi
  else
  {
    $parts = explode(' ', $string);
    foreach($parts as $part)
    {
      $variable = explode('=', $part);
      $var_name = $variable[0];
      $var_value = $variable[1];
      
      if (empty ($var_name))
      {
        continue;
      }
      
      if($var_value == $spacer)
      {
        $var_value = array_shift($quoted);
      }
      
      $result[$var_name] = $var_value;
    }
  }
  return $result;
}
?>