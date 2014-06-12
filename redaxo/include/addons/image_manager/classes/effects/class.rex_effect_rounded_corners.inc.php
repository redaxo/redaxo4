<?php

/**
 * Runde Ecken
 *
 * @author staabm
 */

class rex_effect_rounded_corners extends rex_effect_abstract
{

    function rex_effect_rounded_corners()
    {

    }

    function execute()
    {

        if (!$this->image->isImage()) {
            return false;
        }

        $gdimage = & $this->image->getImage();
        $w = $this->image->getWidth();
        $h = $this->image->getHeight();

        $radius = array(
            'tl' => $this->params['topleft'],
            'tr' => $this->params['topright'],
            'br' => $this->params['bottomright'],
            'bl' => $this->params['bottomleft']
        );

        $colour = 'ffffff';

        foreach ($radius as $k => $r) {
            if (empty($r) or $r < 0) {
                continue;
            }

            $corner_image = imagecreatetruecolor(
                $r,
                $r
            );

            $clear_colour = imagecolorallocate(
                $corner_image,
                0,
                0,
                0
            );

            $solid_colour = imagecolorallocate(
                $corner_image,
                hexdec( substr( $colour, 0, 2 ) ),
                hexdec( substr( $colour, 2, 2 ) ),
                hexdec( substr( $colour, 4, 2 ) )
            );

            imagecolortransparent(
                $corner_image,
                $clear_colour
            );

            imagefill(
                $corner_image,
                0,
                0,
                $solid_colour
            );

            imagefilledellipse(
                $corner_image,
                $r,
                $r,
                $r * 2,
                $r * 2,
                $clear_colour
            );

            switch ($k) {
                case 'tl':
                    imagecopymerge(
                        $gdimage,
                        $corner_image,
                        0,
                        0,
                        0,
                        0,
                        $r,
                        $r,
                        100
                    );
                break;

                case 'tr':
                    $corner_image = imagerotate( $corner_image, 270, 0 );
                    imagecopymerge(
                        $gdimage,
                        $corner_image,
                        $w - $r,
                        0,
                        0,
                        0,
                        $r,
                        $r,
                        100
                    );

                break;

                case 'br':
                    $corner_image = imagerotate( $corner_image, 180, 0 );
                    imagecopymerge(
                        $gdimage,
                        $corner_image,
                        $w - $r,
                        $h - $r,
                        0,
                        0,
                        $r,
                        $r,
                        100
                    );        break;

                case 'bl':
                    $corner_image = imagerotate( $corner_image, 90, 0 );
                    imagecopymerge(
                        $gdimage,
                        $corner_image,
                        0,
                        $h - $r,
                        0,
                        0,
                        $r,
                        $r,
                        100
                    );
                break;
            }
        }

    }

    function getParams()
    {
        global $REX, $I18N;

        return array(
            array(
                'label' => $I18N->msg('imanager_effect_rounded_corners_topleft'),
                'name' => 'topleft',
                'type' => 'int'
            ),
            array(
                'label' => $I18N->msg('imanager_effect_rounded_corners_topright'),
                'name' => 'topright',
                'type' => 'int'
            ),
            array(
                'label' => $I18N->msg('imanager_effect_rounded_corners_bottomleft'),
                'name' => 'bottomleft',
                'type' => 'int'
            ),
            array(
                'label' => $I18N->msg('imanager_effect_rounded_corners_bottomright'),
                'name' => 'bottomright',
                'type' => 'int'
            )
        );
    }
}
