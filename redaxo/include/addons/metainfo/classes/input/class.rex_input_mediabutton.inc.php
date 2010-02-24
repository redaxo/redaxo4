<?php

class rex_input_mediabutton extends rex_input
{
  var $buttonId;
  var $categoryId;
  
  function rex_input_mediabutton()
  {
    parent::rex_input();
    $this->buttonId = '';
  }
  
  function setButtonId($buttonId)
  {
    $this->buttonId = $buttonId;
    $this->setAttribute('id', 'REX_MEDIA_'. $buttonId);
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
    
    $field = rex_var_media::getMediaButton($buttonId, $categoryId);
    $field = str_replace('REX_MEDIA['. $buttonId .']', $value, $field);
    $field = str_replace('MEDIA['. $buttonId .']', $name, $field);
    
    return $field;
  }
}