<?

$article = new sql;
$article->setQuery("select * from rex_article where id='$article_id'");

if ($article->getRows() == 1)
{
	$category_id = $article->getValue("category_id");

	// --------------------------------------------- permissions
	
	$STRUCTURE_PERM = FALSE;
	if ($REX_USER->isValueOf("rights","structure[all]")) $STRUCTURE_PERM = TRUE;

	
	// --------------------------------------------- category pfad und rechte
	
	include $REX[INCLUDE_PATH]."/functions/function_rex_category.inc.php";
	title("Artikel",$KATout);


	// ----------------- HAT USER DIE RECHTE AN DIESEM ARTICLE
	
	if ($STRUCTURE_PERM || $REX_USER->isValueOf("rights","article[$article_id]") || $REX_USER->isValueOf("rights","article[all]"))
	{
		
		// ------------------- SLICE VERSCHIEBEN NACH OBEN ODER UNTEN
		
		if ($REX_USER->isValueOf("rights","advancedMode[]"))
		{
			
			
			
		}
	
	
		// ------------------------------------------ SLICE EDIT / ADD / DELETE

		if (($function == "add" or $function == "edit") and $save==1)
		{
			// ------------------------------------------ check module

			$CM = new sql;
			
			if ($function == "edit") $CM->setQuery("select * from rex_article_slice left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id where rex_article_slice.id='$slice_id'");
			else $CM->setQuery("select * from rex_modultyp where id='$module_id'");
			
			if ($CM->getRows()==1)
			{

				// ------------------- modul ist vorhanden
				
				if (($CM->getValue("php_enable")==0 or $REX_USER->isValueOf("rights","module[php]")) and ($CM->getValue("html_enable")==0 or $REX_USER->isValueOf("rights","module[html]")))
				{
					
					$message = "";
					
					// ------------------------------------------ slices edit/add
					$newsql = new sql;
					$newsql->setTable("rex_article_slice");

					if ($function == "edit")
					{
						$newsql->where("id='$slice_id'");
					}else
					{
						$newsql->setValue("re_article_slice_id",$slice_id);
						$newsql->setValue("article_id",$article_id);
						$newsql->setValue("modultyp_id",$module_id);
					}
					
					for ($i=1;$i<11;$i++)
					{
						$newsql->setValue("value$i",$VALUE[$i]);
						$newsql->setValue("link$i",$LINK[$i]);
					}
					
					if ($REX_USER->isValueOf("rights","module[html]")) $newsql->setValue("html",$INPUT_HTML);
					if ($REX_USER->isValueOf("rights","module[php]")) $newsql->setValue("php",$INPUT_PHP);
					
					for ($fi=1;$fi<11;$fi++)
					{
						$FILE	  = "FILE".$fi;
						$FILEDEL  = "FILEDEL".$fi;
									
						if ($$FILE != "" and $$FILE != "none")
						{
							$FILENAME = "FILE".$fi."_name";
							$FILESIZE = "FILE".$fi."_size";
							$FILETYPE = "FILE".$fi."_type";
							$FILESQL  = "FILESQL".$fi;
				        	$NFILENAME = "";				        		
				        		
				        	// generiere neuen dateinamen
				        	for ($cn=0;$cn<strlen($$FILENAME);$cn++)
							{
								$char = substr($$FILENAME,$cn,1);
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
							
				        		if ( $NFILE_EXT == ".php" || $NFILE_EXT == ".php3" || $NFILE_EXT == ".php4" || $NFILE_EXT == ".pl" || $NFILE_EXT == ".asp"|| $NFILE_EXT == ".aspx"|| $NFILE_EXT == ".cfm" )
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

				        		if (!move_uploaded_file($$FILE,$REX[MEDIAFOLDER]."/$NFILENAME"))
				        		{
				        			$message .= "move file $fi failed | ";
				        		}else
				        		{
								$$FILESQL = new sql;
								$$FILESQL->setTable("rex_file");
								$$FILESQL->setValue("filetype",$$FILETYPE);
								$$FILESQL->setValue("filename",$NFILENAME);
								$$FILESQL->setValue("originalname",$$FILENAME);
								$$FILESQL->setValue("filesize",$$FILESIZE);
								$$FILESQL->insert();
					        		
					        		$newsql->setValue("file".$fi,$NFILENAME);
							}				        		
				        	}elseif($$FILEDEL == "on")
				        	{
				        		$newsql->setValue("file".$fi,'');
				        	}
				        }		
					
					if ($function == "edit")
					{
						$newsql->update();
						$message .= $I18N->msg('block_updated');
					}else
					{
						$newsql->insert();
						$last_id = $newsql->last_insert_id;
						$newsql->query("update rex_article_slice set re_article_slice_id='$last_id' where re_article_slice_id='$slice_id' and id<>'$last_id' and article_id='$article_id'");
						$message .= $I18N->msg('block_added');
					}

					$slice_id = "";
					$function = "";
					$save = "";
					
					generateArticle($article_id);
					
				}else
				{
					$message	= $I18N->msg('no_rights_to_this_function');
					$slice_id = "";
					$function = "";
					$module_id = "";
					$save = "";
				}
				
			}else
			{
				// ------------- MODUL IST NICHT VORHANDEN
				
				$message	= $I18N->msg('module_not_found');
				$slice_id = "";
				$function = "";
				$module_id = "";
				$save = "";
			}
			
		}elseif($function=="delete")
		{
			
			// --------------------- SLICE DELETE
			
			$CM = new sql;
			$CM->setQuery("select * from rex_article_slice left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id where rex_article_slice.id='$slice_id'");
			
			if ($CM->getRows()==1)
			{
				if (($CM->getValue("php_enable")==0 or $REX_USER->isValueOf("rights","module[php]")) and ($CM->getValue("html_enable")==0 or $REX_USER->isValueOf("rights","module[html]")))
				{
					
					// ------------------------------------------ SLICE DELETE
					
					if ($save == 1)
					{
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
						
						generateArticle($article_id);
						
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

		
		// --------------------------------------------------------------------- SLICES AUSGABE

		if ($mode=="") $menu = "<a href=index.php?page=content&article_id=$article_id&category_id=".$article->getValue("category_id")." class=black>".$I18N->msg('preview')."</a>";
		else $menu = "<a href=index.php?page=content&article_id=$article_id&category_id=".$article->getValue("category_id")." class=blue>".$I18N->msg('preview')."</a>";
		if ($mode=="edit") $menu.= " | <a href=index.php?page=content&article_id=$article_id&mode=edit&category_id=".$article->getValue("category_id")." class=black>".$I18N->msg('edit_mode')."</a>";
		else $menu.= " | <a href=index.php?page=content&article_id=$article_id&mode=edit&category_id=".$article->getValue("category_id")." class=blue>".$I18N->msg('edit_mode')."</a>";
		if ($mode=="meta") $menu.= " | <a href=index.php?page=content&article_id=$article_id&mode=meta&category_id=".$article->getValue("category_id")." class=black>".$I18N->msg('metadata')."</a>";
		else $menu.= " | <a href=index.php?page=content&article_id=$article_id&mode=meta&category_id=".$article->getValue("category_id")." class=blue>".$I18N->msg('metadata')."</a>";
		
		echo "	<table border=0 cellpadding=0 cellspacing=1 width=770>
				<tr>
					<td align=center class=grey width=50><img src=pics/document.gif width=16 height=16 border=0><br><img src=pics/leer.gif width=30 height=1></td>
					<td align=left class=grey>&nbsp;&nbsp;$menu</td>
					<td align=left class=grey width=153><img src=pics/leer.gif width=153 height=20></td>
				</tr>";

		if ($message != ""){ echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16 vspace=4></td><td class=warning>&nbsp;&nbsp;$message</td><td class=lgrey>&nbsp;</td></tr>"; }

		echo "	<tr>
					<td class=lgrey>&nbsp;</td>
					<td valign=top class=lblue>";
		
		
		// ---------------------------------------------------------------------- METADATEN
		
		if ($mode == "meta")
		{
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
				#$debugsql = 1;
				$meta_sql = new sql;
				$meta_sql->setTable("rex_article");
				$meta_sql->where("id='$article_id'");
				$meta_sql->setValue("online_von",$jahr_von.$monat_von.$tag_von);
				$meta_sql->setValue("online_bis",$jahr_bis.$monat_bis.$tag_bis);
				$meta_sql->setValue("suchbegriffe",$suchbegriffe);
				$meta_sql->setValue("beschreibung",$beschreibung);
				$meta_sql->setValue("name",$article_name);
				$meta_sql->setValue("type_id",$type_id);
				$meta_sql->setValue("checkbox01",$checkbox01);
				
				// -------------------------- FILE UPLOAD META BILD/FILE
				
				$FILE	  = "METAFILE";
				$FILEDEL  = "METAFILEDEL";
									
			        if ($METAFILE != "" and $METAFILE != "none")
		        	{
		        		$FILENAME = "METAFILE_name";
					$FILESIZE = "METAFILE_size";
					$FILETYPE = "METAFILE_type";
		        		$NFILENAME = "";
		        		
		        		// generiere neuen dateinamen
		        		for ($cn=0;$cn<strlen($$FILENAME);$cn++)
					{
						$char = substr($$FILENAME,$cn,1);
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
					
		        		if ( $NFILE_EXT == ".php" || $NFILE_EXT == ".php3" || $NFILE_EXT == ".php4" || $NFILE_EXT == ".pl" || $NFILE_EXT == ".asp"|| $NFILE_EXT == ".aspx"|| $NFILE_EXT == ".cfm" )
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
	
		        		if (!move_uploaded_file($$FILE,$REX[MEDIAFOLDER]."/$NFILENAME"))
		        		{
		        			$message = " - Datei '$fi' verschieben fehlgeschlagen | ";
		        		}else
		        		{
						$FILESQL = new sql;
						$FILESQL->setTable("rex_file");
						$FILESQL->setValue("filetype",$$FILETYPE);
						$FILESQL->setValue("filename",$NFILENAME);
						$FILESQL->setValue("originalname",$$FILENAME);
						$FILESQL->setValue("filesize",$$FILESIZE);
						$FILESQL->insert();
			        		
			        		$meta_sql->setValue("file",$NFILENAME);
					}
				}elseif($$FILEDEL == "on")
				{
					$meta_sql->setValue("file",'');
				}		
				
				// ----------------------------- / FILE UPLOAD
				
				$meta_sql->update();
				
				$article->setQuery("select * from rex_article where id='$article_id'");
				$err_msg = "Metadaten wurden aktualisiert$message";
				
				generateArticle($article_id);
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

			if ($typesql->getRows()==1) $out = "<input type=hidden name=type_id value=1>";
			else $out = "<tr><td class=grey>Artikeltyp</td><td class=grey>".$typesel->out()."</td></tr>";


			echo "	<table border=0 cellpadding=5 cellspacing=1 width=100%>
				<form action=index.php method=post ENCTYPE=multipart/form-data>
				<input type=hidden name=page value=content>
				<input type=hidden name=article_id value='$article_id'>
				<input type=hidden name=mode value='meta'>
				<input type=hidden name=save value=1>
				<tr>
					<td colspan=2>Allgemein</td>
				</tr>";
			
			if ($err_msg != "") echo "<tr><td colspan=2 class=warning><font class=warning>$err_msg</font></td></tr>";
			
			echo "
				<tr>
					<td class=grey width=150>Online vom</td>
					<td class=grey>".selectdate($article->getValue("online_von"),"_von")."</td>
				</tr>
				<tr>
					<td class=grey>bis zum</td>
					<td class=grey>".selectdate($article->getValue("online_bis"),"_bis")."</td>
				</tr>
				<tr>
					<td class=grey>Name/Bezeichnung</td>
					<td class=grey><input type=text name=article_name value=\"".htmlentities($article->getValue("name"))."\" size=30 style=\"width:100%;\"></td>
				</tr>
				<tr>
					<td class=grey>Beschreibung</td>
					<td class=grey><textarea name=beschreibung cols=30 rows=5 style='width:100%;'>".htmlentities($article->getValue("beschreibung"))."</textarea></td>
				</tr>
				<tr>
					<td class=grey>Suchbegriffe</td>
					<td class=grey><textarea name=suchbegriffe cols=30 rows=5 style='width:100%;'>".htmlentities($article->getValue("suchbegriffe"))."</textarea></td>
				</tr>";
			
			if ($article->getValue("file")!="")
			{
				echo "<tr>
					<td class=grey>Metafile/bild</td>
					<td class=grey><img src=../files/".$article->getValue("file")." width=250></td>
				</tr>
				<tr>
					<td class=grey align=right><input type=checkbox name=METAFILEDEL></td>
					<td class=grey>File löschen</td>
				</tr>";
			}else
			{
				echo "<tr>
					<td class=grey>Artikelbild/-file</td>
					<td class=grey><INPUT NAME=METAFILE TYPE=file size=2></td>
				</tr>";
			}			
			
			echo "<tr bgcolor=#eeeeee>";

			if ($article->getValue("checkbox01")==1) echo "<td align=right class=grey><input type=checkbox name=checkbox01 checked value=1></td>";
			else echo "<td align=right class=grey><input type=checkbox name=checkbox01 value=1></td>";

			echo "	<td class=grey>Auf die Startseite als Teaser nehmen</td>
				</tr>";
			
			echo "	</tr>
				$out
				<tr>
					<td class=grey>&nbsp;</td>
					<td class=grey><input type=submit value='Metadaten aktualisieren' size=8></td>
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
					<tr>
						<td colspan=2>Sonstige Funktionen</td>
					</tr>
					<tr>
						
						<td class=grey width=150>Kategorie</td>
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
				if ($article->getValue("startpage")!=1) echo "<input type=submit name=FUNC_MOVE value=\"Artikel verschieben\" size=8>";
				echo "<input type=submit name=FUNC_COPY value=\"Artikel kopieren\" size=8>";
				
				echo "</td>
					</tr>
					</form>
					</table>";
			}	
							
		}else
		{
			// preview, add, edit, delete , module mode
						
			$CONT = new article;
			$CONT->setArticleId($article_id);
			$CONT->setSliceId($slice_id);
			$CONT->setMode($mode);
			$CONT->setEval(TRUE);
			$CONT->setFunction($function);
					
			eval("?>".$CONT->getArticle());
		}
		
		echo "		</td>
					<td class=lgrey>&nbsp;</td>
				</tr>
				</table>";
		
	}else
	{
	    	echo "<table border=1 cellpadding=6 cellspacing=0 width=770 bgcolor=#eeeeee>
			<tr bgcolor=#eeeeee><td class=warning>Sie haben keine Editiererlaubnis !</td></tr></table>";
	}
}

?>