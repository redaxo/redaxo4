<?php 

/*abstract*/ class rex_input
{
  var $value;
  var $attributes;
  
  /*public*/ function rex_input()
  {
    $this->value = '';
    $this->attributes = array();
  }
  
  /*public*/ function setValue($value)
  {
    $this->value = $value;
  }

  /*public*/ function getValue()
  {
    return $this->value;
  }
  
  /*public*/ function setAttribute($name, $value)
  {
    if($name == 'value')
    {
      $this->value = $value;
    }
    else
    {
      $this->attributes[$name] = $value;
    }
  }

  /*public*/ function getAttribute($name, $default = null)
  {
    if($name == 'value')
    {
      return $this->getValue();
    }
    elseif(isset($this->attributes[$name]))
    {
      return $this->attributes[$name];
    }

    return $default;
  }

  /*public*/ function hasAttribute($name)
  {
    return isset($this->attributes[$name]);
  }

  /*public*/ function setAttributes($attributes)
  {
    $this->attributes = array();
    
    foreach($attributes as $name => $value)
    {
      $this->setAttribute($name, $value);
    }
  }

  /*public*/ function getAttributes()
  {
    return $this->attributes;
  }
  
  /*public*/ function getAttributeString()
  {
    $attr = '';
    foreach($this->attributes as $attributeName => $attributeValue)
    {
      $attr .= ' '. $attributeName .'="'. $attributeValue .'"';
    }
    return $attr;
  }
  

  /*abstract*/ function getHtml()
  {
    // nichts tun
  }
  
  /*public static*/ function factory($inputType)
  {
    
    switch($inputType)
    {
      case 'text': 
      case 'textarea': 
      case 'select': 
      case 'categoryselect': 
      case 'mediacategoryselect': 
      case 'radio': 
      case 'checkbox': 
      case 'date': 
      case 'time': 
      case 'datetime': 
      case 'mediabutton': 
      case 'medialistbutton': 
      case 'linkbutton': 
      case 'linklistbutton': 
      {
        $class = 'rex_input_'. $inputType;
        return new $class(); 
      } 
    }
    return null;
  }
}