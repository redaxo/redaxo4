<?php

/** 
 * Klasse zum erstellen von Listen
 * @package redaxo3 
 * @version $Id$ 
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
	
	function rex_form($tableName, $fieldset, $whereCondition, $method = 'post', $debug = false)
	{
		global $REX;
		// TODO remove flag
//		$debug = true;
		
		if(!in_array($method, array('post', 'get')))
			trigger_error("rex_form: 3. Parameter darf nur die Werte 'post' oder 'get' annehmen!", E_USER_ERROR);
		
		$this->name = md5($tableName . $whereCondition . $method);	
		$this->method = $method;
		$this->tableName = $tableName;
		$this->elements = array();
		$this->params = array();
		$this->addFieldset($fieldset);
		$this->whereCondition = $whereCondition;
		
		// --------- Load Env
		if($REX['REDAXO'])
			$this->loadBackendConfig();
			
		$this->setMessage('');
		
		$this->sql = new rex_sql();
		$this->sql->debugsql =& $this->debug;
		$this->debug = $debug;
		$this->sql->setQuery('SELECT * FROM '. $tableName .' WHERE '. $this->whereCondition .' LIMIT 2');

		$numRows = $this->sql->getRows(); 
		if( $numRows == 0)
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
		
		$this->init();
	}
	
	function init()
	{
		// nichts tun
	}
	
	function loadBackendConfig()
	{
		// TODO translate
		$saveLabel = 'Speichern';
		$applyLabel = 'Übernehmen';
		$deleteLabel = 'Löschen';
		$resetLabel = 'Zurücksetzen';
		$abortLabel = 'Abbrechen';
		
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

		/*
		$resetElement = null;
		if($resetLabel != '')
			$resetElement = $this->addInputField('submit', 'reset', $resetLabel, array('internal::useArraySyntax' => false), false);
			
		$abortElement = null;
		if($abortLabel != '')
			$abortElement = $this->addInputField('submit', 'abort', $abortLabel, array('internal::useArraySyntax' => false), false);
		*/
		
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
			return $this->addElement($element);

		return $element;
	}
	
	function &addInputField($type, $name, $value = null, $attributes = array(), $addElement = true)
	{
		$attributes['type'] = $type;
		return $this->addField('input', $name, $value, $attributes, $addElement);
	}
	
	function &addTextField($name, $value = null, $attributes = array())
	{
		return $this->addInputField('text', $name, $value, $attributes);
	}
	
	function &addReadOnlyTextField($name, $value = null, $attributes = array())
	{
		$attributes['readonly'] = 'readonly';
		return $this->addInputField('text', $name, $value, $attributes);
	}
	
	function &addReadOnlyField($name, $value = null, $attributes = array())
	{
		$attributes['internal::fieldSeparateEnding'] = true;
		$attributes['internal::noNameAttribute'] = true;
		return $this->addField('span', $name, $value, $attributes, true);
	}
	
	function &addHiddenField($name, $value = null, $attributes = array())
	{
		return $this->addInputField('hidden', $name, $value, $attributes, true);
	}
	
	function &addCheckboxField($name, $value = null, $attributes = array())
	{
		return $this->addInputField('checkbox', $name, $value, $attributes);
	}

	function &addRadioField($name, $value = null, $attributes = array())
	{
		return $this->addInputField('radio', $name, $value, $attributes);
	}
	
	function &addTextAreaField($name, $value = null, $attributes = array())
	{
		$attributes['internal::fieldSeparateEnding'] = true;
		return $this->addField('textarea', $name, $value, $attributes);
	}
	
	function &addSelectField($name, $value = null, $attributes = array())
	{
		$attributes['internal::fieldClass'] = 'rex_form_select_element';
		return $this->addField('', $name, $value, $attributes, true);
	}

	function &addControlField($saveElement = null, $applyElement = null, $deleteElement = null, $resetElement = null, $abortElement = null)
	{
		return $this->addElement(new rex_form_control_element($this, $saveElement, $applyElement, $deleteElement, $resetElement, $abortElement));
	}
	
	function addParam($name, $value)
	{
		$this->params[$name] = $value;
	}
	
	function getParams()
	{
		return $this->params;
	}
	
	function &addElement(&$element)
	{
		$this->elements[$this->fieldset][] =& $element;
		return $element;
	}
	
	function &createElement($tag, $name, $value, $attributes = array())
	{
		$id = $this->tableName.'_'.$this->fieldset.'_'.$name;
		
		if($value === null && $this->sql->getRows() == 1)
		{
			// Wert aus der DB nehmen, falls keiner extern angegeben 
			$value = $this->sql->getValue($name);
		}
		
		if(!isset($attributes['internal::useArraySyntax']))
		{
			$attributes['internal::useArraySyntax'] = true;
		}
		
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
		return $this->_getElement($fieldsetName,$normalizedName); 
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
		
		return null;
	}
	
	function getName()
	{
		return $this->name;
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
	 * Callbackfunktion, damit in subklassen der Value noch beeinflusst werden kann kurz vorm speichern
	 */
	function prepareSave($fieldsetName, $fieldName, $fieldValue)
	{
		return $fieldValue;
	}
	
	function getElementPostValue($fieldsetName, $fieldName, $default = null)
	{
			// Name normalisieren, da der gepostete Name auch zuvor normalisiert wurde
			$normalizedFieldsetName = rex_form_element::_normalizeName($fieldsetName);
			// POST-Werte ermitteln
			$fieldsetValues = rex_post($normalizedFieldsetName, 'array');
			if(isset($fieldsetValues[$fieldName]))
			{
				return $fieldsetValues[$fieldName];
			}
			return $default;
	}
	
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
			// Name normalisieren, da der gepostete Name auch zuvor normalisiert wurde
			$normalizedFieldsetName = rex_form_element::_normalizeName($fieldsetName);
			// POST-Werte ermitteln
			foreach(rex_post($normalizedFieldsetName, 'array') as $fieldName => $fieldValue)
			{
				// Callback, um die Values vor dem Speichern noch beeinflussen zu können
				$fieldValue = $this->prepareSave($fieldsetName, $fieldName, $fieldValue);
				
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
		$sql = rex_sql::getInstance();
		$sql->debugsql =& $this->debug;
		$sql->setTable($this->tableName);
		$sql->setWhere($this->whereCondition);
		return $sql->delete();
	}
	
	function redirect($listMessage = '', $params = array())
	{
		if($listMessage != '')
		{
			$listName = rex_request('list', 'string');
			$params[$listName.'_msg'] = $listMessage;
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
		$this->setApplyUrl($this->getUrl(array('func' => ''), false));
		
		if(($controlElement = $this->getControlElement()) !== null)
		{ 
			// TODO Translate
			if($controlElement->saved())
			{
				// speichern und umleiten
				// Nachricht in der Liste anzeigen
				if(($result = $this->save()) === true)
					$this->redirect('Eingaben wurden gespeichert!');
				elseif(is_string($result) && $result != '')
					// Falls ein Fehler auftritt, das Formular wieder anzeigen mit der Meldung
					$this->setMessage($result);
				else 
					$this->redirect('Fehler beim speichern!');
			}
			elseif($controlElement->applied())
			{
				// speichern und wiederanzeigen
 				// Nachricht im Formular anzeigen
				if(($result = $this->save()) === true)
	 				$this->setMessage('Eingaben wurden übernommen');
				elseif(is_string($result) && $result != '')
					$this->setMessage($result);
				else 
					$this->setMessage('Fehler beim speichern!');
			}
			elseif($controlElement->deleted())
			{
				// speichern und wiederanzeigen
				// Nachricht in der Liste anzeigen
				if(($result = $this->delete()) === true)
					$this->redirect('Eingaben wurden gelöscht!');
				elseif(is_string($result) && $result != '')
					$this->redirect($result);
				else
					$this->redirect('Fehler beim löschen!');
			}
			elseif($controlElement->resetted())
			{
				// verwerfen und wiederanzeigen
				// Nachricht im Formular anzeigen
				$this->setMessage('Eingaben wurden verworfen!');
			}
			elseif($controlElement->aborted())
			{
				// verwerfen und umleiten
				// Nachricht in der Liste anzeigen
				$this->redirect('Eingaben wurden verworfen!');
			}
		}
		
		// Parameter dem Formular hinzufügen
		foreach($this->getParams() as $name => $value)
		{
			$this->addHiddenField($name, $value, array('internal::useArraySyntax' => 'none'));
		} 
		
		$s = "\n";
		$s .= '<div class="rex-mdl-editmode">';
		
		$message = $this->getMessage();
		if($message != '')
		{
			$s .= '  <p class="rex-warning">'. $message .'</p>'. "\n";
		}
		
		$i = 0;
		$addHeaders = true;
		$fieldsets = $this->getFieldsetElements();
		$last = count($fieldsets);
		
		$s .= '  <form action="index.php" method="'. $this->method .'">'. "\n";
		foreach($fieldsets as $fieldsetName => $fieldsetElements)
		{
			$s .= '    <fieldset>'. "\n";
			$s .= '      <legend class="rex-lgnd">'. htmlspecialchars($fieldsetName) .'</legend>'. "\n";
			$s .= '      <div class="rex-fldst-wrppr">'. "\n";
			
			// Die HeaderElemente nur im 1. Fieldset ganz am Anfang einfügen
			if($i == 0 && $addHeaders)
			{
				foreach($this->getHeaderElements() as $element)
				{
					// HeaderElemente immer ohne <p>
					$s .= $element->formatElement();
				}
				$addHeaders = false;
			}
			
			foreach($fieldsetElements as $element)
			{
				$s .= $element->get();
			}
			
			// Die FooterElemente nur innerhalb des letzten Fieldsets
			if(($i + 1) == $last)
			{
				foreach($this->getFooterElements() as $element)
				{
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
	
	function rex_form_element($tag, &$table, $attributes = array(), $separateEnding = false)
	{
		$this->value = null;
		$this->label = '';
		$this->tag = $tag;
		$this->table =& $table;
		$this->setAttributes($attributes);
		$this->separateEnding = $separateEnding;
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

	function setLabel($label)
	{
		$this->label = $label;
	}
	
	function getLabel()
	{
		return $this->label;
	}

	function getTag()
	{
		return $this->tag;
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
			
			if($name == 'name')
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
			return '          <'. $this->getTag(). $attr .'/>'. "\n"; 
		}
	}
	
	function _get()
	{
		$s = '';
		
		$s .= $this->formatLabel();
		$s .= $this->formatElement();
		
		return $s;
	}
	
	function get()
	{
		$s = '';

		$s .= '        <p>'. "\n";
		$s .= $this->_get();
		$s .= '        </p>'. "\n";
		
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
		
		if($this->saveElement)
		{
			if(!$this->saveElement->hasAttribute('class'))
				$this->saveElement->setAttribute('class', 'rex-sbmt');
				
			$s .= $this->saveElement->formatElement();
		}
		
		if($this->applyElement)
		{
			if(!$this->applyElement->hasAttribute('class'))
				$this->applyElement->setAttribute('class', 'rex-sbmt');
				
			$s .= $this->applyElement->formatElement();
		}

		if($this->deleteElement)
		{
			if(!$this->deleteElement->hasAttribute('class'))
				$this->deleteElement->setAttribute('class', 'rex-sbmt');
				
			if(!$this->deleteElement->hasAttribute('onclick'))
				$this->deleteElement->setAttribute('onclick', 'return confirm(\'Löschen?\');');

			$s .= $this->deleteElement->formatElement();
		}

		if($this->resetElement)
		{
			if(!$this->resetElement->hasAttribute('class'))
				$this->resetElement->setAttribute('class', 'rex-sbmt');
	
			if(!$this->resetElement->hasAttribute('onclick'))
				$this->resetElement->setAttribute('onclick', 'return confirm(\'Änderungen verwerfen?\');');
				
			$s .= $this->resetElement->formatElement();
		}
			
		if($this->abortElement)
		{
			if(!$this->abortElement->hasAttribute('class'))
				$this->abortElement->setAttribute('class', 'rex-sbmt');
			
			$s .= $this->abortElement->formatElement();
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
	
	// 1. Parameter nicht gentzt, muss aber hier stehen,
	// wg einheitlicher Konstrukturparameter
	function rex_form_select_element($tag = '', &$table, $attributes = array())
	{
		parent::rex_form_element('', $table, $attributes);
				
		$this->select =& new rex_select();
		$this->select->setName($this->getAttribute('name'));
	}
	
	function formatElement()
	{
		$this->select->setSelected($this->getValue());
		return $this->select->get();
	}
	
	function &getSelect()
	{
		return $this->select;
	}
}
?>