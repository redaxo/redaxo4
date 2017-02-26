<?php

class rex_input_select extends rex_input
{
    var $select;

    // this is the new style constructor used by newer php versions.
    // important: if you change the signatur of this method, change also the signature of rex_input_select()
    function __construct()
    { 
        $this->rex_input_select();
    }

    // this is the deprecated old style constructor kept for compat reasons. 
    // important: if you change the signatur of this method, change also the signature of __construct()
    function rex_input_select()
    {
        parent::rex_input();

        $this->select = new rex_select();
        $this->setAttribute('class', 'rex-form-select');
    }

    /*public*/ function setValue($value)
    {
        $this->select->setSelected($value);
        parent::setValue($value);
    }

    /*public*/ function setAttribute($name, $value)
    {
        if ($name == 'name') {
            $this->select->setName($value);
        } elseif ($name == 'id') {
            $this->select->setId($value);
        } else {
            $this->select->setAttribute($name, $value);
        }

        parent::setAttribute($name, $value);
    }

    /*public*/ function &getSelect()
    {
        return $this->select;
    }

    function getHtml()
    {
        return $this->select->get();
    }
}
