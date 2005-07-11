<?

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
	var $ctype;
	var $clang;


	function article()
	{
		$this->article_id = 0;
		$this->template_id = 0;
		$this->clang = 0;
		$this->ctype = "";
		$this->slice_id = 0;
		$this->mode = "view";
		$this->article_content = "";
		$this->eval = FALSE;
		$this->setanker = true;
		unset($save);
	
		// AUSNAHME: modul auswählen problem
		// action=index.php#1212 problem
		if (strpos($_SERVER["HTTP_USER_AGENT"],"Mac") and strpos($_SERVER["HTTP_USER_AGENT"],"MSIE") ) $this->setanker = FALSE;
	}

	function setSliceId($value)
	{
		$this->slice_id = $value;
	}

	function setCType($value)
	{
		$this->ctype = $value;
	}

	function setCLang($value)
	{
		global $REX;
		if ($REX[CLANG][$value] == "") $value = 0;
		$this->clang = $value;
	}
	
	function setArticleId($article_id)
	{
		global $REX;
		
		$article_id = $article_id + 0;
		$this->article_id = $article_id+0;

		if (!$REX[GG])
		{
		
			// ---------- select article
			$this->ARTICLE = new sql;
			// $this->ARTICLE->debugsql = 1;
			$this->ARTICLE->setQuery("select * from rex_article where rex_article.id='$article_id' and clang='".$this->clang."'");
		
			if ($this->ARTICLE->getRows() == 1)
			{
				$this->template_id = $this->ARTICLE->getValue("rex_article.template_id");
				$this->category_id = $this->getValue("category_id");
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
			if (@include $REX[INCLUDE_PATH]."/generated/articles/".$article_id.".".$this->clang.".article")
			{
				return TRUE;
			}else
			{
				return FALSE;
			}
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
		
		if ($value == "category_id")
		{
			if ($this->getValue("startpage")!=1) $value = "re_id";
			else if($REX[GG]) $value = "article_id";
			else $value = "id";
		}
		
		if ($REX[GG]) return $REX[ART][$this->article_id][$value][$this->clang];
		else return $this->ARTICLE->getValue($value);
	}

	function getArticle()
	{
		global $module_id,$FORM,$REX_USER,$REX,$REX_SESSION,$REX_ACTION,$I18N;

		if ($REX[GG])
		{
			if ($this->article_id != 0)
			{
				$this->contents = "";
				$filename = $REX[INCLUDE_PATH]."/generated/articles/".$this->article_id.".".$this->clang.".content";
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
				
				$sql = "select rex_modultyp.id, rex_modultyp.name, rex_modultyp.ausgabe, rex_modultyp.eingabe, rex_modultyp.php_enable, rex_modultyp.html_enable, rex_article_slice.*, rex_article.re_id
					from
						rex_article_slice
					left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id
					left join rex_article on rex_article_slice.article_id=rex_article.id
					where
						rex_article_slice.article_id='".$this->article_id."' and 
						rex_article_slice.clang='".$this->clang."' and 
						rex_article.clang='".$this->clang."' 
					order by
						rex_article_slice.re_article_slice_id";
				
				
				$this->CONT = new sql;
				$this->CONT->setQuery($sql);

				// ---------- SLICE IDS/MODUL SETZEN
				for ($i=0;$i<$this->CONT->getRows();$i++)
				{
					$RE_CONTS[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_article_slice.id");
					$RE_CONTS_CTYPE[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_article_slice.ctype");
					$RE_MODUL_OUT[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.ausgabe");
					$RE_MODUL_IN[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.eingabe");
					$RE_MODUL_ID[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.id");
					$RE_MODUL_NAME[$this->CONT->getValue("re_article_slice_id")] = $this->CONT->getValue("rex_modultyp.name");
					$RE_C[$this->CONT->getValue("re_article_slice_id")] = $i;
					$this->CONT->nextValue();
				}

				// ---------- moduleselect
				if($this->mode=="edit")
				{
					$MODULE = new sql;
					$MODULE->setQuery("select * from rex_modultyp $add_sql order by name");
					
					$MODULESELECT = new select;
					$MODULESELECT->set_name("module_id");
					$MODULESELECT->set_size(1);
					$MODULESELECT->set_style("width:100%;' onchange='this.form.submit();");
					$MODULESELECT->add_option("----------------------------  ".$I18N->msg("add_block"),'');
					
					for ($i=0;$i<$MODULE->getRows();$i++)
					{
						if ($REX_USER->isValueOf("rights","module[".$MODULE->getValue("id")."]") || $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]")) $MODULESELECT->add_option($MODULE->getValue("name"),$MODULE->getValue("id"));
						$MODULE->next();
					}
				}

				// ---------- SLICE IDS SORTIEREN UND AUSGEBEN
				$I_ID = 0;
				$PRE_ID = 0;
				$this->article_content = "";
				$this->CONT->resetCounter();
				$tbl_head = "<table width=100% cellspacing=0 cellpadding=5 border=0><tr><td class=lblue>";
				$tbl_bott = "</td></tr></table>";

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
						<input type=hidden name=clang value=".$this->clang.">
						<input type=hidden name=ctype value=".$this->ctype.">
						<tr>
						<td class=dblue>".$MODULESELECT->out()."</td>
						</tr></form></table>";
						
						
						
						// ----- add select box einbauen
						
						if($this->function=="add" && $this->slice_id == $I_ID)
						{
							$slice_content = $this->addSlice($I_ID,$module_id);
						}else
						{
							$slice_content .= $amodule;
						}
						
						
						// ----- edit / delete 
						
						if($REX_USER->isValueOf("rights","module[".$RE_MODUL_ID[$I_ID]."]") || $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]"))
						{
							
							// hat rechte zum edit und delete	
							
							$mne  = "
								<a name=slice$RE_CONTS[$I_ID]></a>
								<table width=100% cellspacing=0 cellpadding=5 border=0>
								<tr>
								<td class=blue width=380><b>$RE_MODUL_NAME[$I_ID]</b></td>
								<td class=llblue align=center><a href=index.php?page=content&article_id=$this->article_id&mode=edit&slice_id=$RE_CONTS[$I_ID]&function=edit&clang=".$this->clang."&ctype=".$this->ctype."#slice$RE_CONTS[$I_ID] class=green12b><b>".$I18N->msg('edit')."</b></a></td>
								<td class=llblue align=center><a href=index.php?page=content&article_id=$this->article_id&mode=edit&slice_id=$RE_CONTS[$I_ID]&function=delete&clang=".$this->clang."&ctype=".$this->ctype."&save=1#slice$RE_CONTS[$I_ID] class=red12b onclick='return confirm(\"".$I18N->msg('delete')." ?\")'><b>".$I18N->msg('delete')."</b></a></td>
								</tr></table>";
							
							$slice_content .= $mne.$tbl_head;
							if($this->function=="edit" && $this->slice_id == $RE_CONTS[$I_ID])
							{
								$slice_content .= $this->editSlice($RE_CONTS[$I_ID],$RE_MODUL_IN[$I_ID]);
							}else
							{
								$slice_content .= $RE_MODUL_OUT[$I_ID];
							}
							$slice_content .= $tbl_bott;
							$slice_content = $this->sliceIn($slice_content);
							
						}else
						{

							// hat keine rechte an diesem modul	

							$mne = "
								<table width=100% cellspacing=0 cellpadding=5 border=0>
								<tr>
								<td class=blue> MODUL: <b>$RE_MODUL_NAME[$I_ID]</b> | <b>".$I18N->msg('no_editing_rights')."</b></td>
								</tr>
								</table>";
							$slice_content .= $mne.$tbl_head.$RE_MODUL_OUT[$I_ID].$tbl_bott;
							$slice_content = $this->sliceIn($slice_content);
						}
						
		
					}else
					{

						// wenn mode nicht edit
		
						$slice_content .= $RE_MODUL_OUT[$I_ID];
						$slice_content = $this->sliceIn($slice_content);
		
					}
		
					// --------------- ENDE EINZELNER SLICE
					
					// ---------- slice in ausgabe speichern wenn ctype richtig 
					
					if ($this->ctype == "" or $this->ctype == $RE_CONTS_CTYPE[$I_ID]) $this->article_content .= $slice_content;
					
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
					<input type=hidden name=clang value=".$this->clang.">
					<input type=hidden name=ctype value=".$this->ctype.">
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
			$template_name = $REX[INCLUDE_PATH]."/generated/templates/".$this->getValue("template_id").".template";
			if ($fd = fopen ($template_name, "r"))
			{
				$template_content = fread ($fd, filesize ($template_name));
				fclose ($fd);
			}else
			{
				$template_content = $this->getValue("template_id")." not found";
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
		global $REX,$REX_ACTION,$FORM,$I18N;
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
			<input type=hidden name=clang value=".$this->clang.">
			<input type=hidden name=ctype value=".$this->ctype.">
			".$MOD->getValue("eingabe")."
			<br><input type=submit value='".$I18N->msg('add_block')."'></form>";
			$slice_content = $this->sliceClear($slice_content);
			$slice_content .= "</td></tr></table>";
		}
		return $slice_content;
	}


	function editSlice($RE_CONTS,$RE_MODUL_IN)
	{
		global $REX,$REX_ACTION,$FORM,$I18N;
		$slice_content .= "<a name=editslice></a>
			<form ENCTYPE=multipart/form-data action=index.php#slice$RE_CONTS method=post name=REX_FORM>
			<input type=hidden name=article_id value=$this->article_id>
			<input type=hidden name=page value=content>
			<input type=hidden name=mode value=$this->mode>
			<input type=hidden name=slice_id value=$RE_CONTS>
			<input type=hidden name=function value=edit>
			<input type=hidden name=save value=1>
			<input type=hidden name=update value=0>
			<input type=hidden name=clang value=".$this->clang.">
			$RE_MODUL_IN
			<br><br><input type=submit value='".$I18N->msg('save_block')."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value='".$I18N->msg('update_block')."' onClick='REX_FORM.update.value=1'></form>";

		// werte das erst mal aufgerufen / noch nicht gespeichert / gepspeichert und neu
		if (!isset($REX_ACTION[SAVE])) $slice_content = $this->sliceIn($slice_content);
		if (!$REX_ACTION[SAVE]) $slice_content = $this->sliceClear($slice_content);
		else $slice_content = $this->sliceIn($slice_content);

		return $slice_content;
	}

	function sliceIn($slice_content)
	{
		for ($i=1;$i<11;$i++)
		{
			// ----------------------------- LIST BUTTONS
			// REX_FILELIST_BUTTON
			$media = "<input type=text size=30 name=REX_FILELIST_$i value='REX_FILELIST[$i]' class=inpgrey id=REX_FILELIST_$i read2only=read2only>";
			$media = $this->stripPHP($media);
			$slice_content = str_replace("REX_FILELIST_BUTTON[$i]",$media,$slice_content);
			$slice_content = str_replace("REX_FILELIST[$i]",$this->convertString($this->CONT->getValue("rex_article_slice.filelist$i")),$slice_content);
			// REX_LINKLIST_BUTTON
			$media = "<input type=text size=30 name=REX_LINKLIST_$i value='REX_LINKLIST[$i]' class=inpgrey id=REX_LINKLIST_$i reado2nly=read2only>";
			$media = $this->stripPHP($media);
			$slice_content = str_replace("REX_LINKLIST_BUTTON[$i]",$media,$slice_content);
			$slice_content = str_replace("REX_LINKLIST[$i]",$this->convertString($this->CONT->getValue("rex_article_slice.linklist$i")),$slice_content);
				
			// ----------------------------- REX_MEDIA
			$media = "<table><input type=hidden name=REX_MEDIA_DELETE_$i value=0 id=REX_MEDIA_DELETE_$i><tr>";
			$media.= "<td><input type=text size=30 name=REX_MEDIA_$i value='REX_FILE[$i]' class=inpgrey id=REX_MEDIA_$i readonly=readonly></td>";
			$media.= "<td><a href=javascript:openREXMedia($i,".$this->clang.");><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>";
			$media.= "<td><a href=javascript:deleteREXMedia($i,".$this->clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
			$media.= "<td><a href=javascript:addREXMedia($i,".$this->clang.")><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td>";
			$media.= "</tr></table>";
			$media = $this->stripPHP($media);

			$slice_content = str_replace("REX_MEDIA_BUTTON[$i]",$media,$slice_content);
			$slice_content = str_replace("REX_FILE[$i]",$this->convertString($this->CONT->getValue("rex_article_slice.file$i")),$slice_content);

			// ----------------------------- REX_LINK_BUTTON
			if($this->CONT->getValue("rex_article_slice.link$i"))
			{
				$db = new sql;
				$sql = "SELECT name FROM rex_article WHERE id=".$this->CONT->getValue("rex_article_slice.link$i")." and clang=".$this->clang;
				$res = $db->get_array($sql);
				$link_name = $res[0][name];
			}else
			{
				$link_name = "";
			}
			$media = "<table><input type=hidden name=REX_LINK_DELETE_$i value=0 id=REX_LINK_DELETE_$i><input type=hidden name='LINK[$i]' value='REX_LINK[$i]' id=LINK[$i]><tr>";
			$media.= "<td><input type=text size=30 name='LINK_NAME[$i]' value='$link_name' class=inpgrey id=LINK_NAME[$i] readonly=readonly></td>";
			$media.= "<td><a href=javascript:openLinkMap($i,".$this->clang.");><img src=pics/file_open.gif width=16 height=16 title='Linkmap' border=0></a></td>";
			$media.= "<td><a href=javascript:deleteREXLink($i,".$this->clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
			$media.= "</tr></table>";
			$media = $this->stripPHP($media);
			$slice_content = str_replace("REX_LINK_BUTTON[$i]",$media,$slice_content);
			$slice_content = str_replace("REX_LINK[$i]",$this->generateLink($this->CONT->getValue("rex_article_slice.link$i")),$slice_content);
			$slice_content = str_replace("REX_LINK_ID[$i]",$this->CONT->getValue("rex_article_slice.link$i"),$slice_content);
			
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
		$slice_content = str_replace("REX_CUR_CLANG",$this->clang,$slice_content);
		$slice_content = str_replace("REX_CATEGORY_ID",$this->category_id,$slice_content);
		
		// function in function_rex_modrewrite.inc.php
		if ($this->mode != "edit") $slice_content = replaceLinks($slice_content);
		
		return $slice_content;

	}	


	// ----- Slice loeschen damit Werte in den nächsten Slice nicht übernommen werden
	function sliceClear($slice_content)
	{
		
		global $REX_ACTION;
				
		for ($i=1;$i<11;$i++)
		{
			// ----------------------------- LIST BUTTONS
			// REX_FILELIST_BUTTON
			$media = "<input type=text size=30 name=REX_FILELIST_$i value='REX_FILELIST[$i]' class=inpgrey id=REX_FILELIST_$i read2only=reado2nly>";
			$media = $this->stripPHP($media);
			$slice_content = str_replace("REX_FILELIST_BUTTON[$i]",$media,$slice_content);
			$slice_content = str_replace("REX_FILELIST[$i]","",$slice_content);
			// REX_LINKLIST_BUTTON
			$media = "<input type=text size=30 name=REX_LINKLIST_$i value='REX_LINKLIST[$i]' class=inpgrey id=REX_LINKLIST_$i read2only=read2only>";
			$media = $this->stripPHP($media);
			$slice_content = str_replace("REX_LINKLIST_BUTTON[$i]",$media,$slice_content);
			$slice_content = str_replace("REX_LINKLIST[$i]","",$slice_content);
			
			// ----------------------------- REX_MEDIA_BUTTON
			$media = "<table><input type=hidden name=REX_MEDIA_DELETE_$i value=0 id=REX_MEDIA_DELETE_$i><tr>";
			$media.= "<td><input type=text size=30 name=REX_MEDIA_$i value='REX_FILE[$i]' class=inpgrey id=REX_MEDIA_$i readonly=readonly></td>";
			$media.= "<td><a href=javascript:openREXMedia($i,".$this->clang.");><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>";
			$media.= "<td><a href=javascript:deleteREXMedia($i,".$this->clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
			$media.= "<td><a href=javascript:addREXMedia($i,".$this->clang.")><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td>";
			$media.= "</tr></table>";
			$media = $this->stripPHP($media);
			$slice_content = str_replace("REX_MEDIA_BUTTON[$i]",$media,$slice_content);
			$slice_content = str_replace("REX_FILE[$i]",$REX_ACTION[FILE][$i],$slice_content);
			
			// ----------------------------- REX_LINK_BUTTON
			$link_name = "";
			
			if ($REX_ACTION[LINK][$i]>0)
			{
				$db = new sql;
				$sql = "SELECT name FROM rex_article WHERE id=".$REX_ACTION[LINK][$i]." and clang=".$this->clang;
				$res = $db->get_array($sql);
				$link_name = $res[0][name];
			}
			
			$media = "<table><input type=hidden name=REX_LINK_DELETE_$i value=0 id=REX_LINK_DELETE_$i><input type=hidden name='LINK[$i]' value='REX_LINK[$i]' id=LINK[$i]><tr>";
			$media.= "<td><input type=text size=30 name='LINK_NAME[$i]' value='$link_name' class=inpgrey id=LINK_NAME[$i] readonly=readonly></td>";
			$media.= "<td><a href=javascript:openLinkMap($i,".$this->clang.");><img src=pics/file_open.gif width=16 height=16 title='Linkmap' border=0></a></td>";
			$media.= "<td><a href=javascript:deleteREXLink($i,".$this->clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
			$media.= "</tr></table>";
			$media = $this->stripPHP($media);
			$slice_content = str_replace("REX_LINK_BUTTON[$i]",$media,$slice_content);
			$slice_content = str_replace("REX_LINK[$i]",$REX_ACTION[LINK][$i],$slice_content);
			$slice_content = str_replace("REX_LINK_ID[$i]",$REX_ACTION[LINK][$i],$slice_content);
			
			
			// ----------------------------- REX_ OTHER
			$slice_content = str_replace("REX_VALUE[$i]",htmlentities(stripslashes($REX_ACTION[VALUE][$i])),$slice_content);
			$slice_content = str_replace("REX_HTML_VALUE[$i]","",$slice_content);
			$slice_content = str_replace("REX_PHP_VALUE[$i]","",$slice_content);
			$slice_content = str_replace("REX_IS_VALUE[$i]","",$slice_content);
		
		}
		
		$slice_content = str_replace("REX_PHP",htmlentities(stripslashes($REX_ACTION[PHP])),$slice_content);
		$slice_content = str_replace("REX_HTML",htmlentities(stripslashes($REX_ACTION[HTML])),$slice_content);
		
		$slice_content = str_replace("REX_ARTICLE_ID","",$slice_content);
		$slice_content = str_replace("REX_CUR_CLANG","",$slice_content);
		$slice_content = str_replace("REX_CATEGORY_ID","",$slice_content);
		
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


	function generateLink($id)
	{
		global $REX;
		
		if ($this->mode == "edit")
		{
			return $id;
		}else
		{
			if ($REX[GG]) return "aid$id".".php";
			else return rex_getURL($id);
		}
	}

}

?>