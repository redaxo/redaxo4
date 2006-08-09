<?php
global $TINY2;
$TINY2['counter']=0;
$TINY2['script']=0;
$TINY2['boxes']=0;
$TINY2['address']=$REX['HTDOCS_PATH'];


class tiny2editor {

  var $editorCSS = "../files/tinymce/content.css";
  var $advimageCSS="";
  var $disable="";
  var $plugins="advlink,advimage,emotions,iespell,table,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,redaxo";
  var $validhtml="img[class|style|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]";
  var $buttons1="styleselect,separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,outdent,indent";
  var $buttons2="link,unlink,insertEmail,separator,image,separator,removeformat,paste,pastetext,pasteword,code";
  var $buttons3="";
  var $buttons4="";
  var $lang="de";

  var $address="";

  var $width="100%";
  var $height=300;
  var $content;
  var $id;


    function show() {
    $this->address=$_SERVER['SCRIPT_URL'];
    $splitURL=split("/redaxo/",$this->address);

    $this->address=$splitURL[0];


    echo "\n\n<!-- ------------------------------ -->";

    if ($GLOBALS['TINY2']['counter']==0) {
      $this->id=0;
    } else {
      $this->id=$GLOBALS['TINY2']['counter'];
    }
    $GLOBALS['TINY2']['counter']=$GLOBALS['TINY2']['counter']+1;


    if ($GLOBALS['TINY2']['script']!=1) {
      if(strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")) {
        echo "\n".'<script language="javascript" type="text/javascript" src="../files/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>';
      } else {
        echo "\n".'<script language="javascript" type="text/javascript" src="../files/tinymce/jscripts/tiny_mce/tiny_mce_gzip.php"></script>';
      }

      $GLOBALS['TINY2']['script']="1";
    }

    if ($GLOBALS['TINY2']['boxes']!=1) {
      echo "\n".'<div name="REX_FORM"><input type="hidden" name="LINK[1]" /><input type="hidden" name="LINK_NAME[1]" /><input type="hidden" name="REX_MEDIA_1" /></div>';
      $GLOBALS['TINY2']['boxes']="1";
    }

    echo "\n".'<script language="javascript" type="text/javascript">'."\n";
    echo 'var sDocumentBase = "'.$this->address.'/"'.';'."\n";

    echo 'tinyMCE.init({'."\n";
    echo 'document_base_url: sDocumentBase,'."\n";
    echo 'advimage_styles : "'.$this->advimageCSS.'",'."\n";
    echo 'content_css : "'.$this->editorCSS.'",'."\n";
    echo 'mode : "exact",'."\n";
    echo 'elements : "tiny2e'.$this->id.'",'."\n";
    echo 'theme : "advanced",'."\n";
    echo 'advimage_image_browser_callback : "fileBrowserCallBack",'."\n";
    echo 'plugins : "'.$this->plugins.'",'."\n";
    echo 'theme_advanced_disable : "'.$this->disable.'",'."\n";
    echo 'theme_advanced_buttons1 : "'.$this->buttons1.'",'."\n";
    echo 'theme_advanced_buttons2 : "'.$this->buttons2.'",'."\n";
    echo 'theme_advanced_buttons3 : "'.$this->buttons3.'",'."\n";
    echo 'theme_advanced_buttons4 : "'.$this->buttons4.'",'."\n";
    echo 'theme_advanced_toolbar_location : "top",'."\n";
    echo 'theme_advanced_toolbar_align : "left",'."\n";
    echo 'plugin_insertdate_dateFormat : "%Y-%m-%d",'."\n";
    echo 'plugin_insertdate_timeFormat : "%H:%M:%S",'."\n";
    echo 'inline_styles: true,'."\n";
    echo 'extended_valid_elements : "'.$this->validhtml.'",'."\n";
    echo 'advlink_file_browser_callback:"insertIntLink",'."\n";
    echo 'paste_auto_cleanup_on_paste : true,'."\n";
    echo 'paste_convert_headers_to_strong : true,'."\n";
    echo 'convert_fonts_to_spans : true,'."\n";
    echo 'cleanup_on_startup : true,'."\n";
    echo 'remove_linebreaks : true,'."\n";
    echo 'language: "'.$this->lang.'",'."\n";
    echo 'apply_source_formatting : false,'."\n";
    echo 'accessibility_warnings : true'."\n";
    echo '});'."\n";

    echo 'function fileBrowserCallBack(field_name, url, type, win) {'."\n";
    echo 'newWindow( "rexmediapopup", "../../../../../../redaxo/index.php?page=medienpool&opener_input_field=REX_MEDIA_1",660,500,",status=yes,resizable=yes");'."\n";
    echo '}'."\n";

    echo 'function insertIntLink(href, target){'."\n";
    echo 'newWindow( "rexlinkpopup", "../../../../../../redaxo/index.php?page=linkmap&opener_input_field=1",660,500,",status=yes,resizable=yes");'."\n";
    echo '}'."\n";
    echo '</script>';
    echo "\n<!-- ------------------------------ -->\n\n";

    echo '<textarea name="VALUE[1]" class="tiny2" id="tiny2e'.$this->id.'" style="width:'.$this->width.';height:'.$this->height.';">'.$this->content.'</textarea>';
  }
}
?>