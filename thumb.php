<? 
$image_path = $_SERVER['DOCUMENT_ROOT'].$_GET['img'];
$width = isset ($_GET['w']) ? $_GET['w'] : 150;
$height = isset ($_GET['h']) ? $_GET['h'] : 150;
$style = isset ($_GET['style']) ? $_GET['style'] : "normal";

$t = explode("/", $image_path);
$image_filename = $t[sizeof($t) - 1];
$thumb_filename = "thumb_{$image_filename}";
$t[sizeof($t) - 1] = $thumb_filename;
$thumb_path = join("/", $t);

if ($style == "normal") {
	header('Content-Type: image/jpeg');
	header('Content-Disposition: inline; filename=file.jpg');
	 
	if (!file_exists($thumb_path)) {
		resizeJPGImage($width, $height, $image_path, $thumb_path);
	}

	$image = imagecreatefromjpeg($thumb_path);
  imagejpeg($image,"",90);
  imagedestroy($image);
}
else if ($style == "square") {
	header('Content-Type: image/jpeg ');
	header('Content-Disposition: inline; filename = file.jpg');

	if (!file_exists($thumb_path)) {
		resizeToSquare($image_path, $thumb_path);
	}

	$image = imagecreatefromjpeg($thumb_path);
  imagejpeg($image,"",90);
  imagedestroy($image);
} else {
	header("HTTP/1.0 404 Not Found");
}

// function to resize a JPG Image
function resizeJPGImage($forcedwidth, $forcedheight, $sourcefile, $destfile, $imgcomp = 10) {
	$g_imgcomp = 100 - $imgcomp;

	if (file_exists($sourcefile)) {
		$g_is = getimagesize($sourcefile);
		$width = $g_is[0];
		$height = $g_is[1];

		if (($width - $forcedwidth) >= ($height - $forcedheight)) {
			$g_iw = $forcedwidth;
			$g_ih = ($forcedwidth / $width) * $height;
		} else {
			$g_ih = $forcedheight;
			$g_iw = ($g_ih / $height) * $width;
		}
		// sanity check: don't resize images which are smaller than thumbs
		if ($width < $forcedwidth && $height < $forcedheight) {
			$g_iw = $width;
			$g_ih = $height;
		}
		$img_src = imagecreatefromjpeg($sourcefile);
		$img_dst = imagecreatetruecolor($g_iw, $g_ih);
		imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $g_iw, $g_ih, $width, $height);
		imagejpeg($img_dst, $destfile, $g_imgcomp);
		imagedestroy($img_dst);
		imagedestroy($img_src);
		return true;
	} else
		return false;
}

function resizeToSquare($src, $dest, $edge, $quality = 80) {
	if (file_exists($src) && isset ($dest)) {
		// image src size
		$srcSize = getImageSize($src);
		$srcRatio = $srcSize[0] / $srcSize[1]; // width/height ratio
		if ($srcRatio >= 1) {
			$destSize[1] = $edge; //smallest side becomes $edge
			$destSize[0] = $edge * $srcRatio; //other side is enlarged
		} else {
			$destSize[0] = $edge; //smallest side becomse $edge
			$destSize[1] = $edge / $srcRatio; //other side is enlarged
		}
		// true color image, with anti-aliasing
		$destImage = imageCreateTrueColor($edge, $edge);
		// src image
		$srcImage = imageCreateFromJpeg($src);
		// resampling
		if ($srcRatio >= 1) { //when width>height : cut of piece left and right
			imageCopyResampled($destImage, $srcImage, - ($destSize[0] - $edge) / 2, 0, 0, 0, $destSize[0], $destSize[1], $srcSize[0], $srcSize[1]);
		} else { //when width<height : cut of a piece on top and bottom
			imageCopyResampled($destImage, $srcImage, 0, - ($destSize[1] - $edge) / 2, 0, 0, $destSize[0], $destSize[1], $srcSize[0], $srcSize[1]);
		}
		// generating image
		imageJpeg($destImage, $dest, $quality);
		imagedestroy($destImage);
		imagedestroy($srcImage);
		return true;
	} else {
		return false;
	}
}

?>