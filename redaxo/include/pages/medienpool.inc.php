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

$REX[RESIZE]=true;
$REX[MAGICK]="/usr/bin/convert";

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

session_start();

// SET DEFAULT FILE CAT
if($_GET[rex_file_category]==''){
   $_GET[rex_file_category] = 0;
}
$rex_file_category = $_GET[rex_file_category];

// DEFAULT LINKS
$DEFAULT_LINK  = "index.php?page=medienpool&opener_input_field=".$opener_input_field;
$DEFAULT_CAT_LINK  = "index.php?page=medienpool&rex_file_category=".$rex_file_category."&opener_input_field=".$opener_input_field;

// CHECK IF HTMLAREA OR FIELD
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
} else {
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

// METHOD ADD FILE
if($_POST[media_method]=='add_file'){
        // function in function.rex_medienpool.inc.php
        media_savefile($_FILES[file_new],$rex_file_category);
        $msg = $I18N->msg('pool_file_saved');
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
echo "<html><head><title>".$REX[SERVERNAME]." - ".$I18N->msg('pool_media')."</title>
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
<tr><td colspan=3 class=grey align=right>".$REX[SERVERNAME]."</td></tr>
<tr><td class=greenwhite><b>".$I18N->msg('pool_media')."</b></td></tr></table>";


####### CATEGORIES TABLE
$db = new sql();
$file_cat = $db->get_array("SELECT * FROM rex_file_category ORDER BY name ASC");

print "<form name=rex_file_cat action=\"".$DEFAULT_CAT_LINK."\" method=POST>\n";
print "<input type=hidden name=media_method value=add_file_cat>\n";

print "<table border=0 cellpadding=5 cellspacing=0 width=100%>\n";
print "<tr><td width=100 class=grey>\n";
print "<b>".$I18N->msg('pool_kats').":<br>\n";
print "<select name=rex_file_category onChange=\"location.href='".$DEFAULT_LINK."&rex_file_category='+this[this.selectedIndex].value;\">\n";
if(is_array($file_cat)){
        print "<option value=0>".$I18N->msg('pool_kats_no')."</option>\n";
        foreach($file_cat as $var){
                if($var[id] == $_GET[rex_file_category]): $select="selected"; else: $select=""; endif;
                print "<option value=$var[id] $select>$var[name]</option>\n";
        }
} else {
        print "<option value=0>".$I18N->msg('pool_kats_no')."</option>\n";
}
print "</select>\n";
print "</td>\n";
print "<td class=grey>\n";
print "<b>".$I18N->msg('pool_kat_new')."<br>\n";
print "<input type=field name=rex_file_category_new size=20> <input type=submit value=anlegen>\n";
print "</td>\n";
print "</tr>\n";
print "</table>\n";
print "</form>\n";
#######

####### UPLOAD TABLE
print "<form name=rex_file_cat action=\"".$DEFAULT_CAT_LINK."\" method=POST ENCTYPE=multipart/form-data>\n";
print "<input type=hidden name=media_method value=add_file>\n";
print "<input type=hidden name=rex_file_category value=$rex_file_category>\n";
print "<table border=0 cellpadding=5 cellspacing=0 width=100%>\n";
print "<tr><td class=greenwhite>\n";
print "<b>".$I18N->msg('pool_file_insert')."<br>\n";
print "</td>\n";
print "</tr>\n";
print "<tr><td class=grey>\n";
print "<b>".$I18N->msg('pool_file_choose')."<br>";
print "<input type=file name=file_new size=30> ";
print "<input type=submit value=\"".$I18N->msg('pool_file_upload')."\">";
print "</td>\n";
print "</tr>\n";
print "</table>\n";
print "</form>\n";
#######

####### MESSAGE
print "<font color=#FF0000>";
print $msg;
print "</font>";

####### FILE LIST
print "<table border=0 cellpadding=5 cellspacing=0 width=100%>\n";
print "<tr><td class=greenwhite colspan=4>\n";
print "<b>".$I18N->msg('pool_file_list')."<br>\n";
print "</td>\n";
print "</tr>\n";
$files = new sql;
//$files->debugsql = 1;
$files->setQuery("SELECT * FROM rex_file WHERE re_file_id =".$_GET[rex_file_category]." ORDER BY filename");

$tr = 0;
for ($i=0;$i<$files->getRows();$i++)
{

        $file_name = $files->getValue("filename");
        $file_id =   $files->getValue("file_id");

        // get file icon
        $icon_src = "pics/pool_file_icons/file.gif";
        $file_ext = substr(strrchr($file_name,"."),1);
        if(in_array($file_ext,$file_icons)){
                $icon_src = "pics/pool_file_icons/$file_ext.gif";
        }

        // get file size
        $file_size = getfilesize(filesize($REX[MEDIAFOLDER]."/".$file_name));

        $image_info = "<br><br><br><br><br><br>";

        $is_image = false;

        // image check
        $image_ext = "jpeg|png|gif|jpg";
        if(eregi($image_ext,$file_name)){

                $is_image = true;

                $icon_src = $REX[MEDIAFOLDER]."/".$file_name;
                $image_size = getimagesize($REX[MEDIAFOLDER]."/".$file_name);

                // IMAGEMAGICK RESIZE
                if($REX[RESIZE]==true){

                                $image_info = "<form name=resize_$file_id action=".$DEFAULT_CAT_LINK." method=POST>";
                                $image_info.= "<input type=hidden name=media_method value=resize_image>";
                                $image_info.= "<input type=hidden name=file_id value=$file_id>";
                                $image_info.= "<br>";
                                $image_info.= "<table border=0 cellpadding=0 cellspacing=0 width=150>\n";
                                $image_info.= "<tr><td width=40>".$I18N->msg('pool_img_width').": </td><td width=65><input type=field name=width value=$image_size[0] size=5> px</td>";
                                $image_info.= "<td rowspan=2 valign=middle><a href=javascript:void(0) onCLick=resize_$file_id.submit();>".$I18N->msg('pool_img_resize')."</a></td></tr>";
                                $image_info.= "<tr><td>".$I18N->msg('pool_img_height').":</td><td><input type=field name=height value=$image_size[1] size=5> px</td></tr>";
                                $image_info.= "</table>";
                                $image_info.= "</form>";

                } else {

                                $image_info = "<br><br>";
                                $image_info.= "<table border=0 cellpadding=0 cellspacing=0 width=150>\n";
                                $image_info.= "<tr><td width=40>".$I18N->msg('pool_img_width').": </td><td width=65>$image_size[0] px</td></tr>";
                                $image_info.= "<tr><td width=40>".$I18N->msg('pool_img_height').": </td><td width=65>$image_size[1] px</td></tr>";
                                $image_info.= "</table><br><br>";

                }

        }

        if($is_image){
                $link = "href=javascript:void(0) onClick=openImage('$file_name')";
        } else {
                $link = "href=$REX[MEDIAFOLDER]/$file_name target=_blank";
        }

        if($tr == 0) print "<tr>";

        echo "<td class=grey valign=top width=100>\n";
        echo "<br><a $link><img src=\"$icon_src\" width=100 height=100 border=1 class=mediapool>\n</a>";
        echo "</td>\n";
        echo "<td class=grey valign=top>\n";
        echo "<br><a $link>".$file_name."</a>";
        echo "<br>".$I18N->msg('pool_file_size').": $file_size";
        echo $image_info;

        // HTMLAREA / INPUT FIELD CHECK
        if($_SESSION[myarea]==''){
           echo "<a href=javascript:void(0) onClick=selectMedia('".$file_name."');>".$I18N->msg('pool_file_ins')."</a> | ";
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
           echo "<a href=javascript:void(0) onClick=\"insertHTMLArea('$html_source');\">".$I18N->msg('pool_file_ins')."</a> | ";
        }

        echo "<a href=".$DEFAULT_CAT_LINK."&file_delete=". $files->getValue("file_id").">".$I18N->msg('pool_file_delete')."</a>";
        echo "</td>";

        $files->next();

        $tr++;

        if($tr == 2 ){
                print "</tr>";
                $tr = 0;
        }
}

if($tr == 1){
        print "<td class=grey>&nbsp;</td><td class=grey>&nbsp;</td></tr>\n";
}
#######

echo "</table></body></html>";

?>
