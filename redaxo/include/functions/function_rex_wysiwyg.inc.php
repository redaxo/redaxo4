<?php

##################################################################
#
#      Medienpool Functions - vscope new media design
#
##################################################################

function MEDIA_HTMLAREA($VALUE_ID=1,$CONTENT,$WIDTH='',$HEIGHT='',$STYLE_SHEET='',$STYLES='',$LANG='',$BUTTONROW1='',$BUTTONROW2='',$BUTTONROW3='empty',$BUTTONROW4='empty', $PLUGINS = ''){

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

        if ($PLUGINS=="redaxo_default"){
            $PLUGINS = "redaxo,table,emotions,preview";
        } else if ( $PLUGINS==""){
            $PLUGINS = "redaxo,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,searchreplace,print";
        }

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
                    var sDocumentBase = "'. $_SERVER['HTTP_HOST'] .'";
                    if (navigator.appName == "Microsoft Internet Explorer") {
                       sDocumentBase += "/";
                    }

	                tinyMCE.init({
                        document_base_url: sDocumentBase,
	                    language : "'.$LANG.'",
                        auto_focus : "VALUE['.$VALUE_ID.']",
//                        relative_urls : false,
	                    mode : "specific_textareas",
                        plugins : "'. $PLUGINS .'",
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
	                    //external_link_list_url : "example_link_list.js",
	                    //external_image_list_url : "example_image_list.js",
	                    //flash_external_list_url : "example_flash_list.js",
	                    insertimage_callback : "insertMediaPool",
	                    insertlink_callback : "insertIntLink",
                        urlconverter_callback : "rexURLConverter",
	                    extended_valid_elements : ""
	                    +"a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name"
	                      +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rel|rev"
	                      +"|shape<circle?default?poly?rect|style|tabindex|title|target|type],"
	                    +"abbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"acronym[class|dir<ltr?rtl|id|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"address[class|align|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title],"
	                    +"applet[align<bottom?left?middle?right?top|alt|archive|class|code|codebase"
	                      +"|height|hspace|id|name|object|style|title|vspace|width],"
	                    +"area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref"
	                      +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup"
	                      +"|shape<circle?default?poly?rect|style|tabindex|title|target],"
	                    +"base[href|target],"
	                    +"basefont[color|face|id|size],"
	                    +"bdo[class|dir<ltr?rtl|id|lang|style|title],"
	                    +"big[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"blockquote[dir|style|cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
	                      +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
	                      +"|onmouseover|onmouseup|style|title],"
	                    +"body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|onunload|style|title|text|vlink],"
	                    +"br[class|clear<all?left?none?right|id|style|title],"
	                    +"button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur"
	                      +"|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown"
	                      +"|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type"
	                      +"|value],"
	                    +"caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"center[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"cite[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"code[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
	                      +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
	                      +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
	                      +"|valign<baseline?bottom?middle?top|width],"
	                    +"colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl"
	                      +"|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
	                      +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
	                      +"|valign<baseline?bottom?middle?top|width],"
	                    +"dd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"del[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title],"
	                    +"dfn[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"dir[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title],"
	                    +"div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"dl[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title],"
	                    +"dt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"em/i[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"fieldset[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"font[class|color|dir<ltr?rtl|face|id|lang|size|style|title],"
	                    +"form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang"
	                      +"|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit"
	                      +"|style|title|target],"
	                    +"frame[class|frameborder|id|longdesc|marginheight|marginwidth|name"
	                      +"|noresize<noresize|scrolling<auto?no?yes|src|style|title],"
	                    +"frameset[class|cols|id|onload|onunload|rows|style|title],"
	                    +"h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"head[dir<ltr?rtl|lang|profile],"
	                    +"hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|size|style|title|width],"
	                    +"html[dir<ltr?rtl|lang|version],"
	                    +"iframe[align<bottom?left?middle?right?top|class|frameborder|height|id"
	                      +"|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style"
	                      +"|title|width],"
	                    +"img[align<bottom?left?middle?right?top|alt|border|class|dir<ltr?rtl|height"
	                      +"|hspace|id|ismap|lang|longdesc|name|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|src|style|title|usemap|vspace|width],"
	                    +"input[accept|accesskey|align<bottom?left?middle?right?top|alt"
	                      +"|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang"
	                      +"|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
	                      +"|readonly<readonly|size|src|style|tabindex|title"
	                      +"|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text"
	                      +"|usemap|value],"
	                    +"ins[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title],"
	                    +"isindex[class|dir<ltr?rtl|id|lang|prompt|style|title],"
	                    +"kbd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"label[accesskey|class|dir<ltr?rtl|for|id|lang|onblur|onclick|ondblclick"
	                      +"|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
	                      +"|onmouseover|onmouseup|style|title],"
	                    +"legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang"
	                      +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"li[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title|type"
	                      +"|value],"
	                    +"link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|rel|rev|style|title|target|type],"
	                    +"map[class|dir<ltr?rtl|id|lang|name|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"menu[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title],"
	                    +"meta[content|dir<ltr?rtl|http-equiv|lang|name|scheme],"
	                    +"noframes[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"noscript[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"object[align<bottom?left?middle?right?top|archive|border|class|classid"
	                      +"|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name"
	                      +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|standby|style|tabindex|title|type|usemap"
	                      +"|vspace|width],"
	                    +"ol[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|start|style|title|type],"
	                    +"optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick"
	                      +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
	                      +"|onmouseover|onmouseup|selected<selected|style|title|value],"
	                    +"p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"param[id|name|type|value|valuetype<DATA?OBJECT?REF],"
	                    +"pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
	                      +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
	                      +"|onmouseover|onmouseup|style|title|width],"
	                    +"q[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"s[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"samp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"script[charset|defer|language|src|type],"
	                    +"select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name"
	                      +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style"
	                      +"|tabindex|title],"
	                    +"small[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"span[align|class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title],"
	                    +"strike[class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title],"
	                    +"strong/b[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"style[dir<ltr?rtl|lang|media|title|type],"
	                    +"sub[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"sup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title],"
	                    +"table[align<center?left?right|bgcolor|border|cellpadding|cellspacing|class"
	                      +"|dir<ltr?rtl|frame|height|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rules"
	                      +"|style|summary|title|width],"
	                    +"tbody[align<center?char?justify?left?right|char|class|charoff|dir<ltr?rtl|id"
	                      +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
	                      +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
	                      +"|valign<baseline?bottom?middle?top],"
	                    +"td[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
	                      +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
	                      +"|style|title|valign<baseline?bottom?middle?top|width],"
	                    +"textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name"
	                      +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
	                      +"|readonly<readonly|rows|style|tabindex|title],"
	                    +"tfoot[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
	                      +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
	                      +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
	                      +"|valign<baseline?bottom?middle?top],"
	                    +"th[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
	                      +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
	                      +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
	                      +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
	                      +"|style|title|valign<baseline?bottom?middle?top|width],"
	                    +"thead[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
	                      +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
	                      +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
	                      +"|valign<baseline?bottom?middle?top],"
	                    +"title[dir<ltr?rtl|lang],"
	                    +"tr[abbr|align<center?char?justify?left?right|bgcolor|char|charoff|class"
	                      +"|rowspan|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title|valign<baseline?bottom?middle?top],"
	                    +"tt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"u[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
	                      +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
	                    +"ul[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
	                      +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
	                      +"|onmouseup|style|title|type],"
	                    +"var[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
	                      +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
	                      +"|title]"
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

                    function rexURLConverter(url, node, on_save) {
                        // nichts tun
                        return url;
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