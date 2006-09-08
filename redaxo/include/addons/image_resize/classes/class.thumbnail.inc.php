<?php

// ----- class thumbail
class thumbnail
{

  var $img;
  var $gifsupport;
  var $imgfile;
  
  function thumbnail($imgfile)
  {
  // ----- imagepfad speichern
    $this->imgfile = $imgfile;
    
    // ----- gif support ?
    $this->gifsupport = false;
    if (function_exists('imageGIF')) $this->gifsupport = true;
        
    // ----- detect image format
    $this->img['format']=ereg_replace('.*\.(.*)$','\\1',$imgfile);
    $this->img['format']=strtoupper($this->img['format']);
    if(!eregi('cache/',$imgfile)){
      if ($this->img['format']=='JPG' || $this->img['format']=='JPEG') {
        // --- JPEG
        $this->img['format']='JPEG';
        $this->img['src'] = ImageCreateFromJPEG ($imgfile);
  
      }elseif ($this->img['format']=='PNG') {
        // --- PNG
        $this->img['format']='PNG';
        $this->img['src'] = ImageCreateFromPNG ($imgfile);
      
      } elseif ($this->img['format']=='GIF') {
        // --- GIF
        $this->img['format']='GIF';
        if ($this->gifsupport) $this->img['src'] = ImageCreateFromGIF ($imgfile);
  
      } elseif ($this->img['format']=='WBMP') {
        // --- WBMP
        $this->img['format']='WBMP';
        $this->img['src'] = ImageCreateFromWBMP ($imgfile);
      
      } else {
        // --- DEFAULT
        echo 'Not Supported File';
        exit();
      }
      
      @$this->img['width'] = imagesx($this->img['src']);
      @$this->img['height'] = imagesy($this->img['src']);
      
      // --- default quality jpeg
      $this->img['quality']=75;
    }
  }

  function size_height($size=100)
  {
    // --- height
    $this->img['height_thumb']=$size;
    if($this->img['width_thumb']==''){
      @$this->img['width_thumb'] = ($this->img['height_thumb']/$this->img['height'])*$this->img['width'];
    }
  }

  function size_width($size=100)
  {
    // --- width
    $this->img['width_thumb']=$size;
    @$this->img['height_thumb'] = ($this->img['width_thumb']/$this->img['width'])*$this->img['height'];
  }

  function size_auto($size=100)
  {
    // --- size
    if ($this->img['width']>=$this->img['height']) {
      $this->img['width_thumb']=$size;
      @$this->img['height_thumb'] = ($this->img['width_thumb']/$this->img['width'])*$this->img['height'];
    } else {
      $this->img['height_thumb']=$size;
      @$this->img['width_thumb'] = ($this->img['height_thumb']/$this->img['height'])*$this->img['width'];
    }
  }

  function jpeg_quality($quality=85)
  {
    // --- jpeg quality
    $this->img['quality']=$quality;
  }

  function resampleImage()
  {
    if(function_exists('ImageCreateTrueColor')){
      $this->img['des'] = ImageCreateTrueColor($this->img['width_thumb'],$this->img['height_thumb']);
    } else {
      $this->img['des'] = ImageCreate($this->img['width_thumb'],$this->img['height_thumb']);
    }
    imagecopyresampled($this->img['des'], $this->img['src'], 0, 0, 0, 0, $this->img['width_thumb'], $this->img['height_thumb'], $this->img['width'], $this->img['height']);
  }

  function generateImage($save='',$show=true)
  {
    if ($this->img['format']=='GIF' && !$this->gifsupport)
    {
      // --- kein caching -> gif ausgeben
      header('Content-Type: image/'.$this->img['format']);
      readfile($this->imgfile);
      exit;
    }
    
    $this->resampleImage();
    if ($this->img['format']=='JPG' || $this->img['format']=='JPEG') {
      imageJPEG($this->img['des'],$save,$this->img['quality']);
    } elseif ($this->img['format']=='PNG') {
      imagePNG($this->img['des'],$save);
    } elseif ($this->img['format']=='GIF') {
      imageGIF($this->img['des'],$save);
    } elseif ($this->img['format']=='WBMP') {
      imageWBMP($this->img['des'],$save);
    }

    if($show){
      header('Content-Type: image/'.$this->img['format']);
      readfile($save);
    }
  }
}
?>