<?

// todos

// generateArticleList($re_id);

// changed 02.04.04 Carsten Eckelmann <careck@circle42.com>
//   * Internationalisation with $I18N class
//   * to use internationalised messages just global $I18N and write $I18N->msg('message_key')
//   * add the message to <language>.lang eg. submit = abschicken


// ---------------------------------------- MOVE

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


// ---------------------------------------- COPY

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

// ---------------------------------------- GENERATE

function generateArticle($id)
{

	global $PHP_SELF,$module_id,$FORM,$REX_USER,$REX,$I18N;

	// --------------------------------------------------- generiere generated/articles/xx.article
	$REX[RC] = true; // Generiere Content

	$CONT = new article;
	$CONT->setArticleId($id);

	$REX[TEMP] = $REX[BF];
	$REX[BF] = false;
	$article_content = "?>".$CONT->getArticle();
	$REX[BF] = true;
	$article_bcontent = "?>".$CONT->getArticle();
	$REX[BF] = $REX[TEMP];

	$article = "<?

\$REX[ART][$id][name] = \"".addslashes($CONT->getValue("name"))."\";
\$REX[ART][$id][beschreibung] = \"".addslashes($CONT->getValue("beschreibung"))."\";
\$REX[ART][$id][suchbegriffe] = \"".addslashes($CONT->getValue("suchbegriffe"))."\";
\$REX[ART][$id][category_id] = \"".addslashes($CONT->getValue("category_id"))."\";
\$REX[ART][$id][article_id] = \"$id\";
\$REX[ART][$id][type_id] = \"".addslashes($CONT->getValue("type_id"))."\";
\$REX[ART][$id][file] = \"".addslashes($CONT->getValue("file"))."\";
\$REX[ART][$id][startpage] = \"".addslashes($CONT->getValue("startpage"))."\";
\$REX[ART][$id][prio] = \"".addslashes($CONT->getValue("prio"))."\";
\$REX[ART][$id][path] = \"".addslashes($CONT->getValue("path"))."\";
\$REX[ART][$id][online_von] = \"".addslashes($CONT->getValue("online_von"))."\";
\$REX[ART][$id][online_bis] = \"".addslashes($CONT->getValue("online_bis"))."\";
\$REX[ART][$id][erstelldatum] = \"".addslashes($CONT->getValue("erstelldatum"))."\";
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

	// Artikel B content speichern [BARRIEREFREI]

	if ($fp = @fopen ($REX[INCLUDE_PATH]."/generated/articles/".$id.".bcontent", "w"))
	{
		fputs($fp,$article_bcontent);
		fclose($fp);
	}else
	{
		$MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX[INCLUDE_PATH]."generated/articles/";
	}

	if ($MSG != "")
	{
		echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
			<tr><td class=warning>$MSG</td></tr>
			</table>";
	}

	$REX[RC] = false;

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();

}

// ---------------------------------------- DELETE ARTICLE

function deleteArticle($id)
{
	global $REX, $I18N;

	// changed 4.4.04 careck@circle42.com
	// guard against deleting the start article
	if ($id == $REX[STARTARTIKEL_ID]) {
		return $I18N->msg("cant_delete_startarticle");
	}

	$ART = new sql;

	$ART->query("delete from rex_article where id='$id'");
	$ART->query("delete from rex_article_slice where article_id='$id'");
	@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".article");
	@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".content");
	@unlink($REX[INCLUDE_PATH]."/generated/articles/".$id.".bcontent");

	$message = $I18N->msg('article_deleted');

	return $message;

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();
}

// ---------------------------------------- DELETE CATEGORY

function deleteCategory($id)
{
	global $REX,$I18N;

	$KAT = new sql;
	$KAT->setQuery("select * from rex_category where id='$id'");

	if ($KAT->getRows()==1)
	{

		$re_id = $KAT->getValue("re_category_id");

		$KAT->setQuery("select * from rex_article where category_id='$id'");
		for ($i=0;$i<$KAT->getRows();$i++)
		{
			deleteArticle($KAT->getValue("id"));
			$KAT->next();
		}

		$KAT->query("delete from rex_article where category_id='$id'");
		$KAT->query("delete from rex_category where id='$id'");

		@unlink($REX[INCLUDE_PATH]."/generated/categories/".$id.".category");
		@unlink($REX[INCLUDE_PATH]."/generated/categories/".$id.".list.category");

		generateCategoryList($re_id);

		$message = $I18N->msg('category_deleted');
	}else
	{
		$message = $I18N->msg('category_doesnt_exist');
	}

	return $message;

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();

}

// ---------------------------------------- GENERATE CATEGORY

function generateCategory($id)
{
	global $REX;

	$GC = new sql;
	$GC->setQuery("select
		cat1.name,cat1.re_category_id,cat1.prior,cat1.path,cat1.status,rex_article.id
		from rex_category as cat1
		left join rex_category as cat2 on cat1.re_category_id=cat2.id
		left join rex_article on cat1.id=rex_article.category_id
		where
		cat1.id='$id' and
		cat1.id=rex_article.category_id and
		startpage=1
		LIMIT 1");

	if ($GC->getRows()==1)
	{
		$content = "<?

\$REX[CAT][$id][name] = \"".addslashes($GC->getValue("cat1.name"))."\";
\$REX[CAT][$id][re_category_id] = \"".addslashes($GC->getValue("cat1.re_category_id"))."\";
\$REX[CAT][$id][category_id] = \"$id\";
\$REX[CAT][$id][prior] = \"".addslashes($GC->getValue("cat1.prior"))."\";
\$REX[CAT][$id][path] = \"".addslashes($GC->getValue("cat1.path"))."\";
\$REX[CAT][$id][status] = \"".addslashes($GC->getValue("cat1.status"))."\";
\$REX[CAT][$id][article_id] = \"".addslashes($GC->getValue("rex_article.id"))."\";

?>";

		$fp = fopen ($REX[INCLUDE_PATH]."/generated/categories/".$id.".category", "w");
		fputs($fp,$content);
		fclose($fp);

		// kategorienliste speichern

		$re_id = $GC->getValue("cat1.re_category_id");

		generateCategoryList($re_id);


	}

	// generateCategories();

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();
}



// ---------------------------------------- GENERATE CATEGORIES

function generateCategories()
{
	global $REX;

	$GC = new sql;
	$GC->setQuery("select * from rex_category as cat1
		left join rex_category as cat2 on cat1.re_category_id=cat2.id
		left join rex_article on cat1.id=rex_article.category_id
		where cat1.id=rex_article.category_id and startpage=1 order by cat1.re_category_id,cat1.prior
		");

	for ($i=0;$i<$GC->getRows();$i++)
	{

		$id = $GC->getValue("cat1.id");
		$content .= "
\$REX[CAT][$id][name] = \"".addslashes($GC->getValue("cat1.name"))."\";
\$REX[CAT][$id][re_category_id] = \"".addslashes($GC->getValue("cat1.re_category_id"))."\";
\$REX[CAT][$id][category_id] = \"$id\";
\$REX[CAT][$id][prior] = \"".addslashes($GC->getValue("cat1.prior"))."\";
\$REX[CAT][$id][path] = \"".addslashes($GC->getValue("cat1.path"))."\";
\$REX[CAT][$id][status] = \"".addslashes($GC->getValue("cat1.status"))."\";
\$REX[CAT][$id][article_id] = \"".addslashes($GC->getValue("rex_article.id"))."\";
";
		$GC->next();

	}

	$content = "<? $content ?>";

	$fp = fopen ($REX[INCLUDE_PATH]."/generated/categories.php", "w");
	fputs($fp,$content);
	fclose($fp);

    // recache all
	$Cache = new Cache();
	$Cache->removeAllCacheFiles();

}

// ---------------------------------------- GENERATEGLLINK

function generateGLink($content)
{
	// REX_GLINK[] - global link ersetzen durch

	return $content;
}

// ---------------------------------------- GENERATE CATEGORYLIST

function generateCategoryList($re_id)
{
	global $REX;

	$GC = new sql;
	$GC->setQuery("select *
			from rex_category as cat1
			left join rex_article on cat1.id=rex_article.category_id
			where
			cat1.re_category_id='$re_id' and
			cat1.id=rex_article.category_id and
			rex_article.startpage=1
			order by cat1.prior,cat1.name");

	$content = "<?";

	for ($i=0;$i<$GC->getRows();$i++)
	{

		$id = $GC->getValue("cat1.id");

		$content .= "
\$REX[RECAT][$re_id][] = \"".$GC->getValue("cat1.id")."\";

\$REX[CAT][$id][name] = \"".addslashes($GC->getValue("cat1.name"))."\";
\$REX[CAT][$id][re_category_id] = \"".addslashes($GC->getValue("cat1.re_category_id"))."\";
\$REX[CAT][$id][category_id] = \"$id\";
\$REX[CAT][$id][prior] = \"".addslashes($GC->getValue("cat1.prior"))."\";
\$REX[CAT][$id][path] = \"".addslashes($GC->getValue("cat1.path"))."\";
\$REX[CAT][$id][status] = \"".addslashes($GC->getValue("cat1.status"))."\";
\$REX[CAT][$id][article_id] = \"".addslashes($GC->getValue("rex_article.id"))."\";

";
		$GC->next();
	}
	$content .= "?>";

	$fp = fopen ($REX[INCLUDE_PATH]."/generated/categories/".$re_id.".list.category", "w");
	fputs($fp,$content);
	fclose($fp);
}



function generateArticleList($re_id)
{
	global $REX;



}


// ---------------------------------------------- KATEGORIE KOPIEREN
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

// deleteDir ($mydir);

// generate templates,articles,cache,categories
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

	// ----------------------------------------------------------- generiere categorien
	deleteDir($REX[INCLUDE_PATH]."/generated/categories",0);
	// mkdir($REX[INCLUDE_PATH]."/generated/categories",0664);
	$gcc = new sql;
	$gcc->setQuery("select * from rex_category");
	for ($i=0;$i<$gcc->getRows();$i++)
	{
		generateCategory($gcc->getValue("id"));
		$gcc->next();
	}
	// generateCategories();

	$MSG = $I18N->msg('articles_generated')." ".$I18N->msg('old_articles_deleted');

	return $MSG;
}
?>
