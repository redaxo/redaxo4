<?php 

class rex_image_manager_convert2img {


  private 
    $convert_path = '',
    $image_path = '',
    $file_path = '',
    $image_type = '',
    $image_max = 3000;
  static 
    $convert_types = array("pdf", "ps", "psd", "tif", "tiff", "bmp", "eps", "ico");
  
  /* init called by EP: IMAGE_MANAGER_INIT */
  static function init($params)
  {
    if($params['subject']['rex_img_file'] != '' && $params['subject']['rex_img_type'] != "") {
    
      if ( ($ext = self::getExtension($params['subject']['rex_img_file'])) && in_array(strtolower($ext), self::$convert_types) ) {

        $pdf = new rex_image_manager_convert2img();
        $pdf->setImageType($params['subject']['rex_img_type']);
        $pdf->setFilePath($params['subject']['imagepath']);
        
        if ($pdf->isCached()) {
          $params['subject']['imagepath'] = $pdf->getImagePath();
  
        } else if  ($pdf->exec() && $pdf->getImagePath() != '') {
          $params['subject']['imagepath'] = $pdf->getImagePath();
  
        } else {
          // keep old imagepath
        
        }
      
      }

    }

    return $params['subject'];
  }
  
  // ------------------------------------------
  
  public function setFilePath($file_path) 
  {
    global $REX;
    $this->file_path = realpath($file_path);
    $this->setImagePath($REX['GENERATED_PATH'].'/files/image_manager__'.$this->getImageType().'__convert2img_'.md5($this->file_path).'.png');
    
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

  public function setImageType($image_type) 
  {
    $this->image_type = preg_replace('/[^a-zA-Z0-9.\-]/','_',$image_type);
    
  }
  
  public function getImageType() 
  {
    return $this->image_type;
    
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

      $cmd = $convert_path . ' -density 150 "'.$this->getFilePath().'[0]" -colorspace RGB "'.$this->getImagePath().'"';
      exec($cmd, $out = array(),$ret);
      
      if($ret != 0) {
        // Error
        $this->setImagePath('');
        return false;
      
      }

      $cmd = $convert_path . ' "'.$this->getImagePath().'" -resize '.$this->image_max.'x'.$this->image_max.'\> "'.$this->getImagePath().'"';
      exec($cmd, $out = array(), $ret);
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

  static function getExtension($filename) {
      $pos = strrpos($filename, '.');
      if($pos === false) {
          return false;
      } else {
          return substr($filename, $pos+1);
      }
  }


}


