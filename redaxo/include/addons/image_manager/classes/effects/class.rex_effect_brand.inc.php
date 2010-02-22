<?php

/**
 * Branded ein Bild mit einem Wasserzeichen
 *
 * Der Filter sucht im Verzeichnis addons/image_manager/media/
 * nach einem Bild mit dem Dateinamen "brand.*" und verwendet den 1. Treffer
 */

class rex_effect_brand extends rex_effect_abstract{

	
	function execute()
	{
		global $REX;
		
    // -------------------------------------- CONFIG
    $brandimage = $this->params['brandimage'];
    if(!file_exists($brandimage))
      $brandimage = dirname(__FILE__). '/../../media/brand.gif';
      
    // Abstand vom Rand
    $padding_x = -10;
    if(isset($this->params['padding_x']))
      $padding_x = (int) $this->params['padding_x'];
    
    $padding_y = -10;
    if(isset($this->params['padding_y']))
      $padding_y = (int) $this->params['padding_y'];
    
    // horizontale ausrichtung: left/center/right
    $hpos = 'right';
    if(isset($this->params['hpos']))
      $hpos = $this->params['hpos'];
      
    // vertikale ausrichtung:   top/center/bottom
    $vpos = 'bottom';
    if(isset($this->params['vpos']))
      $vpos = $this->params['vpos'];
    
    // -------------------------------------- /CONFIG
  
    $brand = new rex_image($brandimage);
    $brand->prepare();
    $gdbrand =& $brand->getImage();
    $gdimage =& $this->image->getImage();
    
    $image_width = $this->image->getWidth();
    $image_height = $this->image->getHeight();
    $brand_width = $brand->getWidth();
    $brand_height = $brand->getHeight();
    
    switch($hpos)
    {
      case 'left':
        $dstX = 0;
        break;
      case 'center':
        $dstX = (int)(($image_width - $brand_width) / 2);
        break;
      case 'right':
      default:
        $dstX = $image_width - $brand_width;
    }
  
    switch($vpos)
    {
      case 'top':
        $dstY = 0;
        break;
      case 'center':
        $dstY = (int)(($image_height - $brand_height) / 2);
        break;
      case 'bottom':
      default:
        $dstY = $image_height - $brand_height;
    }
    
    imagealphablending($gdimage, true);
    imagecopy($gdimage, $gdbrand, $dstX + $padding_x, $dstY + $padding_y, 0, 0, $brand_width, $brand_height);

    $brand->destroy();
	}
	
	function getParams()
	{
		global $REX,$I18N;

		return array(
			array(
				'title' => $I18N->msg('image'),
				'label' => 'brandimage',
				'type'	=> 'media',
				'default' => ''
			),
			array(
				'title' => $I18N->msg('hpos'),
				'label' => 'hpos',
				'type'	=> 'select',
				'type_params'	=> array('right','center','left'),
				'default' => 'right'
			),
			array(
				'title' => $I18N->msg('vpos'),
				'label' => 'vpos',
				'type'	=> 'select',
				'type_params'	=> array('top','center','bottom'),
				'default' => 'bottom'
			),
			array(
				'title' => $I18N->msg('padding_x'),
				'label' => 'padding_x',
				'type'	=> 'int',
				'default' => '-10'
			),
			array(
				'title' => $I18N->msg('padding_y'),
				'label' => 'padding_y',
				'type'	=> 'int',
				'default' => '-10'
			),
			);
		
	}

}
