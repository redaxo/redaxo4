<?php


// REX_MODULE_ID

class rex_var_globals extends rex_var
{
  function getBEOutput(& $sql, $content)
  {
    // Modulabhängige globale variablen ersetzen
    $content = str_replace('REX_MODULE_ID', $this->getValue($sql, 'modultyp_id'), $content);
    $content = str_replace('REX_SLICE_ID', $this->getValue($sql, 'id'), $content);
    $content = str_replace('REX_CTYPE_ID', $this->getValue($sql, 'ctype'), $content);
    
    return $content;
  }
}
?>