<?php

/**
* Debug_Out gibt Variableninfos aus
*
* Aufrufbeispiel: Debug_Out(array('Variablenname' => $variable));
*
* @param  mixed
* @param  mixed   Anweisungen fuer die switch-Abfrage
*                 'sql', 'obj' - speziell formatierte Ausgabe
*/
function Debug_Out($input, $spezial = '') {
  //echo '<br />';
  $return = '';
  switch ($spezial) {
    case 'sql': 
      if ($spezial == 'sql') {
        $return .= "\n".nl2br($input)."<br /><br />\n";
      }
      break;
    case 'obj': 
      $return .= '<pre>'; print_r($input); echo '</pre>';
      break;
    default:  
  
    if (is_array($input)) {
      foreach ($input as $key => $wert) {
        $return .= $key.' => '.$wert.'<br />';
      }
    }
    if (is_object($input)) {
      $return .= '<pre>'; $return .= print_r($input, true); $return .= '</pre>';
    }
    if (!is_array($input)) {
      $return .= $input.'<br />';
    }
    break;
  } // switch ($spezial)
  flush();
  
  // setze vor die Ausgabe einen deutlichen Hinweis auf die Debug-Ausgabe
  $return = '<span style="font-weight: bold;">DEBUGOUT: </span>'.$return;
  // gib die Debuginfos aus
  echo $return;

  return true;
}


/**
* DebugOut
*
* @see  Debug_Out
*/
function DebugOut($input, $spezial = '') {
  return Debug_Out($input, $spezial = '');
}



?>