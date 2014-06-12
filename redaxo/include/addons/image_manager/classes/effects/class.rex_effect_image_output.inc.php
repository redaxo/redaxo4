<?php

class rex_effect_image_output extends rex_effect_abstract
{

    function execute()
    {
        global $REX;
        $this->image->img['format'] = $this->params['format'];

    }

    function getParams()
    {
        global $REX, $I18N;

        return array(
            array(
                'label' => 'Endformat',
                'name' => 'format',
                'type' => 'select',
                'options' => array('jpg', 'png', 'gif'),
                'default' => 'jpg',
            ),
        );
    }

}
