<?

function createImage($text,$ttfpath,$imagepath,$fontsize,$wordwrap,$height,$width,$posy=22,$bgcolor='white',$fontcolor='grey',$posx=0)
{
	global $REX;
	
	// $height = 30;
	// $width = 440;
	// $wordwrap = 30;
	// $ttfpath = $REX[INCLUDE_PATH]."/../../pics/headlines/helvetica.ttf";
	// $imagepath = $REX[INCLUDE_PATH]."/../../pics/headlines/REX_ARTICLE_ID_".$FORM[lang].".png";
		
	$text = wordwrap( $text, $wordwrap, "§" );
	$text = explode( "§", $text );
	
	if ($text[0]!="") $nheight = $height;
	if ($text[1]!="") $nheight += $height;
	if ($text[2]!="") $nheight += $height;
	
	if ($nheight==0) $nheight=1;
	
	$maxlen = 0;

	if ( strlen($text[0]) > $maxlen ) $maxlen = strlen($text[0]);
	if ( strlen($text[1]) > $maxlen ) $maxlen = strlen($text[1]);
	if ( strlen($text[2]) > $maxlen ) $maxlen = strlen($text[2]);
	
	$nwidth = $maxlen * $fontsize;
	
	// width ausrechnen
	
	if ($width == "exact")
	{
		$textbox = imagettfbbox($fontsize,0,$ttfpath, $text[0]);
		$width = $textbox[2]+1;
	}	
	
	$mp = false;
	
	if (file_exists($imagepath))
	{
		$cachetime = 3600; // 1 Stunde
		
		if (filemtime($imagepath)+$cachetime< time() )
		{
			$mp = true;
		}
	
	}else
	{
		$mp = true;
	}

	if ($mp)
	{
	
		$im = imagecreate ($width+$posx, $nheight);
		
		$black   = ImageColorAllocate ($im, 0, 0, 0);
		$white   = ImageColorAllocate ($im, 255, 255, 255);
		$grey    = ImageColorAllocate ($im, 111, 111, 111);
		// $orange  = ImageColorAllocate ($im, 249, 126, 25);
		$orange  = ImageColorAllocate ($im, 255, 173, 0);
		$blue    = ImageColorAllocate ($im, 14, 130, 175);
		$lred    = ImageColorAllocate ($im, 237, 175, 170);
		$red     = ImageColorAllocate ($im, 236, 0, 31);
		
		ImageFill($im, 1, 1, $$bgcolor);
		
		if ($text[0]!="") ImageTTFText ($im, $fontsize, 0, $posx, $posy, $$fontcolor, $ttfpath, $text[0] );
		if ($text[1]!="") ImageTTFText ($im, $fontsize, 0, $posx, ( $posy + $height ), $$fontcolor, $ttfpath ,$text[1]);
		if ($text[2]!="") ImageTTFText ($im, $fontsize, 0, $posx, ( $posy + $height + $height ), $$fontcolor, $ttfpath ,$text[2]);
		
		ImagePng ($im,$imagepath);
		ImageDestroy ($im);

	}

	$return[0] = $width;
	$return[1] = $nheight;
	
	
	return $return;

}

?>