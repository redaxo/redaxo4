<?php

##################################################################
#
#      Medienpool Functions - vscope new media design
#
##################################################################

function MEDIA_HTMLAREA($VALUE_ID=1,$SLICE_ID,$BUTTONS="",$BODYSTYLE="",$CONFIG="",$STYLE="",$WIDTH="",$HEIGHT=""){

         if($BODYSTYLE=="")  $BODYSTYLE = "body { background-color: #fff; font-family: verdana,sans-serif; font-size: 10pt }";
         if($STYLE=="")      $STYLE = "css/style.css";
         if($WIDTH=="")      $WIDTH = "550px";
         if($HEIGHT=="")     $HEIGHT= "250px";

         $BUTTONS_DEFAULT = '"fontsize","separator","separator","bold", "italic", "underline", "separator",
                    "separator", "insertunorderedlist", "separator", "createlink", "linkmap", "separator",
                    "space", "undo", "redo","separator","justifyleft","justifycenter",
                    "justifyright","separator","separator","mediapool","separator","htmlmode"
                    ';

         if($BUTTONS=="") $BUTTONS = $BUTTONS_DEFAULT;

         $db = new sql;
         $sql = "SELECT value".$VALUE_ID." as slice_value FROM rex_article_slice WHERE id =".$SLICE_ID;
         $res = $db->get_array($sql);
         $CONTENT = $res[0][slice_value];

         print "

	     <style>
	     .separator {
	     	width:2px;
	     }
	     .toolbar {
	     	background-color: #F0EFEB;
	     	height: 22px;
	     	valign: middle;
	     }
	     </style>

         <script type=\"text/javascript\" src=\"js/htmlarea/htmlarea.js\"></script>
         <script type=\"text/javascript\" src=\"js/htmlarea/en.js\"></script>
         <script type=\"text/javascript\" src=\"js/htmlarea/dialog.js\"></script>
         <script type=\"text/javascript\" src=\"js/htmlarea/media.js\"></script>

         <script type=\"text/javascript\">

         var editor = null;

         function initEditor()
         {

                var cfg = new HTMLArea.Config(); // this is the default configuration

                cfg.toolbar = [
                        [".$BUTTONS."]
                        ];

                cfg.registerButton(\"mediapool\", \"Medien Pool\", \"js/htmlarea/images/ed_mediapool.gif\", false,
                    function(editor) {
                             openREXMediaHTMLArea('myarea".$VALUE_ID."');
                    }
                );

                cfg.registerButton(\"linkmap\", \"Linkmap\", \"js/htmlarea/images/ed_link_intern.gif\", false,
                    function(editor) {
                             openLinkMapHTMLArea('myarea".$VALUE_ID."');
                    }
                );

				/*
                cfg.registerButton(\"remove\", \"Formatierung löschen\", \"js/htmlarea/images/ed_remove_format.gif\", false,
                    function(editor) {
                             editor.removeF();
                    }
                );
                */

                cfg.pageStyle = '".$BODYSTYLE."';

                ".$CONFIG."

                myarea".$VALUE_ID." = new HTMLArea('area".$VALUE_ID."', cfg);
                myarea".$VALUE_ID.".generate();
         }

         </script>
         <body onLoad=\"initEditor()\">
         <textarea id=\"area".$VALUE_ID."\" name=\"VALUE[".$VALUE_ID."]\" style=\"width:".$WIDTH.";height:".$HEIGHT.";".$BODYSTYLE."\" rows=\"20\" cols=\"80\">".$CONTENT."</textarea>
         </body>
         ";

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
