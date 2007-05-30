<?php

/** 
 * Klasse zur Erstellung eines HTML-Pulldown-Menues (Select-Box)
 *   
 * @package redaxo3 
 * @version $Id$ 
 */

################ Class Select
class rex_select
{
	var $attributes;
  var $options;
  var $option_selected;

  ################ Konstruktor
  function rex_select()
  {
    $this->init();
  }

  ################ init 
  function init()
  {
    $this->attributes = array();
    $this->resetSelected();
    $this->setName('standard');
    $this->setSize('5');
    $this->setMultiple(false);
  }
  
  function setAttribute($name, $value)
  {
  	$this->attributes[$name] = $value;
  }
  
  function delAttribute($name)
  {
  	if($this->hasAttribute($name))
  	{
  		unset($this->attributes[$name]);
  		return true;
  	}
  	return false;
  }
  
  function hasAttribute($name)
  {
  	return isset($this->attributes[$name]);
  }
  
  function getAttribute($name, $default = '')
  {
  	if($this->hasAttribute($name))
  	{
	  	return $this->attributes[$name];
  	}
  	return $default;
  }

  ############### multiple felder ? 
  function setMultiple($multiple)
  {
  	if($multiple)
  		$this->setAttribute('multiple', 'multiple');
  	else
  		$this->delAttribute('multiple');
  }

  ################ select name
  function setName($name)
  {
  	$this->setAttribute('name', $name);
  }

  ################ select id
  function setId($id)
  {
  	$this->setAttribute('id', $id);
  }

  /**
  * select style
  * Es ist moeglich sowohl eine Styleklasse als auch einen Style zu uebergeben.
  *
  * Aufrufbeispiel:
  * $sel_media->setStyle('class="inp100"');
  * und/oder
  * $sel_media->setStyle("width:150px;");
  */
  function setStyle($style)
  {
    if (strpos($style, 'class=') !== false)
    {
    	if(preg_match('/class=["\']?([^"\']*)["\']?/i', $style, $matches))
    	{
	    	$this->setAttribute('class', $matches[1]);
    	}
    }
    else
    {
    	$this->setAttribute('style', $style);
    }
  }

  ################ select size
  function setSize($size)
  {
  	$this->setAttribute('size', $size);
  }

  ################ selected feld - option value uebergeben
  function setSelected($selected)
  {
  	if(is_array($selected))
  	{
  		foreach($selected as $sectvalue)
  		{
  			$this->setSelected($sectvalue);
  		}
  	}
  	else
  	{
	    $this->option_selected[] = $selected;
  	}
  }

  function resetSelected()
  {
    $this->option_selected = array ();
  }

  ################ optionen hinzufuegen
  /**
   * Fügt eine Option hinzu
   */  
  function addOption($name, $value, $id = 0, $re_id = 0)
  {
    $this->options[$re_id][] = array ($name, $value, $id);
  }
  
  /**
   * Fügt ein Array von Optionen hinzu, dass eine mehrdimensionale Struktur hat.
   *
   * Dim   Wert
   * 0.    Name
   * 1.    Value
   * 2.    Id
   * 3.    Re_Id
   * 4.    Selected
   */  
  function addOptions($options)
  {
    if(is_array($options) && count($options)>0)
    {
      $grouped = isset ($options[0][2]) && isset ($options[0][3]);
      foreach ($options as $key => $option)
      {
      	$option = (array) $option;
        if ($grouped)
        {
          $this->addOption($option[0], $option[1], $option[2], $option[3]);
          if(isset($option[4]))
          {
          	$this->setSelected($option[4]);
          }
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
  
  /**
   * Fügt ein Array von Optionen hinzu, dass eine Key/Value Struktur hat.
   * Wenn $use_keys mit false, werden die Array-Keys mit den Array-Values überschrieben
   */  
  function addArrayOptions($options, $use_keys = true)
  {
  	foreach($options as $key => $value)
  	{
      if(!$use_keys)
        $key = $value;
  		  
      $this->addOption($value, $key);
  	}
  }
  
  /**
   * Fügt Optionen anhand der Übergeben SQL-Select-Abfrage hinzu.
   */  
  function addSqlOptions($qry)
  {
    $sql = new rex_sql;
//     $sql->debugsql = true;
    $this->addOptions($sql->getArray($qry, MYSQL_NUM));
  }

  ############### show select
  function get()
  {
  	$attr = '';
  	foreach($this->attributes as $name => $value)
  	{
  		$attr .= ' '. $name .'="'. $value .'"';
  	}
  	
    $ausgabe = "\n";
		$ausgabe .= '<select'.$attr.'>'."\n";
    
    if (is_array($this->options))
      $ausgabe .= $this->_outGroup(0);
      
    $ausgabe .= '</select>'. "\n";
    return $ausgabe;
  }
  
  ############### show select
  function show()
  {
  	echo $this->get();
  }

  function _outGroup($re_id, $level = 0)
  {

    if ($level > 100)
    {
      // nur mal so zu sicherheit .. man weiss nie ;)
      echo "select->_outGroup overflow ($groupname)";
      exit;
    }

    $ausgabe = '';
    $group = $this->_getGroup($re_id);
    foreach ($group as $option)
    {
      $name = $option[0];
      $value = $option[1];
      $id = $option[2];
      $ausgabe .= $this->_outOption($name, $value, $level);

      $subgroup = $this->_getGroup($id, true);
      if ($subgroup !== false)
      {
        $ausgabe .= $this->_outGroup($id, $level +1);
      }
    }
    return $ausgabe;
  }

  function _outOption($name, $value, $level = 0)
  {
    $bsps = '';
    $style = '';
    for ($i = 0; $i < $level; $i ++)
      $bsps .= "&nbsp;&nbsp;&nbsp;";
    $selected = '';
    if ($this->option_selected !== null)
    {
      $selected = in_array($value, $this->option_selected) ? ' selected="selected"' : '';
    }
    return '    <option value="'.$value.'"'.$style.$selected.'>'.$bsps.$name.'</option>'."\n";
  }

  function _getGroup($re_id, $ignore_main_group = false)
  {

    if ($ignore_main_group && $re_id == 0)
    {
      return false;
    }

    foreach ($this->options as $gname => $group)
    {
      if ($gname == $re_id)
      {
        return $group;
      }
    }

    return false;
  }
}

?>