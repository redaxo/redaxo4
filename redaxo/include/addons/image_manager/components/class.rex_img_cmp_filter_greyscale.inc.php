<?php


class rex_img_cmp_filter_greyscale extends rex_img_cmp_abstract{

	
	function execute()
	{
		
     $src_x = ceil(imagesx($this->img["src"]));
     $src_y = ceil(imagesy($this->img["src"]));
     $dst_x = $src_x;
     $dst_y = $src_y;

     // http://php.about.com/od/gdlibrary/ss/grayscale_gd.htm
     function yiq($r, $g, $b)
     {
        return (($r*0.299)+($g*0.587)+($b*0.114));
     }

     $dst_im = ImageCreateTrueColor($dst_x, $dst_y);

     ImageCopyResampled( $dst_im, $this->img["src"], 0, 0, 0, 0, $dst_x, $dst_y, $src_x, $src_y );

     for ($c=0;$c<256;$c++)
     {
        $palette[$c] = imagecolorallocate($dst_im,$c,$c,$c);
     }

     for ($y=0;$y<$src_y;$y++)
     {
        for ($x=0;$x<$src_x;$x++)
        {
           $rgb = imagecolorat($dst_im,$x,$y);
           $r = ($rgb >> 16) & 0xFF;
           $g = ($rgb >> 8) & 0xFF;
           $b = $rgb & 0xFF;
           $gs = yiq($r,$g,$b);
           imagesetpixel($dst_im, $x, $y, $palette[$gs]);
        }
     }

     $this->img["src"] = $dst_im;
		
	}
	
	function getParams()
	{
		global $REX,$I18N;

		return array(
		);
		
	}

}