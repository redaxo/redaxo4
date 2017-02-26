<?php

class rex_input_textarea extends rex_input
{
    // this is the new style constructor used by newer php versions.
    // important: if you change the signatur of this method, change also the signature of rex_input_textarea()
    function __construct()
    {
        $this->rex_input_textarea();
    }

    // this is the deprecated old style constructor kept for compat reasons. 
    // important: if you change the signatur of this method, change also the signature of __construct()
    function rex_input_textarea()
    {
        parent::rex_input();
        $this->setAttribute('class', 'rex-form-textarea');
        $this->setAttribute('cols', '50');
        $this->setAttribute('rows', '6');
    }

    function getHtml()
    {
        $value = htmlspecialchars($this->value);
        return '<textarea' . $this->getAttributeString() . '>' . $value . '</textarea>';
    }
}
