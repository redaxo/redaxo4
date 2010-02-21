<?php

class rex_effect_resize extends rex_effect_abstract
{
	function execute()
	{
    $gdimage =& $this->image->getImage();
    $w = $this->image->getWidth();
    $h = $this->image->getHeight();

		$this->params['styles'] = array('fit','warp');
		if(!isset($this->params['style']) || !in_array($this->params['style'],$this->params['styles']))
		{
			$this->params['style'] = 'warp';
		}

		if($this->params['style'] == 'fit')
		{
			if (!isset($this->params['height']))
			{
				$this->params['height'] = $this->params['width'];
			}
			 
			$img_ratio  = $w / $h;
			$resize_ratio = $this->params['width'] / $this->params['height'];
			
			if ($img_ratio >= $resize_ratio)
			{
				// --- width
				$this->params['height'] = (int) ($this->params['width'] / $w * $h);
			}else
			{
				// --- height
				$this->params['width']  = (int) ($this->params['height'] / $h * $w);
			}

		}

		// Originalbild selbst sehr klein und wuerde via resize vergroessert
		// => Das Originalbild ausliefern
		if(!isset($this->params["width"]))
		{
			$this->params["width"] = $w;
		}

		if(!isset($this->params["height"]))
		{
			$this->params["height"] = $h;
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
		imagecopyresampled($des, $gdimage, 0, 0, 0, 0, $this->params['width'], $this->params['height'], $w, $h);
		
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
        'title'=>$I18N->msg('width'),
        'name' => 'width',
        'type' => 'int'
      ),
      array(
        'title'=>$I18N->msg('height'),
        'name' => 'height',
        'type' => 'int'
      ),
      array(
        'title' => $I18N->msg('style'),
        'name' => 'style',
        'type'  => 'select',
        'type_params' => array('fit','warp'),
        'default' => 'fit'
      ),
    );
	}
}