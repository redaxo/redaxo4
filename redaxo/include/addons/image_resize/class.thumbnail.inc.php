<?php

	// class thumbail
	class thumbnail
	{
	    var $img;

	    function thumbnail($imgfile)
	    {
	        //detect image format
	        $this->img["format"]=ereg_replace(".*\.(.*)$","\\1",$imgfile);
	        $this->img["format"]=strtoupper($this->img["format"]);
	        if(!eregi('cache/',$imgfile)){
	            if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
	                //JPEG
	                $this->img["format"]="JPEG";
	                $this->img["src"] = @ImageCreateFromJPEG ($imgfile);
	            } elseif ($this->img["format"]=="PNG") {
	                //PNG
	                $this->img["format"]="PNG";
	                $this->img["src"] = @ImageCreateFromPNG ($imgfile);
	            } elseif ($this->img["format"]=="GIF") {
	                //GIF
	                $this->img["format"]="GIF";
	                $this->img["src"] = @ImageCreateFromGIF ($imgfile);

	            } elseif ($this->img["format"]=="WBMP") {
	                //WBMP
	                $this->img["format"]="WBMP";
	                $this->img["src"] = @ImageCreateFromWBMP ($imgfile);
	            } else {
	                //DEFAULT
	                echo "Not Supported File";
	                exit();
	            }
	            @$this->img["lebar"] = imagesx($this->img["src"]);
	            @$this->img["tinggi"] = imagesy($this->img["src"]);
	            //default quality jpeg
	            $this->img["quality"]=85;
	        }

            if ( !$this->img["src"]) {
                $this->_error( $imgfile);
            }
	    }

	    function size_height($size=100)
	    {
	        //height
	        $this->img["tinggi_thumb"]=$size;
	        @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
	    }

	    function size_width($size=100)
	    {
	        //width
	        $this->img["lebar_thumb"]=$size;
	        @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
	    }

	    function size_auto($size=100)
	    {
	        //size
	        if ($this->img["lebar"]>=$this->img["tinggi"]) {
	            $this->img["lebar_thumb"]=$size;
	            @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
	        } else {
	            $this->img["tinggi_thumb"]=$size;
	            @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
	        }
	    }

	    function jpeg_quality($quality=85)
	    {
	        //jpeg quality
	        $this->img["quality"]=$quality;
	    }

	    function show()
	    {
	        //show thumb
	        @header("Content-Type: image/".$this->img["format"]);

            $this->_createImage();

	        @imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

            $this->_sendImage();
	    }

	    function save($save="")
	    {
	        //save thumb
	        if (empty($save)) $save="./thumb." .strtolower($this->img["format"]);

            $this->_createImage();

	        @imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

            $this->_sendImage();
	    }

        function _sendImage() {
            if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
                //JPEG
                imageJPEG($this->img["des"],"",$this->img["quality"]);
            } elseif ($this->img["format"]=="PNG") {
                //PNG
                imagePNG($this->img["des"]);
            } elseif ($this->img["format"]=="GIF") {
                //GIF
                // support ist nur mit den neusten GD-LIBS möglich
                if (function_exists("imagegif")) {
                    imageGIF($this->img["des"]);
                } else {
                    imageJPEG($this->img["des"]);
                }
            } elseif ($this->img["format"]=="WBMP") {
                //WBMP
                imageWBMP($this->img["des"]);
            }
        }

        function _createImage() {
            /* change ImageCreateTrueColor to ImageCreate if GD2 not supported ImageCreateTrueColor function*/

            $this->img["lebar_thumb"] = intval($this->img["lebar_thumb"]);
            $this->img["tinggi_thumb"] = intval($this->img["tinggi_thumb"]);
            if ($this->img["lebar_thumb"] == 0) $this->img["lebar_thumb"] = 1;
            if ($this->img["tinggi_thumb"] == 0) $this->img["tinggi_thumb"] = 1;

            if(function_exists( "ImageCreateTrueColor")){
                $this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
            } else {
                $this->img["des"] = ImageCreate($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
            }
        }

        function _error( $imgfile) {
            header("Content-Type: image/png");

            $this->img["des"] = imagecreate (150,35); /* Create a blank image */

            $bgc = imagecolorallocate ($this->img["des"], 255, 255, 255);
            $tc  = imagecolorallocate ($this->img["des"], 0, 0, 0);

            imagefilledrectangle ($this->img["des"], 0, 0, 150, 30, $bgc);
            /* Output an errmsg */
            imagestring ($this->img["des"], 1, 5, 5, "Error loading", $tc);
            imagestring ($this->img["des"], 1, 5, 20, $imgfile, $tc);

            imagepng( $this->img["des"]);
            imagedestroy( $this->img["des"]);
            exit();
       }
	}
?>