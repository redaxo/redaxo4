<?

// define max file size an jpeg quality magick for gif files
$max_size = 1000;
$jpeg_quality = 75;

// get params
ereg("^([0-9]*)([awh])__(.*)",$image,$resize);

$size = $resize[1];
$mode = $resize[2];
$imagefile = $resize[3];

$cachepath = 'cache/'.$image;
$imagepath = '../../files/'.$imagefile;

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
	if($cachetime > $filetime){
	    $thumb=new thumbnail('cache/'.$image);
	    @Header("Content-Type: image/".$thumb->img["format"]);
	    readfile('cache/'.$image);
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
if($size > $max_size){
    print "Error size to big: max $max_size px";
    exit;
}

// start thumb class
$thumb=new thumbnail($imagepath);

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
$thumb->jpeg_quality($jpeg_quality);

// save cache
$thumb->save($cachepath);

// show file
$thumb->show();

// class thumbail
class thumbnail
{
	var $img;

	function thumbnail($imgfile)
	{
		//detect image format
		$this->img["format"]=ereg_replace(".*\.(.*)$","\\1",$imgfile);
		$this->img["format"]=strtoupper($this->img["format"]);
		if(!eregi('cache/',$imgfile)){
	        if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
	            //JPEG
	            $this->img["format"]="JPEG";
	            $this->img["src"] = ImageCreateFromJPEG ($imgfile);
	        } elseif ($this->img["format"]=="PNG") {
	            //PNG
	            $this->img["format"]="PNG";
	            $this->img["src"] = ImageCreateFromPNG ($imgfile);
	        } elseif ($this->img["format"]=="GIF") {
	            //GIF
	            $this->img["format"]="GIF";
	            $this->img["src"] = ImageCreateFromGIF ($imgfile);

	        } elseif ($this->img["format"]=="WBMP") {
	            //WBMP
	            $this->img["format"]="WBMP";
	            $this->img["src"] = ImageCreateFromWBMP ($imgfile);
	        } else {
	            //DEFAULT
	            echo "Not Supported File";
	            exit();
	        }
	        @$this->img["lebar"] = imagesx($this->img["src"]);
	        @$this->img["tinggi"] = imagesy($this->img["src"]);
	        //default quality jpeg
	        $this->img["quality"]=75;
	    }
	}

	function size_height($size=100)
	{
		//height
    	$this->img["tinggi_thumb"]=$size;
    	@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
	}

	function size_width($size=100)
	{
		//width
		$this->img["lebar_thumb"]=$size;
    	@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
	}

	function size_auto($size=100)
	{
		//size
		if ($this->img["lebar"]>=$this->img["tinggi"]) {
    		$this->img["lebar_thumb"]=$size;
    		@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
		} else {
	    	$this->img["tinggi_thumb"]=$size;
    		@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
 		}
	}

	function jpeg_quality($quality=75)
	{
		//jpeg quality
		$this->img["quality"]=$quality;
	}

	function show()
	{
		//show thumb
		@Header("Content-Type: image/".$this->img["format"]);

		/* change ImageCreateTrueColor to ImageCreate if GD2 not supported ImageCreateTrueColor function*/
		if(function_exists(ImageCreateTrueColor)){
			$this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
		} else {
			$this->img["des"] = ImageCreate($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
		}

    	@imagecopyresized ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			imageJPEG($this->img["des"],"",$this->img["quality"]);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			imagePNG($this->img["des"]);
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			imageJPEG($this->img["des"]);
		} elseif ($this->img["format"]=="WBMP") {
			//WBMP
			imageWBMP($this->img["des"]);
		}
	}

	function save($save="")
	{
		//save thumb
		if (empty($save)) $save=strtolower("./thumb.".$this->img["format"]);
		/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
		$this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
    		@imagecopyresized ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			imageJPEG($this->img["des"],"$save",$this->img["quality"]);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			imagePNG($this->img["des"],"$save");
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			imageJPEG($this->img["des"],"$save");
		} elseif ($this->img["format"]=="WBMP") {
			//WBMP
			imageWBMP($this->img["des"],"$save");
		}
	}
}
?>
