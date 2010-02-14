<?php

class rex_img_type {

	var $img;
	var $gifsupport = FALSE;

	function rex_img_type($type,$file, $filepath, $cachepath)
	{
		global $REX;

		// ----- imagepfad speichern
		$this->img = array();
		$this->img["type"] = $type;
		$this->img["file"] = $file;
		$this->img["filepath"] = $filepath;
		$this->img["cachepath"] = $cachepath;
		$this->img['quality'] = 80;

		// ----- gif support ?
		$this->gifsupport = function_exists('imageGIF');

		// ----- detect image format
		$this->img['format'] = strtoupper(OOMedia::_getExtension($this->img["filepath"]));
		$this->img['src'] = false;
		if ($this->img['format'] == 'JPG' || $this->img['format'] == 'JPEG')
		{
			// --- JPEG
			$this->img['format'] = 'JPEG';
			$this->img['src'] = @ImageCreateFromJPEG($this->img["filepath"]);
		}elseif ($this->img['format'] == 'PNG')
		{
			// --- PNG
			$this->img['src'] = @ImageCreateFromPNG($this->img["filepath"]);
		}elseif ($this->img['format'] == 'GIF')
		{
			// --- GIF
			if ($this->gifsupport)
			$this->img['src'] = @ImageCreateFromGIF($this->img["filepath"]);
		}elseif ($this->img['format'] == 'WBMP')
		{
			// --- WBMP
			$this->img['src'] = @ImageCreateFromWBMP($this->img["filepath"]);
		}

		// ggf error image senden
		if (!$this->img['src'])
		{
			$this->sendError();
			exit();
		}else
		{
			$this->img['width'] = imagesx($this->img['src']);
			$this->img['height'] = imagesy($this->img['src']);
		}

	}

	function prepare()
	{
		global $REX;

		// Default Setting fuer tests
		// TODO FIXE typen fuers backend ..

		if ($this->img['format'] == 'GIF' && !$this->gifsupport)
		{
			// --- kein caching -> gif ausgeben
			$this->send();
		}

		$set = array(
		array('component' => 'resize', 'params' => array( 'height' => 200, 'width' => 200, 'style' => 'fit') ),
		//    array('component' => 'resize', 'params' => array( 'width' => 200, 'height' => 100) ),
		//    array('component' => 'resize', 'params' => array( 'size' => 200, 'style' => 'auto') ),
		//    array('component' => 'filter_blur', 'params' => array( 'amount' => '80', 'radius' => 8, 'threshold' => 3) ),
		//    array('component' => 'filter_sharpen', 'params' => array( 'amount' => '80', 'radius' => 8, 'threshold' => 3) ),
		//    array('component' => 'branding', 'params' => array( 'brandimage' => 'logo.gif', ) ),
		array('component' => 'filter_greyscale', 'params' => array() ),
		//    array('component' => 'filter_sepia', 'params' => array() ),

		);

		foreach($set as $cmp)
		{
			$cl = 'rex_img_cmp_'.$cmp['component'];
			require_once ($REX['INCLUDE_PATH'].'/addons/image_manager/components/class.'.$cl.'.inc.php');
			$c = new $cl;
			// var_dump($cmp['params']);
			$c->setParams($this->img,$cmp['params']);
			$c->execute();
		}

		if ($this->img['format'] == 'JPG' || $this->img['format'] == 'JPEG')
		{
			imageJPEG($this->img['src'], $this->img['cachepath'], $this->img['quality']);
		}
		elseif ($this->img['format'] == 'PNG')
		{
			imagePNG($this->img['src'], $this->img['cachepath']);
		}
		elseif ($this->img['format'] == 'GIF')
		{
			imageGIF($this->img['src'], $this->img['cachepath']);
		}
		elseif ($this->img['format'] == 'WBMP')
		{
			imageWBMP($this->img['src'], $this->img['cachepath']);
		}

		if($this->img['cachepath'])
		@chmod($img['cachepath'], $REX['FILEPERM']);

		$this->send($this->img['cachepath']);

	}

	function deleteCache($filename = '')
	{
		global $REX;

		$folders = array();
		$folders[] = $REX['INCLUDE_PATH'] . '/generated/files/';
		$folders[] = $REX['HTDOCS_PATH'] . 'files/';

		$c = 0;
		foreach($folders as $folder)
		{
			$glob = glob($folder .'rex_img__*');
			if($glob)
			{
				foreach ($glob as $var)
				{
					if ($filename == '' || $filename != '' && $filename == substr($var,strlen($filename) * -1))
					{
						unlink($var);
						$c++;
					}
				}
			}
		}

		return $c;
	}





	function createFromType($type,$file)
	{
		global $REX;

		// Loesche alle Ausgaben zuvor
		while(ob_get_level())
		ob_end_clean();

		// ----- Cachenamen erstellen und prŸfen ob vorhanden
		$file = str_replace("/","",$file);

		$cachefile = 'rex_img__'.md5("$type,$file");

		$cachepath = $REX['INCLUDE_PATH'].'/generated/files/'.$cachefile;
		$filepath = $REX['HTDOCS_PATH'].'files/'.$file;

		// ----- check for cache file
		if (file_exists($cachepath) && 1==2)
		{
			// time of cache
			$cachetime = filectime($cachepath);

			if (file_exists($filepath))
			{
				$filetime = filectime($filepath);
			}else
			{
				print 'Error: Imagefile does not exist - '. $file;
				exit;
			}
			if ($cachetime > $filetime)
			{
				// TODO:
				$i = new rex_img_type($type,$file, $filepath, $cachepath);
				$i->send($cachepath, $cachetime);
				exit;
			}

		}

		// ----- check params
		if (!file_exists($filepath))
		{
			print 'Error: Imagefile does not exist - '. $file;
			exit;
		}

		// ----- check filesize
		$max_file_size = $REX['ADDON']['image_manager']['max_resizekb'] * 1024;
		if (filesize($filepath)>$max_file_size)
		{
			print 'Error: Imagefile is to big. Only files < '.$REX['ADDON']['image_manager']['max_resizekb'].'kb are allowed. - '. $file;
			exit;
		}

		$i = new rex_img_type($type,$file, $filepath, $cachepath);
		$i->prepare();
		exit ();
	}










	// ********************************* SEND IMAGE
	function getImage()
	{
		// Loesche alle Ausgaben zuvor
		while(ob_get_level())
		ob_end_clean();

		return $this->img['src'];
	}

	function send($file = null, $lastModified = null)
	{
		if (!$file)
		$file = $this->img["filepath"];
		if (!$lastModified)
		$lastModified = time();

		$lastModified =  gmdate('D, d M Y H:i:s', $lastModified).' GMT';

		// ----- EXTENSION POINT
		$sendfile = TRUE;
		$sendfile = rex_register_extension_point('IMG_TYPE_SEND', $sendfile,
		array (
      	'img' => $this->img,
        'file' => $this->img["file"],
        'lastModified' => $lastModified,
        'filepath' => $this->img["filepath"]
		)
		);

		if(!$sendfile)
		return FALSE;
			
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastModified)
		{
			header('HTTP/1.1 304 Not Modified');
			exit();
		}

		header('Content-Type: image/' . $this->img['format']);
		header('Last-Modified: ' . $lastModified);
		// caching clientseitig/proxieseitig erlauben
		header('Cache-Control: public');

		readfile($file);
	}



	// ********************************* SEND ERROR
	function sendError($file = null)
	{
		global $REX;

		if(!$file)
		$file = $REX['INCLUDE_PATH'].'/addons/image_manager/media/warning.jpg';

		// ----- EXTENSION POINT
		$sendfile = TRUE;
		$sendfile = rex_register_extension_point('REX_IMG_TYPE_ERROR_SEND', $sendfile,
		array (
      	'img' => $this->img,
        'file' => $file,
		)
		);

		if(!$sendfile)
		return FALSE;

		header('Content-Type: image/JPG');
		// error image nicht cachen
		header('Cache-Control: false');
		header('HTTP/1.0 404 Not Found');
		readfile($file);
		exit();
	}


}
