<?php
################################################################################
#
#	imageResize Addon 0.2
#	code by vscope new media - www.vscope.at - office@vscope.at
#
################################################################################
#
#	Features:
#
#	Makes resize of images on the fly, with extra cache of resized images so
#	performance loss is extremly small.
#
#	Usage:
#
#	call an image that way index.php?rex_resize=100w__imagefile
#	= to resize the imagefile to width = 100
#	other methods: w = width h=height a=automatic
#	important: gif files are cached as jpegs
#
#	Changelog:
#
#	version 0.2 made addon
#	version 0.1 plugin first release
#
################################################################################

$mypage = "image_resize";

$REX[ADDON][rxid][$mypage] = "REX_IMAGE_RESIZE";
$REX[ADDON][page][$mypage] = "$mypage";
$REX[ADDON][name][$mypage] = "Image Resize Addon";
$REX[ADDON][perm][$mypage] = "image_resize[]";
$REX[ADDON][max_size][$mypage] = 1000;
$REX[ADDON][jpeg_quality][$mypage] = 75;

$REX[PERM][] = "image_resize[]";

// Resize Script für das Frontend
if(($REX[REDAXO] === false) && ($_GET[rex_resize]!="")){

	// get params
	ereg("^([0-9]*)([awh])__(.*)",$rex_resize,$resize);

	$size = $resize[1];
	$mode = $resize[2];
	$imagefile = $resize[3];

	$cachepath = $REX[HTDOCS_PATH].'files/cache_resize___'.$rex_resize;
	$imagepath = $REX[HTDOCS_PATH].'files/'.$imagefile;

	// check for cache file
	if(file_exists($cachepath)){

	    // time of cache
	    $cachetime = filectime($cachepath);

	    // file exists?
	    if(file_exists($imagepath)){
	        $filetime = filectime($imagepath);
	    } else {
	        // image file not exists
	        print "Error: Imagefile does not exist - $imagefile";
	        exit;
	    }

	    // cache is newer? - show cache
	    if($cachetime < $filetime){
	        include($REX[HTDOCS_PATH]."redaxo/include/addons/image_resize/class.thumbnail.inc.php");
	        $thumb = new thumbnail($cachepath);
			@Header("Content-Type: image/".$thumb->img["format"]);
			readfile($cachepath);
			exit;
	    }

	}

	// check params
	if(!file_exists($imagepath)){
	    print "Error: Imagefile does not exist - $imagefile";
	    exit;
	}

	if(($mode!='w') and ($mode!='h') and ($mode!='a')){
	    print "Error wrong mode - only h,w,a";
	    exit;
	}
	if($size==''){
	    print "Error size is no INTEGER";
	    exit;
	}
	if($size > $REX[ADDON][max_size][$mypage]){
	    print "Error size to big: max ".$REX[ADDON][max_size][$mypage]." px";
	    exit;
	}

    include($REX[HTDOCS_PATH]."redaxo/include/addons/image_resize/class.thumbnail.inc.php");

    // start thumb class
    $thumb = new thumbnail($imagepath);
	@Header("Content-Type: image/".$thumb->img["format"]);

    // check method
    if($mode=="w"){
        $thumb->size_width($size);
    }
    if($mode=="h"){
        $thumb->size_height($size);
    }
    if($mode=="a"){
        $thumb->size_auto($size);
    }

    // jpeg quality
    $thumb->jpeg_quality($REX[ADDON][jpeg_quality][$mypage]);

    // save cache
    $thumb->save($cachepath);

    // show file
    $thumb->show();
    exit;

}
?>
