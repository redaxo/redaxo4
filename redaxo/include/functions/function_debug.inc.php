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
  switch ($spezial) {
    case 'sql': 
      if ($spezial == 'sql') {
        echo "\n".nl2br($input)."<br /><br />\n";
      }
      break;
    case 'obj': 
      echo '<pre>'; print_r($input); echo '</pre>';
      break;
    default:  
  
    if (is_array($input)) {
      foreach($input as $key => $wert) {
        echo $key.' => '.$wert.'<br />';
      }
    }
    if (is_object($input)) {
      echo '<pre>'; print_r($input); echo '</pre>';
    }
    if (!is_array($input)) {
      echo $input.'<br />';
    }
    break;
  } // switch ($spezial)
  flush();
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