<?php

/**
 * MetaForm Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: class.rex_table_expander.inc.php,v 1.5 2008/03/26 20:29:56 kills Exp $
 */

define('REX_A62_FIELD_TEXT',                 1);
define('REX_A62_FIELD_TEXTAREA',             2);
define('REX_A62_FIELD_SELECT',               3);
define('REX_A62_FIELD_RADIO',                4);
define('REX_A62_FIELD_CHECKBOX',             5);
define('REX_A62_FIELD_REX_MEDIA_BUTTON',     6);
define('REX_A62_FIELD_REX_MEDIALIST_BUTTON', 7);
define('REX_A62_FIELD_REX_LINK_BUTTON',      8);
define('REX_A62_FIELD_REX_LINKLIST_BUTTON',  9);

define('REX_A62_FIELD_COUNT',                9);

class rex_a62_tableExpander extends rex_form
{
  var $metaPrefix;
  var $tableManager;

  function rex_a62_tableExpander($metaPrefix, $metaTable, $tableName, $fieldset, $whereCondition, $method = 'post', $debug = false)
  {
    $this->metaPrefix = $metaPrefix;
    $this->tableManager = new rex_a62_tableManager($metaTable);

    parent::rex_form($tableName, $fieldset, $whereCondition, $method, $debug);
  }

  function init()
  {
    global $REX, $I18N_META_INFOS;

    // ----- EXTENSION POINT
    // IDs aller Feldtypen bei denen das Parameter-Feld eingeblendet werden soll
    $typeFields = rex_register_extension_point( 'A62_TYPE_FIELDS', array(REX_A62_FIELD_SELECT, REX_A62_FIELD_RADIO, REX_A62_FIELD_CHECKBOX));

    $field =& $this->addReadOnlyField('prefix', $this->metaPrefix);
    $field->setLabel($I18N_META_INFOS->msg('field_label_prefix'));

    $field =& $this->addTextField('name');
    $field->setLabel($I18N_META_INFOS->msg('field_label_name'));

    $field =& $this->addSelectField('prior');
    $field->setLabel($I18N_META_INFOS->msg('field_label_prior'));
    $select =& $field->getSelect();
    $select->setSize(1);
    $select->addOption($I18N_META_INFOS->msg('field_first_prior'), 1);
    // Im Edit Mode das Feld selbst nicht als Position einfügen
    $qry = 'SELECT name,prior FROM '. $this->tableName .' WHERE `name` LIKE "'. $this->metaPrefix .'%"';
    if($this->isEditMode())
    {
      $qry .= ' AND field_id != '. $this->getParam('field_id');
    }
    $qry .=' ORDER BY prior';
    $sql = new rex_sql();
    $sql->setQuery($qry);
    for($i = 0; $i < $sql->getRows(); $i++)
    {
      $select->addOption(
        $I18N_META_INFOS->msg('field_after_prior', $sql->getValue('name')),
        $sql->getValue('prior')+1
      );
      $sql->next();
    }

    $field =& $this->addTextField('title');
    $field->setLabel($I18N_META_INFOS->msg('field_label_title'));
    $field->setNotice($I18N_META_INFOS->msg('field_notice_title'));

    $field =& $this->addSelectField('type');
    $field->setLabel($I18N_META_INFOS->msg('field_label_type'));
    $field->setAttribute('onchange', 'checkConditionalFields(this, new Array('. implode(',', $typeFields) .'));');
    $select =& $field->getSelect();
    $select->setSize(1);

    $qry = 'SELECT label,id FROM '. $REX['TABLE_PREFIX'] .'62_type';
    if($this->metaPrefix == 'med_')
      $qry .= ' WHERE label NOT LIKE "REX_MEDIA%"';
    $select->addSqlOptions($qry);

    $notices = '';
    for($i = 1; $i < REX_A62_FIELD_COUNT; $i++)
    {
      if($I18N_META_INFOS->hasMsg('field_params_notice_'. $i))
      {
        $notices .= '<span class="rex-notice" id="a62_field_params_notice_'. $i .'" style="display:none">'. $I18N_META_INFOS->msg('field_params_notice_'. $i) .'</span>'. "\n";
      }
    }
    $notices .= '
    <script type="text/javascript">
      var needle = new getObj("'. $field->getAttribute('id') .'");

      checkConditionalFields(needle.obj, new Array('. implode(',', $typeFields) .'));
    </script>';

    $field =& $this->addTextAreaField('params');
    $field->setLabel($I18N_META_INFOS->msg('field_label_params'));
    $field->setSuffix($notices);

    $field =& $this->addTextAreaField('attributes');
    $field->setLabel($I18N_META_INFOS->msg('field_label_attributes'));
    $notice = '<span class="rex-notice" id="a62_field_attributes_notice">'. $I18N_META_INFOS->msg('field_attributes_notice') .'</span>'. "\n";
    $field->setSuffix($notice);

    $field =& $this->addTextField('default');
    $field->setLabel($I18N_META_INFOS->msg('field_label_default'));

//    $field =& $this->addTextAreaField('validate');
//    $field->setLabel($I18N_META_INFOS->msg('field_label_validate'));
  }

  function getFieldsetName()
  {
    global $I18N_META_INFOS;
    return $I18N_META_INFOS->msg('field_fieldset');
  }

  function getFieldValue($fieldName)
  {
    $el =& $this->getElement($this->getFieldsetName(), $fieldName);
    return $el->getValue();
  }

  function delete()
  {
    if(parent::delete())
    {
      // Prios neu setzen, damit keine lücken entstehen
      $this->organizePriorities(1,2);
      return $this->tableManager->deleteColumn($this->getFieldValue('name'));
    }
    return false;
  }

  function preDelete($fieldsetName, $fieldName, $fieldValue, &$deleteSql)
  {
    global $REX;

    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      // Vorm löschen, Prefix wieder anfügen
      return $this->addPrefix($fieldValue);
    }

    return parent::preDelete($fieldsetName, $fieldName, $fieldValue, $deleteSql);
  }

  function preSave($fieldsetName, $fieldName, $fieldValue, &$saveSql)
  {
    global $REX;

    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      // Den Namen mit Prefix speichern
      return $this->addPrefix($fieldValue);
    }

    return parent::preSave($fieldsetName, $fieldName, $fieldValue, $saveSql);
  }

  function preView($fieldsetName, $fieldName, $fieldValue)
  {
    if($fieldsetName == $this->getFieldsetName() && $fieldName == 'name')
    {
      // Den Namen ohne Prefix anzeigen
      return $this->stripPrefix($fieldValue);
    }
    return parent::preView($fieldsetName, $fieldName, $fieldValue);
  }

  function addPrefix($string)
  {
    $lowerString = strtolower($string);
    if(substr($lowerString, 0, strlen($this->metaPrefix)) !== $this->metaPrefix)
    {
      return $this->metaPrefix . $string;
    }
    return $string;
  }

  function stripPrefix($string)
  {
    $lowerString = strtolower($string);
    if(substr($lowerString, 0, strlen($this->metaPrefix)) === $this->metaPrefix)
    {
      return substr($string, strlen($this->metaPrefix));
    }
    return $string;
  }

  function validate()
  {
    global $I18N_META_INFOS;

    $fieldName = $this->getFieldValue('name');
    if($fieldName == '')
      return $I18N_META_INFOS->msg('field_error_name');

    if(preg_match('/[^a-zA-Z0-9\_]/', $fieldName))
      return $I18N_META_INFOS->msg('field_error_chars_name');

    // Prüfen ob schon eine Spalte mit dem Namen existiert (nur beim add nötig)
    if(!$this->isEditMode())
    {
      $sql = new rex_sql();
      $sql->setQuery('SELECT * FROM '. $this->tableName .' WHERE name="'. $this->addPrefix($fieldName) .'" LIMIT 1');
      if($sql->getRows() == 1)
      {
        return $I18N_META_INFOS->msg('field_error_unique_name');
      }
    }

    return parent::validate();
  }

  function save()
  {
    $fieldName = $this->getFieldValue('name');

    // Den alten Wert aus der DB holen
    // Dies muss hier geschehen, da in parent::save() die Werte für die DB mit den
    // POST werten überschrieben werden!
    $fieldOldName = '';
    $fieldOldPrior = 9999999999999; // dirty, damit die prio richtig läuft...
    $fieldOldDefault = '';
    if($this->sql->getRows() == 1)
    {
      $fieldOldName = $this->sql->getValue('name');
      $fieldOldPrior = $this->sql->getValue('prior');
      $fieldOldDefault = $this->sql->getValue('default');
    }

    if(parent::save())
    {
      global $REX, $I18N;

      $this->organizePriorities($this->getFieldValue('prior'), $fieldOldPrior);
      rex_generateAll();

      $fieldName = $this->addPrefix($fieldName);
      $fieldType = $this->getFieldValue('type');
      $fieldDefault = $this->getFieldValue('default');

      $sql = rex_sql::getInstance();
      $result = $sql->getArray('SELECT `dbtype`, `dblength` FROM `'. $REX['TABLE_PREFIX'] .'62_type` WHERE id='. $fieldType);
      $fieldDbType = $result[0]['dbtype'];
      $fieldDbLength = $result[0]['dblength'];

      // TEXT Spalten dürfen in MySQL keine Defaultwerte haben
      if($fieldDbType == 'text')
        $fieldDefault = null;

      if($this->isEditMode())
      {
        // Spalte in der Tabelle verändern
        $tmRes = $this->tableManager->editColumn($fieldOldName, $fieldName, $fieldDbType, $fieldDbLength, $fieldDefault);
      }
      else
      {
        // Spalte in der Tabelle anlegen
        $tmRes = $this->tableManager->addColumn($fieldName, $fieldDbType, $fieldDbLength, $fieldDefault);
      }

      if($tmRes)
      {
        // DefaultWerte setzen
        if($fieldDefault != $fieldOldDefault)
        {
          $qry = 'UPDATE `'. $this->tableManager->getTableName() .'` SET `'.$fieldName.'`="'. $fieldDefault .'" WHERE `'. $fieldName .'`="'. $fieldOldDefault .'"';
          return $sql->setQuery($qry);
        }
        // Default werte haben schon zuvor gepasst, daher true zurückgeben
        return true;
      }
    }

    return false;
  }

  function organizePriorities($newPrio, $oldPrio)
  {
    if($newPrio == $oldPrio)
      return;

    if ($newPrio < $oldPrio)
      $addsql = 'desc';
    else
      $addsql = 'asc';

    rex_organize_priorities(
      $this->tableName,
      'prior',
      'name LIKE "'. $this->metaPrefix .'%"',
      'prior, updatedate '. $addsql
    );
//    $sql = new rex_sql();
//    $sql->debugsql =& $this->debug;
//    $sql->setQuery('SELECT field_id FROM '. $this->tableName .' WHERE name LIKE "'. $this->metaPrefix .'%" ORDER BY prior, updatedate '. $addsql);
//
//    $updateSql = new rex_sql();
//    $updateSql->debugsql =& $this->debug;
//
//    for($i = 0; $i < $sql->getRows(); $i++)
//    {
//      $updateSql->setTable($this->tableName);
//      $updateSql->setValue('prior', $i+1);
//      $updateSql->setWhere('name LIKE "'. $this->metaPrefix .'%" AND field_id = '. $sql->getValue('field_id'));
//      $updateSql->update();
//      $sql->next();
//    }
  }
}
?>