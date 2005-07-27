<?php

// TODOS
// - thumbnails - einstellbar über specials
// - mediensuche
// - rechte einbauen
// - wysiwyg image pfade anschauen und kontrollieren
// - import checken
// - mehrere ebenen in kategorienedit  einbauen

// KOMMT NOCH
// - only types einbauen (only .gif/.pdf/.xxx ..)
// - direkt katjump von modulen aus
// - direktjump bei &action=media_details&file_name=xysd.jpg


// *************************************** WENN HTMLAREA ODER INPUT FELD.. SAVE

// ----- opener_input_field setzen
if(isset($_GET["opener_input_field"])) $_SESSION["media[opener_input_field]"] = $_GET["opener_input_field"];





// *************************************** PERMS
$PERMALL = false;
if ($REX_USER->isValueOf("rights","admin[]") or $REX_USER->isValueOf("rights","dev[]") or $REX_USER->isValueOf("rights","media[0]")) $PERMALL = true;





// *************************************** CONFIG

$mypath = str_replace("/redaxo/index.php","",$_SERVER[SCRIPT_NAME]);
$doctypes = array ("bmp","css","doc","gif","gz","jpg","mov","mp3","ogg","pdf","png","ppt","rar","rtf","swf","tar","tif","txt","wma","xls","zip");
$imgtypes = array ("image/gif","image/jpg","image/jpeg","image/png");
$thumbs = true;
$thumbsresize = true;
if (!isset($REX['ADDON']['status']['image_resize']) || $REX['ADDON']['status']['image_resize'] != 1) $thumbsresize = false;





// *************************************** HEADER

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="<?php echo $I18N->msg("htmllang"); ?>">
<head>
	<title><?php echo $REX[SERVERNAME].' - '.$I18N->msg('pool_media'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $I18N->msg("htmlcharset"); ?>" />
	<meta http-equiv="Content-Language" content="<?php echo $I18N->msg("htmllang"); ?>" />
	<link rel=stylesheet type=text/css href=css/style.css />
	<script language=Javascript src=js/standard.js></script>
<script language=Javascript>
<!--

var redaxo = true;

function selectMedia(filename)
{
	<?php if ($_SESSION["media[opener_input_field]"]!="") echo "opener.document.REX_FORM.".$_SESSION["media[opener_input_field]"].".value = filename;"; ?>
	self.close();
}

function addMedialist(filename)
{
	<?php 
		if (substr($_SESSION["media[opener_input_field]"],0,14) == "REX_MEDIALIST_")
		{
			$id = substr($_SESSION["media[opener_input_field]"],14,strlen($_SESSION["media[opener_input_field]"]));
			echo "opener.addREXMedialist($id,filename);";
		}
	?>
}

function insertLink(link){
	window.opener.tinyMCE.insertLink( "/files/" + link,"_self");
	self.close();
}

function insertImage(src, alt, width, height)
{
	var image = '<img src="/files/'+ src +'" alt="'+ alt +'" width="'+ width +'" height="'+ height +'" vspacing="5" hspacing="5" align="left" border="0"/>';
	insertHTML( image);
}

function insertHTML(html) {
	window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
	self.close();
}

function SetAllCheckBoxes(FormName, FieldName, mthis)
{
	CheckValue = true;
	
	if (mthis.checked) CheckValue=true;
	else CheckValue=false;
	
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes) return;

	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes) objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++)
			objCheckBoxes[i].checked = CheckValue;
}

// ----- old functions

function openImage(image){
	window.open('index.php?page=medienpool&popimage='+image,'popview','width=123,height=111');
}

function insertHTMLArea(html,filename){
	selection = window.opener.tinyMCE.getContent();
	if(selection!=''){
		html = '<a href=\"/files/'+filename+'\">'+selection+'</a>';
	}
	window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
	self.close();
}



//-->
</script>
</head>

<body bgcolor=#ffffff>

<table border=0 cellpadding=5 cellspacing=1 width=100% class=rex2>
<tr>
	<td colspan=3 class=grey align=right><b><?php echo $I18N->msg('pool_media')." ".$REX[SERVERNAME]; ?></b></td>
</tr>
<tr>
	<td class=greenwhite><b>
<?php 

echo "<a href=index.php?page=medienpool&rex_file_category=$rex_file_category class=white>".$I18N->msg('pool_file_list')."</a>";
echo " | <a href=index.php?page=medienpool&subpage=add_file&rex_file_category=$rex_file_category class=white>".$I18N->msg('pool_file_insert')."</a>";
// if ($PERMALL) echo " | <a href=index.php?page=medienpool&subpage=search class=white>Mediensuche</a>";
if ($PERMALL) echo " | <a href=index.php?page=medienpool&subpage=categories class=white>".$I18N->msg('pool_cat_list')."</a>";
// if ($PERMALL) echo " | <a href=index.php?page=medienpool&subpage=import class=white>Import</a>";

?>
	</b></td>
</tr>
<tr>
	<td colspan=3></td>
</tr>
</table><?php

// *************************************** MESSAGES
if ($msg != "")
{
	print "<table border=0 cellpadding=5 cellspacing=0 width=100%><tr><td width=20><img src=pics/warning.gif width=16 height=16></td><td class=warning>$msg</td></tr></table>";
	$msg = "";
}










// *************************************** SUBPAGE: KATEGORIEN
if ($PERMALL && $subpage == "categories")
{
	
	$msg = "";
	if($_REQUEST["media_method"] == 'edit_file_cat')
	{
		$db = new sql;
		$db->setTable('rex_file_category');
		$db->where("id='$edit_id'");
		$db->setValue('name',$cat_name);
		$db->setValue("updatedate",time());
		$db->setValue("updateuser",$REX_USER->getValue("login"));
		$db->update();
		$msg = $I18N->msg('pool_kat_updated',$cat_name);
		
	}elseif($_REQUEST["media_method"] == 'delete_file_cat')
	{
		$gf = new sql;
		$gf->setQuery("select * from rex_file where category_id='$edit_id'");
		$gd = new sql;
		$gd->setQuery("select * from rex_file_category where re_id='$edit_id'");
		if ($gf->getRows()==0 && $gd->getRows()==0)
		{
			$gf->setQuery("delete from rex_file_category where id='$edit_id'");
			$msg = $I18N->msg('pool_kat_deleted');
		}else
		{
			$msg = $I18N->msg('pool_kat_not_deleted');
		}
	}elseif($media_method=='add_file_cat')
	{
		$db = new sql;
		$db->setTable('rex_file_category');
		$db->setValue('name',$_REQUEST["catname"]);
		$db->setValue('re_id',$_REQUEST["cat_id"]);
		$db->setValue('path',$_REQUEST["catpath"]);
		$db->setValue("createdate",time());
		$db->setValue("createuser",$REX_USER->getValue("login"));
		$db->setValue("updatedate",time());
		$db->setValue("updateuser",$REX_USER->getValue("login"));
		$db->insert();
		$msg = $I18N->msg('pool_kat_saved',$cat_name);
	}
	
	echo "<table width=100% cellpadding=5 cellspacing=1 border=0><tr><td class=grey><b class=head>".$I18N->msg('pool_kats')."</b></td></tr><tr><td></td></tr></table>";

	$link = "index.php?page=medienpool&subpage=categories&cat_id=";

	$textpath = "<a href=$link"."0>Start</a>";
	if ($cat_id == "") $cat_id = 0;
	if ($cat_id==0 || !($OOCat = OOMediaCategory::getCategoryById($cat_id)))
	{
		$OOCats = OOMediaCategory::getRootCategories();
		$cat_id = 0;
		$catpath = "|";
	}else
	{
		$OOCats = $OOCat->getChildren();
		
		$paths = explode("|",$OOCat->getPath());
		
		for ($i=1;$i<count($paths);$i++)
		{
			$iid = current($paths);
			if ($iid != "")
			{
				$icat = OOMediaCategory::getCategoryById($iid);
				$textpath .= " : <a href=$link$iid>".$icat->getName()."</a>";
			}
			next($paths);
		}
		$textpath .= " : <a href=$link$cat_id>".$OOCat->getName()."</a>";
		$catpath = $OOCat->getPath()."$cat_id|";
	}

	echo "<table border=0 cellpadding=5 cellspacing=1 width=100% class=rex style='width:100%'>\n";
	echo "<tr><td class=grey><b>Pfad : $textpath</b></td></tr>";
	echo "</table><br>";

	echo "<table border=0 cellpadding=5 cellspacing=1 width=100% class=rex style='width:100%'>\n";
	echo "<tr>
		<th class=icon><a href=$link$cat_id&media_method=add_cat><img src=pics/folder_plus.gif></a></th>
		<th class=icon>ID</th>
		<th align=left>".$I18N->msg('pool_kat_name')."</th>
		<th align=left width=200>".$I18N->msg('pool_kat_function')."</th>
		</tr>";

	if ($msg!="") echo "<tr class=warning><td class=icon><img src=pics/warning.gif width=16 height=16></td><td colspan=3><b>$msg</b></td></tr>";

	if ($_REQUEST["media_method"] == "add_cat")
	{
			echo "<tr>";
			echo "<form action=index.php method=post>";
			echo "<input type=hidden name=page value=medienpool>\n";
			echo "<input type=hidden name=media_method value=add_file_cat>\n";
			echo "<input type=hidden name=subpage value=categories>";
			echo "<input type=hidden name=cat_id value=$cat_id>";
			echo "<input type=hidden name=catpath value='$catpath'>";
			echo "<td class=icon><img src=pics/folder.gif></td>";
			echo "<td class=icon>&nbsp;</td>";
			echo "<td class=grey><input type=text size=10 class=inp100 name=catname value=\"".htmlentities("")."\"></td>";
			echo "<td class=grey><input type=submit value=\"".$I18N->msg('pool_kat_update')."\"></td>";
			echo "</form>";
			echo "</tr>";	
	}

	foreach( $OOCats as $OOCat) {
		
		$iid = $OOCat->getId();
		$iname = $OOCat->getName();
		
		if($_REQUEST["media_method"] == "update_file_cat" && $edit_id==$iid)
		{
			echo "<tr>";
			echo "<form action=index.php method=post>";
			echo "<input type=hidden name=page value=medienpool>\n";
			echo "<input type=hidden name=media_method value=edit_file_cat>\n";
			echo "<input type=hidden name=subpage value=categories>";
			echo "<input type=hidden name=cat_id value=$cat_id>";
			echo "<input type=hidden name=edit_id value=$iid>";
			echo "<td class=icon><a href=$link$iid><img src=pics/folder.gif></a></td>";
			echo "<td class=icon>$iid</td>";
			echo "<td class=grey><input type=text size=10 class=inp100 name=cat_name value=\"".htmlentities($iname)."\"></td>";
			echo "<td class=grey><input type=submit value=\"".$I18N->msg('pool_kat_update')."\"></td>";
			echo "</form>";
			echo "</tr>";
		}else
		{
			echo "<tr>";
				echo "<td align=center><a href=$link$iid><img src=pics/folder.gif></a></td>";
				echo "<td align=center>$iid</td>";
				echo "<td class=grey><a href=$link$iid>".$OOCat->getName()."</a> &nbsp;</td>";
				echo "<td class=grey><a href=$link$cat_id&media_method=update_file_cat&edit_id=$iid>".$I18N->msg('pool_kat_edit')."</a> | <a href=$link$cat_id&media_method=delete_file_cat&edit_id=$iid onclick='return confirm(\"".$I18N->msg('delete')." ?\")'>".$I18N->msg('pool_kat_delete')."</a></td>";
				echo "</tr>";
		}
	}
	echo "</table>";

}










// *************************************** KATEGORIEN CHECK UND AUSWAHL

// ***** kategorie checken
$gc = new sql;
$gc->setQuery("select * from rex_file_category where id='$rex_file_category'");
if ($gc->getRows()==0) $rex_file_category = 0;

// ***** kategorie auswahl
$db = new sql();
$file_cat = $db->get_array("SELECT * FROM rex_file_category ORDER BY name ASC");
$cat_out = "<table border=0 cellpadding=5 cellspacing=1 width=100% class=rex style='width:100%'>\n";
$cat_out .= "<form name=rex_file_cat action=index.php method=POST>\n";
$cat_out .= "<input type=hidden name=page value=medienpool>";
$cat_out .= "<tr>
			<td class=icon></td>
			<td width=80 class=grey><b>".$I18N->msg('pool_kats')."</td>
			<td class=grey>";

$sel_media = new select;
$sel_media->set_style("'; onChange=\"location.href='index.php?page=medienpool&rex_file_category='+this[this.selectedIndex].value;\" class='inp100");
$sel_media->set_size(1);
$sel_media->set_name("rex_file_category");
$sel_media->add_option($I18N->msg('pool_kats_no'),"0");
$mediacat_ids = array();
if ($rootCats = OOMediaCategory::getRootCategories())
{
    foreach( $rootCats as $rootCat) {
        add_mediacat_options( $sel_media, $rootCat, $mediacat_ids);
    }
}
$sel_media->set_selected($rex_file_category);
$cat_out .= $sel_media->out();

$cat_out .= "</td>\n";
$cat_out .= "<td class=grey width=150><input type=submit value='".$I18N->msg('pool_search')."'></td>";
$cat_out .= "</tr></form></table>";










// *************************************** SUBPAGE: MEDIENSUCHE










// *************************************** FUNCTIONS

function saveMedia($FILE,$rex_file_category,$FILEINFOS){

	global $REX,$REX_USER;
	
	$FILENAME = $FILE[name];
	$FILESIZE = $FILE[size];
	$FILETYPE = $FILE[type];
	$NFILENAME = "";
	
	// ----- neuer filename und extension holen
	$NFILENAME = strtolower(preg_replace("/[^a-zA-Z0-9.]/","_",$FILENAME));
	if (strrpos($NFILENAME,".") != "")
	{
		$NFILE_NAME = substr($NFILENAME,0,strlen($NFILENAME)-(strlen($NFILENAME)-strrpos($NFILENAME,".")));
		$NFILE_EXT  = substr($NFILENAME,strrpos($NFILENAME,"."),strlen($NFILENAME)-strrpos($NFILENAME,"."));
	}else
	{
		$NFILE_NAME = $NFILENAME;
		$NFILE_EXT  = "";
	}

	// ---- ext checken
	$ERROR_EXT = array("php","php3","php4","php5","phtml","pl","asp","aspx","cfm");
	if (in_array($NFILE_EXT,$ERROR_EXT))
	{
		$NFILE_NAME .= $NFILE_EXT;
		$NFILE_EXT = ".txt";
	}
	
	$NFILENAME = $NFILE_NAME.$NFILE_EXT;
	
	// ----- datei schon vorhanden -> namen aendern -> _1 ..
	if (file_exists($REX[MEDIAFOLDER]."/$NFILENAME"))
	{
		for ($cf=1;$cf<1000;$cf++)
		{
			$NFILENAME = $NFILE_NAME."_$cf"."$NFILE_EXT";
			if (!file_exists($REX[MEDIAFOLDER]."/$NFILENAME")) break;
		}
	}
	
	// ----- dateiupload
	$upload = true;
	if(!@move_uploaded_file($FILE[tmp_name],$REX[MEDIAFOLDER]."/$NFILENAME") )
	{
		if (!@copy($FILE[tmp_name],$REX[MEDIAFOLDER]."/$NFILENAME"))
		{
			$message .= "move file $FILENAME failed | ";
			$ok = 0;
			$upload = false;
		}
	}
	
	if($upload)
	{
	
		chmod($REX[MEDIAFOLDER]."/$NFILENAME", 0777);
	
		// get widht height
		$size = @getimagesize($REX[MEDIAFOLDER]."/$NFILENAME");
	
		$FILESQL = new sql;
		// $FILESQL->debugsql=1;
		$FILESQL->setTable("rex_file");
		$FILESQL->setValue("filetype",$FILETYPE);
		$FILESQL->setValue("title",$FILEINFOS[title]);
		$FILESQL->setValue("description",$FILEINFOS[description]);
		$FILESQL->setValue("copyright",$FILEINFOS[copyright]);
		$FILESQL->setValue("filename",$NFILENAME);
		$FILESQL->setValue("originalname",$FILENAME);
		$FILESQL->setValue("filesize",$FILESIZE);
		$FILESQL->setValue("width",$size[0]);
		$FILESQL->setValue("height",$size[1]);
		$FILESQL->setValue("category_id",$rex_file_category);
		$FILESQL->setValue("createdate",time());
		$FILESQL->setValue("createuser",$REX_USER->getValue("login"));
		$FILESQL->setValue("updatedate",time());
		$FILESQL->setValue("updateuser",$REX_USER->getValue("login"));
		$FILESQL->insert();
		$ok = 1;
	}
	
	$RETURN[title] = $FILEINFOS[title];
	$RETURN[width] = $size[0];
	$RETURN[height] = $size[1];
	$RETURN[type] = $FILETYPE;
	$RETURN[msg] = $message;
	$RETURN[ok] = $ok;
	$RETURN[filename] = $NFILENAME;
	
	return $RETURN;
}

function add_mediacat_options( &$select, &$mediacat, &$mediacat_ids, $groupName = '')
{
	if(empty($mediacat)) return;
	$mediacat_ids[] = $mediacat->getId();
	$select->add_option($mediacat->getName(),$mediacat->getId(), $groupName);
	$childs = $mediacat->getChildren();
	if (is_array($childs))
	{
		foreach ( $childs as $child) {
			add_mediacat_options( $select, $child, $mediacat_ids, $mediacat->getName());
		}
	}
}

function add_mediacat_options_wperm( &$select, &$mediacat, &$mediacat_ids, $groupName = '')
{
	global $PERMALL, $REX_USER;
    if(empty($mediacat)) return;
	$mediacat_ids[] = $mediacat->getId();
	if ($PERMALL || $REX_USER->isValueOf("rights","media[".$mediacat->getId()."]")) $select->add_option($mediacat->getName(),$mediacat->getId(), $groupName);
	$childs = $mediacat->getChildren();
	if (is_array($childs))
	{
		foreach ( $childs as $child) {
			add_mediacat_options_wperm( $select, $child, $mediacat_ids, $mediacat->getName());
		}
	}
}










// *************************************** Subpage: ADD FILE

// ----- METHOD ADD FILE
if($subpage == "add_file" && $media_method == 'add_file'){
	
	// echo $_FILES[file_new][name];
	
	// function in function.rex_medienpool.inc.php
	if ($_FILES[file_new][name] != "" and $_FILES[file_new][name] != "none")
	{
		
		$FILEINFOS[title] = $ftitle;
		$FILEINFOS[description] = $fdescription;
		$FILEINFOS[copyright] = $fcopyright;
		
		if (!$PERMALL && !$REX_USER->isValueOf("rights","media[$rex_file_category]")) $rex_file_category = 0;
		
		$return = saveMedia($_FILES[file_new],$rex_file_category,$FILEINFOS);
		$msg = $return[msg];
		$subpage = "";
				
		if ($saveandexit != "" && $return[ok]==1)
		{
			$file_name = $return[filename];
			$ffiletype = $return[type];
			$width = $return[width];
			$height = $return[height];
	
			if($_SESSION["media[opener_input_field]"] == 'TINY')
			{
				if (in_array($ffiletype,$imgtypes))
				{
					$js = "insertImage('$file_name','$file_name','$width','$height');";
				}else
				{
					$js = "insertLink('".$file_name."');";
				}

			}elseif($_SESSION["media[opener_input_field]"] != '')
			{
				$js = "selectMedia('".$file_name."');";
				if (substr($_SESSION["media[opener_input_field]"],0,14)=="REX_MEDIALIST_")
				{
					$js = "addMedialist('".$file_name."');";
				}
			}
					
			echo "<script language=javascript>\n";
			echo $js;
			echo "\nself.close();\n";
			echo "</script>";
			exit;
		}
	
	}else
	{
		// $msg = ;
		$msg = $I18N->msg('pool_file_not_found');
	}
}

// ----- METHOD ADD FORM
if ($subpage == "add_file")
{

	$cats_sel = new select;
	$cats_sel->set_style("' class='inp100");
	$cats_sel->set_size(1);
	$cats_sel->set_name("rex_file_category");
	$cats_sel->add_option($I18N->msg('pool_kats_no'),"0");
	$mediacat_ids = array();
	$rootCat = 0;
	if ($rootCats = OOMediaCategory::getRootCategories())
	{
		foreach( $rootCats as $rootCat) {
			add_mediacat_options_wperm( $cats_sel, $rootCat, $mediacat_ids);
		}
	}
	$cats_sel->set_selected($rex_file_category);

	echo "<table width=100% cellpadding=5 cellspacing=1 border=0><tr><td class=grey><b class=head>".$I18N->msg('pool_file_insert')."</b></td></tr><tr><td></td></tr></table>";
	
	if ($msg != "")
	{
		print "<table border=0 cellpadding=3 cellspacing=0 width=100%><tr><td width=20 class=warning><img src=pics/warning.gif width=16 height=16></td><td class=warning>$msg</td></tr><tr><td colspan=2></td></tr></table>";
		$msg = "";
	}
	
	print "<table border=0 cellpadding=5 cellspacing=1 width=100%>\n";
	print "<form name=rex_file_cat action=index.php method=POST ENCTYPE=multipart/form-data>\n";
	print "<input type=hidden name=page value=medienpool>\n";
	print "<input type=hidden name=media_method value=add_file>\n";
	print "<input type=hidden name=subpage value=add_file>\n";
	print "<tr><td class=grey width=100>".$I18N->msg('pool_file_title').":</td><td class=grey><input type=text size=20 name=ftitle class=inp100 value='".htmlentities(stripslashes($ftitle))."'></td></tr>\n";
	print "<tr><td class=grey>".$I18N->msg('pool_category').":</td><td class=grey>".$cats_sel->out()."</td></tr>\n";
	print "<tr><td class=grey valign=top>".$I18N->msg('pool_description').":</td><td class=grey><textarea cols=30 rows=3 name=fdescription class=inp100>".(stripslashes($fdescription))."</textarea></td></tr>\n";
	print "<tr><td class=grey>".$I18N->msg('pool_copyright').":</td><td class=grey><input type=text size=20 name=fcopyright class=inp100 value='".(stripslashes($fcopyright))."'></td></tr>\n";
	print "<tr><td class=grey>Datei:</td><td class=grey><input type=file name=file_new size=30></td></tr>";
	print "<tr><td class=grey>&nbsp;</td><td class=grey><input type=submit value=\"".$I18N->msg('pool_file_upload')."\">";
	if ($_SESSION["media[opener_input_field]"] != "") echo "<input type=submit name=saveandexit value=\"".$I18N->msg('pool_file_upload_get')."\">";
	print "</td></tr>\n";
	print "</form>\n";
	print "</table>\n";

}










// *************************************** Subpage: Detail

if($subpage=="detail" && $media_method=='delete_file')
{
	$gf = new sql;
	$gf->setQuery("select * from rex_file where file_id='$file_id'");
	
	if ($gf->getRows()==1)
	{
		if ($PERMALL || $REX_USER->isValueOf("rights","media[".$gf->getValue("category_id")."]"))
		{
	
			$file_name = $gf->getValue("filename");
		
			// check if file is in an article slice
			$file_search = '';
	
			for($c=1;$c<11;$c++){
				$file_search.= "OR file$c='$file_name' ";
				$file_search.= "OR value$c LIKE '%$file_name%' ";
			}
			
			$file_search = substr($file_search,2);
			$sql = "SELECT rex_article.name,rex_article.id FROM rex_article_slice LEFT JOIN rex_article on rex_article_slice.article_id=rex_article.id WHERE ".$file_search." AND rex_article_slice.article_id=rex_article.id";
			// $db->setQuery($sql);
			$res1 = $db->get_array($sql);
			
			$sql = "SELECT rex_article.name,rex_article.id FROM rex_article where file='$file_name'";
			$res2= $db->get_array($sql);
		
			if(!is_array($res1) and !is_array($res2)){
			
				$sql = "DELETE FROM rex_file WHERE file_id = '$file_id'";
				$db->query($sql);
				unlink($REX[MEDIAFOLDER]."/".$file_name);
				$msg = $I18N->msg('pool_file_deleted');
				$subpage = "";
			}else{
			
				$msg = $I18N->msg('pool_file_delete_error_1');
				$msg.= $I18N->msg('pool_file_delete_error_2')."<br>";
				foreach($res1 as $var){
					$msg.=" | <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a>";
				}
				foreach($res2 as $var){
					$msg.=" | <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a>";
				}
				$msg .= " | ";
				$subpage = "";
			
			}
		}else
		{
			$msg = $I18N->msg('no_permission');
		}
	}else
	{
		$msg = $I18N->msg('pool_file_not_found');
		$subpage = "";
	}
}

if($subpage=="detail" && $media_method=='edit_file'){
	
	$gf = new sql;
	$gf->setQuery("select * from rex_file where file_id='$file_id'");
	if ($gf->getRows()==1)
	{
		
		if ($PERMALL || ( $REX_USER->isValueOf("rights","media[".$gf->getValue("category_id")."]") &&  $REX_USER->isValueOf("rights","media[$rex_file_category]") ) )
		{
			
			$FILESQL = new sql;
			$FILESQL->setTable("rex_file");
			$FILESQL->where("file_id='$file_id'");
			$FILESQL->setValue("title",$ftitle);
			$FILESQL->setValue("description",$fdescription);
			$FILESQL->setValue("copyright",$fcopyright);
			$FILESQL->setValue("category_id",$rex_file_category);
			
			$msg = "Dateiinformationen wurden aktualisiert!";
			$filename = $gf->getValue("filename");
			$filetype = $gf->getValue("filetype");
		
			if ($_FILES[file_new][name] != "" and $_FILES[file_new][name] != "none")
			{
				
				$ffilename = $_FILES[file_new][tmp_name];
				$ffiletype = $_FILES[file_new][type];
				$ffilesize = $_FILES[file_new][size];
		
				if ($ffiletype == $filetype)
				{
					unlink($REX[MEDIAFOLDER]."/".$filename);
					if (!move_uploaded_file($ffilename,$REX[MEDIAFOLDER]."/$filename"))
					{
						if (!@copy($FILE[tmp_name],$REX[MEDIAFOLDER]."/$NFILENAME"))
						{
							$msg .= "<br>".$I18N->msg('pool_file_upload_error');
						}else
						{
							$FILESQL->setValue("filetype",$ffiletype);
							$FILESQL->setValue("originalname",$ffilename);
							$FILESQL->setValue("filesize",$ffilesize);
							$msg .= "<br>Die Datei wurde ausgetauscht!";
						}
					}else
					{
						$FILESQL->setValue("filetype",$ffiletype);
						$FILESQL->setValue("originalname",$ffilename);
						$FILESQL->setValue("filesize",$ffilesize);
						$msg .= "<br>Die Datei wurde ausgetauscht!";
					}
				}else
				{
					$msg .= "<br>".$I18N->msg('pool_file_upload_errortype');
				}
			}
			$size = @getimagesize($REX[INCLUDE_PATH]."/../../files/$filename");
			
			$FILESQL->setValue("updatedate",time());
			$FILESQL->setValue("updateuser",$REX_USER->getValue("login"));
			$FILESQL->update();
		}else
		{
			$msg = $I18N->msg('no_permission');
		}
	}else
	{
		$msg = $I18N->msg('pool_file_not_found');
		$subpage = "";
	}

}

if ($subpage == "detail")
{
	$gf = new sql;
	
	if ($file_name != "") $gf->setQuery("select * from rex_file where filename='$file_name'");
	if ($gf->getRows()==1) $file_id = $gf->getValue("file_id");
	
	$gf->setQuery("select * from rex_file where file_id='$file_id'");
	if ($gf->getRows()==1)
	{

		$TPERM = false;
		if ($PERMALL || $REX_USER->isValueOf("rights","media[".$gf->getValue("category_id")."]")) $TPERM = true;

		echo "<table width=100% cellpadding=5 cellspacing=1 border=0><tr><td class=grey><b class=head>".$I18N->msg('pool_file_detail')."</b></td></tr><tr><td></td></tr></table>";
		echo $cat_out;
		
		$ftitle = $gf->getValue("title");
		$fdescription = $gf->getValue("description");
		$fcopyright = $gf->getValue("copyright");
		$fname = $gf->getValue("filename");
		$ffiletype = $gf->getValue("filetype");
		$rex_category_id = $gf->getValue("category_id");
		
		$file_ext = substr(strrchr($file_name,"."),1);
		$icon_src = "pics/mime_icons/mime-default.gif";
		if (in_array($file_ext,$doctypes)) $icon_src = "pics/mime_icons/mime-".$file_ext.".gif";
		$thumbnail = "<img src=$icon_src align=left border=0>";
		
		$ffiletype_ii = in_array($ffiletype,$imgtypes);
		if ($ffiletype_ii==1)
		{
			$size = getimagesize($REX[INCLUDE_PATH]."/../../files/$fname");
			$fwidth = $size[0];
			$fheight = $size[1];
			if ($fwidth >199) $rfwidth = 200;
			else $rfwidth = $fwidth;
		}

		if ($msg != "")
		{
			print "<table border=0 cellpadding=3 cellspacing=0 width=100%><tr><td width=20 class=warning><img src=pics/warning.gif width=16 height=16></td><td class=warning>$msg</td></tr><tr><td colspan=2></td></tr></table>";
			$msg = "";
		}

		if($_SESSION["media[opener_input_field]"] == 'TINY')
		{
			$opener_link = "";
			if (in_array($ffiletype,$imgtypes))
			{
				$opener_link .= "<a href=javascript:insertImage('$fname','$fname','".$gf->getValue("width")."','".$gf->getValue("height")."');>".$I18N->msg('pool_image_get')."</a> | ";
			}
			$opener_link .= "<a href=javascript:insertLink('".$fname."');>".$I18N->msg('pool_link_get')."</a>";
		}elseif($_SESSION["media[opener_input_field]"] != '')
		{
			$opener_link = "<a href=javascript:selectMedia('".$fname."');>".$I18N->msg('pool_file_get')."</a>";
			if (substr($_SESSION["media[opener_input_field]"],0,14)=="REX_MEDIALIST_")
			{
				$opener_link = "<a href=javascript:addMedialist('".$fname."');>".$I18N->msg('pool_file_get')."</a>";
			}
		}
		
		if ($TPERM)
		{

			$cats_sel = new select;
			$cats_sel->set_style("' class='inp100");
			$cats_sel->set_size(1);
			$cats_sel->set_name("rex_file_category");
			$cats_sel->add_option($I18N->msg('pool_kats_no'),"0");
			$mediacat_ids = array();
			$rootCat = 0;
			if ($rootCats = OOMediaCategory::getRootCategories())
			{
			    foreach( $rootCats as $rootCat) {
			        add_mediacat_options_wperm( $cats_sel, $rootCat, $mediacat_ids);
			    }
			}
			$cats_sel->set_selected($rex_file_category);

			print "<table border=0 cellpadding=5 cellspacing=1 width=100%>\n";
			print "<tr><th align=left colspan=4>Detailinformationen | $opener_link</th></tr>";
			print "<form name=rex_file_cat action=index.php method=POST ENCTYPE=multipart/form-data>\n";
			print "<input type=hidden name=page value=medienpool>\n";
			print "<input type=hidden name=media_method value=edit_file>\n";
			print "<input type=hidden name=subpage value=detail>\n";
			print "<input type=hidden name=file_id value=$file_id>\n";
			print "<tr><td class=grey width=120>Titel:</td><td class=grey colspan=2><input type=text size=20 name=ftitle class=inp100 value='".htmlentities(stripslashes($ftitle))."'></td>";
	
			if ($ffiletype_ii)
			{
				$imgn = "../files/$fname width=$rfwidth"; 
				if ($thumbs && $thumbsresize && $rfwidth>199) $imgn = "../index.php?rex_resize=200w__$fname";
				echo "<td rowspan=12 width=220 align=center class=lgrey valign=top><br><img src=$imgn  border=0></td>";
			}
	
			print "</tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_category').":</td><td class=grey colspan=2>".$cats_sel->out()."</td></tr>\n";
			print "<tr><td class=grey valign=top>".$I18N->msg('pool_description').":</td><td class=grey colspan=2><textarea cols=30 rows=3 name=fdescription class=inp100>".(stripslashes($fdescription))."</textarea></td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_copyright').":</td><td class=grey colspan=2><input type=text size=20 name=fcopyright class=inp100 value='".(stripslashes($fcopyright))."'></td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_filename').":</td><td class=grey colspan=2><a href=../files/$fname target=_blank>$fname</a></td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_last_update').":</td><td class=grey colspan=2>".strftime($I18N->msg('datetimeformat'),$gf->getValue("updatedate"))." [".$gf->getValue("updateuser")."]</td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_created').":</td><td class=grey colspan=2>".strftime($I18N->msg('datetimeformat'),$gf->getValue("createdate"))." [".$gf->getValue("createuser")."]</td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_file_exchange').":</td><td class=grey colspan=2><input type=file name=file_new size=30></td></tr>";
			print "<tr><td class=grey>&nbsp;</td><td class=grey width=120><input type=submit value=\"".$I18N->msg('pool_file_update')."\"></td>\n";
			print "</form>\n";
			print "<form name=rex_file_cat action=index.php method=POST ENCTYPE=multipart/form-data>\n";
			print "<input type=hidden name=page value=medienpool>\n";
			print "<input type=hidden name=media_method value=delete_file>\n";
			print "<input type=hidden name=subpage value=detail>\n";
			print "<input type=hidden name=file_id value=$file_id>\n";
			print "<input type=hidden name=rex_file_category value=$rex_file_category>\n";
			print "<td class=grey><input type=submit value=\"".$I18N->msg('pool_file_delete')."\"  onclick='return confirm(\"".$I18N->msg('delete')." ?\")'></td></tr>\n";
			print "</form>";
			print "</table>\n";

		}else
		{
			$Cat = OOMediaCategory::getCategoryById($rex_file_category);
			
			print "<table border=0 cellpadding=5 cellspacing=1 width=100%>\n";
			print "<tr><th align=left colspan=4>Detailinformationen | $opener_link</th></tr>";
			print "<tr><td class=grey width=120>Titel:</td><td class=grey colspan=2>".htmlentities(stripslashes($ftitle))."</td>";
	
			if ($ffiletype_ii)
			{
				$imgn = "../files/$fname width=$rfwidth"; 
				if ($thumbs && $thumbsresize && $rfwidth>199) $imgn = "../index.php?rex_resize=200w__$fname";
				echo "<td rowspan=10 width=220 align=center class=lgrey valign=top><br><img src=$imgn  border=0></td>";
			}
	
			print "</tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_category').":</td><td class=grey colspan=2>".$Cat->getName()."</td></tr>\n";
			print "<tr><td class=grey valign=top>".$I18N->msg('pool_description').":</td><td class=grey colspan=2>".(stripslashes($fdescription))."</td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_copyright').":</td><td class=grey colspan=2>".(stripslashes($fcopyright))."</td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_filename').":</td><td class=grey colspan=2><a href=../files/$fname target=_blank>$fname</a></td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_last_update').":</td><td class=grey colspan=2>".strftime($I18N->msg('datetimeformat'),$gf->getValue("updatedate"))." [".$gf->getValue("updateuser")."]</td></tr>\n";
			print "<tr><td class=grey>".$I18N->msg('pool_created').":</td><td class=grey colspan=2>".strftime($I18N->msg('datetimeformat'),$gf->getValue("createdate"))." [".$gf->getValue("createuser")."]</td></tr>\n";
			print "</table>\n";
		}
	
	}else
	{
		$msg = $I18N->msg('pool_file_not_found');
		$subpage = "";
	}
}
















// ----- METHOD IMPORT IMPORT DIR
if($PERMALL && ($subpage=='import') && ($method=="do")){
	
	$FILE_PATH = $REX[MEDIAFOLDER]."/";
	
	$db = new sql;
	
	if (!function_exists('mime_content_type')) {
		function mime_content_type($f) {
			$f = escapeshellarg($f);
			return trim( `file -bi $f` );
		}
	}

	if(is_array($_POST[importfolder])){
		foreach($_POST[importfolder] as $var){
			if ($handle = opendir($FILE_PATH.$var)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
					
						unset($MEDIA);
						
						$THIS_PATH = $FILE_PATH.$var."/".$file;
						
						// prepare data for saveMedia();
						$MEDIA[name] = $file;
						$MEDIA[tmp_name] = $THIS_PATH;
						$MEDIA[type] = mime_content_type($THIS_PATH);
						$MEDIA[size] = filesize($THIS_PATH);
						$MEDIA_CATEGORY = $_POST[importcategory];
						//trägt dateinamen als title ein
						$RESULT = saveMedia($MEDIA,$MEDIA_CATEGORY,array(title=>$file));
						
						$cnt++;
					}
				}
				closedir($handle);
			}
		}
	}


    $msg = $cnt." File wurden erfolgreich importiert";

}

// ----- METHOD IMPORT LIST DIRS
if($PERMALL && $subpage=='import'){

	print "<form name=rex_file_import action=index.php method=post>\n";
	print "<input type=hidden name=page value=medienpool>\n";
	print "<input type=hidden name=subpage value=import>\n";
	print "<input type=hidden name=method value=do>\n";
	
	echo "<table width=100% cellpadding=5 cellspacing=1 border=0><tr><td class=grey><b class=head>".$I18N->msg('pool_import_list')."</td></tr><tr><td></td></tr></table>";

	if ($msg != "")
	{
		print "<table border=0 cellpadding=3 cellspacing=0 width=100%><tr><td width=20 class=warning><img src=pics/warning.gif width=16 height=16></td><td class=warning>$msg</td></tr><tr><td colspan=2></td></tr></table>";
		$msg = "";
	}

	print "<table border=0 cellpadding=5 cellspacing=1 width=100%>\n";
	
	print "<tr><th colspan=2 class=grey align=left>".$I18N->msg('pool_import_help')."</td></tr>";

	// Print Folders in Files Dir
	$FILE_PATH = $REX[MEDIAFOLDER]."/";
	if ($handle = opendir($FILE_PATH)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if(is_dir($FILE_PATH.$file)){
					print "<tr>";
					print "<td width=20 class=grey>";
					print "<input type=checkbox name=importfolder[] value='$file'>";
					print "</td>";
					print "<td class=grey>";
					print "&nbsp;";
					print $file;
					print "</td>";
					print "</tr>";
				}
			}
		}
		closedir($handle);
	}
	print "<tr><td colspan=2><br></td></tr>";
	print "<tr><th colspan=2 class=grey align=left>";
	print "<b>".$I18N->msg('pool_import_target_info')."</b>";
	print "</th></tr>";
	print "<tr><td colspan=2 class=grey><br></td></tr>";
	print "<tr><td colspan=2 class=grey>";

	print "<select name=importcategory>";
	$db = new sql();
	$db->debugsql = true;
	$file_cat = $db->get_array("SELECT * FROM rex_file_category ORDER BY name ASC");
	foreach($file_cat as $var){
		print "<option value=$var[id]>$var[name]</option>\n";
	}
	print "</select>";
	print "<br><br><input type=submit value='Import Folder'>";
	print "</td></tr>";
	print "</table>";
	print "</form>";

}










// *************************************** EXTRA FUNCTIONS

if($PERMALL && $media_method=='updatecat_selectedmedia')
{
	if(is_array($_POST["selectedmedia"])){

		foreach($_POST["selectedmedia"] as $file_id){
		
			$db = new sql;
			// $db->debugsql = true;
			$db->setTable('rex_file');
			$db->where("file_id='$file_id'");
			$db->setValue('category_id',$rex_file_category);
			$db->setValue("updatedate",time());
			$db->setValue("updateuser",$REX_USER->getValue("login"));
			$db->update();
		
			$msg = $I18N->msg('pool_selectedmedia_moved');
		}
	}else{
		$msg = $I18N->msg('pool_selectedmedia_error');
	}
}

if($PERMALL && $media_method=='delete_selectedmedia')
{
	
	if(is_array($_POST["selectedmedia"]))
	{
		
		foreach($_POST["selectedmedia"] as $file_id){
			
			//kopiet von Dateidetails delete_file
			$gf = new sql;
			$gf->setQuery("select * from rex_file where file_id='$file_id'");
			if ($gf->getRows()==1)
			{
				$file_name = $gf->getValue("filename");
			
				// check if file is in an article slice
				$file_search = '';
				
				for($c=1;$c<11;$c++){
					$file_search.= "OR file$c='$file_name' ";
					$file_search.= "OR value$c LIKE '%$file_name%' ";
				}
				
				$file_search = substr($file_search,2);
				$sql = "SELECT rex_article.name,rex_article.id FROM rex_article_slice LEFT JOIN rex_article on rex_article_slice.article_id=rex_article.id WHERE ".$file_search." AND rex_article_slice.article_id=rex_article.id";
				// $db->setQuery($sql);
				$res1 = $db->get_array($sql);
			
				$sql = "SELECT rex_article.name,rex_article.id FROM rex_article where file='$file_name'";
				$res2 = $db->get_array($sql);
			
				if(!is_array($res1) and !is_array($res2)){
			
					$sql = "DELETE FROM rex_file WHERE file_id = '$file_id'";
					$db->query($sql);
					unlink($REX[MEDIAFOLDER]."/".$file_name);
					$msg = $I18N->msg('pool_file_deleted');
					$subpage = "";
				}else{
					$msg = $I18N->msg('pool_file_delete_error_1');
					$msg.= $I18N->msg('pool_file_delete_error_2')."<br>";
					foreach($res1 as $var){
						$msg.=" | <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a>";
					}
					foreach($res2 as $var){
						$msg.=" | <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a>";
					}
					$msg .= " | ";
					$subpage = "";
				}
			}else
			{
				$msg = $I18N->msg('pool_file_not_found');
				$subpage = "";
			}
		}
	}else{
		$msg = $I18N->msg('pool_selectedmedia_error');
	}
}










// *************************************** SUBPAGE: "" -> MEDIEN ANZEIGEN

if($subpage == "")
{
	$cats_sel = new select;
	$cats_sel->set_style("width:150px;");
	$cats_sel->set_size(1);
	$cats_sel->set_name("rex_file_category");
	$cats_sel->add_option($I18N->msg('pool_kats_no'),"0");
	$mediacat_ids = array();
	$rootCat = 0;
	if ($rootCats = OOMediaCategory::getRootCategories())
	{
	    foreach( $rootCats as $rootCat) {
	        add_mediacat_options_wperm( $cats_sel, $rootCat, $mediacat_ids);
	    }
	}
	$cats_sel->set_selected($rex_file_category);

	echo "<table width=100% cellpadding=5 cellspacing=1 border=0 ><tr><td class=grey><b class=head>".$I18N->msg('pool_file_list')."</b></td></tr><tr><td></td></tr></table>";
	echo $cat_out;
	print "<table class=rex border=0 cellpadding=5 cellspacing=1 style='width:100%'>\n";
	print "<tr>
		<th align=left class=icon></th>
		<th align=left width=80><b>".$I18N->msg('pool_file_thumbnail')."</b></th>
		<th align=left><b>".$I18N->msg('pool_file_info')."/ ".$I18N->msg('pool_file_description')."</b></th>
		<th align=left width=150><b>".$I18N->msg('pool_file_functions')."</b></th>
		</tr>\n";

	if ($msg != "")
	{
		print "<tr class=warning><td align=center><img src=pics/warning.gif width=16 height=16></td><td class=warning colspan=3>$msg</td></tr>";
		$msg = "";
	}
	
	//deletefilelist und cat change
	print "<form name=rex_file_list action=index.php method=post ENCTYPE=multipart/form-data>\n";
	print "<input type=hidden name=page value=medienpool>\n";
	print "<input type=hidden name=rex_file_category value=$rex_file_category>\n";
	print "<input type=hidden name=media_method value=''>\n";
	
	$files = new sql;
	// $files->debugsql = 1;
	$files->setQuery("SELECT * FROM rex_file WHERE category_id=".$rex_file_category." ORDER BY updatedate desc");
	
	for ($i=0;$i<$files->getRows();$i++)
	{
	
		$file_id =   $files->getValue("file_id");
		$file_name = $files->getValue("filename");
		$file_oname = $files->getValue("originalname");
		$file_title = $files->getValue("title");
		$file_description = $files->getValue("description");
		$file_copyright = $files->getValue("copyright");
		$file_type = $files->getValue("filetype");
		$file_size = $files->getValue("filesize");
		$file_stamp = date("d-M-Y | H:i",$files->getValue("updatedate"))."h";
		$file_updateuser = $files->getValue("updateuser");

		// check if file exists
		// was passiert wenn nicht da ?
		// if(!file_exists($REX[MEDIAFOLDER]."/".$file_name)) continue;
	
		$file_ext = substr(strrchr($file_name,"."),1);
		$icon_src = "pics/mime_icons/mime-default.gif";
		if (in_array($file_ext,$doctypes))
		{
			$icon_src = "pics/mime_icons/mime-".$file_ext.".gif";
		}
		$thumbnail = "<img src=$icon_src width=44 height=38 border=0>";
		if (in_array($file_type,$imgtypes) && $thumbs)
		{
			$thumbnail = "<img src=../files/$file_name width=80 border=0>";
			if ($thumbsresize) $thumbnail = "<img src=../index.php?rex_resize=80w__$file_name width=80 border=0>";
		}

		// ----- get file size
		$size = $file_size;
		$kb = 1024;         // Kilobyte
		$mb = 1024 * $kb;   // Megabyte
		$gb = 1024 * $mb;   // Gigabyte
		$tb = 1024 * $gb;   // Terabyte
		if($size < $kb)	$file_size = $size." Bytes";
		else if($size < $mb) $file_size = round($size/$kb,2)." KBytes";
		else if($size < $gb) $file_size = round($size/$mb,2)." MBbytes";
		else if($size < $tb) $file_size = round($size/$gb,2)." GBytes";
		else $file_size = round($size/$tb,2)." TBbytes";
	
		if ($file_title == "") $file_title = "[".$I18N->msg('pool_file_notitle')."]";
		if ($file_description == "") $file_description = "[".$I18N->msg('pool_file_nodescription')."]";
	
		// ----- opener
		if($_SESSION["media[opener_input_field]"] == 'TINY')
		{
			$opener_link = "";
			if (in_array($file_type,$imgtypes))
			{
				$opener_link .= "<a href=javascript:insertImage('$file_name','$file_name','".$files->getValue("width")."','".$files->getValue("height")."');>".$I18N->msg('pool_image_get')."</a><br>";
			}
			$opener_link .= "<a href=javascript:insertLink('".$file_name."');>".$I18N->msg('pool_link_get')."</a>";

		}elseif($_SESSION["media[opener_input_field]"] != '')
		{
			$opener_link = "<a href=javascript:selectMedia('".$file_name."');>".$I18N->msg('pool_file_get')."</a>";
			if (substr($_SESSION["media[opener_input_field]"],0,14)=="REX_MEDIALIST_")
			{
				$opener_link = "<a href=javascript:addMedialist('".$file_name."');>".$I18N->msg('pool_file_get')."</a>";
			}
		}
			
		$ilink = "index.php?page=medienpool&subpage=detail&file_id=$file_id&rex_file_category=$rex_file_category";
		echo "<tr>";
	
		if ($PERMALL) echo "<td class=icon><input type=checkbox name=selectedmedia[] value='$file_id'></td>";
		else echo "<td class=icon>&nbsp;</td>";
		
		echo "<td style='background-color:#e6e6e6; text-align:center; vertical-align:middle;'><a href=$ilink>$thumbnail</a></td>";
		echo "<td valign=top class=grey><b><a href=$ilink>$file_title</a></b><br><br><b>$file_name [$file_size]</b><br>".nl2br(htmlentities($file_description))."<br><br>$file_stamp | $file_updateuser</td>";
		echo "<td valign=top class=grey>$opener_link</td>";
		echo "</tr>";
		$files->next();
	}
	
	if ($files->getRows()==0)
	{
		
		// ----- no items found
		// print "<tr><td colspan=5>&nbsp;</td>";
		print "<tr>
			<td class=grey align=center>&nbsp;</td>
			<td class=grey colspan=3>".$I18N->msg('pool_nomediafound')."</td>
			</tr>";
	}elseif($PERMALL)
	{
		print "</table>";
		
		print "<table class=rex border=0 cellpadding=5 cellspacing=1 style='width:100%'>\n";
		// ----- move and delete selected items
		print "<tr>
			<td align=center class=icon><!-- ".$I18N->msg('pool_select_all')." --><input type=checkbox name=checkie value=0 onClick=\"SetAllCheckBoxes('rex_file_list','selectedmedia[]',this)\"></td>";
		
		$filecat = new sql();
		$filecat->setQuery("SELECT * FROM rex_file_category ORDER BY name ASC LIMIT 1");
		if ($filecat->getRows() > 0)
		{	
			print "
			<!-- <td class=grey><b>".$I18N->msg('pool_selectedmedia')."</b>&nbsp;</td>-->
			<td class=grey>".$cats_sel->out()."</td>
			<td class=grey><input type=submit value=\"".$I18N->msg('pool_changecat_selectedmedia')."\" onclick=\"document.rex_file_list.media_method.value='updatecat_selectedmedia';\"></td>
			<td class=grey width=150><input type=submit value=\"".$I18N->msg('pool_delete_selectedmedia')."\" onclick=\"document.rex_file_list.media_method.value='delete_selectedmedia';return confirm('".$I18N->msg('delete')." ?');\"></td>
			";
		}else
		{
			print "
			<td class=grey>&nbsp;</td>
			<td class=grey width=150><input type=submit value=\"".$I18N->msg('pool_delete_selectedmedia')."\" onclick=\"document.rex_file_list.media_method.value='delete_selectedmedia';return confirm('".$I18N->msg('delete')." ?');\" ></td>
			";
		}
		print "</tr>";
	}
	print "</form>";
	print "</table>";
}

echo "</body></html>";

?>