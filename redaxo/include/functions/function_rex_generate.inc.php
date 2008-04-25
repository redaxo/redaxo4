<?php


/**
 * Funktionensammlung für die generierung der Artikel/Templates/Kategorien/Metainfos.. etc.
 * @package redaxo4
 * @version $Id: function_rex_generate.inc.php,v 1.12 2008/03/26 20:19:47 kills Exp $
 */

// ----------------------------------------- Alles generieren

/**
 * Startet den kompletten Generations-Prozess von allen Artikel/Kategorien für alle Dateien
 */
function rex_generateAll()
{
  global $REX, $I18N;

  // alles existiert schon
  // -> generiere templates
  // -> generiere article und listen
  // -> generiere file meta

  // ----------------------------------------------------------- generiere templates
  rex_deleteDir($REX['INCLUDE_PATH'].'/generated/templates', 0);

  // ----------------------------------------------------------- generiere artikel
  rex_deleteDir($REX['INCLUDE_PATH'].'/generated/articles', 0);

  // ----------------------------------------------------------- generiere files
  rex_deleteDir($REX['INCLUDE_PATH'].'/generated/files', 0);
  /*
  $gc = new rex_sql;
  $gc->setQuery("select distinct id from ".$REX['TABLE_PREFIX']."article");
  for ($i = 0; $i < $gc->getRows(); $i ++)
  {
    rex_generateArticle($gc->getValue("id"));
    $gc->next();
  }
  */

  // ----------------------------------------------------------- generiere clang
  $lg = new rex_sql();
  $lg->setQuery("select * from ".$REX['TABLE_PREFIX']."clang order by id");
  $content = "";
  for ($i = 0; $i < $lg->getRows(); $i ++)
  {
    $id = $lg->getValue("id");
    $name = $lg->getValue("name");
    $content .= "\$REX['CLANG']['$id'] = \"$name\";\n";
    $lg->next();
  }

  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  rex_replace_dynamic_contents($file, $content);

  // ----------------------------------------------------------- generiere filemetas ...
  // **********************

  // ----------------------------------------------------------- message
  $MSG = $I18N->msg('delete_cache_message');

  // ----- EXTENSION POINT
  $MSG = rex_register_extension_point('ALL_GENERATED', $MSG);

  return $MSG;
}

// ----------------------------------------- ARTICLE


/**
 * Löscht die gecachten Dateien eines Artikels. Wenn keine clang angegeben, wird
 * der Artikel in allen Sprachen gelöscht.
 *
 * @param $id ArtikelId des Artikels
 * @param [$clang ClangId des Artikels]
 */

function rex_deleteCacheArticle($id, $clang = null)
{
  global $REX;

  foreach($REX['CLANG'] as $_clang => $clang_name)
  {
    if(!$clang || $clang && $clang == $_clang)
    {
      @unlink($REX['INCLUDE_PATH'].'/generated/articles/'. $id .'.'. $_clang .'.article');
      @unlink($REX['INCLUDE_PATH'].'/generated/articles/'. $id .'.'. $_clang .'.content');
      @unlink($REX['INCLUDE_PATH'].'/generated/articles/'. $id .'.'. $_clang .'.alist');
      @unlink($REX['INCLUDE_PATH'].'/generated/articles/'. $id .'.'. $_clang .'.clist');
    }
	}
}


/**
 * Generiert alle *.article u. *.content Dateien eines Artikels/einer Kategorie
 *
 * @param $id ArtikelId des Artikels, der generiert werden soll
 * @param $refreshall Boolean Bei True wird der Inhalte auch komplett neu generiert, bei False nur die Metainfos
 */
function rex_generateArticle($id, $refreshall = true)
{
  global $REX, $I18N;

  // artikel generieren
  // vorraussetzung: articel steht schon in der datenbank
  //
  // -> infos schreiben -> abhaengig von clang
  // --> artikel infos / einzelartikel metadaten
  // --> artikel content / einzelartikel content
  // --> listen generieren // wenn startpage = 1
  // ---> artikel liste
  // ---> category liste

  // --------------------------------------------------- generiere generated/articles/xx.article

  foreach($REX['CLANG'] as $clang => $clang_name)
  {
    $MSG = '';
    $CONT = new rex_article;
    $CONT->setCLang($clang);
    $CONT->getContentAsQuery();
    $CONT->setMode("generate"); // keine Ausgabe als eval(CONTENT) sondern nur speichern in datei
    if (!$CONT->setArticleId($id)) return false;

    // --------------------------------------------------- Artikelparameter speichern
    $params = array(
      'article_id' => $id,
      'last_update_stamp' => time()
    );

    $class_vars = OORedaxo::getClassVars();
    unset($class_vars[array_search('id', $class_vars)]);
    $db_fields = $class_vars;

    foreach($db_fields as $field)
      $params[$field] = $CONT->getValue($field);

    $content = '<?php'."\n";
    foreach($params as $name => $value)
    {
      $content .='$REX[\'ART\']['. $id .'][\''. $name .'\']['. $clang .'] = \''. rex_addslashes($value,'\\\'') .'\';'."\n";
    }
    $content .= '?>';

    $article_file = $REX['INCLUDE_PATH']."/generated/articles/$id.$clang.article";
    if (!rex_put_file_contents($article_file, $content))
    {
      $MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
    }

    // --------------------------------------------------- Artikelcontent speichern
  	if ($refreshall)
	  {
      $article_content_file = $REX['INCLUDE_PATH']."/generated/articles/$id.$clang.content";
      $article_content = "?>".$CONT->getArticle();

      // ----- EXTENSION POINT
      $article_content = rex_register_extension_point('GENERATE_FILTER', $article_content,
        array (
          'id' => $id,
          'clang' => $clang,
          'article' => $CONT
        )
      );

	    if (!rex_put_file_contents($article_content_file, $article_content))
	    {
	      $MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
	    }
	  }

    // ----- EXTENSION POINT
    $MSG = rex_register_extension_point('CLANG_ARTICLE_GENERATED', '',
      array (
        'id' => $id,
        'clang' => $clang,
        'article' => $CONT
      )
    );

    if ($MSG != '')
      echo rex_warning($MSG);

    // --------------------------------------------------- Listen generieren
    if ($CONT->getValue("startpage") == 1)
    {
      rex_generateLists($id);
      rex_generateLists($CONT->getValue("re_id"));
    }
    else
    {
      rex_generateLists($CONT->getValue("re_id"));
    }

  }

  // ----- EXTENSION POINT
  $MSG = rex_register_extension_point('ARTICLE_GENERATED','',array ('id' => $id));

  return true;
}

/**
 * Löscht einen Artikel
 *
 * @param $id ArtikelId des Artikels, der gelöscht werden soll
 */
function rex_deleteArticle($id, $ebene = 0)
{
  global $I18N;

  $result = _rex_deleteArticle($id, $ebene);

  return $result === true ? $I18N->msg('category_deleted').' '.$I18N->msg('article_deleted') : $result;
}

function _rex_deleteArticle($id, $ebene)
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

  if ($id == $REX['START_ARTICLE_ID'])
  {
    return $I18N->msg('cant_delete_sitestartarticle');
  }
  if ($id == $REX['NOTFOUND_ARTICLE_ID'])
  {
    return $I18N->msg('cant_delete_notfoundarticle');
  }

  $ART = new rex_sql;
  $ART->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id='$id' and clang='0'");

  if ($ART->getRows() > 0)
  {
    $re_id = $ART->getValue('re_id');
    $allowDelete = true;
    if ($ART->getValue('startpage') == 1)
    {
      $SART = new rex_sql;
      $SART->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$id' and clang='0'");
      for ($i = 0; $i < $SART->getRows(); $i ++)
      {
        $allowDelete = _rex_deleteArticle($id, ($ebene +1));
        $SART->next();
      }
    }

    // Rekursion über alle Kindkategorien ergab keine Fehler
    // => löschen erlaubt
    if($allowDelete === true)
    {
      rex_deleteCacheArticle($id);
      $ART->setQuery("delete from ".$REX['TABLE_PREFIX']."article where id='$id'");
      $ART->setQuery("delete from ".$REX['TABLE_PREFIX']."article_slice where article_id='$id'");

      // --------------------------------------------------- Listen generieren
      rex_generateLists($re_id);
    }

    return $allowDelete;
  }
  else
  {
    return $I18N->msg('category_doesnt_exist');
  }

}

/**
 * Generiert alle *.alist u. *.clist Dateien einer Kategorie/eines Artikels
 *
 * @param $re_id   KategorieId oder ArtikelId, die erneuert werden soll
 */
function rex_generateLists($re_id)
{
  global $REX;

  // generiere listen
  //
  //
  // -> je nach clang
  // --> artikel listen
  // --> catgorie listen
  //

  foreach($REX['CLANG'] as $clang => $clang_name)
  {
    // --------------------------------------- ARTICLE LIST

    $GC = new rex_sql;
    // $GC->debugsql = 1;
    $GC->setQuery("select * from ".$REX['TABLE_PREFIX']."article where (re_id=$re_id and clang=$clang and startpage=0) OR (id=$re_id and clang=$clang and startpage=1) order by prior,name");
    $content = "<?php\n";
    for ($i = 0; $i < $GC->getRows(); $i ++)
    {
      $id = $GC->getValue("id");
      $content .= "\$REX['RE_ID']['$re_id']['$i'] = \"".$GC->getValue("id")."\";\n";
      $GC->next();
    }
    $content .= "\n?>";

    $article_list_file = $REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.alist";
    rex_put_file_contents($article_list_file, $content);

    // --------------------------------------- CAT LIST

    $GC = new rex_sql;
    $GC->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id=$re_id and clang=$clang and startpage=1 order by catprior,name");
    $content = "<?php\n";
    for ($i = 0; $i < $GC->getRows(); $i ++)
    {
      $id = $GC->getValue("id");
      $content .= "\$REX['RE_CAT_ID']['$re_id']['$i'] = \"".$GC->getValue("id")."\";\n";
      $GC->next();
    }
    $content .= "\n?>";

    $article_categories_file = $REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.clist";
    rex_put_file_contents($article_categories_file, $content);
  }
}

/**
 * Macht einen Artikel zum Startartikel der eigenen Kategorie
 *
 * @param $aid	Artikel ID
 */
function rex_article2startpage($neu_id){

	global $REX;

	$GAID = array();

  // neuen startartikel holen und schauen ob da
	$neu = new rex_sql;
	$neu->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id=$neu_id and startpage=0 and clang=0");
	if ($neu->getRows()!=1) return false;
	$neu_path = $neu->getValue("path");
	$neu_cat_id = $neu->getValue("re_id");

	// in oberster kategorie dann return
	if ($neu_cat_id == 0) return false;

	// alten startartikel
	$alt = new rex_sql;
	$alt->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id=$neu_cat_id and startpage=1 and clang=0");
	if ($alt->getRows()!=1) return false;
	$alt_path = $alt->getValue('path');
	$alt_id = $alt->getValue('id');

	// cat felder sammeln. +
	$params = array('path','prior','catname','startpage','catprior','status');
	$db_fields = OORedaxo::getClassVars();
  foreach($db_fields as $field)
  {
		if(substr($field,0,4)=='cat_') $params[] = $field;
	}

	// LANG SCHLEIFE
	foreach($REX['CLANG'] as $clang => $clang_name)
	{

    // alter startartikel
		$alt->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id=$neu_cat_id and startpage=1 and clang=$clang");

    // neuer startartikel
		$neu->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id=$neu_id and startpage=0 and clang=$clang");

    // alter startartikel updaten
		$alt2 = new rex_sql();
		$alt2->setTable($REX['TABLE_PREFIX']."article");
		$alt2->setWhere("id=$alt_id and clang=". $clang);
		$alt2->setValue("re_id",$neu_id);

    // neuer startartikel updaten
		$neu2 = new rex_sql();
		$neu2->setTable($REX['TABLE_PREFIX']."article");
		$neu2->setWhere("id=$neu_id and clang=". $clang);
		$neu2->setValue("re_id",$alt->getValue("re_id"));

		// austauschen der definierten paramater
		foreach($params as $param)
    {
			$neu_value = $neu->getValue($param);
			$alt_value = $alt->getValue($param);
			$alt2->setValue($param,$neu_value);
			$neu2->setValue($param,$alt_value);
		}
		$alt2->update();
		$neu2->update();
	}

	// alle artikel suchen nach |art_id| und pfade ersetzen
	// alles artikel mit re_id alt_id suchen und ersetzen

	$articles = new rex_sql();
	$ia = new rex_sql();
	$articles->setQuery("select * from ".$REX['TABLE_PREFIX']."article where path like '%|$alt_id|%'");
	for($i=0;$i<$articles->getRows();$i++)
  {
		$iid = $articles->getValue("id");
		$ipath = str_replace("|$alt_id|","|$neu_id|",$articles->getValue("path"));

		$ia->setTable($REX['TABLE_PREFIX']."article");
		$ia->setWhere('id='.$iid);
		$ia->setValue("path",$ipath);
		if ($articles->getValue("re_id")==$alt_id) $ia->setValue("re_id",$neu_id);
		$ia->update();
		$GAID[$iid] = $iid;
		$articles->next();
	}

	$GAID[$neu_id] = $neu_id;
	$GAID[$alt_id] = $alt_id;

	foreach($GAID as $gid)
	{
		rex_generateArticle($gid);
	}

	return true;
}


/**
 * Berechnet die Prios der Kategorien in einer Kategorie neu
 *
 * @param $re_id    KategorieId der Kategorie, die erneuert werden soll
 * @param $clang    ClangId der Kategorie, die erneuert werden soll
 * @param $new_prio Neue PrioNr der Kategorie
 * @param $old_prio Alte PrioNr der Kategorie
 *
 * @deprecated 4.1 - 26.03.2008
 * Besser die rex_organize_priorities() Funktion verwenden!
 */
function rex_newCatPrio($re_id, $clang, $new_prio, $old_prio)
{
	global $REX;
  if ($new_prio != $old_prio)
  {
    if ($new_prio < $old_prio)
      $addsql = "desc";
    else
      $addsql = "asc";

    rex_organize_priorities(
      $REX['TABLE_PREFIX'].'article',
      'catprior',
      'clang='. $clang .' AND re_id='. $re_id .' AND startpage=1',
      'catprior,updatedate '. $addsql
    );
//    $gu = new rex_sql;
//    $gr = new rex_sql;
//    $gr->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$re_id' and clang='$clang' and startpage=1 order by catprior,updatedate $addsql");
//    for ($i = 0; $i < $gr->getRows(); $i ++)
//    {
//      $ipid = $gr->getValue("pid");
//      $iprior = $i +1;
//      $gu->setQuery("update ".$REX['TABLE_PREFIX']."article set catprior=$iprior where pid='$ipid'");
//      $gr->next();
//    }
    rex_generateLists($re_id);
  }
}

/**
 * Berechnet die Prios der Artikel in einer Kategorie neu
 *
 * @param $re_id    KategorieId der Kategorie, die erneuert werden soll
 * @param $clang    ClangId der Kategorie, die erneuert werden soll
 * @param $new_prio Neue PrioNr der Kategorie
 * @param $old_prio Alte PrioNr der Kategorie
 *
 * @deprecated 4.1 - 26.03.2008
 * Besser die rex_organize_priorities() Funktion verwenden!
 */
function rex_newArtPrio($re_id, $clang, $new_prio, $old_prio)
{
	global $REX;
  if ($new_prio != $old_prio)
  {
    if ($new_prio < $old_prio)
      $addsql = "desc";
    else
      $addsql = "asc";

    rex_organize_priorities(
      $REX['TABLE_PREFIX'].'article',
      'prior',
      'clang='. $clang .' AND ((startpage<>1 AND re_id='. $re_id .') OR (startpage=1 AND id='. $re_id .'))',
      'prior,updatedate '. $addsql
    );
//    $gu = new rex_sql;
//    $gr = new rex_sql;
//    $gr->setQuery("select * from ".$REX['TABLE_PREFIX']."article where clang='$clang' and ((startpage<>1 and re_id='$re_id') or (startpage=1 and id=$re_id))order by prior,updatedate $addsql");
//    for ($i = 0; $i < $gr->getRows(); $i ++)
//    {
//      // echo "<br>".$gr->getValue("pid")." ".$gr->getValue("id")." ".$gr->getValue("name");
//      $ipid = $gr->getValue("pid");
//      $iprior = $i +1;
//      $gu->setQuery("update ".$REX['TABLE_PREFIX']."article set prior=$iprior where pid='$ipid'");
//      $gr->next();
//    }
    rex_generateLists($re_id);
  }
}

/**
 * Verschieben eines Artikels von einer Kategorie in eine Andere
 *
 * @param $id          ArtikelId des zu verschiebenden Artikels
 * @param $from_cat_id KategorieId des Artikels, der Verschoben wird
 * @param $to_cat_id   KategorieId in die der Artikel verschoben werden soll
 */
function rex_moveArticle($id, $from_cat_id, $to_cat_id)
{
  global $REX, $REX_USER;

  $id = (int) $id;
  $to_cat_id = (int) $to_cat_id;
  $from_cat_id = (int) $from_cat_id;

  if ($from_cat_id == $to_cat_id)
    return false;

  // Artikel in jeder Sprache verschieben
  foreach ($REX['CLANG'] as $clang => $clang_name)
  {
    // validierung der id & from_cat_id
    $from_sql = new rex_sql;
    $from_sql->setQuery('select * from '.$REX['TABLE_PREFIX'].'article where clang="'. $clang .'" and startpage<>1 and id="'. $id .'" and re_id="'. $from_cat_id .'"');

    if ($from_sql->getRows() == 1)
    {
      // validierung der to_cat_id
      $to_sql = new rex_sql;
      $to_sql->setQuery('select * from '.$REX['TABLE_PREFIX'].'article where clang="'. $clang .'" and startpage=1 and id="'. $to_cat_id .'"');

      if ($to_sql->getRows() == 1 || $to_cat_id == 0)
      {
				if ($to_sql->getRows() == 1)
				{
					$re_id = $to_sql->getValue('id');
					$path = $to_sql->getValue('path').$to_sql->getValue('id').'|';
					$catname = $to_sql->getValue('name');
				}else
				{
					// In RootEbene
					$re_id = 0;
      		$path = '|';
      		$catname = $from_sql->getValue("name");
				}

        $art_sql = new rex_sql;
        //$art_sql->debugsql = 1;

        $art_sql->setTable($REX['TABLE_PREFIX'].'article');
        $art_sql->setValue('re_id', $re_id);
        $art_sql->setValue('path', $path);
        $art_sql->setValue('catname', $catname);
        // Artikel als letzten Artikel in die neue Kat einfügen
        $art_sql->setValue('prior', '99999');
        // Kopierter Artikel offline setzen
        $art_sql->setValue('status', '0');
        $art_sql->addGlobalUpdateFields();

        $art_sql->setWhere('clang="'. $clang .'" and startpage<>1 and id="'. $id .'" and re_id="'. $from_cat_id .'"');
        $art_sql->update();

        // Prios neu berechnen
        rex_newArtPrio($to_cat_id, $clang, 1, 0);
        rex_newArtPrio($from_cat_id, $clang, 1, 0);
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }

  // Generated des Artikels neu erzeugen
  rex_generateArticle($id,false);

  // Generated der Kategorien neu erzeugen, da sich derin befindliche Artikel geändert haben
  rex_generateArticle($from_cat_id,false);
  rex_generateArticle($to_cat_id,false);

  return true;
}

/**
 * Verschieben einer Kategorie in eine andere
 *
 * @param $from_cat_id KategorieId der Kategorie, die verschoben werden soll (Quelle)
 * @param $to_cat_id   KategorieId der Kategorie, IN die verschoben werden soll (Ziel)
 */
function rex_moveCategory($from_cat, $to_cat)
{

  global $REX;

  $from_cat = (int) $from_cat;
  $to_cat = (int) $to_cat;

  if ($from_cat == $to_cat)
  {
  	// kann nicht in gleiche kategroie kopiert werden
	return false;
  }else
  {
	// kategorien vorhanden ?
  	// ist die zielkategorie im pfad der quellkategeorie ?
  	$fcat = new rex_sql;
  	$fcat->setQuery("select * from ".$REX['TABLE_PREFIX']."article where startpage=1 and id=$from_cat and clang=0");

  	$tcat = new rex_sql;
  	$tcat->setQuery("select * from ".$REX['TABLE_PREFIX']."article where startpage=1 and id=$to_cat and clang=0");

	if ($fcat->getRows()!=1 or ($tcat->getRows()!=1 && $to_cat != 0))
	{
		// eine der kategorien existiert nicht
		return false;
	}else
	{
		if ($to_cat>0)
		{
			$tcats = explode("|",$tcat->getValue("path"));
			if (in_array($from_cat,$tcats))
			{
				// zielkategorie ist in quellkategorie -> nicht verschiebbar
				return false;
			}
		}

		// ----- folgende cats regenerate
		$RC = array();
		$RC[$fcat->getValue("re_id")] = 1;
		$RC[$from_cat] = 1;
		$RC[$to_cat] = 1;

		if ($to_cat>0)
		{
			$to_path = $tcat->getValue("path").$to_cat."|";
			$to_re_id = $tcat->getValue("re_id");
		}else
		{
			$to_path = "|";
			$to_re_id = 0;
		}

		$from_path = $fcat->getValue("path").$from_cat."|";

		$gcats = new rex_sql;
		// $gcats->debugsql = 1;
		$gcats->setQuery("select * from ".$REX['TABLE_PREFIX']."article where path like '".$from_path."%' and clang=0");

		$up = new rex_sql;
		// $up->debugsql = 1;
		for($i=0;$i<$gcats->getRows();$i++)
		{
			// make update
			$new_path = $to_path.$from_cat."|".str_replace($from_path,"",$gcats->getValue("path"));
			$icid = $gcats->getValue("id");
			$irecid = $gcats->getValue("re_id");

			// path aendern und speichern
			$up->setTable($REX['TABLE_PREFIX']."article");
			$up->setWhere("id=$icid");
			$up->setValue("path",$new_path);
			$up->update();

			// cat in gen eintragen
			$RC[$icid] = 1;

			$gcats->next();
		}

		// ----- clang holen, max catprio holen und entsprechen updaten

		$gmax = new rex_sql;
		$up = new rex_sql;
		// $up->debugsql = 1;
    foreach($REX['CLANG'] as $clang => $clang_name)
		{
			$gmax->setQuery("select max(catprior) from ".$REX['TABLE_PREFIX']."article where re_id=$to_cat and clang=".$clang);
			$catprior = (int) $gmax->getValue("max(catprior)");
			$up->setTable($REX['TABLE_PREFIX']."article");
			$up->setWhere("id=$from_cat and clang=$clang ");
			$up->setValue("path",$to_path);
			$up->setValue("re_id",$to_cat);
			$up->setValue("catprior",($catprior+1));
			$up->update();
		}

		// ----- generiere artikel neu - ohne neue inhaltsgenerierung
		foreach($RC as $id => $key)
		{
			rex_generateArticle($id,false);
		}

    foreach($REX['CLANG'] as $clang => $clang_name)
		{
			rex_newCatPrio($fcat->getValue("re_id"),$clang,0,1);
		}

		return true;
	}
  }
}

/**
 * Kopieren eines Artikels von einer Kategorie in eine andere
 *
 * @param $id          ArtikelId des zu kopierenden Artikels
 * @param $to_cat_id   KategorieId in die der Artikel kopiert werden soll
 */
function rex_copyArticle($id, $to_cat_id)
{
  global $REX, $REX_USER;

  $id = (int) $id;
  $to_cat_id = (int) $to_cat_id;
  $new_id = '';

  // Artikel in jeder Sprache kopieren
  foreach ($REX['CLANG'] as $clang => $clang_name)
  {
    // validierung der id & from_cat_id
    $from_sql = new rex_sql;
    $qry = 'select * from '.$REX['TABLE_PREFIX'].'article where clang="'.$clang.'" and id="'. $id .'"';
    $from_sql->setQuery($qry);

    if ($from_sql->getRows() == 1)
    {
      // validierung der to_cat_id
      $to_sql = new rex_sql;
      $to_sql->setQuery('select * from '.$REX['TABLE_PREFIX'].'article where clang="'.$clang.'" and startpage=1 and id="'. $to_cat_id .'"');

      if ($to_sql->getRows() == 1 || $to_cat_id == 0)
      {
      	if ($to_sql->getRows() == 1)
      	{
      		$path = $to_sql->getValue('path').$to_sql->getValue('id').'|';
      	  $catname = $to_sql->getValue('name');
      	}else
      	{
      		// In RootEbene
      		$path = '|';
      		$catname = $from_sql->getValue("name");
      	}

        $art_sql = new rex_sql;
        $art_sql->setTable($REX['TABLE_PREFIX'].'article');
        if ($new_id == "") $new_id = $art_sql->setNewId('id');
        $art_sql->setValue('id', $new_id); // neuen auto_incrment erzwingen
        $art_sql->setValue('re_id', $to_cat_id);
        $art_sql->setValue('path', $path);
        $art_sql->setValue('catname', $catname);
        $art_sql->setValue('catprior', 0);
        $art_sql->setValue('prior', 99999); // Artikel als letzten Artikel in die neue Kat einfügen
        $art_sql->setValue('status', 0); // Kopierter Artikel offline setzen
        $art_sql->setValue('startpage', 0);
        $art_sql->addGlobalCreateFields();

        // schon gesetzte Felder nicht wieder überschreiben
        $dont_copy = array ('id', 'pid', 're_id', 'catname', 'catprior', 'path', 'prior', 'status', 'createdate', 'createuser', 'startpage');

        foreach (array_diff($from_sql->getFieldnames(), $dont_copy) as $fld_name)
        {
          $art_sql->setValue($fld_name, $from_sql->getValue($fld_name));
        }

        $art_sql->setValue("clang", $clang);
        $art_sql->insert();

        // ArticleSlices kopieren
        rex_copyContent($id, $new_id, $clang, $clang);

        // Prios neu berechnen
        rex_newArtPrio($to_cat_id, $clang, 1, 0);
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }

  // Generated des Artikels neu erzeugen
  rex_generateArticle($id,false);

  // Generated der Kategorien neu erzeugen, da sich derin befindliche Artikel geändert haben
  rex_generateArticle($to_cat_id,false);

  return $new_id;
}

/**
 * Kopieren einer Kategorie in eine andere
 *
 * @param $from_cat_id KategorieId der Kategorie, die kopiert werden soll (Quelle)
 * @param $to_cat_id   KategorieId der Kategorie, IN die kopiert werden soll (Ziel)
 */
function rex_copyCategory($from_cat, $to_cat)
{
  // TODO rex_copyCategory implementieren
}

/**
 * Kopiert die Metadaten eines Artikels in einen anderen Artikel
 *
 * @param $from_id      ArtikelId des Artikels, aus dem kopiert werden (Quell ArtikelId)
 * @param $to_id        ArtikelId des Artikel, in den kopiert werden sollen (Ziel ArtikelId)
 * @param [$from_clang] ClangId des Artikels, aus dem kopiert werden soll (Quell ClangId)
 * @param [$to_clang]   ClangId des Artikels, in den kopiert werden soll (Ziel ClangId)
 * @param [$params]     Array von Spaltennamen, welche kopiert werden sollen
 */
function rex_copyMeta($from_id, $to_id, $from_clang = 0, $to_clang = 0, $params = array ())
{
  global $REX, $REX_USER;

  $from_clang = (int) $from_clang;
  $to_clang = (int) $to_clang;
  $from_id = (int) $from_id;
  $to_id = (int) $to_id;
  if (!is_array($params))
    $params = array ();

  if ($from_id == $to_id && $from_clang == $to_clang)
    return false;

  $gc = new rex_sql;
  $gc->setQuery("select * from ".$REX['TABLE_PREFIX']."article where clang='$from_clang' and id='$from_id'");

  if ($gc->getRows() == 1)
  {
    $uc = new rex_sql;
    // $uc->debugsql = 1;
    $uc->setTable($REX['TABLE_PREFIX']."article");
    $uc->setWhere("clang='$to_clang' and id='$to_id'");
    $uc->addGlobalUpdateFields();

    foreach ($params as $key => $value)
    {
      $var = $gc->getValue("$value");
      $uc->setValue("$value", $var);
    }

    $uc->update();

    rex_generateArticle($to_id,false);
    return true;
  }
  return false;

}

/**
 * Kopiert die Inhalte eines Artikels in einen anderen Artikel
 *
 * @param $from_id           ArtikelId des Artikels, aus dem kopiert werden (Quell ArtikelId)
 * @param $to_id             ArtikelId des Artikel, in den kopiert werden sollen (Ziel ArtikelId)
 * @param [$from_clang]      ClangId des Artikels, aus dem kopiert werden soll (Quell ClangId)
 * @param [$to_clang]        ClangId des Artikels, in den kopiert werden soll (Ziel ClangId)
 * @param [$from_re_sliceid] Id des Slices, bei dem begonnen werden soll
 */
function rex_copyContent($from_id, $to_id, $from_clang = 0, $to_clang = 0, $from_re_sliceid = 0)
{
  global $REX, $REX_USER;

  if ($from_id == $to_id && $from_clang == $to_clang)
    return false;

  $gc = new rex_sql;
  $gc->setQuery("select * from ".$REX['TABLE_PREFIX']."article_slice where re_article_slice_id='$from_re_sliceid' and article_id='$from_id' and clang='$from_clang'");

  if ($gc->getRows() == 1)
  {

    // letzt slice_id des ziels holen ..
    $glid = new rex_sql;
    $glid->setQuery("select r1.id, r1.re_article_slice_id
                     from ".$REX['TABLE_PREFIX']."article_slice as r1
                     left join ".$REX['TABLE_PREFIX']."article_slice as r2 on r1.id=r2.re_article_slice_id
                     where r1.article_id=$to_id and r1.clang=$to_clang and r2.id is NULL;");
    if ($glid->getRows() == 1)
      $to_last_slice_id = $glid->getValue("r1.id");
    else
      $to_last_slice_id = 0;

    $ins = new rex_sql;
    // $ins->debugsql = 1;
    $ins->setTable($REX['TABLE_PREFIX']."article_slice");

    $cols = new rex_sql;
    // $cols->debugsql = 1;
    $cols->setquery("SHOW COLUMNS FROM ".$REX['TABLE_PREFIX']."article_slice");
    for ($j = 0; $j < $cols->rows; $j ++, $cols->next())
    {
      $colname = $cols->getvalue("Field");
      if ($colname == "clang")
        $value = $to_clang;
      elseif ($colname == "re_article_slice_id") $value = $to_last_slice_id;
      elseif ($colname == "article_id") $value = $to_id;
      elseif ($colname == "createdate") $value = time();
      elseif ($colname == "updatedate") $value = time();
      elseif ($colname == "createuser") $value = $REX_USER->getValue("login");
      elseif ($colname == "updateuser") $value = $REX_USER->getValue("login");
      else
        $value = addslashes($gc->getValue("$colname"));

      if ($colname != "id")
        $ins->setValue($colname, $value);
    }
    $ins->insert();

    // id holen und als re setzen und weitermachen..
    rex_copyContent($from_id, $to_id, $from_clang, $to_clang, $gc->getValue("id"));
    return true;
  }

  rex_generateArticle($to_id);

  return true;
}

// ----------------------------------------- CTYPE

// ----------------------------------------- FILE

/**
 * Löscht einen Ordner/Datei mit Unterordnern
 *
 * @param $file Zu löschender Ordner/Datei
 * @param $delete_folders Ordner auch löschen? false => nein, true => ja
 */
function rex_deleteDir($file, $delete_folders = false)
{
  $state = true;

  if (file_exists($file))
  {
    // Fehler unterdrücken, falls keine Berechtigung
    if (@ is_dir($file))
    {
      $handle = opendir($file);
      if (!$handle)
      {
        return false;
      }

      while ($filename = readdir($handle))
      {
        if ($filename == '.' || $filename == '..')
        {
          continue;
        }

        if (!rex_deleteDir($file.'/'.$filename, $delete_folders) && $state === true)
        {
          $state = false;
        }
      }
      closedir($handle);

      if ($state !== true)
      {
        return false;
      }

      // Ordner auch löschen?
      if ($delete_folders)
      {
        // Fehler unterdrücken, falls keine Berechtigung
        if (!@ rmdir($file))
        {
          return false;
        }
      }
    }
    else
    {
      // Datei löschen
      // Fehler unterdrücken, falls keine Berechtigung
      if (!@ unlink($file))
      {
        return false;
      }
    }
  }
  else
  {
    // Datei/Ordner existiert nicht
    return false;
  }

  return true;
}

// ----------------------------------------- CLANG

/**
 * Löscht eine Clang
 *
 * @param $id Zu löschende ClangId
 */
function rex_deleteCLang($clang)
{
  global $REX;

  if ($clang == 0)
    return "";

  $content = "";

  foreach($REX['CLANG'] as $cur => $val)
  {
    if ($cur != $clang)
      $content .= "\$REX['CLANG']['$cur'] = \"$val\";\n";
  }

  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  rex_replace_dynamic_contents($file, $content);

  $del = new rex_sql();
  $del->setQuery("select * from ".$REX['TABLE_PREFIX']."article where clang='$clang'");
  for ($i = 0; $i < $del->getRows(); $i ++)
  {
    $aid = $del->getValue("id");
    rex_deleteCacheArticle($aid, $clang);
    $del->next();
  }

  $del->setQuery("delete from ".$REX['TABLE_PREFIX']."article where clang='$clang'");
  $del->setQuery("delete from ".$REX['TABLE_PREFIX']."article_slice where clang='$clang'");

  unset ($REX['CLANG'][$clang]);
  $del = new rex_sql();
  $del->setQuery("delete from ".$REX['TABLE_PREFIX']."clang where id='$clang'");

  // ----- EXTENSION POINT
  rex_register_extension_point('CLANG_DELETED','',array ('id' => $clang));

  rex_generateAll();
}

/**
 * Erstellt eine Clang
 *
 * @param $id   Id der Clang
 * @param $name Name der Clang
 */
function rex_addCLang($id, $name)
{
  global $REX;
  $REX['CLANG'][$id] = $name;

  $content = "";
  foreach($REX['CLANG'] as $cur => $val)
  {
    $content .= "\$REX['CLANG']['$cur'] = \"$val\";\n";
  }

  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  rex_replace_dynamic_contents($file, $content);

  $add = new rex_sql();
  $add->setQuery("select * from ".$REX['TABLE_PREFIX']."article where clang='0'");
  $fields = $add->getFieldnames();

  $adda = new rex_sql;
  // $adda->debugsql = 1;
  for ($i = 0; $i < $add->getRows(); $i ++)
  {
    $adda->setTable($REX['TABLE_PREFIX']."article");

    foreach($fields as $key => $value)
    {
      if ($value == 'pid')
        echo ''; // nix passiert
      else
        if ($value == 'clang')
          $adda->setValue('clang', $id);
        else
          if ($value == 'status')
            $adda->setValue('status', '0'); // Alle neuen Artikel offline
      else
        $adda->setValue($value, rex_addslashes($add->getValue($value)));
    }

    $adda->insert();
    $add->next();
  }

  $add = new rex_sql();
  $add->setQuery("insert into ".$REX['TABLE_PREFIX']."clang set id='$id',name='$name'");

  // ----- EXTENSION POINT
  rex_register_extension_point('CLANG_ADDED','',array ('id' => $id, 'name' => $name));

  rex_generateAll();
}

/**
 * Ändert eine Clang
 *
 * @param $id   Id der Clang
 * @param $name Name der Clang
 */
function rex_editCLang($id, $name)
{
  global $REX;

  $REX['CLANG'][$id] = $name;
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";

  $cont = rex_get_file_contents($file);
  $cont = ereg_replace("(REX\['CLANG'\]\['$id\'].?\=.?)[^;]*", "\\1\"". ($name)."\"", $cont);
  rex_put_file_contents($file, $cont);

  $edit = new rex_sql;
  $edit->setQuery("update ".$REX['TABLE_PREFIX']."clang set name='$name' where id='$id'");

  // ----- EXTENSION POINT
  rex_register_extension_point('CLANG_UPDATED','',array ('id' => $id, 'name' => $name));
}

/**
* Schreibt Addoneigenschaften in die Datei include/addons.inc.php
* @param array Array mit den Namen der Addons aus dem Verzeichnis addons/
*/
function rex_generateAddons($ADDONS, $debug = false)
{
  global $REX;
  natsort($ADDONS);

  $content = "";
  foreach ($ADDONS as $cur)
  {
    if (!OOAddon :: isInstalled($cur))
      $REX['ADDON']['install'][$cur] = 0;

    if (!OOAddon :: isActivated($cur))
      $REX['ADDON']['status'][$cur] = 0;

    $content .= "\$REX['ADDON']['install']['$cur'] = ".$REX['ADDON']['install'][$cur].";\n"."\$REX['ADDON']['status']['$cur'] = ".$REX['ADDON']['status'][$cur].";\n\n";
  }

  // Da dieser Funktion öfter pro request aufgerufen werden kann,
  // hier die caches löschen
  clearstatcache();

  $file = $REX['INCLUDE_PATH']."/addons.inc.php";
  if(!rex_replace_dynamic_contents($file, $content))
  {
    return 'Datei "'.$file.'" hat keine Schreibrechte';
  }
  return true;
}

function rex_generateTemplate($template_id)
{
  global $REX;

  $sql = new rex_sql();
  $qry = 'SELECT * FROM '. $REX['TABLE_PREFIX']  .'template WHERE id = '.$template_id;
  $sql->setQuery($qry);

  if($sql->getRows() == 1)
  {
    $templatesDir = rex_template::getTemplatesDir();
    $templateFile = rex_template::getFilePath($template_id);

  	$content = $sql->getValue('content');
  	foreach($REX['VARIABLES'] as $var)
  	{
  		$content = $var->getTemplate($content);
  	}
    if(rex_put_file_contents($templateFile, $content))
    {
      return true;
    }
    else
    {
      trigger_error('Unable to generate template '. $template_id .'!', E_USER_ERROR);

      if(!is_writable())
        trigger_error('directory "'. $templatesDir .'" is not writable!', E_USER_ERROR);
    }
  }
  else
  {
    trigger_error('Template with id "'. $template_id .'" does not exist!', E_USER_ERROR);
  }

  return false;
}

// ----------------------------------------- generate helpers

/**
 * Escaped einen String
 *
 * @param $string Zu escapender String
 */
function rex_addslashes($string, $flag = '\\\'\"')
{
  if ($flag == '\\\'\"')
  {
    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace('\'', '\\\'', $string);
    $string = str_replace('"', '\"', $string);
  }elseif ($flag == '\\\'')
  {
    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace('\'', '\\\'', $string);
  }
  return $string;
}

?>