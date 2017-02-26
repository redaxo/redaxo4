<?php

class rex_effect_filter_blur extends rex_effect_abstract
{

    var $options;

    // this is the new style constructor used by newer php versions.
    // important: if you change the signatur of this method, change also the signature of rex_effect_filter_blur()
    function __construct()
    {
        $this->rex_effect_filter_blur();
    }

    // this is the deprecated old style constructor kept for compat reasons. 
    // important: if you change the signatur of this method, change also the signature of __construct()
    function rex_effect_filter_blur()
    {
        $this->options = array(
            '', 'gaussian', 'selective'
        );
        $this->options_smoothit = array(
            -10, -9, -8, -7, -6, -5, -4, -3, -2, -1, '', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
        );
    }


    function execute()
    {

        if (!$this->image->isImage()) {
            return false;
        }

        $options = array();
        $options['gaussian'] = IMG_FILTER_GAUSSIAN_BLUR;
        $options['selective'] = IMG_FILTER_SELECTIVE_BLUR;

        $gdimage = & $this->image->getImage();

        $this->params['repeats'] = (int) $this->params['repeats'];
        if ($this->params['repeats'] < 0 ) {
            return;
        }

        if ( !in_array($this->params['type'], $this->options) ) {
            $this->params['type'] = '';
        }

        if ( !in_array($this->params['smoothit'], $this->options_smoothit) ) {
            $this->params['smoothit'] = '';
        }


        for ($i = 0; $i < $this->params['repeats']; $i++) {

            if ($this->params['smoothit'] != '') {
                imagefilter($gdimage, IMG_FILTER_SMOOTH, $this->params['smoothit']);
            }

            if ($this->params['type'] != '') {
                imagefilter($gdimage, $options[$this->params['type']] );
            }

        }

        return;

    }


    function getParams()
    {
        global $REX, $I18N;

        return array(
            array(
                'label' => $I18N->msg('imanager_effect_blur_repeats'),
                'name' => 'repeats',
                'type'    => 'int',
                'default' => '10'
            ),
            array(
                'label' => $I18N->msg('imanager_effect_blur_type'),
                'name' => 'type',
                'type'  => 'select',
                'options' => $this->options,
                'default' => 'gaussian'
            ),
            array(
                'label' => $I18N->msg('imanager_effect_blur_smoothit'),
                'name' => 'smoothit',
                'type'  => 'select',
                'options' => $this->options_smoothit,
                'default' => ''
            ),
        );

    }

}
