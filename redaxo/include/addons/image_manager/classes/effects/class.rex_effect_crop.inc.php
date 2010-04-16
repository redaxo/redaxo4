<?php

class rex_effect_crop extends rex_effect_abstract
{
  var $options;
  
  function rex_effect_crop()
  {
    $this->options = array(
      'top_left','top_center','top_right',
      'middle_left','middle_center','middle_right',
      'bottom_left','bottom_center','bottom_right',
    );
  }
  
	function execute()
	{
    $gdimage =& $this->image->getImage();
    $w = $this->image->getWidth();
    $h = $this->image->getHeight();

		if(empty($this->params['position']) || !in_array($this->params['position'],$this->options))
		{
			$this->params['position'] = 'middle_center';
		}
		
		if(empty($this->params['width']) || $this->params['width'] < 0 || 
		   empty($this->params['height']) || $this->params['height'] < 0)
		{
		  return;
		}
		
    $width_ratio = $w / $this->params['width'];
    $height_ratio = $h / $this->params['height'];
    
    $offset_width = 0;
    $offset_height = 0;
    if(empty($this->params['offset'])) $this->params['offset'] = 0;

    // Es muss an der Breite beschnitten werden
    if ($width_ratio > $height_ratio)
    {
      $offset_width = (int) (round(($w - $this->params['width'] * $height_ratio) / 2) + $this->params['offset']);
      $w            = (int) round($this->params['width'] * $height_ratio);
    }
    // es muss an der Höhe beschnitten werden
    elseif ($width_ratio < $height_ratio)
    {
      $offset_height = (int) (round(($h - $this->params['height'] * $width_ratio) / 2) + $this->params['offset']);
      $h             = (int) round($this->params['height'] * $width_ratio);
    }
		
		if (function_exists('ImageCreateTrueColor'))
		{
			$des = @ImageCreateTrueColor($this->params['width'], $this->params['height']);
		}else
		{
			$des = @ImageCreate($this->params['width'], $this->params['height']);
		}

		if(!$des)
		{
			return;
		}

		// Transparenz erhalten
		$this->keepTransparent($des);
    imagecopyresampled($des, $gdimage, 0, 0, $offset_width, $offset_height, $this->params['width'], $this->params['height'], $w, $h);
		
		$gdimage = $des;
		$this->image->refreshDimensions();
	}


	function keepTransparent($des)
	{
	  $image = $this->image;
		if ($image->getFormat() == 'PNG')
		{
			imagealphablending($des, false);
			imagesavealpha($des, true);
		}
		else if ($image->getFormat() == 'GIF')
		{
		  $gdimage =& $image->getImage();
			$colorTransparent = imagecolortransparent($gdimage);
			imagepalettecopy($gdimage, $des);
			if($colorTransparent>0)
			{
				imagefill($des, 0, 0, $colorTransparent);
				imagecolortransparent($des, $colorTransparent);
			}
			imagetruecolortopalette($des, true, 256);
		}
	}



	function getParams()
	{
		global $REX,$I18N;

		return array(
  		array(
        'label'=>$I18N->msg('imanager_effect_crop_width'),
        'name' => 'width',
        'type' => 'int'
      ),
      array(
        'label'=>$I18N->msg('imanager_effect_crop_height'),
        'name' => 'height',
        'type' => 'int'
      ),
      array(
        'label'=>$I18N->msg('imanager_effect_crop_offset'),
        'name' => 'offset',
        'type' => 'int'
      ),
      array(
        'label' => $I18N->msg('imanager_effect_crop_position'),
        'name' => 'position',
        'type'  => 'select',
        'options' => $this->options,
        'default' => 'middle_center'
      ),
    );
	}
}