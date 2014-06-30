<?php

class rex_image
{

    var $img;
    var $gifsupport = false;

    var $image_mimetype_map = array(
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/vnd.wap.wbmp' => 'wbmp',
        'image/png' => 'png',
        'image/gif' => 'gif'
    );

    function rex_image($filepath)
    {
        global $REX;

        // ----- check params
        if (!file_exists($filepath)) {
            // 'Imagefile does not exist - '. $filepath
            $this->sendErrorImage();
        }

        // ----- imagepfad speichern
        $this->img = array();
        $this->img['file'] = basename($filepath);
        $this->img['filepath'] = $filepath;
        $this->img['quality'] = $REX['ADDON']['image_manager']['jpg_quality'];
        $this->img['format'] = strtolower(OOMedia::_getExtension($this->img['filepath']));
    }

    public function prepare()
    {
        if (!isset($this->img['src'])) {
            $this->gifsupport = function_exists('imagegif');
            // if mimetype detected and in imagemap -> change format
            if ( class_exists("finfo") && ($finfo = new finfo(FILEINFO_MIME_TYPE)) ) {
                if ( ($ftype = @$finfo->file($this->img['filepath'])) ) {
                    if (array_key_exists($ftype, $this->image_mimetype_map)) {
                        $this->img['format'] = $this->image_mimetype_map[$ftype];

                    }

                }

            }

            // ----- detect image format
            if ($this->img['format'] == 'jpg' || $this->img['format'] == 'jpeg') {
                $this->img['format'] = 'jpeg';
                $this->img['src'] = @imagecreatefromjpeg($this->img['filepath']);

            } elseif ($this->img['format'] == 'png') {
                $this->img['src'] = @imagecreatefrompng($this->img['filepath']);
                imagealphablending($this->img['src'], false);
                imagesavealpha($this->img['src'], true);

            } elseif ($this->img['format'] == 'gif') {
                if ($this->gifsupport) {
                    $this->img['src'] = @imagecreatefromgif($this->img['filepath']);
                }

            } elseif ($this->img['format'] == 'wbmp') {
                $this->img['src'] = @imagecreatefromwbmp($this->img['filepath']);

            }

            if (isset($this->img['src'])) {
                $this->refreshDimensions();
            }
        }
    }

    public function refreshDimensions()
    {
        $this->img['width'] = imagesx($this->img['src']);
        $this->img['height'] = imagesy($this->img['src']);
    }

    public function hasGifSupport()
    {
        return $this->gifsupport;
    }

    public function &getImage()
    {
        return $this->img['src'];
    }

    public function getFormat()
    {
        return $this->img['format'];
    }

    public function getFileName()
    {
        return $this->img['file'];
    }

    public function getFilePath()
    {
        return $this->img['filepath'];
    }

    public function getWidth()
    {
        return $this->img['width'];
    }

    public function getHeight()
    {
        return $this->img['height'];
    }

    public function destroy()
    {
        imagedestroy($this->img['src']);
    }

    public function save($filename)
    {
        $this->_sendImage($filename);
    }

    public function send($lastModified = null)
    {
        ob_start();
        $res = $this->_sendImage(null, $lastModified);
        $content = ob_get_clean();

        if (!$res) {
            return false;
        }

        $this->sendHeader();
        rex_send_resource($content, false, $lastModified);
    }

    public function sendHeader($params = array())
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Disposition: inline; filename="' . $this->img['file'] . '"');
        header('Content-Type: image/' . $this->img['format']);
        if (isset($params['Content-Length'])) {
            header('Content-Length: ' . $params['Content-Length']);
        }
    }

    /*protected*/ function _sendImage($saveToFileName = null, $lastModified = null)
    {
        global $REX;

        $file = $this->img['filepath'];

        if (!$lastModified) {
            $lastModified = time();
        }

        // ----- EXTENSION POINT
        $sendfile = true;
        $sendfile = rex_register_extension_point('IMAGE_SEND', $sendfile,
            array (
            // TODO Parameter anpassen
                    'img' => $this->img,
                'file' => $this->img['file'],
                'lastModified' => $lastModified,
                'filepath' => $this->img['filepath']
            )
        );

        if (!$sendfile) {
            return false;
        }

        // output image
        if ($this->img['format'] == 'jpg' || $this->img['format'] == 'jpeg') {
            imagejpeg($this->img['src'], $saveToFileName, $this->img['quality']);
        } elseif ($this->img['format'] == 'png') {
            if (isset($saveToFileName)) {
                imagepng($this->img['src'], $saveToFileName);
            } else {
                imagepng($this->img['src']);
            }
        } elseif ($this->img['format'] == 'gif') {
            imagegif($this->img['src'], $saveToFileName);
        } elseif ($this->img['format'] == 'wbmp') {
            imagewbmp($this->img['src'], $saveToFileName);
        }

        if ($saveToFileName)
            @chmod($saveToFileName, $REX['FILEPERM']);

        return true;
    }

    /*protected*/ function sendErrorImage($file = null)
    {
        if (!$file) {
            $file = dirname(__FILE__) . '/../media/warning.jpg';
        }

        // ----- EXTENSION POINT
        $sendfile = true;
        $sendfile = rex_register_extension_point('IMAGE_ERROR_SEND', $sendfile,
            array (
                'img' => $this->img,
                'file' => $file,
            )
        );

        if (!$sendfile) {
            return false;
        }

        $this->sendHeader(array('Content-Length' => filesize($file)));

        // error image nicht cachen
        header('Cache-Control: false');
        header('HTTP/1.0 404 Not Found');

        readfile($file);
        exit;
    }

    /*
     * Static Method: Returns True, if the given image is a valid rex_image
     */
    public static function isValid($image)
    {
        return is_object($image) && is_a($image, 'rex_image');
    }

    public function isImage()
    {
        if (isset($this->img['src']) && $this->img['src'] != '') {
            return true;
        } else {
            return false;
        }

    }

}
