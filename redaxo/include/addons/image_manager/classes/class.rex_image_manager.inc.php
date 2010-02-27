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
    
		
    if(!$this->image_cacher->isCached($image, $type))
    {
      $set = $this->effectsFromType($type);
  		$image->prepare();
  
  		// execute effects on image
  		foreach($set as $effect_params)
  		{
  			$effect_class = 'rex_effect_'.$effect_params['effect'];
  			require_once dirname(__FILE__).'/effects/class.'.$effect_class.'.inc.php';
  			
  			$effect = new $effect_class;
  			$effect->setImage($image);
  			$effect->setParams($effect_params['params']);
  			$effect->execute();
  		}
    }
    
    return $image;
	}
	
  /*public*/ function effectsFromType($type)
  {
    global $REX;
    
    $qry = '
      SELECT e.* 
      FROM '. $REX['TABLE_PREFIX'].'679_types t, '. $REX['TABLE_PREFIX'].'679_type_effects e
      WHERE e.type_id = t.id AND t.name="'. $type .'"';
    
    $sql = rex_sql::factory();
//    $sql->debugsql = true;
    $sql->setQuery($qry);
    
    $effects = array();
    while($sql->hasNext())
    {
      $effname = $sql->getValue('effect');
      $params = unserialize($sql->getValue('parameters'));
      $effparams = array();
      
      // extract parameter out of array
      if(isset($params['rex_effect_'. $effname]))
      {
        foreach($params['rex_effect_'. $effname] as $name => $value)
        {
          $effparams[str_replace('rex_effect_'. $effname .'_', '', $name)] = $value;
          unset($effparams[$name]);
        }
      }
      
      $effect = array(
        'effect' => $effname,
        'params' => $effparams,
      );
      
      $effects[] = $effect;
      $sql->next();
    }
    return $effects;
    
//    return array(
//    array('effect' => 'resize', 'params' => array( 'height' => 200, 'width' => 200, 'style' => 'fit') ),
////        array('effect' => 'resize', 'params' => array( 'width' => 200, 'height' => 100) ),
////        array('effect' => 'resize', 'params' => array( 'size' => 200, 'style' => 'auto') ),
////        array('effect' => 'filter_blur', 'params' => array( 'amount' => '80', 'radius' => 8, 'threshold' => 3) ),
////        array('effect' => 'filter_sharpen', 'params' => array( 'amount' => '80', 'radius' => 8, 'threshold' => 3) ),
//        array('effect' => 'brand', 'params' => array( 'brandimage' => 'logo.gif', ) ),
//    array('effect' => 'filter_greyscale', 'params' => array() ),
//    //    array('effect' => 'filter_sepia', 'params' => array() ),
//    );    
  }
  /*public*/ function sendImage(/*rex_image*/ $image, $type)
  {
    $this->image_cacher->sendImage($image, $type);
  }
}
