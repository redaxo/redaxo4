<?php

class rex_image_cacher
{
	var $cache_path;
	var $cache_file;

	function rex_image_cacher($cache_path)
	{
		global $REX;
		
		$this->image = $image;
		$this->cache_path = $cache_path;
		$this->cache_file = false;
	}
	
	/*public*/ function isCached(/*rex_image*/ $image, $cacheParams)
  {
    if(!rex_image::isValid($image))
    {
      trigger_error('Given image is not a valid rex_image', E_USER_ERROR);
    }
    
    $this->cache_file = $this->cache_path .'image_manager__'. md5(serialize($cacheParams)) .'_'. $image->getFileName();
    
    // ----- check for cache file
    if (file_exists($this->cache_file))
    {
      // time of cache
      $cachetime = filectime($this->cache_file);
      $imagepath = $image->getFilePath();

      // file exists?
      if (file_exists($imagepath))
      {
        $filetime = filectime($imagepath);
      }
      else
      {
        $image->sendError('Missing original file for cache-validation!');
        exit();
      }
      // cache is newer?
      if ($cachetime > $filetime)
      {
        return true;
      }
    }
    
    return false;
  }
	
  /*public*/ function sendImage(/*rex_image*/ $image, $cacheParams, $lastModified = null)
	{
    if(!rex_image::isValid($image))
    {
      trigger_error('Given image is not a valid rex_image', E_USER_ERROR);
    }
    
	  // caching gifs doesn't work
	  if($image->getFormat() == 'GIF' && !$image->hasGifSupport())
	  {
	    $image->send($lastModified, $this->cache_file);
	  }
	  
	  // save image to file
	  if(!$this->isCached($image, $cacheParams))
	  {
	    $image->prepare();
	    $image->save($this->cache_file);
	  }
	  
	  // send file
    $image->sendHeader();
    readfile($this->cache_file);
	}
	
  /*
   * Static Method: Returns True, if the given cacher is a valid rex_image_cacher
   */
  /*public static*/ function isValid($cacher)
  {
    return is_object($cacher) && is_a($cacher, 'rex_image_cacher');
  }
  
  /**
	 * deletes all cache files for the given filename.
	 * if not filename is provided all cache files are cleared.
	 * 
	 * Returns the number of cachefiles which have been removed. 
	 * 
	 * @param $filename
	 */
	function deleteCache($filename = '')
	{
		global $REX;

		$folders = array();
		$folders[] = $REX['INCLUDE_PATH'] . '/generated/files/';
		$folders[] = $REX['HTDOCS_PATH'] . 'files/';

		$counter = 0;
		foreach($folders as $folder)
		{
			$glob = glob($folder .'image_manager__*');
			if($glob)
			{
				foreach ($glob as $var)
				{
					if ($filename == '' || $filename != '' && $filename == substr($var,strlen($filename) * -1))
					{
						if(unlink($var))
						{
  						$counter++;
						}
					}
				}
			}
		}

		return $counter;
	}
}
