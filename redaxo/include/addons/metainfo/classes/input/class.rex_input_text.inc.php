<?php

class rex_input_text extends rex_input
{
    // this is the new style constructor used by newer php versions.
    // important: if you change the signatur of this method, change also the signature of rex_input_text()
    function __construct()
    {
        $this->rex_input_text();
    }

    // this is the deprecated old style constructor kept for compat reasons. 
    // important: if you change the signatur of this method, change also the signature of __construct()
    function rex_input_text()
    {
        parent::rex_input();
        $this->setAttribute('class', 'rex-form-text');
        $this->setAttribute('type', 'text');
    }

    function getHtml()
    {
        $value = htmlspecialchars($this->value);
        return '<input' . $this->getAttributeString() . ' value="' . $value . '" />';
    }
}
