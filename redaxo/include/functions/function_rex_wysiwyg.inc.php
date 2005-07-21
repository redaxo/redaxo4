<?php

##################################################################
#
#      Medienpool Functions - vscope new media design
#
##################################################################

class rex_wysiwyg_editor {

	var $id;
    var $content;
    var $width = '';
    var $height = '';
    var $stylesheet = '';
    var $styles = '';
    var $lang = '';
    var $buttonrow1 = '';
    var $buttonrow2 = '';
    var $buttonrow3 = 'empty';
    var $buttonrow4 = 'empty';

    function show(){
        echo MEDIA_HTMLAREA(
        		$this->id,
                $this->content,
                $this->width,
                $this->height,
                $this->stylesheet,
                $this->styles,
                $this->lang,
                $this->buttonrow1,
                $this->buttonrow2,
                $this->buttonrow3,
                $this->buttonrow4
        );
    }

}

function MEDIA_HTMLAREA($VALUE_ID=1,$CONTENT,$WIDTH='',$HEIGHT='',$STYLE_SHEET='',$STYLES='',$LANG='',$BUTTONROW1='',$BUTTONROW2='',$BUTTONROW3='empty',$BUTTONROW4='empty'){

        // lang = de oder en

        global $TINYMCE;

        if($WIDTH =='') 		$WIDTH="100%";
        if($HEIGHT =='') 		$HEIGHT="300px";
        if($STYLE_SHEET =='') 	$STYLE_SHEET="css/style.css";
        if($STYLES =='') 		$STYLES="all";
        if($LANG =='') 			$LANG="de";

		// All buttons
        /*

        editor buttons:
        bold, italic, underline, strikethrough, justifyleft, justifycenter, justifyright, justifyfull,
        styleselect, bullist, numlist, outdent, indent, undo,redo, link, unlink, image,
        cleanup, help, code, table, row_before, row_after, delete_row, separator, rowseparator,
        col_before, col_after, delete_col, hr, removeformat, sub, sup, formatselect, fontselect,
        fontsizeselect, forecolor, charmap, visualaid, spacer, cut, copy, paste,

        redaxo buttons:
        linkHack,pasteRichtext,insertEmail
        

        */

        if($BUTTONROW1==""){
        	$BUTTONROW1 = "styleselect,separator,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,outdent,indent";
        }
        if($BUTTONROW2==""){
        	$BUTTONROW2 = "link,linkHack,unlink,insertEmail,separator,image,separator,removeformat,pasteRichtext,code";
        }
        if($BUTTONROW3==""){
        	$BUTTONROW3 = "tablecontrols, separator, visualaid";
        }
        if($BUTTONROW4==""){
        	$BUTTONROW4 = "rowseparator,formatselect,fontselect,fontsizeselect,forecolor,charmap";
        }

        // tiny mce init
        if($TINYMCE!="done"){

				$print .= '
	            <!-- tinyMCE -->
	            <script language="javascript" type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
	            <script language="javascript" type="text/javascript">
	                tinyMCE.init({
	                    language : "'.$LANG.'",
	                    mode : "specific_textareas",
                        plugins : "redaxo,table,emotions,preview",
                        // plugins : "redaxo,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,searchreplace,print",
	                    theme : "advanced",
                        theme_advanced_buttons1 : "'.$BUTTONROW1.'",
                        theme_advanced_buttons2 : "'.$BUTTONROW2.'",
                        theme_advanced_buttons3 : "'.$BUTTONROW3.'",
                        theme_advanced_buttons3_add : "'.$BUTTONROW4.'",
	                    theme_advanced_toolbar_location : "top",
	                    theme_advanced_toolbar_align : "left",
	                    theme_advanced_path_location : "bottom",
	                    content_css : "'.$STYLE_SHEET.'",
	                    plugin_insertdate_dateFormat : "%d.%m.%Y",
	                    plugin_insertdate_timeFormat : "%H:%M:%S",
	                    extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	                    external_link_list_url : "example_link_list.js",
	                    external_image_list_url : "example_image_list.js",
	                    flash_external_list_url : "example_flash_list.js",
	                    insertimage_callback : "insertMediaPool",
	                    insertlink_callback : "insertIntLink"
	                    //file_browser_callback : "fileBrowserCallBack"
	                });

	                function fileBrowserCallBack(field_name, url, type) {
	                    // This is where you insert your custom filebrowser logic
	                    alert("Filebrowser callback: " + field_name + "," + url + "," + type);
	                }

					// custom redaxo callback functions
					function insertMediaPool(src, alt, border, hspace, vspace, width, height, align){
						newWindow( "rexmediapopup", "index.php?page=medienpool&opener_input_field=TINY", 660,500,",status=yes,resizable=yes");
					}
					function insertIntLink(href, target){
						newWindow( "rexlinkpopup", "index.php?page=linkmap&HTMLArea=TINY", 660,500,",status=yes,resizable=yes");
					}

	                function tinyMCEEmail(){
	                        var email = prompt("Geben Sie eine Emailadresse ein","");
	                        alert(tinyMCE.getContent());
	                        tinyMCE.execCommand("mceInsertContent", false, "<a href=mailto:"+email+">"+email+"</a>");
	                }

	            </script>
	            <!-- /tinyMCE -->
	            ';

				$GLOBALS[TINYMCE] = 'done';

        }


        $print .= '

        <textarea id="VALUE['.$VALUE_ID.']" name="VALUE['.$VALUE_ID.']" style="width:'.$WIDTH.';height:'.$HEIGHT.'" rows="15" mce_editable="true">'.$CONTENT.'</textarea>

        ';

		return $print;

}

?>