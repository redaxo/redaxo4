<?php
class Cache {

	var $cache;
	var $article_id;
	var $cacheFile;
	var $makeCacheFile = false;

	function Cache($article_id=''){

		global $REX;

	    if ( file_exists($REX[HTDOCS_PATH].'redaxo/include/generated/articles/'.$article_id.'.article') ) {
	    	$this->article_id = $article_id;
	    } else {
			$this->article_id = $REX[STARTARTIKEL_ID];
		}

		include($REX[HTDOCS_PATH].'redaxo/include/generated/cache/cache.php');
		$this->cache = $cache;

		// UPDATE CACHE with REX_CACHE_UPDATE[]
		if($_POST[REX_CACHE_UPDATE] or $_GET[REX_CACHE_UPDATE]){

			if (is_array($_POST[REX_CACHE_UPDATE])){
				foreach($_POST[REX_CACHE_UPDATE] as $key => $var){
					$this->removeCacheFiles($key);
				}
			}

			if (is_array($_GET[REX_CACHE_UPDATE])) {
				foreach($_GET[REX_CACHE_UPDATE] as $key=>$var){
					$this->removeCacheFiles($key);
				}
			}
		}

	    if(is_array($_POST)){
	        foreach($_POST as $key=>$var){
	            $POSTSTRING.=$key.$var;
	        }
	    }

		$this->cacheFile = 'redaxo/include/generated/cache/'.$article_id.'.'.md5($_SERVER[REQUEST_URI].$_SERVER[PHP_SELF].$POSTSTRING).'.cache';

	}

	function isCacheConf(){
		if(in_array($this->article_id,$this->cache)){
			return true;
		} else {
			return false;
		}
	}

	function isCacheFile(){
		if(file_exists($this->cacheFile)){
			return true;
		} else {
			return false;
		}
	}

	function startCacheFile(){
		ob_start();
		$this->makeCacheFile = true;
	}

	function writeCacheFile(){
	    $ob_content = ob_get_contents();
	    if($ob_content!=''){
	        $handle = fopen($this->cacheFile, 'w');
	        fwrite($handle, $ob_content);
	        fclose($handle);
	        return true;
	    } else {
	    	return false;
	    }
	}

	function printCacheFile(){
		readfile($this->cacheFile);
	}

	function insertCacheConf($article_id){
		$key = array_search($article_id,$this->cache);
		if($key){
			return false;
		} else {
			$tmp_cache = $this->cache;
			$tmp_cache[] = $article_id;
			$this->writeCacheConf($tmp_cache,$this->recache);
			return true;
		}
	}

	function removeCacheConf($article_id){
		$key = array_search($article_id,$this->cache);
		$tmp_cache = $this->cache;
		unset($tmp_cache[$key]);
		$this->writeCacheConf($tmp_cache,$this->cache);
	}

	function writeCacheConf($cache,$recache){

			global $REX;

            $string = '<?php'."\n";
            $string.= '$cache = explode(\'#\',\''.implode('#',$cache).'\');'."\n";
    		$string.= '?>';

            $handle = fopen($REX[HTDOCS_PATH].'redaxo/include/generated/cache/cache.php','w');
			fwrite($handle, $string);
			fclose($handle);
	}

	function removeCacheFiles($article_id){

			global $REX;

            if ($handle = opendir($REX[HTDOCS_PATH].'redaxo/include/generated/cache/')) {
                while (false !== ($file = readdir($handle))) {
                    if (eregi('^'.$article_id.'\..*\.cache$',$file)) {
                        unlink($REX[HTDOCS_PATH].'redaxo/include/generated/cache/'.$file);
                    }
                }
                closedir($handle);
            }


	}

	function removeAllCacheFiles(){

			global $REX;

            if ($handle = opendir($REX[HTDOCS_PATH].'redaxo/include/generated/cache/')) {
                while (false !== ($file = readdir($handle))) {
                    if (strstr ($file,'.cache')){
                        unlink($REX[HTDOCS_PATH].'redaxo/include/generated/cache/'.$file);
                    }
                }
                closedir($handle);
            }
	}


}
?>
