<?php

##################################################################
#
#      Medienpool Functions - vscope new media design
#
##################################################################

function MEDIA_HTMLAREA($VALUE_ID=1,$CONTENT,$WIDTH="100%",$HEIGHT='300px',$STYLE_SHEET='css/style.css',$STYLES='all',$LANG='de'){

	// lang = de oder uk

	global $TINYMCE;

	// tiny mce init
	if($TINYMCE!="done"){

		if(is_array($STYLES)){
			$ADVANCED_STYLES = "theme_advanced_styles :\"";
			foreach($STYLES as $key => $var){
				$ADVANCED_STYLES.= "$key=$var;";
			}
			$ADVANCED_STYLES = substr($ADVANCED_STYLES,0,-1);
			$ADVANCED_STYLES.="\",";
		}

		print '

		<script language="javascript" type="text/javascript" src="js/tiny_mce/tiny_mce_src.js"></script>
		<script language="javascript" type="text/javascript">
	    tinyMCE.init({
	        theme : "advanced",
	        language : "'.$LANG.'",
	        mode : "specific_textareas",
	    	insertlink_callback : "insertIntLink",
	        insertimage_callback : "insertMediaPool",
	        theme_advanced_source_editor_width : 600,
	        theme_advanced_source_editor_height : 400,
	        relative_urls : false,
	        content_css : "'.$STYLE_SHEET.'",
	        //extended_valid_elements : "a[href|target|name]",
	        //invalid_elements : "a",
	        '.$ADVANCED_STYLES.'
	        debug : false
	    });

	    function insertMediaPool(src, alt, border, hspace, vspace, width, height, align){
	    	window.open("index.php?page=medienpool&HTMLArea=TINY","pool","width=660,height=500,status=yes,resizable=yes");
	    }

	    function insertIntLink(href, target){
	    	window.open("index.php?page=linkmap&HTMLArea=TINY","link","width=660,height=500,status=yes,resizable=yes");
	    }

	    function tinyMCEEmail(){
	    	var email = prompt("Geben Sie eine Emailadresse ein","");
	    	alert(tinyMCE.getContent());
	    	tinyMCE.execCommand("mceInsertContent", false, "<a href=mailto:"+email+">"+email+"</a>");
	    }

		</script>
	    ';

	    $GLOBALS[TINYMCE] = 'done';
	}

	print '

	<textarea id="VALUE['.$VALUE_ID.']" name="VALUE['.$VALUE_ID.']" style="width:'.$WIDTH.';height:'.$HEIGHT.'" rows="15" mce_editable="true">'.$CONTENT.'</textarea>

	';
}


function media_resize($FILE,$width,$height,$make_copy=false){

        $REX = $GLOBALS[REX];

        if ($REX[IMAGEMAGICK])
        {
	        $magick = $REX[IMAGEMAGICK_PATH];

	        if($width>0){
	                $sizer = "-geometry ".$width;
	        }else if($height>0){
	                $sizer = "-geometry x".$height;
	        }else if($width>0 && $height!=""){
	                $sizer = "-geometry ".$width."x".$height."!";
	        }

	        $system = $magick." ".$FILE." ".$sizer." -colorspace rgb -density 72 ".$FILE;
	        system($system);
	}else
	{
		return false;
	}
}

function media_savefile($FILE,$rex_file_category,$FILEINFOS){

	$FILENAME = $FILE[name];
	$FILESIZE = $FILE[size];
	$FILETYPE = $FILE[type];
	$NFILENAME = "";
	$REX = $GLOBALS[REX];

	// generiere neuen dateinamen
	for ($cn=0;$cn<strlen($FILENAME);$cn++)
	{
	       $char = substr($FILENAME,$cn,1);
	       if ( preg_match("([_A-Za-z0-9\.-])",$char) ) $NFILENAME .= strtolower($char);
	       else if ($char == " ") $NFILENAME .= "_";
	}


	if (strrpos($NFILENAME,".") != "")
	{
	       $NFILE_NAME = substr($NFILENAME,0,strlen($NFILENAME)-(strlen($NFILENAME)-strrpos($NFILENAME,".")));
	       $NFILE_EXT  = substr($NFILENAME,strrpos($NFILENAME,"."),strlen($NFILENAME)-strrpos($NFILENAME,"."));
	}else
	{
	       $NFILE_NAME = $NFILENAME;
	       $NFILE_EXT  = "";
	}

	if ( $NFILE_EXT == ".php" || $NFILE_EXT == ".php3" || $NFILE_EXT == ".php4" || $NFILE_EXT == ".php5" || $NFILE_EXT == ".phtml" || $NFILE_EXT == ".pl" || $NFILE_EXT == ".asp"|| $NFILE_EXT == ".aspx"|| $NFILE_EXT == ".cfm" )
	{
	       $NFILE_EXT .= ".txt";
	}

	$NFILENAME = $NFILE_NAME.$NFILE_EXT;

	if (file_exists($REX[MEDIAFOLDER]."/$NFILENAME"))
	{
	       // datei schon vorhanden ? wenn ja dann _1
	       for ($cf=0;$cf<1000;$cf++)
	       {
	               $NFILENAME = $NFILE_NAME."_$cf"."$NFILE_EXT";
	               if (!file_exists($REX[MEDIAFOLDER]."/$NFILENAME")) break;
	       }
	}

	if (!move_uploaded_file($FILE[tmp_name],$REX[MEDIAFOLDER]."/$NFILENAME"))
	{
	       $message .= "move file $FILENAME failed | ";
	       $ok = 0;
	}else
	{
		$FILESQL = new sql;
		//$FILESQL->debugsql=1;
		$FILESQL->setTable("rex_file");
		$FILESQL->setValue("filetype",$FILETYPE);
		$FILESQL->setValue("title",$FILEINFOS[title]);
		$FILESQL->setValue("description",$FILEINFOS[description]);
		$FILESQL->setValue("copyright",$FILEINFOS[copyright]);
		$FILESQL->setValue("filename",$NFILENAME);
		$FILESQL->setValue("originalname",$FILENAME);
		$FILESQL->setValue("filesize",$FILESIZE);
		$FILESQL->setValue("category_id",$rex_file_category);
		$FILESQL->setValue("stamp",time());
		$FILESQL->insert();

		$ok = 1;
	}

	$RETURN[msg] = $message;
	$RETURN[ok] = $ok;
	$RETURN[filename] = $NFILENAME;

	return $RETURN;
}

function getfilesize($size) {

   // Setup some common file size measurements.
   $kb = 1024;         // Kilobyte
   $mb = 1024 * $kb;   // Megabyte
   $gb = 1024 * $mb;   // Gigabyte
   $tb = 1024 * $gb;   // Terabyte
   // Get the file size in bytes.

   // If it's less than a kb we just return the size, otherwise we keep going until
   // the size is in the appropriate measurement range.
   if($size < $kb) {
       return $size." Bytes";
   }
   else if($size < $mb) {
       return round($size/$kb,2)." KBytes";
   }
   else if($size < $gb) {
       return round($size/$mb,2)." MBbytes";
   }
   else if($size < $tb) {
       return round($size/$gb,2)." GBytes";
   }
   else {
       return round($size/$tb,2)." TBbytes";
   }
}

?>
