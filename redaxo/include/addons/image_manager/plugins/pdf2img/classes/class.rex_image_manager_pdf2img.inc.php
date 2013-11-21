<?php 

class rex_image_manager_pdf2img {


  private 
    $convert_path = '',
    $image_path = '',
    $file_path = '';
  

  /* init called by EP: IMAGE_MANAGER_INIT */
  static function init($params)
  {
  
    if($params['subject']['rex_img_file'] != '' && substr($params['subject']['rex_img_file'], -4) == ".pdf") {
    
      $pdf = new rex_image_manager_pdf2img();
      $pdf->setFilePath($params['subject']['imagepath']);
      
      if ($pdf->isCached()) {
        $params['subject']['imagepath'] = $pdf->getImagePath();

      } else if  ($pdf->exec() && $pdf->getImagePath() != '') {
        $params['subject']['imagepath'] = $pdf->getImagePath();

      } else {
        // keep old imagepath
      
      }

    }

    return $params['subject'];
  }
  
  // ------------------------------------------
  
  public function setFilePath($file_path) 
  {
    global $REX;
    $this->file_path = realpath($file_path);
    $this->setImagePath($REX['GENERATED_PATH'].'/files/image_manager_pdf2img_'.md5($this->file_path).'.png');
    
  }
  
  public function getFilePath() 
  {
    return $this->file_path;
    
  }
  
  public function setImagePath($image_path) 
  {
    $this->image_path = $image_path;
    
  }
  
  public function getImagePath() 
  {
    return $this->image_path;
    
  }

  public function isCached()
  {
    if(file_exists($this->getImagePath())) {
      return true;
    }
    return false;
  }

  public function exec() 
  {
  
    global $REX;
    
    $convert_path = self::getConvertPath();
    if ($convert_path != '') {

      $cmd = $convert_path . ' -density 600 "'.$this->getFilePath().'[0]" -colorspace RGB -resample 300  "'.$this->getImagePath().'"';
      // convert -density 600 document.pdf[0] -colorspace RGB -resample 300 output.jpg
      // convert -density 260 -profile 'SWOP.icc' -profile 'sRGB.icm' 'baby_aRCWTU.pdf' 'baby_aRCWTU.jpg'
      // pdftops baby_aRCWTU.pdf baby_aRCWTU.ps

      exec($cmd, $out = array(),$ret);
      
      if($ret != 0) {

        // Error
        $this->setImagePath('');
        return false;
      
      }
      return true;
    
    } else {
      $this->setImagePath('');
      return false;

    }
    
  }


  static function getConvertPath() 
  {
    $path = '';
    if(function_exists('exec')) {
      $out = array();
      $cmd = 'which convert';
      exec($cmd, $out, $ret);
      if(isset($ret) && $ret !== null) {
        switch($ret) {
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

}


