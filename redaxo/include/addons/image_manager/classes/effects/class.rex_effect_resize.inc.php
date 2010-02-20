<?php

class rex_effect_resize extends rex_effect_abstract{


	function execute()
	{

		$this->img['width'] = imagesx($this->img['src']);
		$this->img['height'] = imagesy($this->img['src']);

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
			 
			$img_ratio  = $this->img['width'] / $this->img['height'];
			$resize_ratio = $this->params['width'] / $this->params['height'];
			
			if ($img_ratio >= $resize_ratio)
			{
				// --- width
				$this->params['height'] = (int) ($this->params['width'] / $this->img['width'] * $this->img['height']);
			}else
			{
				// --- height
				$this->params['width']  = (int) ($this->params['height'] / $this->img['height'] * $this->img['width']);
			}

		}



		// Originalbild selbst sehr klein und wuerde via resize vergroessert
		// => Das Originalbild ausliefern

		if(!isset($this->params["width"]))
		{
			$this->params["width"] = $this->img["width"];
		}

		if(!isset($this->params["height"]))
		{
			$this->params["height"] = $this->img["height"];
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
		imagecopyresampled($des, $this->img['src'], 0, 0, 0, 0, $this->params['width'], $this->params['height'], $this->img['width'], $this->img['height']);
		$this->img['src'] = $des;
	}


	function keepTransparent($des)
	{
		if ($this->img['format'] == 'PNG')
		{
			imagealphablending($des, false);
			imagesavealpha($des, true);
		}
		else if ($this->img['format'] == 'GIF')
		{
			$colorTransparent = imagecolortransparent($this->img['src']);
			imagepalettecopy($this->img['src'], $des);
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