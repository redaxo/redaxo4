<?php
/**
 * Getter Funktionen zum Handling von Superglobalen Variablen 
 * 
 * @package redaxo4
 * @version $Id: function_rex_globals.inc.php,v 1.1 2007/12/28 10:45:10 kills Exp $
 */

/**
 * Gibt die Superglobale variable $varname des Array $_GET zurück und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_get($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_GET, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_POST zurück und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_post($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_POST, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_REQUEST zurück und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_request($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_REQUEST, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_SERVER zurück und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_server($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_SERVER, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_SESSION zurück und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_session($varname, $vartype = '', $default = '')
{
  global $REX;

  if(isset($_SESSION[$varname][$REX['INSTNAME']]))
  {
    return _rex_cast_var($_SESSION[$varname][$REX['INSTNAME']], $vartype);
  }
  
  return $default;
}

/**
 * Setzt den Wert einer Session Variable.
 * 
 * Variablen werden Instanzabhängig gespeichert.
 */
function rex_set_session($varname, $value)
{
  global $REX;

  $_SESSION[$varname][$REX['INSTNAME']] = $value;
}

/**
 * Löscht den Wert einer Session Variable.
 * 
 * Variablen werden Instanzabhängig gelöscht.
 */
function rex_unset_session($varname)
{
  global $REX;

  unset($_SESSION[$varname][$REX['INSTNAME']]);
}

/**
 * Gibt die Superglobale variable $varname des Array $_COOKIE zurück und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_cookie($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_COOKIE, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_FILES zurück und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_files($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_FILES, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_ENV zurück und castet dessen Wert ggf.
 * 
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_env($varname, $vartype = '', $default = '')
{
  return _rex_array_key_cast($_ENV, $varname, $vartype, $default);
}

/**
 * Durchsucht das Array $haystack nach dem Schlüssel $needle.
 *  
 * Falls ein Wert gefunden wurde wird dieser nach 
 * $vartype gecastet und anschließend zurückgegeben.
 * 
 * Falls die Suche erfolglos endet, wird $default zurückgegeben
 * 
 * @access private
 */
function _rex_array_key_cast($haystack, $needle, $vartype, $default = '')
{
  if(!is_array($haystack))
  {
    trigger_error('Array expected for $haystack in _rex_array_key_cast()!', E_USER_ERROR);
    exit();
  }
  
  if(!is_scalar($needle))
  {
    trigger_error('Scalar expected for $needle in _rex_array_key_cast()!', E_USER_ERROR);
    exit();
  }
  
  if(array_key_exists($needle, $haystack))
  {
    $var = $haystack[$needle];
    return _rex_cast_var($var, $vartype);
  }
  
  return _rex_cast_var($default, $vartype);
}

/**
 * Castet die Variable $var zum Typ $vartype
 * 
 * Mögliche Typen sind:
 *  - bool (auch boolean)
 *  - int (auch integer)
 *  - double
 *  - string
 *  - float
 *  - object
 *  - array
 *  - '' (nicht casten)
 * 
 * @access private
 */
function _rex_cast_var($var, $vartype)
{
  if(!is_string($vartype))
  {
    trigger_error('String expected for $vartype in _rex_cast_var()!', E_USER_ERROR);
    exit(); 
  }
  
  // Variable Casten    
  switch($vartype)
  {
    case 'bool'   :
    case 'boolean': $var = (boolean) $var; break; 
    case 'int'    : 
    case 'integer': $var = (int)     $var; break; 
    case 'double' : $var = (double)  $var; break; 
    case 'float'  : $var = (float)   $var; break; 
    case 'string' : $var = (string)  $var; break; 
    case 'object' : $var = (object)  $var; break; 
    case 'array'  : $var = (array)   $var; break;
    
    // kein Cast, nichts tun
    case ''       : break;
    
    // Evtl Typo im vartype, deshalb hier fehlermeldung!
    default: trigger_error('Unexpected vartype "'. $vartype .'" in _rex_cast_var()!', E_USER_ERROR); exit(); 
  }
  
  return $var;
}
?>