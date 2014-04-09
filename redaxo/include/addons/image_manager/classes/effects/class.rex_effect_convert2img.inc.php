<?php

/**
 * Benutzt den Konsolen convert Befehl
 *
 * @author jan
 */

class rex_effect_convert2img extends rex_effect_abstract
{

    static $convert_types = array('pdf', 'ps', 'psd', 'tif', 'tiff', 'bmp', 'eps', 'ico');
    static $image_max = 3000;
    private $tmp_imagepath = '';


    function rex_effect_convert2img()
    {

    }

    function execute()
    {
        global $REX;

        $from_path = realpath($this->image->img['filepath']);

        if ( ($ext = self::getExtension($from_path)) && in_array(strtolower($ext), self::$convert_types) ) {

            // convert possible
            $convert_path = self::getConvertPath();
            if ($convert_path != '') {

                // convert to image and save in tmp
                $to_path = $REX['GENERATED_PATH'] . '/files/image_manager__convert2img_' . md5($this->image->img['filepath']) . '_' . $this->image->img['file'] . '.png';

                $cmd = $convert_path . ' -density 150 "' . $from_path . '[0]" -colorspace RGB "' . $to_path . '"';

                // echo $cmd;

                exec($cmd, $out, $ret);

                if ($ret != 0) {
                    return false;

                }

                $this->image->img['file'] = $this->image->img['file'] . '.png';
                $this->image->img['filepath'] = $to_path;
                $this->image->img['format'] = strtoupper(OOMedia::_getExtension($to_path));

                $this->tmp_imagepath = $to_path;

                $this->image->prepare();

            }

        } else {

            // no image

        }

        return;

    }


    function getParams()
    {
        global $REX, $I18N;

        return array(

/*
            array(
                'label' => $I18N->msg('imanager_effect_convert'),
                'name' => 'vpos',
                'type'    => 'select',
                'options'    => array('top','middle','bottom'),
                'default' => 'middle'
            ),


            array(
                'label'=>$I18N->msg('imanager_effect_convert'),
                'name' => 'width',
                'type' => 'int'
            ),
            array(
                'label'=>$I18N->msg('imanager_effect_crop_height'),
                'name' => 'height',
                'type' => 'int'
            ),
            array(
                'label'=>$I18N->msg('imanager_effect_crop_offset_width'),
                'name' => 'offset_width',
                'type' => 'int'
            ),
            array(
                'label'=>$I18N->msg('imanager_effect_crop_offset_height'),
                'name' => 'offset_height',
                'type' => 'int'
            ),
            array(
                'label' => $I18N->msg('imanager_effect_brand_hpos'),
                'name' => 'hpos',
                'type'    => 'select',
                'options'    => array('left','center','right'),
                'default' => 'center'
            ),

        */


        );



    }

    private function getConvertPath()
    {
        $path = '';
        if (function_exists('exec')) {
            $out = array();
            $cmd = 'which convert';
            exec($cmd, $out, $ret);
            if (isset($ret) && $ret !== null) {
                switch ($ret) {
                    case 0:
                        $path = $out[0];
                        break;
                    case 1:
                        $path = '';
                        break;
                    default:
                }
            }
        }
        return $path;
    }

    private function getExtension($filename)
    {
            $pos = strrpos($filename, '.');
            if ($pos === false) {
                    return false;
            } else {
                    return substr($filename, $pos + 1);
            }
    }

    public function __destruct()
    {
        if ($this->tmp_imagepath != '') {
            unlink($this->tmp_imagepath);
        }
    }
    /*
    */


}
