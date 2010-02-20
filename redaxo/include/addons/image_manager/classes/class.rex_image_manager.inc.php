<?php

class rex_image_manager
{
  var $image_cacher;

	function rex_image_manager(/*rex_image_cacher*/ $image_cacher)
	{
	  if(!rex_image_cacher::isValid($image_cacher))
	  {
	    trigger_error('Given cache is not a valid rex_image_cacher', E_USER_ERROR);
	  }
	  $this->image_cacher = $image_cacher;
	}

	function applyEffects(/*rex_image*/ $image, $type)
	{
		global $REX;
		
    if(!rex_image::isValid($image))
    {
      trigger_error('Given image is not a valid rex_image', E_USER_ERROR);
    }
    
    $set = $this->effectsFromType($type);
		
    if(!$this->image_cacher->isCached($image, $set))
    {
  		$image->prepare();
  
  		// execute effects on image
  		foreach($set as $effect_params)
  		{
  			$effect_class = 'rex_effect_'.$effect_params['effect'];
  			require_once ($REX['INCLUDE_PATH'].'/addons/image_manager/classes/effects/class.'.$effect_class.'.inc.php');
  			$effect = new $effect_class;
  			// var_dump($cmp['params']);
  			$effect->setImage($image);
  			$effect->setParams($effect_params['params']);
  			$effect->execute();
  		}
    }
    
    return $image;
	}
	
  /*public*/ function effectsFromType($type)
  {
    return array(
//    array('effect' => 'resize', 'params' => array( 'height' => 200, 'width' => 200, 'style' => 'fit') ),
//        array('effect' => 'resize', 'params' => array( 'width' => 200, 'height' => 100) ),
//        array('effect' => 'resize', 'params' => array( 'size' => 200, 'style' => 'auto') ),
//        array('effect' => 'filter_blur', 'params' => array( 'amount' => '80', 'radius' => 8, 'threshold' => 3) ),
//        array('effect' => 'filter_sharpen', 'params' => array( 'amount' => '80', 'radius' => 8, 'threshold' => 3) ),
        array('effect' => 'brand', 'params' => array( 'brandimage' => 'logo.gif', ) ),
//    array('effect' => 'filter_greyscale', 'params' => array() ),
    //    array('effect' => 'filter_sepia', 'params' => array() ),
    );    
  }
  /*public*/ function sendImage(/*rex_image*/ $image, $type)
  {
    $set = $this->effectsFromType($type);
    $this->image_cacher->sendImage($image, $set);
  }
	
	/**
	 * static function to create a managed_image from a image_type
	 * 
	 * @param $type
	 * @param $file
	 */
	function createFromType($type,$filename)
	{
		global $REX;

		// Loesche alle Ausgaben zuvor
		while(ob_get_level())
		{
		   ob_end_clean();
		}

		// ----- Cachenamen erstellen und prueŸfen ob vorhanden
		$filename = str_replace('/', '', $filename);
		$filepath = $REX['HTDOCS_PATH'].'files/'.$filename;

		// ----- check params
		if (!file_exists($filepath))
		{
			print 'Error: Imagefile does not exist - '. $filename;
			exit;
		}

		// ----- check filesize
		$max_file_size = $REX['ADDON']['image_manager']['max_resizekb'] * 1024;
		if (filesize($filepath)>$max_file_size)
		{
			print 'Error: Imagefile is to big. Only files < '.$REX['ADDON']['image_manager']['max_resizekb'].'kb are allowed. - '. $filename;
			exit;
		}

		$i = new rex_managed_image($filepath, $type);
		$i->prepare();
		exit ();
	}
}
