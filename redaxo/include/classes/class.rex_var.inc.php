<?php

/**
 * Abtrackte Basisklasse für REX_VARS innerhalb der Module
 * @package redaxo3
 * @version $Id$
 */

class rex_var
{
  /**
   * Ausgabe eines Modules fürs Frontend
   * sql Objekt mit der passenden Slice
   * 
   * FE = Frontend
   */	
	function getFEOutput(&$sql,$content)
	{	
		return $this->getBEOutput($sql,$content);
	}
	
  /**
   * Ausgabe eines Modules im Backend bei der Ausgabe
   * sql Objekt mit der passenden Slice
   * 
   * BE = Backend
   */
	function getBEOutput(&$sql,$content)
	{
		return $content;
	}
	
	/**
	 * Ausgabe eines Modules im Backend bei der Eingabe
	 * sql Objekt mit der passenden Slice
   * 
   * BE = Backend
   */
	function getBEInput(&$sql,$content)
	{
		return $this->getBEOutput($sql,$content);
	}

  /**
   * Wandelt PHP Code in Einfache Textausgaben um
   */
	function stripPHP($content)
	{
		$content = str_replace("<?","&lt;?",$content);
		$content = str_replace("?>","?&gt;",$content);
		return $content;
	}
  
  /**
   * GetValue Wrapper, da hier immer auf die gleiche Tabelle gearbeitet wird und 
   * mit MySQL 3.x mit Tabellenprefix angegeben werden muss, da der gleichnamige
   * Spalten unterschiedlicher Tabellen enthält. 
   */
  function getValue(&$sql, $value)
  {
    global $REX;
    return $sql->getValue($REX['TABLE_PREFIX'].'article_slice.'.$value);
  }
  
  /**
   * Findet den Parameter einer Variable in der Ausgabe.
   * Da dort immer nur ein ID Feld sinnvoll ist, wird das ganze hier in der
   * Basisklasse gemacht.
   * @access protected
   */
  function getOutputParam($content, $varname)
  {
    $result = array();
    
    $match = $this->matchVar($content, $varname);
    foreach ($match as $param_str)
    {
      $params = $this->splitString($param_str);
      
      $id = '';
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
      
      if($id != '')
      {
        $result[] = array($param_str, $id);
      }
      
    }
      
    return $result;
  }
  
  /**
   * Durchsucht den String $content nach Variablen mit dem Namen $varname.
   * Gibt die Parameter der Treffer (Text der Variable zwischen den []) als Array zurück.
   */
  function matchVar($content, $varname)
  {
    $result = array();
    
    if (preg_match_all('/'.$varname.'\[([^\]]*)\]/ms', $content, $matches = array ()))
    {
      foreach($matches[1] as $match)
      {
        $result[] = $match;
      }
    }
    
    return $result;
  }
  
  /**
   * Trennt einen String an Leerzeichen auf.
   * Abschnitte die in "" oder '' stehen, werden als ganzes behandelt und
   * darin befindliche Leerzeichen nicht getrennt.
   * @access protected
   */
  function splitString($string)
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
}
?>