<?php

/**
 * Klasse zum erstellen von Listen
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_form
{
  var $name;
  var $tableName;
  var $method;
  var $fieldset;
  var $whereCondition;
  var $elements;
  var $params;
  var $mode;
  var $sql;
  var $debug;
  var $applyUrl;
  var $message;
  var $warning;
  var $divId;

  function rex_form($tableName, $fieldset, $whereCondition, $method = 'post', $debug = false)
  {
    global $REX;
//    $debug = true;

    if(!in_array($method, array('post', 'get')))
      trigger_error("rex_form: 3. Parameter darf nur die Werte 'post' oder 'get' annehmen!", E_USER_ERROR);

    $this->name = md5($tableName . $whereCondition . $method);
    $this->method = $method;
    $this->tableName = $tableName;
    $this->elements = array();
    $this->params = array();
    $this->addFieldset($fieldset);
    $this->whereCondition = $whereCondition;
    $this->divId = 'rex-addon-editmode';

    // --------- Load Env
    if($REX['REDAXO'])
      $this->loadBackendConfig();

    $this->setMessage('');

    $this->sql = new rex_sql();
    $this->sql->debugsql =& $this->debug;
    $this->debug = $debug;
    $this->sql->setQuery('SELECT * FROM '. $tableName .' WHERE '. $this->whereCondition .' LIMIT 2');

    $numRows = $this->sql->getRows();
    if($numRows == 0)
    {
      // Kein Datensatz gefunden => Mode: Add
      $this->setEditMode(false);
    }
    elseif($numRows == 1)
    {
      // Kein Datensatz gefunden => Mode: Edit
      $this->setEditMode(true);
    }
    else
    {
      trigger_error('rex_form: Die gegebene Where-Bedingung führt nicht zu einem eindeutigen Datensatz!', E_USER_ERROR);
    }
  }

  function init()
  {
    // nichts tun
  }

  function factory($tableName, $fieldset, $whereCondition, $method = 'post', $debug = false, $class = null)
  {
    // keine spezielle klasse angegeben -> default klasse verwenden?
    if(!$class)
    {
      // ----- EXTENSION POINT
      $class = rex_register_extension_point('REX_FORM_CLASSNAME', 'rex_form',
        array(
          'tableName'      => $tableName,
          'fieldset'       => $fieldset,
          'whereCondition' => $whereCondition,
          'method'         => $method,
          'debug'          => $debug)
      );
    }

    return new $class($tableName, $fieldset, $whereCondition, $method, $debug);
  }

  function loadBackendConfig()
  {
    global $I18N;

    $saveLabel = $I18N->msg('form_save');
    $applyLabel = $I18N->msg('form_apply');
    $deleteLabel = $I18N->msg('form_delete');
    $resetLabel = $I18N->msg('form_reset');
    $abortLabel = $I18N->msg('form_abort');

    $func = rex_request('func', 'string');

    $this->addParam('page', rex_request('page', 'string'));
    $this->addParam('subpage', rex_request('subpage', 'string'));
    $this->addParam('func', $func);
    $this->addParam('list', rex_request('list', 'string'));

    $saveElement = null;
    if($saveLabel != '')
      $saveElement = $this->addInputField('submit', 'save', $saveLabel, array('internal::useArraySyntax' => false), false);

    $applyElement = null;
    $deleteElement = null;
    if($func == 'edit')
    {
      if($applyLabel != '')
        $applyElement = $this->addInputField('submit', 'apply', $applyLabel, array('internal::useArraySyntax' => false), false);

      if($deleteLabel != '')
        $deleteElement = $this->addInputField('submit', 'delete', $deleteLabel, array('internal::useArraySyntax' => false), false);
    }

    $resetElement = null;
//    if($resetLabel != '')
//      $resetElement = $this->addInputField('submit', 'reset', $resetLabel, array('internal::useArraySyntax' => false), false);

    $abortElement = null;
//    if($abortLabel != '')
//      $abortElement = $this->addInputField('submit', 'abort', $abortLabel, array('internal::useArraySyntax' => false), false);

    if($saveElement || $applyElement || $deleteElement || $resetElement || $abortElement)
      $this->addControlField($saveElement, $applyElement, $deleteElement, $resetElement, $abortElement);
  }

  /**
   * Gibt eine Urls zurück
   */
  function getUrl($params = array(), $escape = true)
  {
    $params = array_merge($this->getParams(), $params);
    $params['form'] = $this->getName();

    $paramString = '';
    foreach($params as $name => $value)
    {
      $paramString .= $name .'='. $value .'&';
    }

    $url = 'index.php?'. $paramString;
    if($escape)
    {
      $url = str_replace('&', '&amp;', $url);
    }

    return $url;
  }

  // --------- Sections

  function addFieldset($fieldset)
  {
    $this->fieldset = $fieldset;
  }

  // --------- Fields

  function &addField($tag, $name, $value = null, $attributes = array(), $addElement = true)
  {
    $element =& $this->createElement($tag, $name, $value, $attributes);

    if($addElement)
    {
      $this->addElement($element);
      return $element;
    }

    return $element;
  }

  function &addInputField($type, $name, $value = null, $attributes = array(), $addElement = true)
  {
    $attributes['type'] = $type;
    $field =& $this->addField('input', $name, $value, $attributes, $addElement);
    return $field;
  }

  function &addTextField($name, $value = null, $attributes = array())
  {
    if(!isset($attributes['class']))
    	$attributes['class'] = 'rex-form-text';
    $field =& $this->addInputField('text', $name, $value, $attributes);
    return $field;
  }

  function &addReadOnlyTextField($name, $value = null, $attributes = array())
  {
    $attributes['readonly'] = 'readonly';
    if(!isset($attributes['class']))
    	$attributes['class'] = 'rex-form-read';
    $field =& $this->addInputField('text', $name, $value, $attributes);
    return $field;
  }

  function &addReadOnlyField($name, $value = null, $attributes = array())
  {
    $attributes['internal::fieldSeparateEnding'] = true;
    $attributes['internal::noNameAttribute'] = true;
    if(!isset($attributes['class']))
    	$attributes['class'] = 'rex-form-read';
    $field =& $this->addField('span', $name, $value, $attributes, true);
    return $field;
  }

  function &addHiddenField($name, $value = null, $attributes = array())
  {
    $field =& $this->addInputField('hidden', $name, $value, $attributes, true);
    return $field;
  }

  function &addCheckboxField($name, $value = null, $attributes = array())
  {
    $attributes['internal::fieldClass'] = 'rex_form_checkbox_element';
    if(!isset($attributes['class']))
    	$attributes['class'] = 'rex-form-checkbox rex-form-label-right';
    $field =& $this->addField('', $name, $value, $attributes);
    return $field;
  }

  function &addRadioField($name, $value = null, $attributes = array())
  {
    if(!isset($attributes['class']))
    	$attributes['class'] = 'rex-form-radio';
    $attributes['internal::fieldClass'] = 'rex_form_radio_element';
    $field =& $this->addField('radio', $name, $value, $attributes);
    return $field;
  }

  function &addTextAreaField($name, $value = null, $attributes = array())
  {
    $attributes['internal::fieldSeparateEnding'] = true;
    if(!isset($attributes['cols']))
      $attributes['cols'] = 50;
    if(!isset($attributes['rows']))
      $attributes['rows'] = 6;
    if(!isset($attributes['class']))
    	$attributes['class'] = 'rex-form-textarea';

    $field =& $this->addField('textarea', $name, $value, $attributes);
    return $field;
  }

  function &addSelectField($name, $value = null, $attributes = array())
  {
    if(!isset($attributes['class']))
    	$attributes['class'] = 'rex-form-select';
    $attributes['internal::fieldClass'] = 'rex_form_select_element';
    $field =& $this->addField('', $name, $value, $attributes, true);
    return $field;
  }

  function &addMediaField($name, $value = null, $attributes = array())
  {
    $attributes['internal::fieldClass'] = 'rex_form_widget_media_element';
    $field =& $this->addField('', $name, $value, $attributes, true);
    return $field;
  }

  function &addMedialistField($name, $value = null, $attributes = array())
  {
    $attributes['internal::fieldClass'] = 'rex_form_widget_medialist_element';
    $field =& $this->addField('', $name, $value, $attributes, true);
    return $field;
  }

  function &addLinkmapField($name, $value = null, $attributes = array())
  {
    $attributes['internal::fieldClass'] = 'rex_form_widget_linkmap_element';
    $field =& $this->addField('', $name, $value, $attributes, true);
    return $field;
  }

  function &addControlField($saveElement = null, $applyElement = null, $deleteElement = null, $resetElement = null, $abortElement = null)
  {
    $field =& $this->addElement(new rex_form_control_element($this, $saveElement, $applyElement, $deleteElement, $resetElement, $abortElement));
    return $field;
  }

  function addParam($name, $value)
  {
    $this->params[$name] = $value;
  }

  function getParams()
  {
    return $this->params;
  }

  function getParam($name, $default = null)
  {
    if(isset($this->params[$name]))
    {
      return $this->params[$name];
    }
    return $default;
  }

  function &addElement(&$element)
  {
    $this->elements[$this->fieldset][] =& $element;
    return $element;
  }

  function &createElement($tag, $name, $value, $attributes = array())
  {
    $id = $this->tableName.'_'.$this->fieldset.'_'.$name;

    $postValue = $this->elementPostValue($this->getFieldsetName(), $name);
    // Evtl postwerte wieder übernehmen (auch externe Werte überschreiben)
    if($postValue !== null)
    {
      $value = stripslashes($postValue);
    }

    // Wert aus der DB nehmen, falls keiner extern und keiner im POST angegeben
    if($value === null && $this->sql->getRows() == 1)
    {
      $value = $this->sql->getValue($name);
    }

    if(!isset($attributes['internal::useArraySyntax']))
    {
      $attributes['internal::useArraySyntax'] = true;
    }

    // Eigentlichen Feldnamen nochmals speichern
    $fieldName = $name;
    if($attributes['internal::useArraySyntax'] === true)
    {
      $name = $this->fieldset . '['. $name .']';
    }
    elseif($attributes['internal::useArraySyntax'] === false)
    {
      $name = $this->fieldset . '_'. $name;
    }
    unset($attributes['internal::useArraySyntax']);

    $class = 'rex_form_element';
    if(isset($attributes['internal::fieldClass']))
    {
      $class = $attributes['internal::fieldClass'];
      unset($attributes['internal::fieldClass']);
    }

    $separateEnding = false;
    if(isset($attributes['internal::fieldSeparateEnding']))
    {
      $separateEnding = $attributes['internal::fieldSeparateEnding'];
      unset($attributes['internal::fieldSeparateEnding']);
    }

    $internal_attr = array('name' => $name);
    if(isset($attributes['internal::noNameAttribute']))
    {
      $internal_attr = array();
      unset($attributes['internal::noNameAttribute']);
    }

    // 1. Array: Eigenschaften, die via Parameter Überschrieben werden können/dürfen
    // 2. Array: Eigenschaften, via Parameter
    // 3. Array: Eigenschaften, die hier fest definiert sind / nicht veränderbar via Parameter
    $attributes = array_merge(array('id' => $id), $attributes, $internal_attr);
    $element = new $class($tag, $this, $attributes, $separateEnding);
    $element->setFieldName($fieldName);
    $element->setValue($value);
    return $element;
  }

  function setEditMode($isEditMode)
  {
    if($isEditMode)
      $this->mode = 'edit';
    else
      $this->mode = 'add';
  }

  function isEditMode()
  {
    return $this->mode == 'edit';
  }

  function setApplyUrl($url)
  {
    if(is_array($url))
      $url = $this->getUrl($url, false);

    $this->applyUrl = $url;
  }

  // --------- Form Methods

  function isHeaderElement($element)
  {
    return is_object($element) && $element->getTag() == 'input' && $element->getAttribute('type') == 'hidden';
  }

  function isFooterElement($element)
  {
    return $this->isControlElement($element);
  }

  function isControlElement($element)
  {
    return is_object($element) && is_a($element, 'rex_form_control_element');
  }

  function getHeaderElements()
  {
    $headerElements = array();
    foreach($this->elements as $fieldsetName => $fieldsetElementsArray)
    {
      foreach($fieldsetElementsArray as $element)
      {
        if($this->isHeaderElement($element))
        {
          $headerElements[] = $element;
        }
      }
    }
    return $headerElements;
  }

  function getFooterElements()
  {
    $footerElements = array();
    foreach($this->elements as $fieldsetName => $fieldsetElementsArray)
    {
      foreach($fieldsetElementsArray as $element)
      {
        if($this->isFooterElement($element))
        {
          $footerElements[] = $element;
        }
      }
    }
    return $footerElements;
  }

  function getFieldsetName()
  {
    return $this->fieldset;
  }

  function getFieldsets()
  {
    $fieldsets = array();
    foreach($this->elements as $fieldsetName => $fieldsetElementsArray)
    {
      $fieldsets[] = $fieldsetName;
    }
    return $fieldsets;
  }

  function getFieldsetElements()
  {
    $fieldsetElements = array();
    foreach($this->elements as $fieldsetName => $fieldsetElementsArray)
    {
      foreach($fieldsetElementsArray as $element)
      {
        if($this->isHeaderElement($element)) continue;
        if($this->isFooterElement($element)) continue;

        $fieldsetElements[$fieldsetName][] = $element;
      }
    }
    return $fieldsetElements;
  }

  function &getControlElement()
  {
    foreach($this->elements as $fieldsetName => $fieldsetElementsArray)
    {
      foreach($fieldsetElementsArray as $element)
      {
        if($this->isControlElement($element))
        {
          return $element;
        }
      }
    }
    return null;
  }

  function &getElement($fieldsetName, $elementName)
  {
    $normalizedName = rex_form_element::_normalizeName($fieldsetName.'['. $elementName .']');
    $result =& $this->_getElement($fieldsetName,$normalizedName);
    return $result;
  }

  function &_getElement($fieldsetName, $elementName)
  {
    if(is_array($this->elements[$fieldsetName]))
    {
      for($i = 0; $i < count($this->elements[$fieldsetName]); $i++)
      {
        if($this->elements[$fieldsetName][$i]->getAttribute('name') == $elementName)
        {
          return $this->elements[$fieldsetName][$i];
        }
      }
    }
    $result = null;
    return $result;
  }

  function getName()
  {
    return $this->name;
  }

  function setWarning($warning)
  {
    $this->warning = $warning;
  }
  
  function getWarning()
  {
    $warning = rex_request($this->getName().'_warning', 'string');
    if($this->warning != '')
    {
      $warning .= "\n". $this->warning;
    }
    return $warning;
  }
  
  function setMessage($message)
  {
    $this->message = $message;
  }

  function getMessage()
  {
    $message = rex_request($this->getName().'_msg', 'string');
    if($this->message != '')
    {
      $message .= "\n". $this->message;
    }
    return $message;
  }

  /**
   * Callbackfunktion, damit in subklassen der Value noch beeinflusst werden kann
   * kurz vorm löschen
   */
  function preDelete($fieldsetName, $fieldName, $fieldValue, &$deleteSql)
  {
    return $fieldValue;
  }

  /**
   * Callbackfunktion, damit in subklassen der Value noch beeinflusst werden kann
   * kurz vorm speichern
   */
  function preSave($fieldsetName, $fieldName, $fieldValue, &$saveSql)
  {
    global $REX;

    static $setOnce = false;

    if(!$setOnce)
    {
      $fieldnames = $this->sql->getFieldnames();

      if(in_array('updateuser', $fieldnames))
        $saveSql->setValue('updateuser', $REX['USER']->getValue('login'));

      if(in_array('updatedate', $fieldnames))
        $saveSql->setValue('updatedate', time());

      if(!$this->isEditMode())
      {
        if(in_array('createuser', $fieldnames))
          $saveSql->setValue('createuser', $REX['USER']->getValue('login'));

        if(in_array('createdate', $fieldnames))
          $saveSql->setValue('createdate', time());
      }
      $setOnce = true;
    }

    return $fieldValue;
  }

  /**
   * Callbackfunktion, damit in subklassen der Value noch beeinflusst werden kann
   * wenn das Feld mit Datenbankwerten angezeigt wird
   */
  function preView($fieldsetName, $fieldName, $fieldValue)
  {
    return $fieldValue;
  }

  function fieldsetPostValues($fieldsetName)
  {
    // Name normalisieren, da der gepostete Name auch zuvor normalisiert wurde
    $normalizedFieldsetName = rex_form_element::_normalizeName($fieldsetName);

    return rex_post($normalizedFieldsetName, 'array');
  }

  function elementPostValue($fieldsetName, $fieldName, $default = null)
  {
    $fields = $this->fieldsetPostValues($fieldsetName);

    if(isset($fields[$fieldName]))
      return $fields[$fieldName];

    return $default;
  }

  /**
   * Validiert die Eingaben.
   * Gibt true zurück wenn alles ok war, false bei einem allgemeinen Fehler oder
   * einen String mit einer Fehlermeldung.
   *
   * Eingaben sind via
   *   $el    =& $this->getElement($fieldSetName, $fieldName);
   *   $val   = $el->getValue();
   * erreichbar.
   */
  function validate()
  {
    return true;
  }

  /**
   * Speichert das Formular
   *
   * Gibt true zurück wenn alles ok war, false bei einem allgemeinen Fehler oder
   * einen String mit einer Fehlermeldung.
   */
  function save()
  {
    // trigger extensions point
    // Entscheiden zwischen UPDATE <-> CREATE via editMode möglich
    // Falls die Extension FALSE zurückgibt, nicht speicher,
    // um hier die Möglichkeit offen zu haben eigene Validierungen/Speichermechanismen zu implementieren
    if(rex_register_extension_point('REX_FORM_'.strtoupper($this->getName()).'_SAVE', '', array ('form' => $this)) === false)
    {
      return;
    }

    $sql = rex_sql::getInstance();
    $sql->debugsql =& $this->debug;
    $sql->setTable($this->tableName);

    foreach($this->getFieldsets() as $fieldsetName)
    {
      // POST-Werte ermitteln
      $fieldValues = $this->fieldsetPostValues($fieldsetName);
      foreach($fieldValues as $fieldName => $fieldValue)
      {
        // Callback, um die Values vor dem Speichern noch beeinflussen zu können
        $fieldValue = $this->preSave($fieldsetName, $fieldName, $fieldValue, $sql);

        if (is_array($fieldValue))
          $fieldValue = implode('|+|', $fieldValue);

        // Element heraussuchen
        $element =& $this->getElement($fieldsetName, $fieldName);

        // Den POST-Wert als Value in das Feld speichern
        // Da generell alles von REDAXO escaped wird, hier slashes entfernen
        $element->setValue(stripslashes($fieldValue));

        // Den POST-Wert in die DB speichern (inkl. slahes)
        $sql->setValue($fieldName, $fieldValue);
      }
    }

    if($this->isEditMode())
    {
      $sql->setWhere($this->whereCondition);
      return $sql->update();
    }
    else
    {
      return $sql->insert();
    }
  }

  function delete()
  {
    $deleteSql = rex_sql::getInstance();
    $deleteSql->debugsql =& $this->debug;
    $deleteSql->setTable($this->tableName);
    $deleteSql->setWhere($this->whereCondition);

    foreach($this->getFieldsets() as $fieldsetName)
    {
      // POST-Werte ermitteln
      $fieldValues = $this->fieldsetPostValues($fieldsetName);
      foreach($fieldValues as $fieldName => $fieldValue)
      {
        // Callback, um die Values vor dem Löschen noch beeinflussen zu können
        $fieldValue = $this->preDelete($fieldsetName, $fieldName, $fieldValue, $deleteSql);

        // Element heraussuchen
        $element =& $this->getElement($fieldsetName, $fieldName);

        // Den POST-Wert als Value in das Feld speichern
        // Da generell alles von REDAXO escaped wird, hier slashes entfernen
        $element->setValue(stripslashes($fieldValue));
      }
    }

    return $deleteSql->delete();
  }

  function redirect($listMessage = '', $listWarning = '', $params = array())
  {
    if($listMessage != '')
    {
      $listName = rex_request('list', 'string');
      $params[$listName.'_msg'] = $listMessage;
    }

    if($listWarning != '')
    {
      $listName = rex_request('list', 'string');
      $params[$listName.'_warning'] = $listWarning;
    }
    
    $paramString = '';
    foreach($params as $name => $value)
    {
      $paramString = $name .'='. $value .'&';
    }

    if($this->debug)
    {
      echo 'redirect to: '. $this->applyUrl . $paramString;
      exit();
    }

    header('Location: '. $this->applyUrl . $paramString);
    exit();
  }

  function get()
  {
    global $I18N;

    $this->init();

    $this->setApplyUrl($this->getUrl(array('func' => ''), false));

    if(($controlElement = $this->getControlElement()) !== null)
    {
      if($controlElement->saved())
      {
        // speichern und umleiten
        // Nachricht in der Liste anzeigen
        if(($result = $this->validate()) === true && ($result = $this->save()) === true)
          $this->redirect($I18N->msg('form_saved'));
        elseif(is_string($result) && $result != '')
          // Falls ein Fehler auftritt, das Formular wieder anzeigen mit der Meldung
          $this->setWarning($result);
        else
          $this->setWarning($I18N->msg('form_save_error'));
      }
      elseif($controlElement->applied())
      {
        // speichern und wiederanzeigen
        // Nachricht im Formular anzeigen
        if(($result = $this->validate()) === true && ($result = $this->save()) === true)
          $this->setMessage($I18N->msg('form_applied'));
        elseif(is_string($result) && $result != '')
          $this->setWarning($result);
        else
          $this->setWarning($I18N->msg('form_save_error'));
      }
      elseif($controlElement->deleted())
      {
        // speichern und wiederanzeigen
        // Nachricht in der Liste anzeigen
        if(($result = $this->delete()) === true)
          $this->redirect($I18N->msg('form_deleted'));
        elseif(is_string($result) && $result != '')
          $this->setWarning($result);
        else
          $this->setWarning($I18N->msg('form_delete_error'));
      }
      elseif($controlElement->resetted())
      {
        // verwerfen und wiederanzeigen
        // Nachricht im Formular anzeigen
        $this->setMessage($I18N->msg('form_resetted'));
      }
      elseif($controlElement->aborted())
      {
        // verwerfen und umleiten
        // Nachricht in der Liste anzeigen
        $this->redirect($I18N->msg('form_resetted'));
      }
    }

    // Parameter dem Formular hinzufügen
    foreach($this->getParams() as $name => $value)
    {
      $this->addHiddenField($name, $value, array('internal::useArraySyntax' => 'none'));
    }

    $s = "\n";

    $warning = $this->getWarning();
    $message = $this->getMessage();
    if($warning != '')
    {
      $s .= '  '. rex_warning($warning). "\n";
    }
    else if($message != '')
    {
      $s .= '  '. rex_info($message). "\n";
    }

    $s .= '<div id="'. $this->divId .'" class="rex-form">'. "\n";

    $i = 0;
    $addHeaders = true;
    $fieldsets = $this->getFieldsetElements();
    $last = count($fieldsets);

    $s .= '  <form action="index.php" method="'. $this->method .'">'. "\n";
    foreach($fieldsets as $fieldsetName => $fieldsetElements)
    {
      $s .= '    <fieldset class="rex-form-col-1">'. "\n";
      $s .= '      <legend>'. htmlspecialchars($fieldsetName) .'</legend>'. "\n";
      $s .= '      <div class="rex-form-wrapper">'. "\n";

      // Die HeaderElemente nur im 1. Fieldset ganz am Anfang einfügen
      if($i == 0 && $addHeaders)
      {
        foreach($this->getHeaderElements() as $element)
        {
          // Callback
          $element->setValue($this->preView($fieldsetName, $element->getFieldName(), $element->getValue()));
          // HeaderElemente immer ohne <p>
          $s .= $element->formatElement();
        }
        $addHeaders = false;
      }

      foreach($fieldsetElements as $element)
      {
        // Callback
        $element->setValue($this->preView($fieldsetName, $element->getFieldName(), $element->getValue()));
        $s .= $element->get();
      }

      // Die FooterElemente nur innerhalb des letzten Fieldsets
      if(($i + 1) == $last)
      {
        foreach($this->getFooterElements() as $element)
        {
          // Callback
          $element->setValue($this->preView($fieldsetName, $element->getFieldName(), $element->getValue()));
          $s .= $element->get();
        }
      }

      $s .= '      </div>'. "\n";
      $s .= '    </fieldset>'. "\n";

      $i++;
    }

    $s .= '  </form>'. "\n";
    $s .= '</div>'. "\n";

    return $s;
  }

  function show()
  {
    echo $this->get();
  }
}

// Stellt ein Element im Formular dar
// Nur für internes Handling!
class rex_form_element
{
  var $value;
  var $label;
  var $tag;
  var $table;
  var $attributes;
  var $separateEnding;
  var $fieldName;
  var $header;
  var $footer;
  var $prefix;
  var $suffix;
  var $notice;

  function rex_form_element($tag, &$table, $attributes = array(), $separateEnding = false)
  {
    $this->value = null;
    $this->label = '';
    $this->tag = $tag;
    $this->table =& $table;
    $this->setAttributes($attributes);
    $this->separateEnding = $separateEnding;
    $this->setHeader('');
    $this->setFooter('');
    $this->setPrefix('');
    $this->setSuffix('');
    $this->setFieldName('');
  }

  // --------- Attribute setter/getters

  function setValue($value)
  {
    $this->value = $value;
  }

  function getValue()
  {
    return $this->value;
  }

  function setFieldName($name)
  {
    $this->fieldName = $name;
  }

  function getFieldName()
  {
    return $this->fieldName;
  }

  function setLabel($label)
  {
    $this->label = $label;
  }

  function getLabel()
  {
    return $this->label;
  }

  function setNotice($notice)
  {
    $this->notice = $notice;
  }

  function getNotice()
  {
    return $this->notice;
  }

  function getTag()
  {
    return $this->tag;
  }

  function setSuffix($suffix)
  {
    $this->suffix = $suffix;
  }

  function getSuffix()
  {
    return $this->suffix;
  }

  function setPrefix($prefix)
  {
    $this->prefix = $prefix;
  }

  function getPrefix()
  {
    return $this->prefix;
  }

  function setHeader($header)
  {
    $this->header = $header;
  }

  function getHeader()
  {
    return $this->header;
  }

  function setFooter($footer)
  {
    $this->footer = $footer;
  }

  function getFooter()
  {
    return $this->footer;
  }

  function _normalizeId($id)
  {
    return preg_replace('/[^a-zA-Z\-0-9_]/i','_', $id);
  }

  function _normalizeName($name)
  {
    return preg_replace('/[^\[\]a-zA-Z\-0-9_]/i','_', $name);
  }

  function setAttribute($name, $value)
  {
    if($name == 'value')
    {
      $this->setValue($value);
    }
    else
    {
      if($name == 'id')
      {
        $value = $this->_normalizeId($value);
      }
      elseif($name == 'name')
      {
        $value = $this->_normalizeName($value);
      }

      // Wenn noch kein Label gesetzt, den Namen als Label verwenden
      if($name == 'name' && $this->getLabel() == '')
      {
        $this->setLabel($value);
      }

      $this->attributes[$name] = $value;
    }
  }

  function getAttribute($name, $default = null)
  {
    if($name == 'value')
    {
      return $this->getValue();
    }
    elseif($this->hasAttribute($name))
    {
      return $this->attributes[$name];
    }

    return $default;
  }

  function setAttributes($attributes)
  {
    foreach($attributes as $name => $value)
    {
      $this->setAttribute($name, $value);
    }
  }

  function getAttributes()
  {
    return $this->attributes;
  }

  function hasAttribute($name)
  {
    return isset($this->attributes[$name]);
  }

  function hasSeparateEnding()
  {
    return $this->separateEnding;
  }

  // --------- Element Methods

  function formatClass()
  {
    $s = '';
    $class = $this->getAttribute('class');

    if ($class != '')
    {
    	$s .= $class;
    }

    return $s;
  }

  function formatLabel()
  {
    $s = '';
    $label = $this->getLabel();

    if($label != '')
    {
      $s .= '          <label for="'. $this->getAttribute('id') .'">'. $label .'</label>'. "\n";
    }

    return $s;
  }

  function formatElement()
  {
    $attr = '';
    $value = htmlspecialchars($this->getValue());

    foreach($this->getAttributes() as $attributeName => $attributeValue)
    {
      $attr .= ' '. $attributeName .'="'. $attributeValue .'"';
    }

    if($this->hasSeparateEnding())
    {
      return '          <'. $this->getTag(). $attr .'>'. $value .'</'. $this->getTag() .'>'. "\n";
    }
    else
    {
      $attr .= ' value="'. $value .'"';
      return '          <'. $this->getTag(). $attr .' />'. "\n";
    }
  }

  function formatNotice()
  {
    $notice = $this->getNotice();
    if($notice != '')
    {
      return '<span class="rex-form-notice" id="'. $this->getAttribute('id') .'_notice">'. $notice .'</span>';
    }
    return '';
  }

  function _get()
  {
    $s = '';
		$class = ' class="rex-form-col-a';
		$formatClass = $this->formatClass();
		
		if ($formatClass != '')
			$class .= ' '.$formatClass;
			
		$class .= '"';
		    
    $s .= '        <p'.$class.'>'. "\n";
    $s .= $this->getPrefix();
    
    $s .= $this->formatLabel();
    $s .= $this->formatElement();
    $s .= $this->formatNotice();

    $s .= $this->getSuffix();
    $s .= '        </p>'. "\n";

    return $s;
  }

  function get()
  {
    $s = '';
    $s .= $this->getHeader();

		$s .= '    <div class="rex-form-row">'. "\n";
		
    $s .= $this->_get();
    
    $s .= '    </div>'. "\n";

    $s .= $this->getFooter();
    return $s;
  }

  function show()
  {
    echo $this->get();
  }
}

class rex_form_control_element extends rex_form_element
{
  var $saveElement;
  var $applyElement;
  var $deleteElement;
  var $resetElelement;
  var $abortElement;

  function rex_form_control_element(&$table, $saveElement = null, $applyElement = null, $deleteElement = null, $resetElement = null, $abortElement = null)
  {
    parent::rex_form_element('', $table);

    $this->saveElement = $saveElement;
    $this->applyElement = $applyElement;
    $this->deleteElement = $deleteElement;
    $this->resetElement = $resetElement;
    $this->abortElement = $abortElement;
  }

  function _get()
  {
    $s = '';
    
    $class = '';

    if($this->saveElement)
    {
      if(!$this->saveElement->hasAttribute('class'))
        $this->saveElement->setAttribute('class', 'rex-form-submit');
			
			$class = $this->saveElement->formatClass();
			
      $s .= $this->saveElement->formatElement();
    }

    if($this->applyElement)
    {
      if(!$this->applyElement->hasAttribute('class'))
        $this->applyElement->setAttribute('class', 'rex-form-submit rex-form-submit-2');
			
			$class = $this->applyElement->formatClass();

      $s .= $this->applyElement->formatElement();
    }

    if($this->deleteElement)
    {
      if(!$this->deleteElement->hasAttribute('class'))
        $this->deleteElement->setAttribute('class', 'rex-form-submit rex-form-submit-2');

      if(!$this->deleteElement->hasAttribute('onclick'))
        $this->deleteElement->setAttribute('onclick', 'return confirm(\'Löschen?\');');
			
			$class = $this->deleteElement->formatClass();

      $s .= $this->deleteElement->formatElement();
    }

    if($this->resetElement)
    {
      if(!$this->resetElement->hasAttribute('class'))
        $this->resetElement->setAttribute('class', 'rex-form-submit rex-form-submit-2');

      if(!$this->resetElement->hasAttribute('onclick'))
        $this->resetElement->setAttribute('onclick', 'return confirm(\'Änderungen verwerfen?\');');
			
			$class = $this->resetElement->formatClass();

      $s .= $this->resetElement->formatElement();
    }

    if($this->abortElement)
    {
      if(!$this->abortElement->hasAttribute('class'))
        $this->abortElement->setAttribute('class', 'rex-form-submit rex-form-submit-2');
			
			$class = $this->abortElement->formatClass();

      $s .= $this->abortElement->formatElement();
    }
    
    if ($s != '')
    {
    	if ($class != '')
    	{
    		$class = ' '.$class;
    	}
    	$s = '<p class="rex-form-col-a'.$class.'">'.$s.'</p>';
    }

    return $s;
  }

  function submitted($element)
  {
    return is_object($element) && rex_post($element->getAttribute('name'), 'string') != '';
  }

  function saved()
  {
    return $this->submitted($this->saveElement);
  }

  function applied()
  {
    return $this->submitted($this->applyElement);
  }

  function deleted()
  {
    return $this->submitted($this->deleteElement);
  }

  function resetted()
  {
    return $this->submitted($this->resetElement);
  }

  function aborted()
  {
    return $this->submitted($this->abortElement);
  }
}

class rex_form_select_element extends rex_form_element
{
  var $select;
  var $separator;

  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_select_element($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_element('', $table, $attributes);

    $this->select =& new rex_select();
    $this->setSeparator('|+|');
  }

  function formatElement()
  {
    $multipleSelect = false;
    // Hier die Attribute des Elements an den Select weitergeben, damit diese angezeigt werden
    foreach($this->getAttributes() as $attributeName => $attributeValue)
    {
      if ($attributeName == 'multiple')
        $multipleSelect = true;

      $this->select->setAttribute($attributeName, $attributeValue);
    }

    if ($multipleSelect)
    {
        $this->setAttribute('name', $this->getAttribute('name').'[]');

        $selectedOptions = explode($this->separator, $this->getValue());
        if (is_array($selectedOptions) AND $selectedOptions[0] != '')
        {
          foreach($selectedOptions as $selectedOption)
          {
           $this->select->setSelected($selectedOption);
          }
        }
    }
    else
      $this->select->setSelected($this->getValue());

    $this->select->setName($this->getAttribute('name'));
    return $this->select->get();
  }

  function setSeparator($separator)
  {
    $this->separator = $separator;
  }

  function &getSelect()
  {
    return $this->select;
  }
}

class rex_form_options_element extends rex_form_element
{
  var $options;

  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_options_element($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_element($tag, $table, $attributes);
    $this->options = array();
  }

  function addOption($name, $value)
  {
    $this->options[$name] = $value;
  }

  function addOptions($options, $useOnlyValues = false)
  {
    if(is_array($options) && count($options)>0)
    {
      foreach ($options as $key => $option)
      {
        $option = (array) $option;
        if($useOnlyValues)
        {
          $this->addOption($option[0], $option[0]);
        }
        else
        {
          if(!isset($option[1]))
            $option[1] = $key;

          $this->addOption($option[0], $option[1]);
        }
      }
    }
  }

  function addSqlOptions($qry)
  {
    $sql = new rex_sql;
    $this->addOptions($sql->getArray($qry, MYSQL_NUM));
  }

  function addDBSqlOptions($qry)
  {
    $sql = new rex_sql;
    $this->addOptions($sql->getDBArray($qry, MYSQL_NUM));
  }

  function getOptions()
  {
    return $this->options;
  }
}

class rex_form_checkbox_element extends rex_form_options_element
{
  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_checkbox_element($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_options_element('', $table, $attributes);
    // Jede checkbox bekommt eingenes Label
    $this->setLabel('');
  }

  function formatLabel()
  {
    // Da Jedes Feld schon ein Label hat, hier nur eine "Ueberschrift" anbringen
    return '<span>'. $this->getLabel() .'</span>';
  }

  function formatElement()
  {
    $s = '';
    $values = explode('|+|', $this->getValue());
    $options = $this->getOptions();
    $name = $this->getAttribute('name');
    $id = $this->getAttribute('id');

    $attr = '';
    foreach($this->getAttributes() as $attributeName => $attributeValue)
    {
      if($attributeName == 'name' || $attributeName == 'id') continue;
      $attr .= ' '. $attributeName .'="'. $attributeValue .'"';
    }

    foreach($options as $opt_name => $opt_value)
    {
      $checked = in_array($opt_value, $values) ? ' checked="checked"' : '';
      $opt_id = $id .'_'. $this->_normalizeId($opt_value);
      $opt_attr = $attr . ' id="'. $opt_id .'"';
      $s .= '<input type="checkbox" name="'. $name .'['. $opt_value .']" value="'. htmlspecialchars($opt_value) .'"'. $opt_attr . $checked.' />
             <label for="'. $opt_id .'">'. $opt_name .'</label>';
    }
    return $s;
  }
}

class rex_form_radio_element extends rex_form_options_element
{
  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_radio_element($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_options_element('', $table, $attributes);
    // Jedes radio bekommt eingenes Label
  }

  function formatLabel()
  {
    // Da Jedes Feld schon ein Label hat, hier nur eine "Ueberschrift" anbringen
    return '<span>'. $this->getLabel() .'</span>';
  }

  function formatElement()
  {
    $s = '';
    $value = $this->getValue();
    $options = $this->getOptions();
    $id = $this->getAttribute('id');

    $attr = '';
    foreach($this->getAttributes() as $attributeName => $attributeValue)
    {
      if($attributeName == 'id') continue;
      $attr .= ' '. $attributeName .'="'. $attributeValue .'"';
    }

    foreach($options as $opt_name => $opt_value)
    {
      $checked = $opt_value == $value ? ' checked="checked"' : '';
      $opt_id = $id .'_'. $this->_normalizeId($opt_value);
      $opt_attr = $attr . ' id="'. $opt_id .'"';
      $s .= '<input type="radio" value="'. htmlspecialchars($opt_value) .'"'. $opt_attr . $checked.' />
             <label for="'. $opt_id .'">'. $opt_name .'</label>';
    }
    return $s;
  }
}

class rex_form_widget_media_element extends rex_form_element
{
  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_widget_media_element($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_element('', $table, $attributes);
  }


  function formatElement()
  {
    static $widget_counter = 1;

		$html = rex_var_media::getMediaButton($widget_counter);
		$html = str_replace('REX_MEDIA['. $widget_counter .']', $this->getValue(), $html);
		$html = str_replace('MEDIA['. $widget_counter .']', $this->getAttribute('name'), $html);

    $widget_counter++;
    return $html;
  }
}


class rex_form_widget_medialist_element extends rex_form_element
{
  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_widget_medialist_element($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_element('', $table, $attributes);
  }


  function formatElement()
  {
    static $widget_counter = 1;

    $html = rex_var_media::getMediaListButton($widget_counter, $this->getValue());
    $html = str_replace('MEDIALIST['. $widget_counter .']', $this->getAttribute('name').'[]', $html);

    $widget_counter++;
    return $html;
  }
}


class rex_form_widget_linkmap_element extends rex_form_element
{
  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_widget_linkmap_element($tag = '', &$table, $attributes = array())
  {
    parent::rex_form_element('', $table, $attributes);
  }


  function formatElement()
  {
    static $widget_counter = 1;

    $html = rex_var_link::getLinkButton($widget_counter, $this->getValue());
    $html = str_replace('LINK['. $widget_counter .']', $this->getAttribute('name'), $html);

    $widget_counter++;
    return $html;
  }
}