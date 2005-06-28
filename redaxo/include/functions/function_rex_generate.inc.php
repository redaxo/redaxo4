<?php

// ----------------------------------------- Alles generieren

function rex_generateAll()
{

	global $REX, $I18N;

	// alles existiert schon
	// -> generiere templates
	// -> generiere article und listen
	// -> generiere file meta
	// -> loesche cache


	// ----------------------------------------------------------- generiere templates
	rex_deleteDir($REX[INCLUDE_PATH]."/generated/templates",0);
	// mkdir($REX[INCLUDE_PATH]."/generated/templates",$REX[FILEPERM]);
	$gt = new sql;
	$gt->setQuery("select * from rex_template");
	for ($i=0;$i<$gt->getRows();$i++)
	{
		$fp = fopen ($REX[INCLUDE_PATH]."/generated/templates/".$gt->getValue("rex_template.id").".template", "w");
		fputs($fp,$gt->getValue("rex_template.content"));
		fclose($fp);
		chmod($REX[INCLUDE_PATH]."/generated/templates/".$gt->getValue("rex_template.id").".template",0777);
		$gt->next();
	}


	// ----------------------------------------------------------- generiere artikel
	rex_deleteDir($REX[INCLUDE_PATH]."/generated/articles",0);
	// mkdir($REX[INCLUDE_PATH]."/generated/articles",$REX[FILEPERM]);
	$gc = new sql;
	$gc->setQuery("select distinct id from rex_article");
	for ($i=0;$i<$gc->getRows();$i++)
	{
		rex_generateArticle($gc->getValue("id"));
		$gc->next();
	}


	// ----------------------------------------------------------- generiere clang
	$lg = new sql();
	$lg->setQuery("select * from rex_clang order by id");
	$content = "// --- DYN\n\r";
	for ($i=0;$i<$lg->getRows();$i++)
	{
		$id = $lg->getValue("id");
		$name = $lg->getValue("name");
		$content .= "\n\r\$REX[CLANG][$id] = \"$name\";";
		$lg->next();
	}
	$content .= "\n\r// --- /DYN";
	$file = $REX[INCLUDE_PATH]."/clang.inc.php";
	$h = fopen($file,"r");
	$fcontent = fread($h,filesize($file));
	$fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)",$content,$fcontent);
	fclose($h);
	$h = fopen($file,"w+");
	fwrite($h,$fcontent,strlen($fcontent));
	fclose($h);
	chmod($file,0777);


	// ----------------------------------------------------------- generiere filemetas ...
	// **********************


	// ----------------------------------------------------------- delete cache
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();


	// ----------------------------------------------------------- message
	$MSG = $I18N->msg('articles_generated')." ".$I18N->msg('old_articles_deleted');

	return $MSG;
}




// ----------------------------------------- ARTICLE

function rex_generateArticle($id,$refresh=0)
{
	global $PHP_SELF,$module_id,$FORM,$REX_USER,$REX,$I18N;

	// artikel generieren
	// vorraussetzung: articel steht schon in der datenbank
	//
	// -> infos schreiben -> abhaengig von clang
	// --> artikel infos / einzelartikel metadaten
	// --> artikel content / einzelartikel content
	// --> listen generieren // wenn startpage = 1
	// ---> artikel liste
	// ---> category liste
	// --> cache loeschen

	// --------------------------------------------------- generiere generated/articles/xx.article

	$CL = $REX[CLANG];
	reset($CL);
	for ($i=0;$i<count($CL);$i++)
	{

		$clang = key($CL);
		$REX[RC] = true; // keine Ausgabe als eval(CONTENT) sondern nur speichern in datei
		$CONT = new article;
		$CONT->setCLang($clang);
		$CONT->setArticleId($id);
		$article_content = "?>".$CONT->getArticle();

		// --------------------------------------------------- Artikelparameter speichern
		$article = "<?\n".
					"\n\$REX[ART][$id][article_id][$clang] = \"$id\";".
					"\n\$REX[ART][$id][re_id][$clang] = \"".addslashes($CONT->getValue("re_id"))."\";".
					"\n\$REX[ART][$id][name][$clang] = \"".addslashes($CONT->getValue("name"))."\";".
					"\n\$REX[ART][$id][catname][$clang] = \"".addslashes($CONT->getValue("catname"))."\";".
					"\n\$REX[ART][$id][cattype][$clang] = \"".addslashes($CONT->getValue("name"))."\";".
					"\n\$REX[ART][$id][alias][$clang] = \"".addslashes($CONT->getValue("name"))."\";".
					"\n\$REX[ART][$id][description][$clang] = \"".addslashes($CONT->getValue("description"))."\";".
					"\n\$REX[ART][$id][attribute][$clang] = \"".addslashes($CONT->getValue("attribute"))."\";".
					"\n\$REX[ART][$id][file][$clang] = \"".addslashes($CONT->getValue("file"))."\";".
					"\n\$REX[ART][$id][type_id][$clang] = \"".addslashes($CONT->getValue("type_id"))."\";".
					"\n\$REX[ART][$id][teaser][$clang] = \"".addslashes($CONT->getValue("teaser"))."\";".
					"\n\$REX[ART][$id][startpage][$clang] = \"".addslashes($CONT->getValue("startpage"))."\";".
					"\n\$REX[ART][$id][prior][$clang] = \"".addslashes($CONT->getValue("prior"))."\";".
					"\n\$REX[ART][$id][path][$clang] = \"".addslashes($CONT->getValue("path"))."\";".
					"\n\$REX[ART][$id][status][$clang] = \"".addslashes($CONT->getValue("status"))."\";".
					"\n\$REX[ART][$id][online_from][$clang] = \"".addslashes($CONT->getValue("online_from"))."\";".
					"\n\$REX[ART][$id][online_to][$clang] = \"".addslashes($CONT->getValue("online_to"))."\";".
					"\n\$REX[ART][$id][createdate][$clang] = \"".addslashes($CONT->getValue("createdate"))."\";".
					"\n\$REX[ART][$id][updatedate][$clang] = \"".addslashes($CONT->getValue("updatedate"))."\";".
					"\n\$REX[ART][$id][keywords][$clang] = \"".addslashes($CONT->getValue("keywords"))."\";".
					"\n\$REX[ART][$id][template_id][$clang] = \"".addslashes($CONT->getValue("template_id"))."\";".
					"\n\$REX[ART][$id][createuser][$clang] = \"".addslashes($CONT->getValue("createuser"))."\";".
					"\n\$REX[ART][$id][updateuser][$clang] = \"".addslashes($CONT->getValue("updateuser"))."\";".
					"\n\$REX[ART][$id][last_update_stamp][$clang] = \"".time()."\";".
					"\n?>";
		if ($fp = @fopen ($REX[INCLUDE_PATH]."/generated/articles/$id.$clang.article", "w"))
		{
			fputs($fp,$article);
			fclose($fp);
			chmod($REX[INCLUDE_PATH]."/generated/articles/$id.$clang.article",0777);
		}else
		{
			$MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX[INCLUDE_PATH]."/generated/articles/";
		}


		// --------------------------------------------------- Artikelcontent speichern
		if ($fp = @fopen ($REX[INCLUDE_PATH]."/generated/articles/$id.$clang.content", "w"))
		{
			fputs($fp,$article_content);
			fclose($fp);
			chmod($REX[INCLUDE_PATH]."/generated/articles/$id.$clang.content",0777);
		}else
		{
			$MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX[INCLUDE_PATH]."/generated/articles/";
		}
		if ($MSG != "")	echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=warning>$MSG</td></tr></table>";
		$REX[RC] = false;


		// --------------------------------------------------- Listen generieren
		if ($CONT->getValue("startpage")==1)
		{
			rex_generateLists($id);
			rex_generateLists($CONT->getValue("re_id"));
		}else
		{
			rex_generateLists($CONT->getValue("re_id"));
		}

		next($CL);

	}

    // --------------------------------------------------- recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();

}

function rex_deleteArticle($id,$ebene=0)
{
	global $REX, $I18N;

	// artikel loeschen
	//
	// kontrolle ob erlaubnis nicht hier.. muss vorher geschehen
	//
	// -> startpage = 0
	// --> artikelfiles löschen
	// ---> article
	// ---> content
	// ---> clist
	// ---> alist
	// -> startpage = 1
	// --> rekursiv aufrufen

	if ($id == $REX[STARTARTIKEL_ID]) {
		return $I18N->msg("cant_delete_startarticle");
	}

	$ART = new sql;
	$ART->setQuery("select * from rex_article where id='$id' and clang='0'");

	if ($ART->getRows()>0)
	{
		$re_id = $ART->getValue("re_id");
		if ($ART->getValue("startpage")==1)
		{
			$SART = new sql;
			$SART->setQuery("select * from rex_article where re_id='$id' and clang='0'");
			for($i=0;$i<$SART->getRows();$i++)
			{
				rex_deleteArticle($id,($ebene+1));
				$SART->next();
			}
		}

		$CL = $REX[CLANG];
		reset($CL);
		for ($i=0;$i<count($CL);$i++)
		{
			$clang = key($CL);
			@unlink($REX[INCLUDE_PATH]."/generated/articles/$id.$clang.article");
			@unlink($REX[INCLUDE_PATH]."/generated/articles/$id.$clang.content");
			@unlink($REX[INCLUDE_PATH]."/generated/articles/$id.$clang.alist");
			@unlink($REX[INCLUDE_PATH]."/generated/articles/$id.$clang.clist");
			$ART->query("delete from rex_article where id='$id'");
			$ART->query("delete from rex_article_slice where article_id='$id'");
			next($CL);
		}


		// --------------------------------------------------- Listen generieren
		rex_generateLists($re_id);


		// --------------------------------------------------- recache all
		$Cache = new Cache();
		$Cache->removeAllCacheFiles();
		return $I18N->msg('category_deleted').$I18N->msg('article_deleted');

	}else
	{
		return $I18N->msg('category_doesnt_exist');
	}

}

function rex_generateLists($re_id,$refresh=0)
{
	global $REX;

	// generiere listen
	//
	//
	// -> je nach clang
	// --> artikel listen
	// --> catgorie listen
	//

	$CL = $REX[CLANG];
	reset($CL);
	for ($j=0;$j<count($CL);$j++)
	{

		$clang = key($CL);

		// --------------------------------------- ARTICLE LIST

		$GC = new sql;
		// $GC->debugsql = 1;
		$GC->setQuery("select * from rex_article where (re_id=$re_id and clang=$clang and startpage=0) OR (id=$re_id and clang=$clang and startpage=1) order by prior,name");
		$content = "<?php\n";
		for ($i=0;$i<$GC->getRows();$i++)
		{
			$id = $GC->getValue("id");
			$content .= "\$REX[RE_ID][$re_id][$i] = \"".$GC->getValue("id")."\";\n";
			$GC->next();
		}
		$content .= "\n?>";
		$fp = fopen ($REX[INCLUDE_PATH]."/generated/articles/$re_id.$clang.alist", "w");
		fputs($fp,$content);
		fclose($fp);
		chmod($REX[INCLUDE_PATH]."/generated/articles/$re_id.$clang.alist",0777);

		// --------------------------------------- CAT LIST

		$GC = new sql;
		$GC->setQuery("select * from rex_article where re_id=$re_id and clang=$clang and startpage=1 order by catprior,name");
		$content = "<?php\n";
		for ($i=0;$i<$GC->getRows();$i++)
		{
			$id = $GC->getValue("id");
			$content .= "\$REX[RE_CAT_ID][$re_id][$i] = \"".$GC->getValue("id")."\";\n";
			$GC->next();
		}
		$content .= "\n?>";
		$fp = fopen ($REX[INCLUDE_PATH]."/generated/articles/$re_id.$clang.clist", "w");
		fputs($fp,$content);
		fclose($fp);
		chmod($REX[INCLUDE_PATH]."/generated/articles/$re_id.$clang.clist",0777);

		next($CL);
	}

}

function rex_newCatPrio($re_id,$clang,$new_prio,$old_prio)
{
	if ($new_prio != $old_prio)
	{
		if ($new_prio < $old_prio) $addsql = "desc";
		else $addsql = "asc";
		
		$gu = new sql;
		$gr = new sql;
		$gr->setQuery("select * from rex_article where re_id='$re_id' and clang='$clang' order by catprior,updatedate $addsql");
		for ($i=0;$i<$gr->getRows();$i++)
		{
			$ipid = $gr->getValue("pid");
			$iprior = $i+1;
			$gu->query("update rex_article set catprior=$iprior where pid='$ipid' and clang='$clang'");
			$gr->next();
		}
		rex_generateLists($re_id);
	}

}

function rex_newArtPrio($re_id,$clang,$new_prio,$old_prio)
{
	if ($new_prio != $old_prio)
	{
		if ($new_prio < $old_prio) $addsql = "desc";
		else $addsql = "asc";
		
		$gu = new sql;
		$gr = new sql;
		$gr->setQuery("select * from rex_article where re_id='$re_id' and clang='$clang' order by prior,updatedate $addsql");
		for ($i=0;$i<$gr->getRows();$i++)
		{
			$ipid = $gr->getValue("pid");
			$iprior = $i+1;
			$gu->query("update rex_article set catprior=$iprior where pid='$ipid' and clang='$clang'");
			$gr->next();
		}
		rex_generateLists($re_id);
	}

}


function rex_moveArticle($id,$to_cat_id,$from_cat_id)
{

}

function rex_copyArticle($id,$to_cat_id)
{

}

function rex_copyCategory($which,$to_cat)
{


}



// ----------------------------------------- CTYPE




// ----------------------------------------- FILE

function rex_deleteDir($file,$what = 1)
{
	if (file_exists($file))
	{
		// chmod($file,0775);
		if (is_dir($file))
		{
			$handle = opendir($file);
			while($filename = readdir($handle))
			{
				if ($filename != "." && $filename != "..")
				{
					rex_deleteDir($file."/".$filename);
				}
			}
			closedir($handle);
			if ($what == 1) rmdir($file);
			else echo ""; // do nothing;
		}else
		{
			unlink($file);
		}
	}

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();
}




// ----------------------------------------- CLANG

function rex_deleteCLang($id)
{
	global $REX;
	$content = "// --- DYN\n\r";
	
	reset($REX[CLANG]);
	for ($i=0;$i<count($REX[CLANG]);$i++)
	{
		$cur = key($REX[CLANG]);
		$val = current($REX[CLANG]);
		if ($cur != $id && $id != 0 ) $content .= "\n\r\$REX[CLANG][$cur] = \"$val\";";
		next($REX[CLANG]);
	}
	$content .= "\n\r// --- /DYN";
	$file = $REX[INCLUDE_PATH]."/clang.inc.php";
	
	$h = fopen($file,"r");
	$fcontent = fread($h,filesize($file));
	$fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)",$content,$fcontent);
	fclose($h);
	$h = fopen($file,"w+");
	fwrite($h,$fcontent,strlen($fcontent));
	fclose($h);
	chmod($file,0777);
	
	$del = new sql();
	$del->setQuery("select * from rex_article where clang='$id'");
	for($i=0;$i<$del->getRows();$i++)
	{
		$aid = $del->getValue("id");
		// rex_deleteArticle($del->getValue("id"),$id,0);
		@unlink($REX[INCLUDE_PATH]."/generated/articles/$aid.$id.article");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/$aid.$id.content");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/$aid.$id.alist");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/$aid.$id.clist");
		$del->next();
	}
	
	$del->query("delete from rex_article where clang='$id'");
	$del->query("delete from rex_article_slice where clang='$id'");

	rex_generateAll();

	if ($id>0) unset($REX[CLANG][$id]);
	$del = new sql();
	$del->query("delete from rex_clang where id='$id'");
	
}

function rex_addCLang($id,$name)
{
	global $REX;
	$REX[CLANG][$id] = $name;
	$content = "// --- DYN\n\r";
	reset($REX[CLANG]);
	for ($i=0;$i<count($REX[CLANG]);$i++)
	{
		$cur = key($REX[CLANG]);
		$val = current($REX[CLANG]);

		$content .= "\n\r\$REX[CLANG][$cur] = \"$val\";";
		next($REX[CLANG]);
	}
	$content .= "\n\r// --- /DYN";

	$file = $REX[INCLUDE_PATH]."/clang.inc.php";
	$h = fopen($file,"r");
	$fcontent = fread($h,filesize($file));
	$fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)",$content,$fcontent);
	fclose($h);

	$h = fopen($file,"w+");
	fwrite($h,$fcontent,strlen($fcontent));
	fclose($h);
	chmod($file,0777);

	$add = new sql();
	$add->setQuery("select * from rex_article where clang='0'");
	$fields = $add->getFieldnames();
	for($i=0;$i<$add->getRows();$i++)
	{
		$adda = new sql;
		// $adda->debugsql = 1;
		$adda->setTable("rex_article");
		reset($fields);
		while (list($key, $value) = each($fields)) {

			if ($value == "pid") echo ""; // nix passiert
			else if ($value == "clang") $adda->setValue("clang",$id);
			else $adda->setValue($value,$add->getValue("$value"));
			//	createuser
			//	updateuser
		}
		$adda->insert();

		$add->next();
	}
	$add = new sql();
	$add->query("insert into rex_clang set id='$id',name='$name'");
}

function rex_editCLang($id,$name)
{
	global $REX;

	$REX[CLANG][$id] = $name;
	$file = $REX[INCLUDE_PATH]."/clang.inc.php";
	$h = fopen($file,"r");
	$cont = fread($h,filesize($file));
	$cont = ereg_replace("(REX\[CLANG\]\[$id\].?\=.?)[^;]*","\\1\"".($name)."\"",$cont);
	fclose($h);
	$h = fopen($REX[INCLUDE_PATH]."/clang.inc.php","w+");
	fwrite($h,$cont,strlen($cont));
	fclose($h);
	chmod($REX[INCLUDE_PATH]."/clang.inc.php",0777);
	$edit = new sql;
	$edit->query("update rex_clang set name='$name' where id='$id'");
}

?>