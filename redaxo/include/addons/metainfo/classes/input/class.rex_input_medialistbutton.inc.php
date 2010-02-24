<?php

class rex_input_medialistbutton extends rex_input
{
  var $buttonId;
  var $categoryId;
  
  function rex_input_medialistbutton()
  {
    parent::rex_input();
    $this->buttonId = '';
  }
  
  function setButtonId($buttonId)
  {
    $this->buttonId = $buttonId;
    $this->setAttribute('id', 'REX_MEDIALIST_'. $buttonId);
  }
  
  function setCategoryId($categoryId)
  {
    $this->categoryId = $categoryId;
  }
  
  function getHtml()
  {
    $buttonId = $this->buttonId;
    $categoryId = $this->categoryId;
    $value = htmlspecialchars($this->value);
    $name = $this->attributes['name'];
    
    $field = rex_var_media::getMediaListButton($buttonId, $value, $categoryId);
    $field = str_replace('MEDIALIST['. $buttonId .']', $name, $field);
    
    return $field;
  }
}