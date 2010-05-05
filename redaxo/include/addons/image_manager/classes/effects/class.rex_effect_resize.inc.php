<?php

// Todo:
// - Vergrš§ern erlauben oder nicht. aber eher als Modul einsetzen, also
// fit

class rex_effect_resize extends rex_effect_abstract
{
	var $options;
	var $script;

	function rex_effect_resize()
	{
		$this->options = array(
		  'longest',
		  'shortest',
		  'warp'
		);

		$randId = get_class($this). rand();
		$this->script = '
		  <span id="'. $randId .'"></span>
      <script type="text/javascript">
      <!--
      
        (function($) {
          $(function() {
            var $domAnchor   = $("#'. $randId .'");
            var $select      = $domAnchor.prev("select");
            var $heightInput = $domAnchor.closest("div").prev();
            
            $select.change(function(){
              if(jQuery(this).val() == "warp")
              {
                $heightInput.show();
              }
              else
              {
                $heightInput.hide();
              }
            }).change();
          });
        })(jQuery);
      
      //--></script>';
	}

	function execute()
	{
		$gdimage =& $this->image->getImage();
		$w = $this->image->getWidth();
		$h = $this->image->getHeight();

		if(!isset($this->params['style']) || !in_array($this->params['style'],$this->options))
		{
			$this->params['style'] = 'longest';
		}

    // relatives resizen
    if(substr(trim($this->params['width']), -1) === '%')
    {
      $this->params['width'] = round($w * (rtrim($this->params['width'], '%') / 100));     
    }
    if(substr(trim($this->params['height']), -1) === '%')
    {
      $this->params['height'] = round($h * (rtrim($this->params['height'], '%') / 100));
    }
    
		if($this->params['style'] == 'longest')
		{
		  $this->resizeLongest($w, $h);
		}
		else if($this->params['style'] == 'shortest') 
		{
		  $this->resizeShortest($w, $h);
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
	
	function resizeLongest($w, $h)
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
	
	function resizeShortest($w, $h)
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
        'type' => 'int',
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
        'suffix' => $this->script
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