<?php

// Todo:
// - Vergrš§ern erlauben oder nicht. aber eher als Modul einsetzen, also
// fit

class rex_effect_resize extends rex_effect_abstract
{
	var $options;

	function rex_effect_resize()
	{
		$this->options = array('fit','warp','minimum');
	}

	function execute()
	{
		$gdimage =& $this->image->getImage();
		$w = $this->image->getWidth();
		$h = $this->image->getHeight();

		if(!isset($this->params['style']) || !in_array($this->params['style'],$this->options))
		{
			$this->params['style'] = 'fit';
		}

		if($this->params['style'] == 'fit')
		{
		  $this->resizeFit($w, $h);
		}
		else if($this->params['style'] == 'minimum') 
		{
		  $this->resizeMinimum($w, $h);
		}
		else
		{
		  // warp => nichts tun
		}

		// TODO prŸfen!
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
	
	function resizeFit($w, $h)
	{
    if (!empty($this->params['height']) && !empty($this->params['width']))
    {
      $img_ratio  = $w / $h;
      $resize_ratio = $this->params['width'] / $this->params['height'];
      
      if ($img_ratio >= $resize_ratio)
      {
        // --- width
        $this->params['height'] = ceil ($this->params['width'] / $w * $h);
      }else
      {
        // --- height
        $this->params['width']  = ceil ($this->params['height'] / $h * $w);
      }
    }
    elseif (!empty($this->params['height']))
    {
      $img_factor  = $h / $this->params['height'];
      $this->params['width'] = ceil ($w / $img_factor);
    }
    elseif (!empty($this->params['width']))
    {
      $img_factor  = $w / $this->params['width'];
      $this->params['height'] = ceil ($h / $img_factor);
    }
	}
	
	function resizeMinimum($w, $h)
	{
    if (!empty($this->params['height']) && !empty($this->params['width']))
    {
      $img_ratio  = $w / $h;
      $resize_ratio = $this->params['width'] / $this->params['height'];

      if ($img_ratio < $resize_ratio)
      {
        // --- width
        $this->params['height'] = ceil ($this->params['width'] / $w * $h);
      }else
      {
        // --- height
        $this->params['width']  = ceil ($this->params['height'] / $h * $w);
      }
    }
    elseif (!empty($this->params['height']))
    {
      $img_factor  = $h / $this->params['height'];
      $this->params['width'] = ceil ($w / $img_factor);
    }
    elseif (!empty($this->params['width']))
    {
      $img_factor  = $w / $this->params['width'];
      $this->params['height'] = ceil ($h / $img_factor);
    }
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
        'label'=>$I18N->msg('imanager_effect_resize_width'),
        'name' => 'width',
        'type' => 'int'
        ),
        array(
        'label'=>$I18N->msg('imanager_effect_resize_height'),
        'name' => 'height',
        'type' => 'int'
        ),
        array(
        'label' => $I18N->msg('imanager_effect_resize_style'),
        'name' => 'style',
        'type'  => 'select',
        'options' => $this->options,
        'default' => 'fit',
        ),
        /*
         array(
         'label'=>$I18N->msg('imanager_effect_resize_allow_enlarge'),
         'name' => 'allow_enlarge',
         'type' => 'select',
         'options' => array($I18N->msg('yes'), $I18N->msg('no')),
         'default' => $I18N->msg('no'),
         ),
         */
        );
	}
}