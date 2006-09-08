<?php

/** 
 * Klasse zur Erstellung eines HTML-Pulldown-Menues (Select-Box)  
 * @package redaxo3 
 * @version $Id$ 
 */

################ Class Select
class rex_select
{

  var $select_name; //
  var $select_id;
  var $options; // 
  var $option_selected; //
  var $select_size; // 
  var $select_multiple; //
  var $select_style;
  var $select_extra;
  var $select_style_class;

  ################ Konstruktor
  function rex_select()
  {
    $this->init();
  }

  ############### multiple felder ? 
  function multiple($mul)
  {
    if ($mul == 1)
    {
      $this->select_multiple = ' multiple="multiple"';
    }
    else
    {
      $this->select_multiple = '';
    }
  }

  ################ init 
  function init()
  {
    //    $this->counter    = 0;
    $this->select_name = "standard";
    $this->select_size = 5;
    $this->select_multiple = "";
    $this->option_selected = array ();

  }

  ################ select name
  function set_name($name)
  {
    $this->select_name = $name;
  }

  ################ select extra
  function set_selectextra($extra)
  {
    $this->select_extra = $extra;
  }

  ################ select id
  function set_id($id)
  {
    $this->select_id = $id;
  }

  /**
  * select style
  * Es ist moeglich sowohl eine Styleklasse als auch einen Style zu uebergeben.
  *
  * Aufrufbeispiel:
  * $sel_media->set_style('class="inp100"');
  * und/oder
  * $sel_media->set_style("width:150px;");
  */
  function set_style($style)
  {
    if (ereg("class=", $style))
    {
      $this->select_style_class = $style;
    }
    else
    {
      $this->select_style = 'style="'.$style.'"';
    }
  }

  ################ select size
  function set_size($size)
  {
    $this->select_size = $size;
  }

  ################ selected feld - option value uebergeben
  function set_selected($selected)
  {
    $this->option_selected[] = $selected;
  }

  function reset_selected()
  {
    $this->option_selected = array ();
  }

  ################ optionen hinzufuegen
  /**
   * Fügt eine Option hinzu
   */  
  function add_option($name, $value, $id = 0, $re_id = 0)
  {
    $this->options[$re_id][] = array ($name, $value, $id);
  }
  
  /**
   * Fügt ein Array von Optionen hinzu, dass eine mehrdimensionale Struktur hat.
   *
   * Dim   Wert
   * 1.    Name
   * 2.    Value
   * 3.    Id
   * 4.    Re_Id
   * 5.    Selected
   */  
  function add_options($options)
  {
    if(is_array($options) && count($options)>0)
    {
      $grouped = isset ($option[0][2]) && isset ($option[0][3]);
      foreach ($options as $option)
      {
        if ($grouped)
        {
          $this->add_option($option[0], $option[1], $option[2], $option[3]);
          if(isset($option[4]))
          {
          	$this->set_selected($option[4]);
          }
        }
        else
        {
          if(!isset($option[1]))
            $option[1] = $option[0];
            
          $this->add_option($option[0], $option[1]);
        }
      }
    }
  }
  
  /**
   * Fügt ein Array von Optionen hinzu, dass eine Key/Value Struktur hat.
   * Wenn $use_keys mit false, werden die Array-Keys mit den Array-Values überschrieben
   */  
  function add_array_options($options, $use_keys = true)
  {
  	foreach($options as $key => $value)
  	{
      if(!$use_keys)
        $key = $value;
  		  
      $this->add_option($value, $key);
  	}
  }
  
  /**
   * Fügt Optionen anhand der Übergeben SQL-Select-Abfrage hinzu.
   */  
  function add_sql_options($qry)
  {
    $sql = new rex_sql;
    // $sql->debugsql = true;
    $this->add_options($sql->getArray($qry, MYSQL_NUM));
  }

  ############### show select
  function out()
  {

    global $STYLE;
    $ausgabe = "\n".'<select '.$STYLE.' '.$this->select_multiple.' name="'.$this->select_name.'" size="'.$this->select_size.'" '.$this->select_style_class.' '.$this->select_style.' id="'.$this->select_id.'" '.$this->select_extra.'>'."\n";
    if (is_array($this->options))
      $ausgabe .= $this->out_group(0);
    $ausgabe .= "</select>\n";
    return $ausgabe;
  }

  function out_group($re_id, $level = 0)
  {

    if ($level > 100)
    {
      // nur mal so zu sicherheit .. man weiss nie ;)
      echo "select->out_group overflow ($groupname)";
      exit;
    }

    $ausgabe = '';
    $group = $this->get_group($re_id);
    foreach ($group as $option)
    {
      $name = $option[0];
      $value = $option[1];
      $id = $option[2];
      $ausgabe .= $this->out_option($name, $value, $level);

      $subgroup = $this->get_group($id, true);
      if ($subgroup !== false)
      {
        $ausgabe .= $this->out_group($id, $level +1);
      }
    }
    return $ausgabe;
  }

  function out_option($name, $value, $level = 0)
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

  function get_group($re_id, $ignore_main_group = false)
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