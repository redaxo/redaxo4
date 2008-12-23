<?php

/**
 * REX_MODULE_ID,
 * REX_SLICE_ID,
 * REX_CTYPE_ID
 *
 * @package redaxo4
 * @version $Id: class.rex_var_globals.inc.php,v 1.3 2008/02/26 10:42:46 kills Exp $
 */

class rex_var_globals extends rex_var
{
  // --------------------------------- Actions

  function getACRequestValues($REX_ACTION)
  {
    // SLICE ID im Update Mode setzen
    if($this->isEditEvent())
    {
      $REX_ACTION['EVENT'] = 'EDIT';
      $REX_ACTION['SLICE_ID'] = rex_request('slice_id', 'int');
    }
    // SLICE ID im Delete Mode setzen
    elseif ($this->isDeleteEvent())
    {
      $REX_ACTION['EVENT'] = 'DELETE';
      $REX_ACTION['SLICE_ID'] = rex_request('slice_id', 'int');
    }
    // Im Add Mode 0 setze wg auto-increment
    else
    {
      $REX_ACTION['EVENT'] = 'ADD';
      $REX_ACTION['SLICE_ID'] = 0;
    }

    // Variablen hier einfuegen, damit sie in einer
    // Aktion abgefragt werden können
    $REX_ACTION['ARTICLE_ID'] = rex_request('article_id', 'int');
    $REX_ACTION['CLANG_ID'] = rex_request('clang', 'int');
    $REX_ACTION['CTYPE_ID'] = rex_request('ctype', 'int');
    $REX_ACTION['MODULE_ID'] = rex_request('module_id', 'int');

    return $REX_ACTION;
  }

  function getACDatabaseValues($REX_ACTION, & $sql)
  {
    // Variablen hier einfuegen, damit sie in einer
    // Aktion abgefragt werden können
    $REX_ACTION['ARTICLE_ID'] = $this->getValue($sql, 'article_id');
    $REX_ACTION['CLANG_ID'] = $this->getValue($sql, 'clang');
    $REX_ACTION['CTYPE_ID'] = $this->getValue($sql, 'ctype');
    $REX_ACTION['MODULE_ID'] = $this->getValue($sql, 'modultyp_id');
    $REX_ACTION['SLICE_ID'] = $this->getValue($sql, 'id');

    return $REX_ACTION;
  }

  function setACValues(& $sql, $REX_ACTION, $escape = false)
  {
    $this->setValue($sql, 'id', $REX_ACTION['SLICE_ID'], $escape);
    $this->setValue($sql, 'ctype', $REX_ACTION['CTYPE_ID'], $escape);
    $this->setValue($sql, 'modultyp_id', $REX_ACTION['MODULE_ID'], $escape);
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