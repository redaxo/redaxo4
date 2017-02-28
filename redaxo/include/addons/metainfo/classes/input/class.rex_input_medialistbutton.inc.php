<?php

class rex_input_medialistbutton extends rex_input
{
    var $buttonId;
    var $categoryId;
    var $args = array();

    // this is the new style constructor used by newer php versions.
    // important: if you change the signatur of this method, change also the signature of rex_input_medialistbutton()
    function __construct()
    {
        $this->rex_input_medialistbutton();
    }

    // this is the deprecated old style constructor kept for compat reasons. 
    // important: if you change the signatur of this method, change also the signature of __construct()
    function rex_input_medialistbutton()
    {
        parent::rex_input();
        $this->buttonId = '';
    }

    function setButtonId($buttonId)
    {
        $this->buttonId = $buttonId;
        $this->setAttribute('id', 'REX_MEDIALIST_' . $buttonId);
    }

    function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    function setTypes($types)
    {
        $this->args['types'] = $types;
    }

    function setPreview($preview = true)
    {
        $this->args['preview'] = $preview;
    }

    function getHtml()
    {
        $buttonId = $this->buttonId;
        $categoryId = $this->categoryId;
        $value = htmlspecialchars($this->value);
        $name = $this->attributes['name'];
        $args = $this->args;

        $field = rex_var_media::getMediaListButton($buttonId, $value, $categoryId, $args);
        $field = str_replace('MEDIALIST[' . $buttonId . ']', $name, $field);

        return $field;
    }
}
