<?php

/**
 * REX_MODULE_ID,
 * REX_SLICE_ID,
 * REX_CTYPE_ID
 *
 * @package redaxo3
 * @version $Id$
 */

class rex_var_globals extends rex_var
{
  // --------------------------------- Actions

  function getACRequestValues($REX_ACTION)
  {
    $REX_ACTION['SLICE_ID'] = rex_request('slice_id', 'int');
    $REX_ACTION['CTYPE_ID'] = rex_request('ctype', 'int');
    $REX_ACTION['MODULE_ID'] = rex_request('module_id', 'int');

    return $REX_ACTION;
  }

  function getACDatabaseValues($REX_ACTION, & $sql)
  {
    $REX_ACTION['SLICE_ID'] = $this->getValue($sql, 'id');
    $REX_ACTION['CTYPE_ID'] = $this->getValue($sql, 'ctype');
    $REX_ACTION['MODULE_ID'] = $this->getValue($sql, 'modultyp_id');

    return $REX_ACTION;
  }

  function setACValues(& $sql, $REX_ACTION, $escape = false)
  {
    $this->setValue($sql, 'id', $REX_ACTION['SLICE_ID']);
    $this->setValue($sql, 'ctype', $REX_ACTION['CTYPE_ID']);
    $this->setValue($sql, 'modultyp_id', $REX_ACTION['MODULE_ID']);
  }

  // --------------------------------- Output

  function getBEOutput(& $sql, $content)
  {
    // Modulabhängige Globale Variablen ersetzen
    $content = str_replace('REX_MODULE_ID', $this->getValue($sql, 'modultyp_id'), $content);
    $content = str_replace('REX_SLICE_ID', $this->getValue($sql, 'id'), $content);
    $content = str_replace('REX_CTYPE_ID', $this->getValue($sql, 'ctype'), $content);

    return $content;
  }
}
?>