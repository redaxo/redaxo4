<?php

/*
 * rex generate funktionen fuer artikel und clang
 */

// ----------------------------------------- ARTICLE

function getUrl($id,$params = null) {
	
	/*
	 * Object Helper Function:
	 * Returns a url for linking to this article
	 * This url respects the setting for mod_rewrite
	 * support!
	 *
	 * If you pass an associative array for $params,
	 * then these parameters will be attached to the URL.
	 * e.g.:
	 *   $param = array("order" => "123", "name" => "horst");
	 *   $article->getUrl($param);
	 * will return:
	 *   index.php?article_id=1&order=123&name=horst
	 * or if mod_rewrite support is activated:
	 *   /1-The_Article_Name?order=123&name=horst
	 */
	
	
	global $REX;
	$param_string = "";
	if ($params && sizeof($params) > 0) {
		$param_string = $REX['MOD_REWRITE'] ? "?" : "&amp;";
		foreach ($params as $key => $val) {
			$param_string .= "{$key}={$val}&amp;";
		}
	}
	$param_string = substr($param_string,0,strlen($param_string)-5); // cut off the last '&'
	$url = $REX['MOD_REWRITE'] ? "/$id-{$mr_name}"
	                           : "index.php?article_id=$id";
  return $REX['WWW_PATH']."{$url}{$param_string}";
}

function generateArticle($id)
{

	global $PHP_SELF,$module_id,$FORM,$REX_USER,$REX,$I18N;

	// --------------------------------------------------- generiere generated/articles/xx.article
	$REX[RC] = true; // Generiere Content

	$CONT = new article;
	$CONT->setArticleId($id);

	$article_content = "?>".$CONT->getArticle();

	$article = "<?

\$REX[ART][$id][name] = \"".addslashes($CONT->getValue("name"))."\";
\$REX[ART][$id][description] = \"".addslashes($CONT->getValue("description"))."\";
\$REX[ART][$id][keywords] = \"".addslashes($CONT->getValue("keywords"))."\";
\$REX[ART][$id][re_id] = \"".addslashes($CONT->getValue("re_id"))."\";
\$REX[ART][$id][article_id] = \"$id\";
\$REX[ART][$id][type_id] = \"".addslashes($CONT->getValue("type_id"))."\";
\$REX[ART][$id][file] = \"".addslashes($CONT->getValue("file"))."\";
\$REX[ART][$id][startpage] = \"".addslashes($CONT->getValue("startpage"))."\";
\$REX[ART][$id][prio] = \"".addslashes($CONT->getValue("prio"))."\";
\$REX[ART][$id][path] = \"".addslashes($CONT->getValue("path"))."\";
\$REX[ART][$id][online_from] = \"".addslashes($CONT->getValue("online_from"))."\";
\$REX[ART][$id][online_to] = \"".addslashes($CONT->getValue("online_to"))."\";
\$REX[ART][$id][createdate] = \"".addslashes($CONT->getValue("createdate"))."\";
\$REX[ART][$id][last_update_stamp] = \"".time()."\";
\$REX[ART][$id][template_id] = \"".addslashes($CONT->getValue("template_id"))."\";
\$REX[ART][$id][status] = \"".addslashes($CONT->getValue("status"))."\";

?>";

	// Artikelparameter speichern

	if ($fp = @fopen ($REX[INCLUDE_PATH]."/generated/articles/".$id.".article", "w"))
	{
		fputs($fp,$article);
		fclose($fp);
	}else
	{
		$MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX[INCLUDE_PATH]."/generated/articles/";
	}

	// Artikelcontent speichern

	if ($fp = @fopen ($REX[INCLUDE_PATH]."/generated/articles/".$id.".content", "w"))
	{
		fputs($fp,$article_content);
		fclose($fp);
	}else
	{
		$MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX[INCLUDE_PATH]."/generated/articles/";
	}

	if ($MSG != "")
	{
		echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
			<tr><td class=warning>$MSG</td></tr>
			</table>";
	}

	$REX[RC] = false;

	if ($CONT->getValue("startpage")==1) generateLists($id);
	else generateLists($CONT->getValue("re_id"));

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();

}

function deleteArticle($id)
{
	global $REX, $I18N;

	if ($id == $REX[STARTARTIKEL_ID]) {
		return $I18N->msg("cant_delete_startarticle");
	}

	$ART = new sql;
	$ART->setQuery("select * from rex_article where id='$id'");

	if ($ART->getRows()>0)
	{
		$re_id = $ART->getValue("re_id");

		$ART->query("delete from rex_article where id='$id' and startpage=0");
		
		@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".article");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".content");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".alist");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".clist");

		$ART->query("delete from rex_article where id='$id'");
		$ART->query("delete from rex_article_slice where article_id='$id'");
		
		// generateArticleList($re_id);
		
		$Cache = new Cache();
		$Cache->removeAllCacheFiles();

		return $I18N->msg('category_deleted').$I18N->msg('article_deleted');
		
	}else
	{
		return $I18N->msg('category_doesnt_exist');
	}

}

function deleteCategory($id)
{
	global $REX, $I18N;

	if ($id == $REX[STARTARTIKEL_ID]) {
		return $I18N->msg("cant_delete_startarticle");
	}

	$ART = new sql;
	$ART->setQuery("select * from rex_article where id='$id' and startpage=1");

	if ($ART->getRows()==1)
	{
		$re_id = $ART->getValue("re_id");

		$KAT = new sql;
		$KAT->setQuery("select * from rex_article where re_id='$id' and startpage=0");
		
		for ($i=0;$i<$KAT->getRows();$i++)
		{
			$kid = $KAT->getValue("id");
			deleteArticle($id);
			$KAT->next();
		}
		$KAT->query("delete from rex_article where id='$id' and startpage=1");
		
		@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".article");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".content");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".alist");
		@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".clist");

		$KAT->query("delete from rex_article where id='$id'");
		$KAT->query("delete from rex_article_slice where article_id='$id'");
		
		// generateArticleList($re_id);
		
		$Cache = new Cache();
		$Cache->removeAllCacheFiles();

		return $I18N->msg('category_deleted').$I18N->msg('article_deleted');
		
	}else
	{
		return $I18N->msg('category_doesnt_exist');
	}

}

function generateLists($re_id)
{
	global $REX;
	$GC = new sql;
	
	// --------------------------------------- ARTICLE LIST
	// $GC->debugsql = 1;
	$GC->setQuery("select * from rex_article where re_id=$re_id and startpage=0 order by prior,name");
	$content = "<?php\n";
	for ($i=0;$i<$GC->getRows();$i++)
	{
		$id = $GC->getValue("id");
		$content .= "\$REX[RE_ID][$re_id][] = \"".$GC->getValue("id")."\";\n";
		$GC->next();
	}
	$content .= "\n?>";

	$fp = fopen ($REX[INCLUDE_PATH]."/generated/articles/".$re_id.".alist", "w");
	fputs($fp,$content);
	fclose($fp);
	
	// --------------------------------------- CAT LIST
	$GC->setQuery("select * from rex_article where re_id=$re_id and startpage=1 order by prior,name");
	$content = "<?php\n";
	for ($i=0;$i<$GC->getRows();$i++)
	{
		$id = $GC->getValue("id");
		$content .= "\$REX[RE_CAT_ID][$re_id][] = \"".$GC->getValue("id")."\";\n";
		$GC->next();
	}
	$content .= "\n?>";

	$fp = fopen ($REX[INCLUDE_PATH]."/generated/articles/".$re_id.".clist", "w");
	fputs($fp,$content);
	fclose($fp);
	
}

function deleteDir($file,$what = 1)
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
					deleteDir($file."/".$filename);
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

function generateAll()
{

	global $REX, $I18N;

	// ----------------------------------------------------------- generiere templates
	deleteDir($REX[INCLUDE_PATH]."/generated/templates",0);
	// mkdir($REX[INCLUDE_PATH]."/generated/templates",0664);
	$gt = new sql;
	$gt->setQuery("select * from rex_template");
	for ($i=0;$i<$gt->getRows();$i++)
	{
		$fp = fopen ($REX[INCLUDE_PATH]."/generated/templates/".$gt->getValue("rex_template.id").".template", "w");
		fputs($fp,$gt->getValue("rex_template.content"));
		fclose($fp);
		$gt->next();
	}

	// ----------------------------------------------------------- generiere artikel
	deleteDir($REX[INCLUDE_PATH]."/generated/articles",0);
	// mkdir($REX[INCLUDE_PATH]."/generated/articles",0664);
	$gc = new sql;
	$gc->setQuery("select * from rex_article");
	for ($i=0;$i<$gc->getRows();$i++)
	{
		generateArticle($gc->getValue("id"));
		$gc->next();
	}

	$MSG = $I18N->msg('articles_generated')." ".$I18N->msg('old_articles_deleted');

	return $MSG;
}

function moveArticle($id,$to_cat_id,$from_cat_id)
{
	global $I18N;
	// to katgorie vorhanden ?

	$gcat = new sql;
	$gcat->setQuery("select * from rex_category where id='$to_cat_id'");

	if ($gcat->getRows()==1)
	{
		// article updaten
		$path = $gcat->getValue("path")."-$to_cat_id";
		$gcat->query("update rex_article set prior = prior + 1 where category_id='$to_cat_id'");
		$gcat->query("update rex_article set category_id='$to_cat_id',path='$path',prior = 1 where id='$id'");
		$return = $I18N->msg('article_moved');

	}else
	{
		$return = $I18N->msg('could_not_move_article')." ".$I18N->msg('category_doesnt_exist');
	}

	// article neu generieren

	generateArticle($id);

	// catgegoy neu generieren
	generateCategory($to_cat_id);

	// category alt generieren
	generateCategory($from_cat_id);

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();

	return $return;

}

function copyArticle($id,$to_cat_id)
{

	##
	### make new path
	##
	$get_parent_cat = new sql;
	$get_parent_cat->setQuery("SELECT path FROM rex_category WHERE id=$to_cat_id");
	$path = $get_parent_cat->getValue("path")."-".$to_cat_id;

	##
	### check if article is firstarticle in the new category
	##
	$get_cat = new sql;
	$get_cat->setQuery("SELECT count(*) FROM rex_article WHERE category_id=$to_cat_id");
	if($get_cat->getValue("count(*)") == 0) $startarticle = 1;
	else $startarticle = 0;

	##
	### copy article
	##
	$get_article = new sql;
	$get_article->setQuery("SELECT * FROM rex_article WHERE id=$id");

	$get_article_fields = new sql;
	$get_article_fields->setQuery("DESCRIBE rex_article");

	$add_article = new sql;
	$order_id = $add_article->new_order("rex_article","prior","category_id",$to_cat_id);
	$add_article->setTable("rex_article");
	$add_article->setValue('prior',$order_id);

	for($i=0;$i<$get_article_fields->rows;$i++,$get_article_fields->next())
	{
		if($get_article_fields->getValue("Field")=='prior') continue;

		if($get_article_fields->getValue("Field") == "category_id")
			$add_article->setValue(category_id, $to_cat_id);
		elseif($get_article_fields->getValue("Field") == "path")
			$add_article->setValue(path,$path);
		elseif($get_article_fields->getValue("Field") == "startpage")
			$add_article->setValue(startpage, $startarticle);
		elseif($get_article_fields->getValue("Field") != "id")
			$add_article->setValue($get_article_fields->getValue("Field"),$get_article->getValue($get_article_fields->getValue("Field")));
	}
	//$add_article->debugsql=true;
	$add_article->insert();
	$last_id = $add_article->last_insert_id;

	##
	### copy slices
	##

	$get_slices = new sql;
	$get_slices->setQuery("SELECT * FROM rex_article_slice WHERE article_id=$id ORDER BY re_article_slice_id");

	$get_slice_fields = new sql;
	$get_slice_fields->setQuery("DESCRIBE rex_article_slice");

	$parent_slice = 0;
	$preparent_slice = 0;

	// hack: max 100 slices pro article -- noch zu verbessern

	for($k=0;$k<100;$k++)
	{
		$get_slices->counter=0;
		for($i=0;$i<$get_slices->getRows();$i++,$get_slices->next())
		{
			if ($preparent_slice == $get_slices->getValue("re_article_slice_id")) break;
		}

		if($i>=$get_slices->rows) break;

		$preparent_slice = $get_slices->getValue("id");

		// $get_slices->  OBJ mit entsprechenden id

		$add_new_slice = new sql;
		// $add_new_slice->debugsql = 1;
		$add_new_slice->setTable("rex_article_slice");
		for($j=0;$j<$get_slice_fields->rows;$j++,$get_slice_fields->next())
		{

			if($get_slice_fields->getValue("Field") == "re_article_slice_id")
				$add_new_slice->setValue(re_article_slice_id, $parent_slice);
			elseif($get_slice_fields->getValue("Field") == "article_id")
				$add_new_slice->setValue(article_id, $last_id);
			elseif($get_slice_fields->getValue("Field") != "id")
				$add_new_slice->setValue($get_slice_fields->getValue("Field"),addslashes($get_slices->getValue($get_slice_fields->getValue("Field"))));

		}

		// $add_new_slice->debugsql=true;
		$add_new_slice->insert();
		$get_slice_fields->counter=0;
		$parent_slice = $add_new_slice->last_insert_id;

	}

	// article neu generieren
	generateArticle($last_id);

	// catgegoy neu generieren
	generateCategory($to_cat_id);

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();

}

function copyCategory($which,$to_cat)
{

	## orginal selecten
	$orig = new sql;
	$orig->setQuery("SELECT * FROM rex_category WHERE id=$which");

	if($to_cat != 0)
	{
		## ziel selecten um den path zu bekomme
		$ziel = new sql;
		$ziel->setQuery("SELECT * FROM rex_category WHERE id=$to_cat");
		$zielpath = $ziel->getValue("path")."-".$to_cat;
	}else
	{
		## ziel is top also path
		$zielpath = "";
	}

	## neue kategorie schreiben
	$add = new sql;
	$add->setTable("rex_category");
	$add->setValue("name", $orig->getValue("name"));
	$add->setValue("re_category_id", $to_cat);
	$add->setValue("prior", $orig->getValue("prior"));
	$add->setValue("path", $zielpath);
	$add->setvalue("status", $orig->getValue("status"));
	$add->insert();

	## artikel kopieren order by !!! da sonst startartikel falsch
	$articles = new sql;
	$articles->setQuery("SELECT * FROM rex_article WHERE category_id=$which order by startpage desc");
	for($i=0;$i<$articles->rows;$i++,$articles->next())
		copyArticle($articles->getValue("id"),$add->last_insert_id);

	## suchen nach unterkategorien und diese dann natürlich mitkopieren
	## "rekursier on" hier
	$subcats = new sql;
	$subcats->setQuery("SELECT * FROM rex_category WHERE re_category_id=$which");
	for($i=0;$i<$subcats->rows;$i++,$subcats->next())
		copyCategory($subcats->getValue("id"),$add->last_insert_id);


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
	if ($id>0) unset($REX[CLANG][$id]);
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
	
}

?>