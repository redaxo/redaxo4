<?

##############################################################
#                                                            #
#  MEDIA POOL 1.0  - vscope new media design                 #
#                                                            #
##############################################################

##############################################################
## Configuration Media Pool Htmlarea                         #
##############################################################

// DEFINE WHICH EXTENSIONS GETS WHICH HMTL WRAPPED IN HTMLARA
$htmlarea["default"] = "<a href=###URL### target=_blank>###FILE_NAME###</a>";
$htmlarea[".gif|.jpg|.jpeg|.png"] = "<img src=###URL### width=100 height=100 vspacing=5 hspacing=5 align=left border=0>";

// imagetypes
$imagetype[] = "image/gif";
$imagetype[] = "image/jpeg";
$imagetype[] = "image/jpg";
$imagetype[] = "image/png";


$REX[IMAGEMAGICK] = true;
$REX[IMAGEMAGICK_PATH] = "/usr/bin/convert";


// ----- kategorie checken
$gc = new sql;
$gc->setQuery("select * from rex_file_category where id='$rex_file_category'");
if ($gc->getRows()==0) $rex_file_category = 0;

// ----- kategorie auswahl
$db = new sql();
$file_cat = $db->get_array("SELECT * FROM rex_file_category ORDER BY name ASC");

$cat_out = "<table border=0 cellpadding=5 cellspacing=1 width=100%>\n";
$cat_out .= "<form name=rex_file_cat action=index.php method=POST>\n";
$cat_out .= "<input type=hidden name=page value=medienpool>";
$cat_out .= "<tr>
	<td width=100 class=grey><b>".$I18N->msg('pool_kats')."</td>
	<td width=200 class=grey><select class=inp100 name=rex_file_category onChange=\"location.href='index.php?page=medienpool&rex_file_category='+this[this.selectedIndex].value;\">\n";
if(is_array($file_cat)){
        $cat_out .=  "<option value=0>".$I18N->msg('pool_kats_no')."</option>\n";
        foreach($file_cat as $var){
                if($var[id] == $rex_file_category): $select="selected"; else: $select=""; endif;
                $cat_out .=  "<option value=$var[id] $select>$var[name]</option>\n";
        }
} else {
        $cat_out .=  "<option value=0>".$I18N->msg('pool_kats_no')."</option>\n";
}
$cat_out .= "</select>\n";
$cat_out .= "</td>\n";
$cat_out .= "<td class=grey><input type=submit value='go'></td>";
$cat_out .= "</tr><tr><td colspan=3></td></tr></form></table>";













##############################################################
## IMAGE POPUP WINDOW                                        #
##############################################################

// POPUP WINDOW SHOW IMAGE
if($_GET[popimage]!=''){
        $size = getimagesize($REX[MEDIAFOLDER]."/".$_GET[popimage]);
        print "<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onload=window.resizeTo($size[0],$size[1]);self.focus()>";
        print "<a href=javascript:self.close()><img src=".$REX[MEDIAFOLDER]."/".$_GET[popimage]." border=0></a>";
        print "</body>";
        exit;
}

##############################################################
## MAIN METHODS                                              #
##############################################################

// DEFAULT LINKS
/*
$DEFAULT_LINK  = "index.php?page=medienpool&opener_input_field=".$opener_input_field;
$DEFAULT_CAT_LINK  = "index.php?page=medienpool&rex_file_category=".$rex_file_category."&opener_input_field=".$opener_input_field;
*/

// CHECK IF HTMLAREA OR FIELD
if ($_SESSION[opener_input_field] == "" and $opener_input_field != "")
{
	$_SESSION[opener_input_field] = $opener_input_field;
}
if ($_SESSION[opener_input_field] != "")
{
	$opener_input_field = $_SESSION[opener_input_field];
}

if($_GET[HTMLArea] != ''){
   $_SESSION[myarea] = $HTMLArea;
}
if($_GET[opener_input_field] != ''){
   $_SESSION[myarea] = '';
   session_unregister('myarea');
}
if($_SESSION[myarea] != ''){
   $opener_input_field = 'none';
   $insert_area = $_SESSION[myarea];
}else{
   $insert_area = 'none';
}


// READ OUT FILE ICONS
if ($handle = opendir('pics/pool_file_icons/')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $file_icons[]=str_replace(".gif","",$file);
        }
    }
    closedir($handle);
}

// METHOD ADD FILE CAT
if($_POST[media_method]=='add_file_cat'){
        $db = new sql;
        $db->setTable('rex_file_category');
        $db->setValue('name',$_POST[rex_file_category_new]);
        $db->insert();
        $_GET[rex_file_category] = $db->last_insert_id;
        $msg = $I18N->msg('pool_kat_saved',$_POST[rex_file_category_new]);
        $msg.= "<br><br>";
}



// METHOD DELETE FILE
if($_GET[file_delete]!=""){

        $del = $_GET[file_delete];

        // get file name
        $db = new sql;
        $sql = "SELECT filename FROM rex_file WHERE file_id='$del'";
        $res = $db->get_array($sql);
        $file_name = $res[0][filename];

        // check if file is in an article slice
        $file_search = '';
        for($c=1;$c<=10;$c++){
                $file_search.= "OR file$c='$file_name' ";
                $file_search.= "OR value$c LIKE '%$file_name%' ";
        }
        $file_search = substr($file_search,2);
        $sql = "SELECT rex_article.name,rex_article.id FROM rex_article_slice LEFT JOIN rex_article on rex_article_slice.article_id=rex_article.id WHERE ".$file_search." AND rex_article_slice.article_id=rex_article.id";
        $res = $db->get_array($sql);


        if(!is_array($res)){

                $sql = "DELETE FROM rex_file WHERE file_id = '$del'";
                $db->query($sql);
                unlink($REX[MEDIAFOLDER]."/".$file_name);
                $msg = $I18N->msg('pool_file_deleted');
                $msg.= "<br>";

        } else {

                $msg = $I18N->msg('pool_file_delete_error_1');
                $msg.= $I18N->msg('pool_file_delete_error_2')."<br><br>";
                foreach($res as $var){
                        $msg.=" - <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a><br>";
                }
                $msg.= "<br>";

        }

}


// METHOD RESIZE IMAGE
if($_POST[media_method]=='resize_image'){

        $file_id = $_POST[file_id];
        $width = $_POST[width];
        $height = $_POST[height];

        // get file name
        $db = new sql;
        $sql = "SELECT filename FROM rex_file WHERE file_id='$file_id'";
        $res = $db->get_array($sql);
        $file_name = $res[0][filename];

        // get current image size
        $size = getimagesize($REX[MEDIAFOLDER]."/".$file_name);

        if($size[0]==$width) $width = '';
        if($size[1]==$height) $height = '';

        if(($width!= '') || ($height != '')){
           media_resize($file_name,$width,$height);
        }
        $msg = $I18N->msg('pool_file_is_resized');
        $msg.= "<br><br>";
}

##############################################################
## SHOW MEDIA POOL                                           #
##############################################################

// HEADLINE
echo "<html>
<head>
<title>".$REX[SERVERNAME]." - ".$I18N->msg('pool_media')."</title>
<link rel=stylesheet type=text/css href=css/style.css>
<script language=Javascript>
<!--
var redaxo = true;
function selectMedia(filename)
{
        opener.document.REX_FORM.$opener_input_field.value = filename;
        self.close();
}
function openImage(image){
         window.open('index.php?page=medienpool&popimage='+image,'popview','width=123,height=111');
}
function insertHTMLArea(html){
        window.opener.".$insert_area.".insertHTML(html);
        self.close();

}
//-->
</script>
</head><body bgcolor=#ffffff>
<table border=0 cellpadding=5 cellspacing=0 width=100%>
<tr><td colspan=3 class=grey align=right><b>".$I18N->msg('pool_media')." ".$REX[SERVERNAME]."</b></td></tr>
<tr><td class=greenwhite><b>
	<a href=index.php?page=medienpool class=white>Medien</a> | 
	<!-- <a href=index.php?page=medienpool&mode=search class=white>Mediensuche</a> |  -->
	<a href=index.php?page=medienpool&mode=add class=white>".$I18N->msg('pool_file_insert')."</a> | 
	<a href=index.php?page=medienpool&mode=categories class=white>Kategorien verwalten</a>
	
	</b></td></tr>
<tr><td colspan=3></td></tr>
</table>";




####### MESSAGE
if ($msg != "")
{
	print "<table border=0 cellpadding=5 cellspacing=0 width=100%><tr><td width=20><img src=pics/warning.gif width=16 height=16></td><td class=warning>$msg</td></tr></table>";
	$msg = "";
}









// ------------------------------------- Datei hinzufügen

// METHOD ADD FILE
if($media_method=='add_file'){
	
	// echo $_FILES[file_new][name];
	
        // function in function.rex_medienpool.inc.php
	if ($_FILES[file_new][name] != "" and $_FILES[file_new][name] != "none")
	{
		$FILEINFOS[title] = $ftitle;
		$FILEINFOS[description] = $fdescription;
		$FILEINFOS[copyright] = $fcopyright;
		
		$return = media_savefile($_FILES[file_new],$rex_file_category,$FILEINFOS);
		$msg = $return[msg];
		$mode = "";
		
		if ($saveandexit != "" && $return[ok]==1) 
		{
			$file_name = $return[filename];
		        if($_SESSION[myarea]==''){
				$js = "selectMedia('".$file_name."');";
		        } else {
				$html_source = str_replace("###URL###",$REX[WWW_PATH]."/files/".$file_name,$htmlarea['default']);
				$html_source = str_replace("###FILE_NAME###",$file_name,$html_source);
				$file_ext = strrchr($file_name,".");
				foreach($htmlarea as $key => $var){
					if(eregi($file_ext,$key)){
						$html_source = str_replace("###URL###",$REX[WWW_PATH]."/files/".$file_name,$htmlarea[$key]);
						$html_source = str_replace("###FILE_NAME###",$file_name,$html_source);
					}
				}
				$js = "insertHTMLArea('$html_source');";
		        }
			
			echo "<script language=javascript>\n";
			echo $js;
			echo "\nself.close();\n";
			echo "</script>";
			exit;			
		}
		
        }else
        {
		// $msg = $I18N->msg('pool_file_saved');
		$msg = "Datei wurde nicht gefunden";
        	
        }
        

}

if ($mode == "add")
{

	$cats = new sql();
	$cats->setQuery("SELECT * FROM rex_file_category ORDER BY name ASC");
	
	$cats_sel = new select;
	$cats_sel->set_name("rex_file_category");
	$cats_sel->set_size(1);
	$cats_sel->set_style("' class='inp100");
	
	$cats_sel->add_option("Kein Kategorie","0");
	for ($i=0;$i<$cats->getRows();$i++)
	{
		$cats_sel->add_option($cats->getValue("name"),$cats->getValue("id"));
		$cats->next();	
	}
	
	$cats_sel->set_selected($rex_file_category);

	echo "<table width=100% cellpadding=5 cellspacing=1 border=0><tr><td class=grey><b class=head>".$I18N->msg('pool_file_insert')."</b></td></tr><tr><td></td></tr></table>";

        if ($msg != "")
	{
		print "<table border=0 cellpadding=3 cellspacing=0 width=100%><tr><td width=20 class=warning><img src=pics/warning.gif width=16 height=16></td><td class=warning>$msg</td></tr><tr><td colspan=2></td></tr></table>";
		$msg = "";
	}

	####### UPLOAD TABLE
	print "<table border=0 cellpadding=5 cellspacing=1 width=100%>\n";
	print "<form name=rex_file_cat action=index.php method=POST ENCTYPE=multipart/form-data>\n";
	print "<input type=hidden name=page value=medienpool>\n";
	print "<input type=hidden name=media_method value=add_file>\n";
	print "<input type=hidden name=mode value=add>\n";
	print "<tr><td class=grey width=100>Titel:</td><td class=grey><input type=text size=20 name=ftitle class=inp100 value='".htmlentities(stripslashes($ftitle))."'></td></tr>\n";
	print "<tr><td class=grey>Kategorie:</td><td class=grey>".$cats_sel->out()."</td></tr>\n";
	print "<tr><td class=grey valign=top>Beschreibung:</td><td class=grey><textarea cols=30 rows=3 name=fdescription class=inp100>".(stripslashes($fdescription))."</textarea></td></tr>\n";
	print "<tr><td class=grey>Copyright:</td><td class=grey><input type=text size=20 name=fcopyright class=inp100 value='".(stripslashes($fcopyright))."'></td></tr>\n";
	print "<tr><td class=grey>Datei:</td><td class=grey><input type=file name=file_new size=30></td></tr>";
	print "<tr><td class=grey>&nbsp;</td><td class=grey><input type=submit value=\"".$I18N->msg('pool_file_upload')."\"><input type=submit name=saveandexit value=\"Speichern und Übernehmen\"></td></tr>\n";
	print "</form>\n";
	print "</table>\n";
	// print "<b>".$I18N->msg('pool_file_choose')."<br>";
	#######

}


// ------------------------------------- Kategorienverwaltung
if ($mode == "categories")
{
	print "<input type=hidden name=media_method value=add_file_cat>\n";
}





// ------------------------------------- Dateidetails

if($media_method=='edit_file'){

	$gf = new sql;
	$gf->setQuery("select * from rex_file where file_id='$file_id'");
	if ($gf->getRows()==1)
	{
		$FILESQL = new sql;
		$FILESQL->setTable("rex_file");
		$FILESQL->where("file_id='$file_id'");
		$FILESQL->setValue("title",$ftitle);
		$FILESQL->setValue("description",$fdescription);
		$FILESQL->setValue("copyright",$fcopyright);
		$FILESQL->setValue("category_id",$rex_file_category);
		$FILESQL->setValue("stamp",time());

		$msg = "Dateiinformationen wurden aktualisiert!";
		$filename = $gf->getValue("filename");
		$filetype = $gf->getValue("filetype");
		
		if ($_FILES[file_new][name] != "" and $_FILES[file_new][name] != "none")
		{
			$filetype_ii = in_array($filetype,$imagetype);

			$ffilename = $_FILES[file_new][tmp_name];
			$ffiletype = $_FILES[file_new][type];
			$ffilesize = $_FILES[file_new][size];
			
			if ($ffiletype == $filetype)
			{
				unlink($REX[MEDIAFOLDER]."/".$filename);
				if (!move_uploaded_file($ffilename,$REX[MEDIAFOLDER]."/$filename"))
				{
					$msg .= "<br>Fehler beim Upload der Datei";
				}else
				{
					$FILESQL->setValue("filetype",$ffiletype);
					$FILESQL->setValue("originalname",$ffilename);
					$FILESQL->setValue("filesize",$ffilesize);
					$msg .= "<br>Die Datei wurde ausgetauscht!";
				}
			}else
			{
				$msg .= "<br>Diese Datei kann nicht zum Austausch benutzt werden, da sie von einem anderen Dateityp ist!";	
			}
		}
		
		$width = $width+0;
		$height = $height+0;
		
		if ($width > 0 and $height > 0)
		{
			media_resize($REX[MEDIAFOLDER]."/$filename",$width,$height);
			$msg .= "<br>".$I18N->msg('pool_file_is_resized');
		}elseif($width > 0)
		{
			media_resize($REX[MEDIAFOLDER]."/$filename",$width,$height);
			$msg .= "<br>".$I18N->msg('pool_file_is_resized');
		}elseif($height > 0)
		{
			media_resize($REX[MEDIAFOLDER]."/$filename",$width,$height);
			$msg .= "<br>".$I18N->msg('pool_file_is_resized');
		}			
		
		$FILESQL->update();

	}else
	{
		$msg = "File not found!";
		$mode = "";
	}
	
} 

if ($mode == "detail")
{
	$gf = new sql;
	$gf->setQuery("select * from rex_file where file_id='$file_id'");
	if ($gf->getRows()==1)
	{
		
		echo "<table width=100% cellpadding=5 cellspacing=1 border=0><tr><td class=grey><b class=head>Medien: Detailansicht</b></td></tr><tr><td></td></tr></table>";
		
		echo $cat_out;

		$ftitle = $gf->getValue("title");
		$fdescription = $gf->getValue("description");
		$fcopyright = $gf->getValue("copyright");
		$fname = $gf->getValue("filename");
		$ffiletype = $gf->getValue("filetype");
		$ffiletype_ii = in_array($ffiletype,$imagetype);
		
		$cats = new sql();
		$cats->setQuery("SELECT * FROM rex_file_category ORDER BY name ASC");
		
		$cats_sel = new select;
		$cats_sel->set_name("rex_file_category");
		$cats_sel->set_size(1);
		$cats_sel->set_style("' class='inp100");
		
		$cats_sel->add_option("Kein Kategorie","0");
		for ($i=0;$i<$cats->getRows();$i++)
		{
			$cats_sel->add_option($cats->getValue("name"),$cats->getValue("id"));
			$cats->next();	
		}
		
		$cats_sel->set_selected($rex_file_category);
	
	        if ($msg != "")
		{
			print "<table border=0 cellpadding=3 cellspacing=0 width=100%><tr><td width=20 class=warning><img src=pics/warning.gif width=16 height=16></td><td class=warning>$msg</td></tr><tr><td colspan=2></td></tr></table>";
			$msg = "";
		}
	
		####### UPLOAD TABLE
		print "<table border=0 cellpadding=5 cellspacing=1 width=100%>\n";
		print "<tr><th align=left colspan=3>Detailinformationen</th></tr>";
		print "<form name=rex_file_cat action=index.php method=POST ENCTYPE=multipart/form-data>\n";
		print "<input type=hidden name=page value=medienpool>\n";
		print "<input type=hidden name=media_method value=edit_file>\n";
		print "<input type=hidden name=mode value=detail>\n";
		print "<input type=hidden name=file_id value=$file_id>\n";
		print "<tr><td class=grey width=100>Titel:</td><td class=grey><input type=text size=20 name=ftitle class=inp100 value='".htmlentities(stripslashes($ftitle))."'></td>";
		
		
		if ($ffiletype_ii) echo "<td rowspan=9 width=220 align=center class=lgrey valign=top><br><img src=../files/$fname width=200></td>";
		
		print "</tr>\n";
		print "<tr><td class=grey>Kategorie:</td><td class=grey>".$cats_sel->out()."</td></tr>\n";
		print "<tr><td class=grey valign=top>Beschreibung:</td><td class=grey><textarea cols=30 rows=3 name=fdescription class=inp100>".(stripslashes($fdescription))."</textarea></td></tr>\n";
		print "<tr><td class=grey>Copyright:</td><td class=grey><input type=text size=20 name=fcopyright class=inp100 value='".(stripslashes($fcopyright))."'></td></tr>\n";
		print "<tr><td class=grey>Dateiname:</td><td class=grey><a href=../files/$fname target=_blank>$fname</a></td></tr>\n";
		print "<tr><td class=grey>Datei <br>austauschen:</td><td class=grey><input type=file name=file_new size=30></td></tr>";

		// hier noch überprüfen ob grafik oder nicht und dann erst ausgeben
		if ($ffiletype_ii)
		{
			echo "<tr>
				<td class=lgrey>".$I18N->msg('pool_img_width')." W</td>
				<td class=lgrey>";
			if ($REX[IMAGEMAGICK]) echo "<input type=field name=width size=5> px";
			else echo "-";
			echo "</td>
				</tr>";
			// echo $I18N->msg('pool_img_resize');
			echo "<tr>
				<td class=lgrey>".$I18N->msg('pool_img_height')." H</td>
				<td class=lgrey>";
			if ($REX[IMAGEMAGICK]) echo "<input type=field name=height size=5> px";
			else echo "-";
			echo "</td>
				</tr>";
		}


		print "<tr><td class=grey>&nbsp;</td><td class=grey><input type=submit value=\"Aktualisieren\"></td></tr>\n";
		print "</form>\n";
		print "</table>\n";

	}else
	{
		$msg = "File not found!";
		$mode = "";
	}
}


// ------------------------------------- Dateiliste

if($mode == "")
{

	echo "<table width=100% cellpadding=5 cellspacing=1 border=0><tr><td class=grey><b class=head>Medien</b></td></tr><tr><td></td></tr></table>";

	echo $cat_out;

	if ($msg != "")
	{
		print "<table border=0 cellpadding=5 cellspacing=1 width=100%><tr><td width=20 class=warning><img src=pics/warning.gif width=16 height=16></td><td class=warning>$msg</td></tr><tr><td colspan=2></td></tr></table>";
		$msg = "";
	}


	####### FILE LIST
	print "<table border=0 cellpadding=5 cellspacing=1 width=100%>\n";
	print "<tr>
		<th align=left><b>Thumbnail</th>
		<th align=left><b>Dateiinfo</th>
		<th align=left><b>Beschreibung</th>
		<th align=left><b>Funktionen</th>
		</tr>\n";

	$files = new sql;
	// $files->debugsql = 1;
	$files->setQuery("SELECT * FROM rex_file WHERE category_id=".$rex_file_category." ORDER BY stamp desc,title");
	
	for ($i=0;$i<$files->getRows();$i++)
	{
	
	        $file_id =   $files->getValue("file_id");
	        $file_name = $files->getValue("filename");
	        $file_oname = $files->getValue("originalname");
	        $file_title = $files->getValue("title");
	        $file_description =   $files->getValue("description");
	        $file_copyright =   $files->getValue("copyright");
	        $file_type = $files->getValue("filetype");
	        $file_size = $files->getValue("filesize");
	        $file_stamp = date("d-M-Y | H:i",$files->getValue("stamp"))."h";
		$file_type_ii = in_array($file_type,$imagetype);

		// check if file exists 
		// was passiert wenn nicht da ?
		// if(!file_exists($REX[MEDIAFOLDER]."/".$file_name)) continue;
	
	        // get file icon
	        $icon_src = "pics/pool_file_icons/file.gif";
	        $file_ext = substr(strrchr($file_name,"."),1);
	        if(in_array($file_ext,$file_icons)){
	                $icon_src = "pics/pool_file_icons/$file_ext.gif";
	        }
	
	        // get file size
	        $file_size = getfilesize($file_size);
	
		if ($file_type_ii) $thumbnail = "<img src=../files/$file_name width=80>";
		else $thumbnail = "<img src=pics/leer.gif width=1 height=80 align=left>Keine Anzeige möglich";
		
		if ($file_title == "") $file_title = "[Kein Titel eingegeben]";
		if ($file_description == "") $file_description = "[Keine Beschreibung eingegeben]";
		
		// HTMLAREA / INPUT FIELD CHECK / link setzen zum übernehmen
	        if($_SESSION[myarea]==''){
			$opener_link = "<a href=javascript:void(0) onClick=selectMedia('".$file_name."');>".$I18N->msg('pool_file_ins')."</a>";
	        } else {
	           // GET HTML WRAP FROM CONFIG FILE
	           $html_source = str_replace("###URL###",$REX[WWW_PATH]."/files/".$file_name,$htmlarea['default']);
	           $html_source = str_replace("###FILE_NAME###",$file_name,$html_source);
	           $file_ext = strrchr($file_name,".");
	           foreach($htmlarea as $key => $var){
	                   if(eregi($file_ext,$key)){
	                      $html_source = str_replace("###URL###",$REX[WWW_PATH]."/files/".$file_name,$htmlarea[$key]);
	                      $html_source = str_replace("###FILE_NAME###",$file_name,$html_source);
	                   }
	           }
	           $opener_link = "<a href=javascript:void(0) onClick=\"insertHTMLArea('$html_source');\">".$I18N->msg('pool_file_ins')."</a>";
	        }

		echo "<tr>";
		echo "<td valign=top class=grey width=100>$thumbnail</td>";
		echo "<td valign=top class=grey width=200><b><a href=index.php?page=medienpool&mode=detail&file_id=$file_id&rex_file_category=$rex_file_category>$file_title</a></b><br><br>$file_name<br>$file_size<br><br>$file_stamp</td>";
		echo "<td valign=top class=grey>".nl2br(htmlentities($file_description))."</td>";
		echo "<td valign=top class=grey>$opener_link</td>";
		echo "</tr>";
	        $files->next();
	}
	echo "</table>";
}

echo "</body></html>";

?>
