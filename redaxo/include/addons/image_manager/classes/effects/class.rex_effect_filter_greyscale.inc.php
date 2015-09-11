<?php

class rex_effect_filter_greyscale extends rex_effect_abstract
{
    function execute()
    {

        if (!$this->image->isImage()) {
          return false;
        }

        $gdimage = & $this->image->getImage();
        imagesavealpha($gdimage, true);
		imagealphablending($gdimage, true);

        imagefilter($gdimage, IMG_FILTER_GRAYSCALE);

    }

    function getParams()
    {
        global $REX, $I18N;
        return array();
    }
}
