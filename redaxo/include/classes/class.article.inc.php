<?

// class article 1.0 [redaxo]
//
// erstellt 01.12.2003
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

// changed 02.04.04 Carsten Eckelman <careck@circle42.com>
//   * Internationalisation with $I18N hash
//   * to use internationalised messages just global $I18N and write $I18N->msg('message_key')
//   * add the message to text_<language>.inc.php eg. $I18N->msg('submit') = "abschicken";

class article
{

        var $mediafolder;
        var $mediafolder_www;
        var $slice_id;
        var $article_id;
        var $mode;
        var $article_content;
        var $function;
        var $eval;
        var $category_id;
        var $CONT;
        var $template_id;
        var $ViewSliceId;
        var $contents;
        var $setanker;
        var $save;

        function article()
        {

                $this->article_id = 0;
                $this->template_id = 0;
                $this->slice_id = 0;
                $this->mode = "view";
                $this->article_content = "";
                $this->eval = FALSE;
                $this->setanker = true;
                unset($save);

                // AUSNAHME: modul auswählen problem
                if (strpos($_SERVER["HTTP_USER_AGENT"],"Mac") and strpos($_SERVER["HTTP_USER_AGENT"],"MSIE") ) $this->setanker = FALSE;
        }

        function setSliceId($value)
        {
                $this->slice_id = $value;
        }

        function setArticleId($article_id)
        {
                global $REX;

                $article_id = $article_id+0;
                $this->article_id = $article_id+0;

                if (!$REX[GG])
                {

                        // ---------- select article
                        $this->ARTICLE = new sql;
                        $this->ARTICLE->setQuery("select * from rex_article where rex_article.id='$article_id'");

                        if ($this->ARTICLE->getRows() == 1)
                        {
                                $this->template_id = $this->ARTICLE->getValue("rex_article.template_id");
                                $this->category_id = $this->ARTICLE->getValue("rex_article.re_id");
                                return TRUE;
                        }else
                        {
                                $this->article_id = 0;
                                $this->template_id = 0;
                                $this->category_id = 0;
                                return FALSE;
                        }

                }else
                {
                        if (@include $REX[INCLUDE_PATH]."/generated/articles/".$article_id.".article") return TRUE;
                        else return FALSE;
                }
        }

        function setMode($mode)
        {
                $this->mode = $mode;
        }

        function setFunction($function)
        {
                $this->function = $function;
        }

        function setEval($value)
        {
                if ($value) $this->eval = TRUE;
                else $this->eval = FALSE;
        }

        function getValue($value)
        {
                global $REX;
                if ($REX[GG]) return $REX[ART][$this->article_id][$value];
                else return $this->ARTICLE->getValue($value);
        }

        function getArticle()
        {
                global $module_id,$FORM,$REX_USER,$REX,$REX_SESSION,$I18N;

                if ($REX[GG])
                {
                        if ($this->article_id != 0)
                        {
                                $this->contents = "";
                                if ($REX[BC]) $filename = $REX[INCLUDE_PATH]."/generated/articles/".$this->article_id.".bcontent";
                                else $filename = $REX[INCLUDE_PATH]."/generated/articles/".$this->article_id.".content";

                                if ($fd = @fopen ($filename, "r"))
                                {
                                        $this->contents = fread ($fd, filesize ($filename));
                                        fclose ($fd);
                                        eval($this->contents);
                                }
                        }
                }else
                        {

                        if ($this->article_id != 0)
                        {
                                // ---------- select alle slices eines artikels
                                $this->CONT = new sql;
                                // $this->CONT->debugsql = 1;
                                $this->CONT->setQuery("select rex_modultyp.name, rex_modultyp.ausgabe, rex_modultyp.bausgabe, rex_modultyp.eingabe, rex_modultyp.php_enable, rex_modultyp.html_enable, rex_article_slice.*, rex_article.re_id
                                                        from
                                                                rex_article_slice
                                                        left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id
                                                        left join rex_article on rex_article_slice.article_id=rex_article.id
                                                        where
                                                                rex_article_slice.article_id='".$this->article_id."'
                                                        order by
                                                                rex_article_slice.re_article_slice_id");

                                // ---------- SLICE IDS/MODUL SETZEN
                                for ($i=0;$i<$this->CONT->getRows();$i++)
                                {
                                        $RE_CONTS[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_article_slice.id");
                                        if ($REX[BC]) $RE_MODUL_OUT[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.bausgabe");
                                        else $RE_MODUL_OUT[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.ausgabe");
                                        $RE_MODUL_IN[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.eingabe");
                                        $RE_MODUL_NAME[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.name");
                                        $RE_MODUL_PHP[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.php_enable");
                                        $RE_MODUL_HTML[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.html_enable");
                                        $RE_C[$this->CONT->getValue("re_article_slice_id")] = $i;
                                        $this->CONT->nextValue();
                                }

                                // ---------- moduleselect
                                if($this->mode=="edit")
                                {
                                        // auslesen ob php/html rechte
                                        $add_sql = "";

                                        $MODULE_PERM[php] = $REX_USER->isValueOf("rights","module[php]");
                                        $MODULE_PERM[html]= $REX_USER->isValueOf("rights","module[html]");

                                        if (!$MODULE_PERM[php]) $add_sql = "where php_enable='0'";
                                        if (!$MODULE_PERM[html])
                                        {
                                                if ($add_sql != "") $add_sql .= " and html_enable='0'";
                                                else $add_sql = "where html_enable='0'";
                                        }

                                        $MODULE = new sql;
                                        $MODULE->setQuery("select * from rex_modultyp $add_sql order by name");

                                        $MODULESELECT = new select;
                                        $MODULESELECT->set_name("module_id");
                                        $MODULESELECT->set_size(1);
                                        $MODULESELECT->set_style("width:100%;' onchange='this.form.submit();");

                                        $MODULESELECT->add_option("----------------------------  ".$I18N->msg("add_block"),'');

                                        for ($i=0;$i<$MODULE->getRows();$i++)
                                        {
                                                $MODULESELECT->add_option($MODULE->getValue("name"),$MODULE->getValue("id"));
                                                $MODULE->next();
                                        }
                                }

                                // ---------- SLICE IDS SORTIEREN UND AUSGEBEN
                                $I_ID = 0;
                                $PRE_ID = 0;
                                $this->article_content = "";
                                $this->CONT->resetCounter();

                                for ($i=0;$i<$this->CONT->getRows();$i++)
                                {

                                        // ------------- EINZELNER SLICE - AUSGABE
                                        $this->CONT->counter = $RE_C[$I_ID];
                                        $slice_content = "";
                                        $SLICE_SHOW = TRUE;

                                        if($this->mode=="edit")
                                        {

                                                $this->ViewSliceId = $RE_CONTS[$I_ID];

                                                $amodule = "
                                                        <table cellspacing=0 cellpadding=5 border=0 width=100%>
                                                        <form action=index.php";
                                                if ($this->setanker) $amodule .= "#addslice";
                                                $amodule.= " method=get>
                                                        <input type=hidden name=article_id value=$this->article_id>
                                                        <input type=hidden name=page value=content>
                                                        <input type=hidden name=mode value=$this->mode>
                                                        <input type=hidden name=slice_id value=$I_ID>
                                                        <input type=hidden name=function value=add>
                                                        <tr>
                                                        <td class=dblue>".$MODULESELECT->out()."</td>
                                                        </tr></form></table>";

                                                $fmenue  = "
                                                        <a name=slice$RE_CONTS[$I_ID]></a>
                                                        <table width=100% cellspacing=0 cellpadding=5 border=0>
                                                        <tr>
                                                        <td class=blue width=380><b>$RE_MODUL_NAME[$I_ID]</b></td>
                                                        <td class=llblue align=center><a href=index.php?page=content&article_id=$this->article_id&mode=edit&slice_id=$RE_CONTS[$I_ID]&function=edit#slice$RE_CONTS[$I_ID] class=green12b><b>".$I18N->msg('edit')."</b></a></td>
                                                        <td class=llblue align=center><a href=index.php?page=content&article_id=$this->article_id&mode=edit&slice_id=$RE_CONTS[$I_ID]&function=delete#slice$RE_CONTS[$I_ID] class=red12b><b>".$I18N->msg('delete')."</b></a></td>
                                                        </tr>
                                                        </table>";

                                                $p_menue = "
                                                        <table width=100% cellspacing=0 cellpadding=5 border=0>
                                                        <tr>
                                                        <td class=blue> MODUL: <b>$RE_MODUL_NAME[$I_ID]</b> | <b>".$I18N->msg('no_editing_rights')."</b></td>
                                                        </tr>
                                                        </table>";

                                                $tbl_head = "<table width=100% cellspacing=0 cellpadding=5 border=0><tr><td class=lblue>";
                                                $tbl_bott = "</td></tr></table>";

                                                // && ( $RE_MODUL_PHP[$module_id] == 0 || $MODULE_PERM[php] ) && ( $RE_MODUL_HTML[$module_id] == 0 || $MODULE_PERM[html] )


                                                if($this->function=="add" && $this->slice_id == $I_ID)
                                                {
                                                        $slice_content = $this->addSlice($I_ID,$module_id);
                                                }else
                                                {
                                                        $slice_content .= $amodule;
                                                }

                                                if($this->function=="edit" && $this->slice_id == $RE_CONTS[$I_ID] && ($RE_MODUL_PHP[$I_ID]==0 || $MODULE_PERM[php]) && ($RE_MODUL_HTML[$I_ID]==0 || $MODULE_PERM[html]) )
                                                {
                                                        $slice_content .= $fmenue.$tbl_head.$this->editSlice($RE_CONTS[$I_ID],$RE_MODUL_IN[$I_ID]).$tbl_bott;

                                                }elseif($this->function=="delete" && $this->slice_id == $RE_CONTS[$I_ID])
                                                {
                                                        $slice_content .= $fmenue.$tbl_head.$this->deleteSlice($RE_CONTS[$I_ID],$RE_MODUL_OUT[$I_ID],$PRE_ID).$tbl_bott;

                                                }else
                                                {

                                                        if (!$MODULE_PERM[html] && $RE_MODUL_HTML[$I_ID])
                                                        {
                                                                $this->mode="";
                                                                $slice_content .= $p_menue.$tbl_head.$RE_MODUL_OUT[$I_ID].$tbl_bott;
                                                                $slice_content = $this->sliceIn($slice_content);
                                                                // $slice_content .= "**";
                                                                $this->mode="edit";
                                                        }else
                                                        {
                                                                if ( ($RE_MODUL_PHP[$I_ID]==0 || $MODULE_PERM[php]) && ($RE_MODUL_HTML[$I_ID]==0 || $MODULE_PERM[html]) ) $slice_content .= $fmenue.$tbl_head.$RE_MODUL_OUT[$I_ID].$tbl_bott;
                                                                else $slice_content .= $p_menue.$tbl_head.$RE_MODUL_OUT[$I_ID].$tbl_bott;

                                                                $slice_content = $this->sliceIn($slice_content);
                                                        }
                                                }

                                        }else
                                        {
                                                // wenn mode nicht edit

                                                $slice_content .= $RE_MODUL_OUT[$I_ID];
                                                $slice_content = $this->sliceIn($slice_content);

                                        }

                                        // --------------- ENDE EINZELNER SLICE

                                        // ---------- slice in ausgabe speichern
                                        $this->article_content .= $slice_content;

                                        // zum nachsten slice
                                        $I_ID = $RE_CONTS[$I_ID];
                                        $PRE_ID = $I_ID;

                                }

                                if ($this->mode == "edit")
                                {

                                        $amodule = "
                                        <table cellspacing=0 cellpadding=5 border=0 width=100%>
                                        <form action=index.php";
                                        if ($this->setanker) $amodule .= "#addslice";
                                        $amodule.= " method=get>
                                        <input type=hidden name=article_id value=$this->article_id>
                                        <input type=hidden name=page value=content>
                                        <input type=hidden name=mode value=$this->mode>
                                        <input type=hidden name=slice_id value=$I_ID>
                                        <input type=hidden name=function value=add>
                                        <tr>
                                        <td class=dblue>".$MODULESELECT->out()."</td>
                                        </tr></form></table>";

                                        if($this->function=="add" && $this->slice_id == $I_ID)
                                        {
                                                $slice_content = $this->addSlice($I_ID,$module_id);

                                        }else
                                        {
                                                $slice_content = $amodule;
                                        }

                                        $this->article_content .= $slice_content;

                                }

                                // -------------------------- schreibe content

                                if ($REX[RC]) return $this->article_content;
                                else eval("?>".$this->article_content);

                        }else
                        {
                                return $I18N->msg('no_article_available');
                        }
                }

        }

        function getArticleTemplate()
        {
                global $FORM,$REX;

                if ($this->getValue("template_id") == 0 and $this->article_id != 0)
                {
                        return $this->getArticle();

                }elseif ($this->getValue("template_id") != 0 and $this->article_id != 0)
                {
                        if ($REX[BC]) $template_name = $REX[INCLUDE_PATH]."/generated/templates/".$this->getValue("template_id").".btemplate";
                        else $template_name = $REX[INCLUDE_PATH]."/generated/templates/".$this->getValue("template_id").".template";

                        if ($fd = @fopen ($template_name, "r"))
                        {
                                $template_content = fread ($fd, filesize ($template_name));
                                fclose ($fd);
                        }

                        $return = str_replace("REX_ARTICLE_ID",$this->article_id,$template_content);

						// function in function_rex_modrewrite.inc.php
						$slice_content = replaceLinks($slice_content);

                        eval("?>".$return);

                        // echo htmlentities($return);

                }else
                {
                        return "no template";
                }
        }

        function addSlice($I_ID,$module_id)
        {
                global $REX,$FORM,$I18N;
                $FILE1 = "";
                $FILE2 = "";
                $FILE3 = "";
                $FILE4 = "";
                $FILE5 = "";
                $FILE6 = "";
                $FILE7 = "";
                $FILE8 = "";
                $FILE9 = "";
                $FILE10 = "";

                $MOD = new sql;
                $MOD->setQuery("select * from rex_modultyp where id=$module_id");

                if ($MOD->getRows() != 1)
                {
                        $slice_content = "<table width=100% cellspacing=0 cellpadding=5 border=0><tr><td class=dblue>".$I18N->msg('module_doesnt_exist')."</td></tr></table>";
                }else
                {

                        $slice_content = "<a name=addslice></a><table width=100% cellspacing=0 cellpadding=5 border=0>
                        <tr><td class=dblue><b>".$I18N->msg('add_block')."</b></td></tr>
                                <tr><td class=blue>Modul: <b>".$MOD->getValue("name")."</b></td></tr>
                                <tr>
                                <td class=lblue>
                                <form ENCTYPE=multipart/form-data action=index.php#slice$I_ID method=post name=REX_FORM>
                                <input type=hidden name=article_id value=$this->article_id>
                                <input type=hidden name=page value=content>
                                <input type=hidden name=mode value=$this->mode>
                                <input type=hidden name=slice_id value=$I_ID>
                                <input type=hidden name=function value=add>
                                <input type=hidden name=module_id value=$module_id>
                                <input type=hidden name=save value=1>
                                ".$MOD->getValue("eingabe")."
                                <br><input type=submit value='".$I18N->msg('add_block')."'></form>";
                        $slice_content = $this->sliceClear($slice_content);
                        $slice_content .= "</td></tr></table>";

                }

                return $slice_content;

        }

        function deleteSlice($RE_CONTS,$RE_MODUL_OUT,$PRE_ID)
        {
                global $REX,$FORM,$I18N;
                $slice_content .= "<a name=deleteslice></a>$RE_MODUL_OUT
                        <form ENCTYPE=multipart/form-data action=index.php#slice$PRE_ID method=post>
                        <input type=hidden name=article_id value=$this->article_id>
                        <input type=hidden name=page value=content>
                        <input type=hidden name=mode value=$this->mode>
                        <input type=hidden name=slice_id value=$RE_CONTS>
                        <input type=hidden name=function value=delete>
                        <table cellspacing=0 cellpadding=0 border=0 class=high>
                        <tr>
                        <td valign=middle><select name=save size=1><option value=2>".$I18N->msg('dont_delete_block')."</option><option value=1 selected>".$I18N->msg('delete_block')."</option></select></td>
                                <td valign=middle><input type=submit value='".$I18N->msg('submit')."'></td>
                        </tr></form>
                        </table>";
                $slice_content = $this->sliceIn($slice_content);
                return $slice_content;
        }

        function editSlice($RE_CONTS,$RE_MODUL_IN)
        {
                global $REX,$FORM,$I18N;

                $slice_content .= "<a name=editslice></a>
                        <form ENCTYPE=multipart/form-data action=index.php#slice$RE_CONTS method=post name=REX_FORM>
                        <input type=hidden name=article_id value=$this->article_id>
                        <input type=hidden name=page value=content>
                        <input type=hidden name=mode value=$this->mode>
                        <input type=hidden name=slice_id value=$RE_CONTS>
                        <input type=hidden name=function value=edit>
                        <input type=hidden name=save value=1>
                        <input type=hidden name=update value=0>
                        $RE_MODUL_IN
                        <br><br><input type=submit value='".$I18N->msg('save_block')."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value='".$I18N->msg('update_block')."' onClick='REX_FORM.update.value=1'></form>";

                // CRTL - S FOR UPDATE --> ALT - S FOR SAVE
                $slice_content .= '
				<script>
	                if (navigator.appName=="Netscape")
	                {
	                  function processKeypresses(e)
	                  {
	                    var whichASC = e.which;
	                    if (whichASC == \'115\' &&  e.ctrlKey == true){
	                    	document.REX_FORM.update.value = 1;
							document.REX_FORM.submit();
							return false;
	                    }
	                    if (whichASC == \'115\' &&  e.altKey == true){
	                    	document.REX_FORM.update = 0;
							document.REX_FORM.submit();
							return false;
	                    }
	                  }

	                  if (document.captureEvents)
	                  {
	                    document.captureEvents(Event.KEYPRESS);
	                  }
	                  document.onkeypress = processKeypresses;
	                }
	            </script>
                ';
                
                $slice_content = $this->sliceIn($slice_content);
                return $slice_content;
        }

        function sliceIn($slice_content)
        {
                for ($i=1;$i<11;$i++)
                {

                        // ----------------------------- REX_MEDIA
                        $media = "<table><input type=hidden name=REX_MEDIA_DELETE_$i value=0 id=REX_MEDIA_DELETE_$i><tr>";
                        $media.= "<td><input type=text size=30 name=REX_MEDIA_$i value='FILE[$i]' class=inpgrey id=REX_MEDIA_$i readonly=readonly></td>";
                        $media.= "<td><a href=javascript:openREXMedia($i);><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>";
                        $media.= "<td><a href=javascript:deleteREXMedia($i);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
                        $media.= "<td><a href=javascript:addREXMedia($i)><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td>";
                        $media.= "</tr></table>";
                        $media = $this->stripPHP($media);
                        $slice_content = str_replace("REX_MEDIA_BUTTON[$i]",$media,$slice_content);
                        $slice_content = str_replace("FILE[$i]",$this->convertString($this->CONT->getValue("rex_article_slice.file$i")),$slice_content);

                        // ----------------------------- REX_LINK_BUTTON
                        if($this->CONT->getValue("rex_article_slice.link$i")){
                        	$db = new sql;
                        	$sql = "SELECT name FROM rex_article WHERE id=".$this->CONT->getValue("rex_article_slice.link$i");
                        	$res = $db->get_array($sql);
                        	$link_name = $res[0][name];
                        }else
                        {
                        	$link_name = "";
                        }
                        $media = "<table><input type=hidden name=REX_LINK_DELETE_$i value=0 id=REX_LINK_DELETE_$i><input type=hidden name='LINK[$i]' value='REX_LINK[$i]' id=LINK[$i]><tr>";
                        $media.= "<td><input type=text size=30 name='LINK_NAME[$i]' value='$link_name' class=inpgrey id=LINK_NAME[$i] readonly=readonly></td>";
                        $media.= "<td><a href=javascript:openLinkMap($i);><img src=pics/file_open.gif width=16 height=16 title='Linkmap' border=0></a></td>";
                        $media.= "<td><a href=javascript:deleteREXLink($i);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
                        $media.= "</tr></table>";
                        $media = $this->stripPHP($media);
                        $slice_content = str_replace("REX_LINK_BUTTON[$i]",$media,$slice_content);
                        $slice_content = str_replace("REX_LINK[$i]",$this->generateLink($this->CONT->getValue("rex_article_slice.link$i")),$slice_content);


                        // -- show:htmlentities -- edit:nl2br/htmlentities
                        $slice_content = str_replace("REX_VALUE[$i]",$this->convertString($this->CONT->getValue("rex_article_slice.value$i")),$slice_content);

                        // -- show:stripphp -- edit:stripphp
                        $slice_content = str_replace("REX_HTML_VALUE[$i]",$this->stripPHP($this->CONT->getValue("rex_article_slice.value$i")),$slice_content);

                        // -- show:stripphp -- edit:stripphp --
                        $slice_content = str_replace("REX_HTML_BR_VALUE[$i]",nl2br($this->stripPHP($this->CONT->getValue("rex_article_slice.value$i"))),$slice_content);

                        // -- show:- -- edit:-
                        $slice_content = str_replace("REX_PHP_VALUE[$i]",$this->CONT->getValue("rex_article_slice.value$i"),$slice_content);

                        if ($this->CONT->getValue("rex_article_slice.value$i")!="") $slice_content = str_replace("REX_IS_VALUE[$i]","1",$slice_content);


                }

                $slice_content = str_replace("REX_PHP",$this->convertString2($this->CONT->getValue("rex_article_slice.php"),TRUE),$slice_content);
                $slice_content = str_replace("REX_HTML",$this->convertString2($this->CONT->getValue("rex_article_slice.html"),FALSE),$slice_content);

                $slice_content = str_replace("REX_ARTICLE_ID",$this->article_id,$slice_content);
                $slice_content = str_replace("REX_CATEGORY_ID",$this->category_id,$slice_content);

				// function in function_rex_modrewrite.inc.php
		if ($this->mode != "edit") $slice_content = replaceLinks($slice_content);

                return $slice_content;

        }

        // ------------------------------------- CONVERT

        function stripPHP($content)
        {
                $content = str_replace("<?","",$content);
                $content = str_replace("?>","",$content);
                return $content;
        }

        function convertString2($content,$php)
        {
                if (!$php)
                {
                        $content = $this->stripPHP($content);
                }

                if ($this->mode == "edit" && $this->slice_id == $this->ViewSliceId && $this->function=="edit")
                {
                        return htmlentities($content);
                }elseif ($this->mode == "edit")
                {
                        return nl2br(htmlentities($content));
                }else
                {
                        return $content;
                }
        }

        function convertString($content)
        {
                if ($this->mode == "edit" && $this->slice_id == $this->ViewSliceId && $this->function=="edit")
                {
                        return htmlentities($content);
                }else
                {
                        return nl2br(htmlentities($content));
                }
        }

        // ------------------------------------ / CONVERT

        function sliceClear($slice_content)
        {
                for ($i=1;$i<11;$i++)
                {
                	// ----------------------------- REX_MEDIA_BUTTON
                        $media = "<table><input type=hidden name=REX_MEDIA_DELETE_$i value=0 id=REX_MEDIA_DELETE_$i><tr>";
                        $media.= "<td><input type=text size=30 name=REX_MEDIA_$i value='FILE[$i]' class=inpgrey id=REX_MEDIA_$i readonly=readonly></td>";
                        $media.= "<td><a href=javascript:openREXMedia($i);><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>";
                        $media.= "<td><a href=javascript:deleteREXMedia($i);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
                        $media.= "<td><a href=javascript:addREXMedia($i)><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td>";
                        $media.= "</tr></table>";
                        $media = $this->stripPHP($media);
                        $slice_content = str_replace("REX_MEDIA_BUTTON[$i]",$media,$slice_content);
                        $slice_content = str_replace("FILE[$i]","",$slice_content);


			// ----------------------------- REX_LINK_BUTTON
                        $link_name = "";
                        $media = "<table><input type=hidden name=REX_LINK_DELETE_$i value=0 id=REX_LINK_DELETE_$i><input type=hidden name='LINK[$i]' value='REX_LINK[$i]' id=LINK[$i]><tr>";
                        $media.= "<td><input type=text size=30 name='LINK_NAME[$i]' value='$link_name' class=inpgrey id=LINK_NAME[$i] readonly=readonly></td>";
                        $media.= "<td><a href=javascript:openLinkMap($i);><img src=pics/file_open.gif width=16 height=16 title='Linkmap' border=0></a></td>";
                        $media.= "<td><a href=javascript:deleteREXLink($i);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
                        $media.= "</tr></table>";
                        $media = $this->stripPHP($media);
                        $slice_content = str_replace("REX_LINK_BUTTON[$i]",$media,$slice_content);
                        $slice_content = str_replace("REX_LINK[$i]","",$slice_content);

                        $slice_content = str_replace("REX_VALUE[$i]","",$slice_content);
                        $slice_content = str_replace("REX_HTML_VALUE[$i]","",$slice_content);
                        $slice_content = str_replace("REX_PHP_VALUE[$i]","",$slice_content);
                        $slice_content = str_replace("REX_IS_VALUE[$i]","",$slice_content);
                        $slice_content = str_replace("REX_LINK[$i]","",$slice_content);



                }

                $slice_content = str_replace("REX_PHP","",$slice_content);
                $slice_content = str_replace("REX_HTML","",$slice_content);

                $slice_content = str_replace("REX_ARTICLE_ID","",$slice_content);
                $slice_content = str_replace("REX_CATEGORY_ID","",$slice_content);

                return $slice_content;

        }


        function generateLink($id)
        {
                global $REX;

                if ($this->mode == "edit")
                {
                        return $id;
                }else
                {
                        if ($REX[GG]) return "aid$id".".php";
                        else return getURLbyID($id);
                }
        }

}

?>