<?

$article = new sql;
$article->setQuery("select * from rex_article where id='$article_id' and clang=$clang");

if ($article->getRows() == 1)
{

	// --------------------------------------------- ARTIKEL WURDE GEFUNDEN - CATEGORY WAEHLEN
	if ($article->getValue("startpage")==1) $category_id = $article->getValue("id");
	else $category_id = $article->getValue("re_id");

	// ----- category pfad und rechte
	include $REX[INCLUDE_PATH]."/functions/function_rex_category.inc.php";

	// ----- Titel anzeigen
	title("Artikel",$KATout);
	$add = "";
	if (count($REX[CLANG])>1)
	{
		$add = "<table width=770 cellpadding=0 cellspacing=1 border=0><tr><td width=30 class=dgrey><img src=pics/leer.gif width=16 height=16 vspace=5 hspace=12></td><td class=dgrey>&nbsp;<b>Sprachen:</b> | ";
		reset($REX[CLANG]);
		while( list($key,$val) = each($REX[CLANG]) )
		{
			if ($key==$clang)
			{
				$add .= "$val | ";
			}else
			{
				$add .= "<a href=index.php?page=content&clang=$key&category_id=$category_id&article_id=$article_id>$val</a> | ";
			}
		}
		$add .= "</td></tr></table><br>";
		echo $add;
	}else
	{
		$clang = 0;	
	}


	// ----- mode defs
	if ($mode != "meta") $mode = "edit";

	// ----------------- HAT USER DIE RECHTE AN DIESEM ARTICLE
	if (1==1)
	{

		// ------------------------------------------ SLICE EDIT / ADD / DELETE
		if (($function == "add" or $function == "edit") and $save==1)
		{
			// ------------------------------------------ check module
			$CM = new sql;

			if ($function == "edit")
			{
				$CM->setQuery("select * from rex_article_slice left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id where rex_article_slice.id='$slice_id' and clang=$clang");
				if ($CM->getRows()==1) $module_id = $CM->getValue("rex_article_slice.modultyp_id");
			}else
			{
				$CM->setQuery("select * from rex_modultyp where id='$module_id'");
			}

			if ($CM->getRows()==1)
			{

				// ------------------- modul ist vorhanden

				if (($CM->getValue("php_enable")==0 or $REX_USER->isValueOf("rights","module[php]")) and ($CM->getValue("html_enable")==0 or $REX_USER->isValueOf("rights","module[html]")))
				{

					$message = "";

					// ------------------------------------------ slices edit/add
					$newsql = new sql;
					// $newsql->debugsql = 1;
					$newsql->setTable("rex_article_slice");

					if ($function == "edit")
					{
						$newsql->where("id='$slice_id'");
					}else
					{
						$newsql->setValue("re_article_slice_id",$slice_id);
						$newsql->setValue("article_id",$article_id);
						$newsql->setValue("modultyp_id",$module_id);
						$newsql->setValue("clang",$clang);
						$newsql->setValue("ctype",$ctype);
					}

					for ($i=1;$i<11;$i++)
					{
						$FILENAME = "REX_MEDIA_$i";
						$REX_ACTION[VALUE][$i] = $VALUE[$i];
						$REX_ACTION[LINK][$i] = $LINK[$i];
						$REX_ACTION[FILE][$i] = $$FILENAME;
					}

					if ($REX_USER->isValueOf("rights","module[html]")) $REX_ACTION[HTML] = $INPUT_HTML;
					if ($REX_USER->isValueOf("rights","module[php]")) $REX_ACTION[PHP] = $INPUT_PHP;

					// ----- PRE ACTION [ADD UND EDIT]

					if ($function == "edit") $addsql = " and rex_action.prepost=0 and rex_action.status=1"; // pre-action and edit
					else $addsql = " and rex_action.prepost=0 and rex_action.status=0"; // pre-action and add
					$ga = new sql;
					$ga->setQuery("select * from rex_module_action,rex_action where rex_module_action.action_id=rex_action.id and rex_module_action.module_id='$module_id' $addsql");

					for ($i=0;$i<$ga->getRows();$i++)
					{
						$iaction = $ga->getValue("rex_action.action");
						$iaction = str_replace("REX_MODULE_ID",$module_id,$iaction);
						$iaction = str_replace("REX_SLICE_ID",$slice_id,$iaction);
						$iaction = str_replace("REX_CTYPE",$ctype,$iaction);
						$iaction = str_replace("REX_CLANG",$clang,$iaction);
						$iaction = str_replace("REX_CATEGORY_ID",$category_id,$iaction);
						$iaction = str_replace("REX_ARTICLE_ID",$article_id,$iaction);

						$iaction = str_replace("REX_PHP",$REX_ACTION[PHP],$iaction);
						$iaction = str_replace("REX_HTML",$REX_ACTION[HTML],$iaction);

						for ($j=1;$j<11;$j++)
						{
							$iaction = str_replace("REX_VALUE[$j]",$REX_ACTION[VALUE][$j],$iaction);
							$iaction = str_replace("REX_LINK[$j]",$REX_ACTION[LINK][$j],$iaction);
							$iaction = str_replace("FILE[$j]",$REX_ACTION[FILE][$j],$iaction);
						}

						// echo "<br>".nl2br(htmlentities($iaction));
						eval("?>".$iaction);
						if ($REX_ACTION[MSG]!="") $message .= $REX_ACTION[MSG]." | ";
						$ga->next();
					}

					// ----- / PRE ACTION

					for ($i=1;$i<11;$i++)
					{
						$newsql->setValue("value$i",$REX_ACTION[VALUE][$i]);
					}

					if ($REX_USER->isValueOf("rights","module[html]")) $newsql->setValue("html",$REX_ACTION[HTML]);
					if ($REX_USER->isValueOf("rights","module[php]")) $newsql->setValue("php",$REX_ACTION[PHP]);

					// ---------------------------- REX_MEDIA
				        for ($fi=1;$fi<11;$fi++)
					{

						if ($REX_ACTION[LINK][$fi]=="delete link" or $REX_ACTION[LINK][$fi]=="")
						{
							$newsql->setValue("link$fi","");
						}else
						{
							$newsql->setValue("link$fi",$REX_ACTION[LINK][$fi]);
						}

						$FILENAME = $REX_ACTION[FILE][$fi];
						if (($FILENAME == "delete file" or $FILENAME == "") && $CHECK_FILE[$fi] != 1)
						{
							$newsql->setValue("file".$fi,"");
						}elseif ($FILENAME != "" && $CHECK_FILE[$fi] != 1)
						{
							$checkfile = new sql;
							$checkfile->setQuery("select * from rex_file where filename='".$FILENAME."'");
							if ($checkfile->getRows()==1)
							{
								$newsql->setValue("file".$fi,$FILENAME);
							}else
							{
								$message .= $I18N->msg('file');
							}
						}
					}

					
					$newsql->setValue("updatedate",time());
					$newsql->setValue("updateuser",$REX_USER->getValue("login"));


					// ----- Function
					if ($function == "edit")
					{
						$newsql->update();
						$message .= $I18N->msg('block_updated');

					}elseif ($function == "add")
					{
						$newsql->setValue("createdate",time());
						$newsql->setValue("createuser",$REX_USER->getValue("login"));
						$newsql->insert();
						$last_id = $newsql->last_insert_id;
						$newsql->query("update rex_article_slice set re_article_slice_id='$last_id' where re_article_slice_id='$slice_id' and id<>'$last_id' and article_id='$article_id' and clang=$clang");
						$message .= $I18N->msg('block_added');
					}

					// ----- POST ACTION [ADD AND EDIT]
					if ($function == "edit") $addsql = " and rex_action.prepost=1 and rex_action.status=1"; // post-action and edit
					else $addsql = " and rex_action.prepost=1 and rex_action.status=0"; // post-action and add
					$ga = new sql;
					$ga->setQuery("select * from rex_module_action,rex_action where rex_module_action.action_id=rex_action.id and rex_module_action.module_id='$module_id' $addsql");

					for ($i=0;$i<$ga->getRows();$i++)
					{
						$iaction = $ga->getValue("rex_action.action");
						$iaction = str_replace("REX_MODULE_ID",$module_id,$iaction);
						$iaction = str_replace("REX_SLICE_ID",$slice_id,$iaction);
						$iaction = str_replace("REX_CATEGORY_ID",$category_id,$iaction);
						$iaction = str_replace("REX_ARTICLE_ID",$article_id,$iaction);
						$iaction = str_replace("REX_CTYPE",$ctype,$iaction);
						$iaction = str_replace("REX_CLANG",$clang,$iaction);
						$iaction = str_replace("REX_PHP",$REX_ACTION[PHP],$iaction);
						$iaction = str_replace("REX_HTML",$REX_ACTION[HTML],$iaction);
						for ($j=1;$j<11;$j++)
						{
							$iaction = str_replace("REX_VALUE[$j]",$REX_ACTION[VALUE][$j],$iaction);
							$iaction = str_replace("REX_LINK[$j]",$REX_ACTION[LINK][$j],$iaction);
							$iaction = str_replace("FILE[$j]",$REX_ACTION[FILE][$j],$iaction);
						}
						eval("?>".$iaction);
						if ($REX_ACTION[MSG]!="") $message .= " | ".$REX_ACTION[MSG];
						$ga->next();
					}
					// ----- / POST ACTION
					if($update!=1){
	                    $slice_id = "";
	                    $function = "";
	                }
	                $save = "";
	                
					$EA = new sql;
					$EA->setTable("rex_article");
					$EA->where("id='$article_id' and clang=$clang");
					$EA->setValue("updatedate",time());
					$EA->setValue("updateuser",$REX_USER->getValue("login"));
	                $EA->update();
	                
					rex_generateArticle($article_id);

				}else
				{
					$message = $I18N->msg('no_rights_to_this_function');
					$slice_id = "";
					$function = "";
					$module_id = "";
					$save = "";
				}

			}else
			{
				// ------------- MODUL IST NICHT VORHANDEN
				$message = $I18N->msg('module_not_found');
				$slice_id = "";
				$function = "";
				$module_id = "";
				$save = "";
			}

		}elseif($function=="delete")
		{

			// --------------------- SLICE DELETE

			$CM = new sql;
			$CM->setQuery("select * from rex_article_slice left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id where rex_article_slice.id='$slice_id' and clang=$clang");

			if ($CM->getRows()==1)
			{
				if (($CM->getValue("php_enable")==0 or $REX_USER->isValueOf("rights","module[php]")) and ($CM->getValue("html_enable")==0 or $REX_USER->isValueOf("rights","module[html]")))
				{

					// ------------------------------------------ SLICE DELETE

					if ($save == 1)
					{
						$module_id = $CM->getValue("rex_article_slice.modultyp_id");
						$REX_ACTION[PHP] = $CM->getValue("rex_article_slice.php");
						$REX_ACTION[HTML] = $CM->getValue("rex_article_slice.html");
						for ($i=1;$i<11;$i++)
						{
							$REX_ACTION[VALUE][$i] = $CM->getValue("rex_article_slice.value$i");
							$REX_ACTION[LINK][$i] = $CM->getValue("rex_article_slice.link$i");
							$REX_ACTION[FILE][$i] = $CM->getValue("rex_article_slice.file$i");
						}

						// ----- PRE ACTION [DELETE]
						$addsql = " and rex_action.prepost=0 and rex_action.status=2"; // pre-action and delete
						$ga = new sql;
						$ga->setQuery("select * from rex_module_action,rex_action where rex_module_action.action_id=rex_action.id and rex_module_action.module_id='$module_id' $addsql");
						for ($i=0;$i<$ga->getRows();$i++)
						{
							$iaction = $ga->getValue("rex_action.action");
							$iaction = str_replace("REX_MODULE_ID",$module_id,$iaction);
							$iaction = str_replace("REX_SLICE_ID",$slice_id,$iaction);
							$iaction = str_replace("REX_CATEGORY_ID",$category_id,$iaction);
							$iaction = str_replace("REX_ARTICLE_ID",$article_id,$iaction);
							$iaction = str_replace("REX_CTYPE",$ctype,$iaction);
							$iaction = str_replace("REX_CLANG",$clang,$iaction);
							$iaction = str_replace("REX_PHP",$REX_ACTION[PHP],$iaction);
							$iaction = str_replace("REX_HTML",$REX_ACTION[HTML],$iaction);
							for ($j=1;$j<11;$j++)
							{
								$iaction = str_replace("REX_VALUE[$j]",$REX_ACTION[VALUE][$j],$iaction);
								$iaction = str_replace("REX_LINK[$j]",$REX_ACTION[LINK][$j],$iaction);
								$iaction = str_replace("FILE[$j]",$REX_ACTION[FILE][$j],$iaction);
							}
							eval("?>".$iaction);
							if ($REX_ACTION[MSG]!="") $message .= " | ".$REX_ACTION[MSG];
							$ga->next();
						}
						// ----- / PRE ACTION


						// sicher loeschen: ja
						$re_id 	= $CM->getValue("rex_article_slice.re_article_slice_id");

						$newsql	= new sql;
						$newsql->setQuery("select * from rex_article_slice where re_article_slice_id='$slice_id'");
						if ($newsql->getRows()>0)
						{
							$newsql->query("update rex_article_slice set re_article_slice_id='$re_id' where id='".$newsql->getValue("id")."'");
						}
						$newsql->query("delete from rex_article_slice where id='$slice_id'");
						$message = $I18N->msg('block_deleted');

						$EA = new sql;
						$EA->setTable("rex_article");
						$EA->where("id='$article_id' and clang=$clang");
						$EA->setValue("updatedate",time());
						$EA->setValue("updateuser",$REX_USER->getValue("login"));
		                $EA->update();

						rex_generateArticle($article_id);

						// ----- POST ACTION [DELETE]
						$addsql = " and rex_action.prepost=1 and rex_action.status=2"; // pre-action and delete
						$ga = new sql;
						$ga->setQuery("select * from rex_module_action,rex_action where rex_module_action.action_id=rex_action.id and rex_module_action.module_id='$module_id' $addsql");
						for ($i=0;$i<$ga->getRows();$i++)
						{
							$iaction = $ga->getValue("rex_action.action");
							$iaction = str_replace("REX_MODULE_ID",$module_id,$iaction);
							$iaction = str_replace("REX_SLICE_ID",$slice_id,$iaction);
							$iaction = str_replace("REX_CATEGORY_ID",$category_id,$iaction);
							$iaction = str_replace("REX_ARTICLE_ID",$article_id,$iaction);
							$iaction = str_replace("REX_CTYPE",$ctype,$iaction);
							$iaction = str_replace("REX_CLANG",$clang,$iaction);
							$iaction = str_replace("REX_PHP",$REX_ACTION[PHP],$iaction);
							$iaction = str_replace("REX_HTML",$REX_ACTION[HTML],$iaction);
							for ($j=1;$j<11;$j++)
							{
								$iaction = str_replace("REX_VALUE[$j]",$REX_ACTION[VALUE][$j],$iaction);
								$iaction = str_replace("REX_LINK[$j]",$REX_ACTION[LINK][$j],$iaction);
								$iaction = str_replace("FILE[$j]",$REX_ACTION[FILE][$j],$iaction);
							}
							eval("?>".$iaction);
							if ($REX_ACTION[MSG]!="") $message .= $REX_ACTION[MSG]." | ";
							$ga->next();
						}
						// ----- / POST ACTION

					}elseif ($save == 2)
					{
						// sicher loesche: nein
						$function = "";
						$slice_id = "";
					}else
					{
						// sicher loeschen ?
					}

				}else
				{
					$message = $I18N->msg('block_not_deleted').". ".$I18N->msg('no_rights_to_this_module');
					$function = "";
					$slice_id = "";
				}
			}else
			{
					$message = $I18N->msg('block_not_deleted').". ".$I18N->msg('error');
					$function = "";
					$slice_id = "";
			}


		}


		// --------------------------------------------------------------------- CONTENT HEAD MENUE

		reset($REX[CTYPE]);
		if (count($REX[CTYPE])>1)
		{
			$tadd = "<b>Typen:</b> | ";
			while( list($key,$val) = each($REX[CTYPE]) )
			{
				if ($key==$ctype) $tadd .= "$val | ";
				else $tadd .= "<a href=index.php?page=content&clang=$clang&ctype=$key&category_id=$category_id&article_id=$article_id>$val</a> | ";
			}
			$tadd .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}else
		{
			$tadd = "";
		}

		$menu = $tadd." <a href=../index.php?article_id=$article_id&clang=$clang&ctype=$ctype class=blue target=_blank>".$I18N->msg('show')."</a>";
		if ($mode=="edit") $menu.= " | <a href=index.php?page=content&article_id=$article_id&mode=edit&category_id=".$article->getValue("category_id")."&clang=$clang&ctype=$ctype class=black>".$I18N->msg('edit_mode')."</a> | <a href=index.php?page=content&article_id=$article_id&mode=meta&category_id=".$article->getValue("category_id")."&clang=$clang&ctype=$ctype class=blue>".$I18N->msg('metadata')."</a>";
		else $menu.= " | <a href=index.php?page=content&article_id=$article_id&mode=edit&category_id=".$article->getValue("category_id")."&clang=$clang&ctype=$ctype class=blue>".$I18N->msg('edit_mode')."</a> | <a href=index.php?page=content&article_id=$article_id&mode=meta&category_id=".$article->getValue("category_id")."&clang=$clang&ctype=$ctype class=black>".$I18N->msg('metadata')."</a>";
		$menu .= "";

		
		echo "	<table border=0 cellpadding=0 cellspacing=1 width=770>
				<tr>
					<td align=center class=grey width=30><img src=pics/document.gif width=16 height=16 border=0 vspace=5 hspace=12></td>
					<td align=left class=grey>&nbsp;$menu</td>
					<td align=left class=grey width=153><img src=pics/leer.gif width=153 height=20></td>
				</tr>";




		if ($message != ""){ echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16 vspace=4></td><td class=warning>&nbsp;&nbsp;$message</td><td class=lgrey>&nbsp;</td></tr>"; }

		echo "	<tr>
					<td class=lgrey>&nbsp;</td>
					<td valign=top class=lblue>";


		if ($mode == "edit")
		{
			// -------------------------------------------- EDIT VIEW

			// preview, add, edit, delete , module mode

			$CONT = new article;
			$CONT->setArticleId($article_id);
			$CONT->setSliceId($slice_id);
			$CONT->setMode($mode);
			$CONT->setCLang($clang);
			$CONT->setCType($ctype);
			$CONT->setEval(TRUE);
			$CONT->setFunction($function);

			eval("?>".$CONT->getArticle());

		}elseif ($mode == "meta")
		{

			// -------------------------------------------- META VIEW
			
			$extens = "";
			$category_id = $article->getValue("category_id");

			if ($FUNC_MOVE != "" && $func_category_id > 0 && $REX_USER->isValueOf("rights","advancedMode[]"))
			{
				if ($article->getValue("startpage")==1)
				{
					$err_msg = $I18N->msg('article_cannot_be_moved')." ".$I18N->msg('start_article_has_to_stay_in_category');
				}else
				{
					$err_msg = moveArticle($article_id,$func_category_id,$category_id);
					$category_id = $func_category_id;
				}
			}elseif ($FUNC_COPY != "" && $func_category_id > 0 && $REX_USER->isValueOf("rights","advancedMode[]"))
			{
				copyArticle($article_id,$func_category_id);
				$err_msg = $I18N->msg('article_copied');
			}

			if ($save == "1")
			{
				$meta_sql = new sql;
				$meta_sql->setTable("rex_article");
				// $meta_sql->debugsql = 1;
				$meta_sql->where("id='$article_id' and clang=$clang");
				$meta_sql->setValue("online_from",mktime(0,0,0,$monat_von,$tag_von,$jahr_von));
				$meta_sql->setValue("online_to",mktime(0,0,0,$monat_bis,$tag_bis,$jahr_bis));
				$meta_sql->setValue("keywords",$meta_keywords);
				$meta_sql->setValue("description",$meta_description);
				$meta_sql->setValue("name",$meta_article_name);
				$meta_sql->setValue("type_id",$type_id);
				$meta_sql->setValue("teaser",$meta_teaser);
				$meta_sql->setValue("updatedate",time());
				$meta_sql->setValue("updateuser",$REX_USER->getValue("login"));
		        
				// cache
				$Cache = new Cache($article_id);
				if($caching!=1){
					$Cache->removeCacheConf($article_id);
				} else {
					$Cache->insertCacheConf($article_id);
				}
				if($recaching==1){
					$Cache->removeCacheFiles($article_id);
				}

				// -------------------------- FILE UPLOAD META BILD/FILE

				if ($REX_MEDIA_1 == "delete file") $REX_MEDIA_1 = "";
				$meta_sql->setValue("file",$REX_MEDIA_1);

				// ----------------------------- / FILE UPLOAD

				$meta_sql->update();

				$article->setQuery("select * from rex_article where id='$article_id'");
				$err_msg = $I18N->msg("metadata_updated").$message;

				rex_generateArticle($article_id);
			}

			$typesel = new select();
			$typesel->set_name("type_id");
			$typesel->set_style("width:100%;");
			$typesel->set_size(1);
			$typesql = new sql();
			$typesql->setQuery("select * from rex_article_type order by name");

			for ($i=0;$i<$typesql->getRows();$i++)
			{
				$typesel->add_option($typesql->getValue("name"),$typesql->getValue("type_id"));
				$typesql->next();
			}

			$typesel->set_selected($article->getValue("type_id"));

			if ($typesql->getRows()==0) $out = "<input type=hidden name=type_id value=0>";
			else $out = "<tr><td class=grey>".$I18N->msg("article_type_list_name")."</td><td class=grey>".$typesel->out()."</td></tr>";


			echo "	<table border=0 cellpadding=5 cellspacing=1 width=100%>
				<form action=index.php method=post ENCTYPE=multipart/form-data name=REX_FORM>
				<input type=hidden name=page value=content>
				<input type=hidden name=article_id value='$article_id'>
				<input type=hidden name=mode value='meta'>
				<input type=hidden name=save value=1>
				<input type=hidden name=clang value=$clang>
				<input type=hidden name=ctype value=$ctype>
				<tr>
					<td colspan=2>".$I18N->msg("general")."</td>
				</tr>";

			if ($err_msg != "") echo "<tr><td colspan=2 class=warning><font class=warning>$err_msg</font></td></tr>";

			function selectdate($date,$extens){

				$date = date("Ymd",$date);
				$ausgabe = "<select name=jahr$extens size=1>\n";
				for ($i=1999;$i<2011;$i++){
					$ausgabe .= "<option value=\"$i\"";
					if ($i == substr($date,0,4)){ $ausgabe .= " selected"; }
					$ausgabe .= ">$i\n";	
				}
				$ausgabe .= "</select>";
				$ausgabe .= "<select name=monat$extens size=1>\n";
				for ($i=1;$i<13;$i++){
					if ($i<10){ $ii = "0".$i; }else{ $ii = $i; }
					$ausgabe .= "<option value=\"$ii\"";
					if ($ii == substr($date,4,2)){ $ausgabe .= " selected"; }
					$ausgabe .= ">$ii\n";	
				}
				$ausgabe .= "</select>";
				$ausgabe .= "<select name=tag$extens size=1>\n";
				for ($i=1;$i<32;$i++){
					if ($i<10){ $ii = "0".$i; }else{ $ii = $i; }
					$ausgabe .= "<option value=\"$ii\"";		
					if ($ii == substr($date,6,2)){ $ausgabe .= " selected"; }
					$ausgabe .= ">$ii\n";	
				}
				$ausgabe .= "</select>";	
				return $ausgabe;
			}

			echo "
				<tr>
					<td class=grey width=150>".$I18N->msg("online_from")."</td>
					<td class=grey>".selectdate($article->getValue("online_from"),"_von")."</td>
				</tr>
				<tr>
					<td class=grey>".$I18N->msg("online_to")."</td>
					<td class=grey>".selectdate($article->getValue("online_to"),"_bis")."</td>
				</tr>
				<tr>
					<td class=grey>".$I18N->msg("name_description")."</td>
					<td class=grey><input type=text name=meta_article_name value=\"".htmlentities($article->getValue("name"))."\" size=30 style=\"width:100%;\"></td>
				</tr>
				<tr>
					<td class=grey>".$I18N->msg("description")."</td>
					<td class=grey><textarea name=meta_description cols=30 rows=5 style='width:100%;'>".htmlentities($article->getValue("description"))."</textarea></td>
				</tr>
				<tr>
					<td class=grey>".$I18N->msg("keywords")."</td>
					<td class=grey><textarea name=meta_keywords cols=30 rows=5 style='width:100%;'>".htmlentities($article->getValue("keywords"))."</textarea></td>
				</tr>";

			echo "<tr><td class=grey>".$I18N->msg("metadata_image")."</td><td class=grey>";
						
			echo "	<table>
				<input type=hidden name=REX_MEDIA_DELETE_1 value=0 id=REX_MEDIA_DELETE_1>
				<tr>
				<td><input type=text size=30 name=REX_MEDIA_1 value='".$article->getValue("file")."' id=REX_MEDIA_1 readonly=readonly></td>
				<td><a href=javascript:openREXMedia(1);><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>
				<td><a href=javascript:deleteREXMedia(1);><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>
				<td><a href=javascript:addREXMedia(1)><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td>
				</tr></table>";
			echo "</td></tr>";

			echo "<tr bgcolor=#eeeeee>";
			if ($article->getValue("teaser")==1) echo "<td align=right class=grey><input type=checkbox name=meta_teaser checked value=1></td>";
			else echo "<td align=right class=grey><input type=checkbox name=meta_teaser value=1></td>";
			echo "	<td class=grey>".$I18N->msg("teaser")."</td>
				</tr>";

			echo "	</tr>
				$out
				 ";

			// advanced caching
			if($REX_USER->isValueOf("rights","caching[]")){

				include_once("include/classes/class.cache.inc.php");
				$Cache = new Cache($article_id);
				if($Cache->isCacheConf()){
					$cacheCheck="checked";
				} else {
					$cacheCheck="";
				}
				echo "
				<tr>
					<td class=grey width=150>Caching</td>
					<td class=grey valign=middle><input type=checkbox name=caching value=1 ".$cacheCheck."> ".$I18N->msg("yes")." <input type=checkbox name=recaching value=1 > ".$I18N->msg("cache_remove")."</td>
				</tr>";
			}

			echo "
				<tr>
					<td class=grey>&nbsp;</td>
					<td class=grey><input type=submit value='".$I18N->msg("update_metadata")."' size=8></td>
				</tr>
				</form>
				</table>";

			if($REX_USER->isValueOf("rights","advancedMode[]"))
			{

				echo "<table border=0 cellpadding=5 cellspacing=1 width=100%>
					<form action=index.php method=get>
					<input type=hidden name=page value=content>
					<input type=hidden name=article_id value='$article_id'>
					<input type=hidden name=mode value='meta'>
					<input type=hidden name=clang value=$clang>
					<input type=hidden name=ctype value=$ctype>
					<tr>
						<td colspan=2>".$I18N->msg("other_functions")."</td>
					</tr>
					<tr>

						<td class=grey width=150>".$I18N->msg("category")."</td>
						<td class=grey><select name=func_category_id size=1 style='width:100%;'>";

				$csql = new sql;
				$csql->setQuery("select * from rex_category order by re_category_id");

				for ($i=0;$i<$csql->getRows();$i++)
				{
					echo "<option value=".$csql->getValue("id");
					if ($category_id==$csql->getValue("id")) echo " selected";
					echo ">".
					     $csql->getValue("name")." [".$csql->getValue("id")."]".
					     "</option>";

					$csql->next();
				}

				echo "</select></td>
					</tr>
					<tr>
						<td class=grey>&nbsp;</td>
						<td class=grey>";
				if ($article->getValue("startpage")!=1) echo "<input type=submit name=FUNC_MOVE value=\"".$I18N->msg("move_article")."\" size=8>";
				echo "<input type=submit name=FUNC_COPY value=\"".$I18N->msg("copy_article")."\" size=8>";

				echo "</td>
					</tr>
					</form>
					</table>";
			}

		}

		echo "		</td>
					<td class=lgrey>&nbsp;</td>
				</tr>
				</table>";

	}else
	{
	    	echo "<table border=1 cellpadding=6 cellspacing=0 width=770 bgcolor=#eeeeee>
			<tr bgcolor='#eeeeee'><td class=warning><br><br>&nbsp;&nbsp;".$I18N->msg("no_rights_to_edit")."<br><br><br></td></tr></table>";
	}
}

?>
