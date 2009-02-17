<?php

/**
 * Cache layer zum zwischenspeichern von rechenintensiven Aktionen.
 * 
 * Inspiriert von den Cache-Klassen des Symfony-Frameworks.
 * 
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */

define('REX_CACHE_ALL',         1);
define('REX_CACHE_EXPIRED',     2);
define('REX_CACHE_SEPARATOR', ':');

/**
 * sfCache is an abstract class for all cache classes in symfony.
 * Overhauled and simplified for REDAXO neeeds and PHP4 compatibility.
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
/*abstract*/ class rex_cache
{
  var $lifetime;
  
  /*protected*/ function rex_cache($lifetime)
  {
    $this->lifetime = $lifetime;
  }
  
  /**
   * Gets the cache content for a given key.
   *
   * @param  string $key      The cache key
   * @param  mixed  $default  The default value is the key does not exist or not valid anymore
   *
   * @return mixed  The data of the cache
   */
  /*abstract*/ function get($key, $default = null)
  {
    // override
  }
    
  /**
   * Returns true if there is a cache for the given key.
   *
   * @param  string  $key  The cache key
   *
   * @return Boolean true if the cache exists, false otherwise
   */
  /*abstract*/ function has($key)
  {
    // override
  }
  
  /**
   * Saves some data in the cache.
   *
   * @param string $key       The cache key
   * @param mixed  $data      The data to put in cache
   * @param int    $lifetime  The lifetime
   *
   * @return Boolean true if no problem
   */
   /*abstract*/ function set($key, $data, $lifetime = null)
  {
    // override
  }
    
  /**
   * Removes a content from the cache.
   *
   * @param string $key The cache key
   *
   * @return Boolean true if no problem
   */
  /*abstract*/ function remove($key)
  {
    // override
  }
  
  /**
   * Removes content from the cache that matches the given pattern.
   *
   * @param  string  $pattern The cache key pattern
   *
   * @return Boolean true if no problem
   *
   * @see patternToRegexp
   */
  /*abstract*/ public function removePattern($pattern)
  {
    // override
  }  
    
  /**
   * Cleans the cache.
   *
   * @param  string  $mode  The clean mode
   *                        sfCache::ALL: remove all keys (default)
   *                        sfCache::OLD: remove all expired keys
   *
   * @return Boolean true if no problem
   */
  /*abstract*/ function clean($mode = REX_CACHE_ALL)
  {
    // override
  }

  /**
   * Returns the last modification date of the given key.
   *
   * @param string $key The cache key
   *
   * @return int The last modified time
   */
  /*abstract*/ public function getLastModified($key)
  {
    // override
  }

  /**
   * Computes lifetime.
   *
   * @param  integer $lifetime Lifetime in seconds
   *
   * @return integer Lifetime in seconds
   */
  /*public*/ function getLifetime($lifetime)
  {
    return is_null($lifetime) ? $this->lifetime : $lifetime;
  }
  
  /**
   * Converts a pattern to a regular expression.
   *
   * A pattern can use some special characters:
   *
   *  - * Matches a namespace (foo:*:bar)
   *  - ** Matches one or more namespaces (foo:**:bar)
   *
   * @param  string $pattern  A pattern
   *
   * @return string A regular expression
   */
  /*protected*/ function patternToRegexp($pattern)
  {
    $regexp = str_replace(
      array('\\*\\*', '\\*'),
      array('.+?',    '[^'.preg_quote(REX_CACHE_SEPARATOR, '#').']+'),
      preg_quote($pattern, '#')
    );

    return '#^'.$regexp.'$#';
  }  
}

/**
 * This class can be used to cache the result and output of any PHP callable (function and method calls).
 * Overhauled and simplified for REDAXO neeeds and PHP4 compatibility. 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class rex_function_cache
{
  /*private*/ var $cache;
  
  /*public*/ function rex_function_cache(/*rex_cache*/ $cache)
  {
    if(!is_a($cache, 'rex_cache'))
    {
      trigger_error('rex_cache:Expecting argument $cache from type rex_cache!', E_USER_ERROR);
    }
    $this->cache = $cache;
  }
  
  /*public*/ function callWithKey($key, $callable, $arguments, $parseParamsAsArray = false)
  {
    $serialized = $this->cache->get($key);
    if($serialized !== null)
    {
      $data = unserialize($serialized);
    }
    else
    {
      $data = array();
      
      if(!is_callable($callable))
      {
        trigger_error('rex_cache:Argument is not a callable!', E_USER_ERROR);
      }
      
      ob_start();
      ob_implicit_flush(false);
      $data['result'] = rex_call_func($callable, $arguments, $parseParamsAsArray);
      $data['output'] = ob_get_clean();
      
      $this->cache->set($key, serialize($data));
    }
    
    echo $data['output'];
    return $data['result'];
  }
  /*public*/ function call($callable, $arguments, $parseParamsAsArray = false)
  {
    $key = $this->cachekey($callable, $arguments);
    return $this->callWithKey($key, $callable, $arguments, $parseParamsAsArray);
  }
  
  /*public*/ function cachekey($callable, $arguments)
  {
    return md5(serialize($callable).serialize($arguments));
  }
}

define ('REX_FILECACHE_DATA',             1);
define ('REX_FILECACHE_TIMEOUT',          2);
define ('REX_FILECACHE_LAST_MODIFIED',    4);
define ('REX_FILECACHE_EXTENSION', '.cache');

/**
 * Cache class that stores content in files.
 * Overhauled and simplified for REDAXO neeeds and PHP4 compatibility.
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class rex_file_cache extends rex_cache
{
  function rex_file_cache($lifetime = null)
  {
    global $REX;
    
    if(is_null($lifetime)) $lifetime = 86400;
    
    $this->setCacheDir($REX['INCLUDE_PATH']. DIRECTORY_SEPARATOR .'generated');
    parent::rex_cache($lifetime);
  }
  
  /**
   * @see rex_cache
   */
  /*public*/ function get($key, $default = null)
  {
    $file_path = $this->getFilePath($key);
    if (!file_exists($file_path))
    {
      return $default;
    }

    $data = $this->read($file_path, REX_FILECACHE_DATA);

    if ($data[REX_FILECACHE_DATA] === null) {
      return $default;
    }

    return $data[REX_FILECACHE_DATA];
  }

  /**
   * @see rex_cache
   */
  /*public*/ function has($key)
  {
    $path = $this->getFilePath($key);
    return file_exists($path) && $this->isValid($path);
  }

  /**
   * @see rex_cache
   */
  /*public*/ function set($key, $data, $lifetime = null)
  {
    return $this->write($this->getFilePath($key), $data, time() + $this->getLifetime($lifetime));
  }

  /**
   * @see rex_cache
   */
  /*public*/ function remove($key)
  {
    return @unlink($this->getFilePath($key));
  }

  /**
   * @see sfCache
   */
  /*public*/ function removePattern($pattern)
  {
    if (false !== strpos($pattern, '**'))
    {
      $pattern = str_replace(REX_CACHE_SEPARATOR, DIRECTORY_SEPARATOR, $pattern).REX_FILECACHE_EXTENSION;

      $regexp = self::patternToRegexp($pattern);
      $paths = array();
      $hdl = opendir($this->cache_dir);
      if($hdl)
      {
        while(($file = readdir($hdl)) !== false)
        {
          var_dump($file);
          if($file == '.' || $file == '..') continue;
          
          if (preg_match($regexp, str_replace($this->cache_dir.DIRECTORY_SEPARATOR, '', $path)))
          {
            $paths[] = $path;
          }
        }
        closedir($hdl);
      }
    }
    else
    {
      $paths = glob($this->cache_dir.DIRECTORY_SEPARATOR.str_replace(REX_CACHE_SEPARATOR, DIRECTORY_SEPARATOR, $pattern).REX_FILECACHE_EXTENSION);
    }

    foreach ($paths as $path)
    {
      if (is_dir($path))
      {
        rex_deleteDir($path);
      }
      else
      {
        @unlink($path);
      }
    }
  }

  /**
   * @see rex_cache
   */
  /*public*/ function clean($mode = REX_CACHE_ALL)
  {
    if (!is_dir($this->cache_dir))
    {
      return true;
    }

    $result = true;
    $hdl = opendir($this->cache_dir);
    if($hdl)
    {
      while(($file = readdir($hdl)) !== false)
      {
        if($file == '.' || $file == '..') continue;
        
        if(REX_CACHE_ALL == $mode || !$this->isValid($file))
        {
          $result = @unlink($file) && $result;
        }
      }
      closedir($hdl);
    }

    return $result;
  }

  /**
   * @see rex_cache
   */
  /*public*/ function getTimeout($key)
  {
    $path = $this->getFilePath($key);

    if (!file_exists($path))
    {
      return 0;
    }

    $data = $this->read($path, REX_FILECACHE_TIMEOUT);

    return $data[REX_FILECACHE_TIMEOUT] < time() ? 0 : $data[REX_FILECACHE_TIMEOUT];
  }

  /**
   * @see rex_cache
   */
  /*public*/ function getLastModified($key)
  {
    $path = $this->getFilePath($key);

    if (!file_exists($path))
    {
      return 0;
    }
    
    $data = $this->read($path, REX_FILECACHE_TIMEOUT | REX_FILECACHE_LAST_MODIFIED);

    if ($data[REX_FILECACHE_TIMEOUT] < time())
    {
      return 0;
    }
    return $data[REX_FILECACHE_LAST_MODIFIED];
  }

  /*protected*/ function isValid($path)
  {
    $data = $this->read($path, REX_FILECACHE_TIMEOUT);
    return time() < $data[REX_FILECACHE_TIMEOUT];
  }

 /**
  * Converts a cache key to a full path.
  *
  * @param string  $key  The cache key
  *
  * @return string The full path to the cache file
  */
  /*protected*/ function getFilePath($key)
  {
    return $this->cache_dir.DIRECTORY_SEPARATOR.str_replace(REX_CACHE_SEPARATOR, DIRECTORY_SEPARATOR, $key).REX_FILECACHE_EXTENSION;
  }

 /**
  * Reads the cache file and returns the content.
  *
  * @param string $path The file path
  * @param mixed  $type The type of data you want to be returned
  *                     sfFileCache::READ_DATA: The cache content
  *                     sfFileCache::READ_TIMEOUT: The timeout
  *                     sfFileCache::READ_LAST_MODIFIED: The last modification timestamp
  *
  * @return array the (meta)data of the cache file. E.g. $data[sfFileCache::READ_DATA]
  */
  /*protected*/ function read($path, $type = REX_FILECACHE_DATA)
  {
    if (!$fp = @fopen($path, 'rb'))
    {
      trigger_error(sprintf('Unable to read cache file "%s".', $path), E_USER_ERROR);
    }

    @flock($fp, LOCK_SH);
    $data[REX_FILECACHE_TIMEOUT] = intval(@stream_get_contents($fp, 12, 0));
    if ($type != REX_FILECACHE_TIMEOUT && time() < $data[REX_FILECACHE_TIMEOUT]) {
      if ($type & REX_FILECACHE_LAST_MODIFIED)
      {
        $data[REX_FILECACHE_LAST_MODIFIED] = intval(@stream_get_contents($fp, 12, 12));
      }
      if ($type & REX_FILECACHE_DATA)
      {
        fseek($fp, 0, SEEK_END);
        $length = ftell($fp) - 24;
        fseek($fp, 24);
        $data[REX_FILECACHE_DATA] = @fread($fp, $length);
      }
    } else {
      $data[REX_FILECACHE_LAST_MODIFIED] = null;
      $data[REX_FILECACHE_DATA] = null;
    }
    @flock($fp, LOCK_UN);
    @fclose($fp);

    return $data;
  }

 /**
  * Writes the given data in the cache file.
  *
  * @param  string  $path     The file path
  * @param  string  $data     The data to put in cache
  * @param  integer $timeout  The timeout timestamp
  *
  * @return boolean true if ok, otherwise false
  */
  /*protected*/ function write($path, $data, $timeout)
  {
    $current_umask = umask();
    umask(0000);

    if (!is_dir(dirname($path)))
    {
      // create directory structure if needed
      mkdir(dirname($path), 0777, true);
    }

    $tmpFile = $path . '.' . getmypid();
    if (!$fp = @fopen($tmpFile, 'wb'))
    {
       trigger_error(sprintf('Unable to write cache file "%s".', $tmpFile), E_USER_ERROR);
    }

    @fwrite($fp, str_pad($timeout, 12, 0, STR_PAD_LEFT));
    @fwrite($fp, str_pad(time(), 12, 0, STR_PAD_LEFT));
    @fwrite($fp, $data);
    @fclose($fp);

    chmod($tmpFile, 0666);
    @unlink($path);
    rename($tmpFile, $path);
    umask($current_umask);

    return true;
  }

  /**
   * Sets the cache root directory.
   *
   * @param string $cache_dir The directory where to put the cache files
   */
  /*protected*/ function setCacheDir($cache_dir)
  {
    // remove last DIRECTORY_SEPARATOR
    if (DIRECTORY_SEPARATOR == substr($cache_dir, -1))
    {
      $cache_dir = substr($cache_dir, 0, -1);
    }

    // create cache dir if needed
    if (!is_dir($cache_dir))
    {
      $current_umask = umask(0000);
      @mkdir($cache_dir, 0777, true);
      umask($current_umask);
    }
    
    $this->cache_dir = $cache_dir;
  }
}

class rex_cache_config
{
  function articleCache($article_id, $clang, $additionalCacheId = '')
  {
    $additionalCacheId = $additionalCacheId != '' ? ':'. $additionalCacheId : $additionalCacheId;
    return 'article:'. $article_id .':'. $clang . $additionalCacheId;
  }
  
  function articleMetaCache($article_id, $clang)
  {
    return self::articleCache($article_id, $clang, 'meta');
  }
  
  function articleContentCache($article_id, $clang)
  {
    return self::articleCache($article_id, $clang, 'meta');
  }
}