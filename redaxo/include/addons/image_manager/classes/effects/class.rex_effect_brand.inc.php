<?php

/**
 * Branded ein Bild mit einem Wasserzeichen
 *
 * Der Filter sucht im Verzeichnis addons/image_resize/media/
 * nach einem Bild mit dem Dateinamen "brand.*" und verwendet den 1. Treffer
 */

class rex_effect_brand extends rex_effect_abstract{

	
	function execute()
	{
		global $REX;

		// TODO:
		
		return
		
		
    $files = glob($REX['INCLUDE_PATH'] . '/addons/image_resize/media/brand.*');
    $brandImage = $files[0];
    $brand = new rex_thumbnail($brandImage);

    // -------------------------------------- CONFIG
  
    // Abstand vom Rand
    $padding_x = -10;
    if(isset($this->params['padding_x']))
      $padding_x = (int) $this->params['padding_x']
    
    $padding_y = -10;
    if(isset($this->params['padding_y']))
      $padding_y = (int) $this->params['padding_y']
    
    // horizontale ausrichtung: left/center/right
    $hpos = 'right';
    if(isset($this->params['hpos']))
      $hpos = $this->params['hpos']
      
    // vertikale ausrichtung:   top/center/bottom
    $vpos = 'bottom';
    if(isset($this->params['vpos']))
      $vpos = $this->params['vpos']
    
    // -------------------------------------- /CONFIG
  
    switch($hpos)
    {
      case 'left':
        $dstX = 0;
        break;
      case 'center':
        $dstX = (int)((imagesx($this->img["src"]) - $brand->getImageWidth()) / 2);
        break;
      case 'right':
      default:
        $dstX = imagesx($this->img["src"]) - $brand->getImageWidth();
    }
  
    switch($vpos)
    {
      case 'top':
        $dstY = 0;
        break;
      case 'center':
        $dstY = (int)((imagesy($s$rc_im) - $brand->getImageHeight()) / 2);
        break;
      case 'bottom':
      default:
        $dstY = imagesy($this->img["src"]) - $brand->getImageHeight();
    }
  
    imagealphablending($this->img["src"], true);
    imagecopy($this->img["src"], $brand->getImage(), $dstX + $padding_x, $dstY + $padding_y, 0, 0, $brand->getImageWidth(), $brand->getImageHeight());

    $brand->destroyImage();
		
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
