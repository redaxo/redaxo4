<?php


/** 
 * Funktionensammlung für die generierung der Artikel/Templates/Kategorien/Metainfos.. etc. 
 * @package redaxo3 
 * @version $Id$ 
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
  rex_deleteDir($REX['INCLUDE_PATH']."/generated/templates", 0);
  $gt = new rex_sql;
  $gt->setQuery("select * from ".$REX['TABLE_PREFIX']."template");
  for ($i = 0; $i < $gt->getRows(); $i ++)
  {
    rex_generateTemplate($gt->getValue("id"));
    $gt->next();
  }

  // ----------------------------------------------------------- generiere artikel
  rex_deleteDir($REX['INCLUDE_PATH']."/generated/articles", 0);
  $gc = new rex_sql;
  $gc->setQuery("select distinct id from ".$REX['TABLE_PREFIX']."article");
  for ($i = 0; $i < $gc->getRows(); $i ++)
  {
    rex_generateArticle($gc->getValue("id"));
    $gc->next();
  }

  // ----------------------------------------------------------- generiere clang
  $lg = new rex_sql();
  $lg->setQuery("select * from ".$REX['TABLE_PREFIX']."clang order by id");
  $content = "// --- DYN\n\r";
  for ($i = 0; $i < $lg->getRows(); $i ++)
  {
    $id = $lg->getValue("id");
    $name = $lg->getValue("name");
    $content .= "\n\r\$REX['CLANG']['$id'] = \"$name\";";
    $lg->next();
  }
  $content .= "\n\r// --- /DYN";
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  $h = fopen($file, "r");
  $fcontent = fread($h, filesize($file));
  $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", $content, $fcontent);
  fclose($h);
  $h = fopen($file, "w+");
  fwrite($h, $fcontent, strlen($fcontent));
  fclose($h);
  @ chmod($file, $REX['FILEPERM']);

  // ----------------------------------------------------------- generiere filemetas ...
  // **********************

  // ----------------------------------------------------------- message
  $MSG = $I18N->msg('articles_generated')." ".$I18N->msg('old_articles_deleted');

  // ----- EXTENSION POINT
  $MSG = rex_register_extension_point('ALL_GENERATED', $MSG);

  return $MSG;
}

// ----------------------------------------- ARTICLE

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

  $CL = $REX['CLANG'];
  reset($CL);
  for ($i = 0; $i < count($CL); $i ++)
  {
    $MSG = '';
    $clang = key($CL);
    $CONT = new rex_article;
    $CONT->setCLang($clang);
    $CONT->setMode("generate"); // keine Ausgabe als eval(CONTENT) sondern nur speichern in datei
    $CONT->setArticleId($id);

    // --------------------------------------------------- Artikelparameter speichern
    $params = array(
      'article_id' => $id,
      'last_update_stamp' => time()
    );
    
    $db_fields = array(
      're_id',
      'clang',
      'name',
      'catname',
      'label',
      'startpage',
      'template_id',
      'prior',
      'path',
      'url',
      'file',
      'type_id',
      'teaser',
      'keywords',
      'description',
      'attributes',
      'updatedate',
      'createdate',
      'updateuser',
      'createuser',
      'status',
    );
    
    foreach($db_fields as $field)
      $params[$field] = $CONT->getValue($field);

    // ----- Extension Point    
    $params = rex_register_extension_point('ART_META_PARAMS', $params);
    
    $content = '<?php'."\n";
    
    foreach($params as $name => $value)
      $content .='$REX[\'ART\']['. $id .'][\''. $name .'\']['. $clang .'] = \''. rex_addslashes($value) .'\';'."\n";
    
    $content .= '?>';
                
    if ($fp = @ fopen($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.article", "w"))
    {
      fputs($fp, $content);
      fclose($fp);
      @ chmod($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.article", 0777);
    }
    else
    {
      $MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
    }

    // --------------------------------------------------- Artikelcontent speichern
	if ($refreshall)
	{
	    if ($fp = @ fopen($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.content", "w"))
	    {
	      $article_content = "?>".$CONT->getArticle();
	      fputs($fp, $article_content);
	      fclose($fp);
	      @ chmod($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.content", 0777);
	    }
	    else
	    {
	      $MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
	    }
	}
	
    if ($MSG != '')
      echo '<p class="rex-warning">'. $MSG .'</p>';

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

    next($CL);

  }

}

/**
 * Löscht einen Artikel
 * 
 * @param $id ArtikelId des Artikels, der gelöscht werden soll
 */
function rex_deleteArticle($id, $ebene = 0)
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
    return $I18N->msg("cant_delete_startarticle");
  }

  $ART = new rex_sql;
  $ART->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id='$id' and clang='0'");

  if ($ART->getRows() > 0)
  {
    $re_id = $ART->getValue("re_id");
    if ($ART->getValue("startpage") == 1)
    {
      $SART = new rex_sql;
      $SART->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$id' and clang='0'");
      for ($i = 0; $i < $SART->getRows(); $i ++)
      {
        rex_deleteArticle($id, ($ebene +1));
        $SART->next();
      }
    }

    $CL = $REX['CLANG'];
    reset($CL);
    for ($i = 0; $i < count($CL); $i ++)
    {
      $clang = key($CL);
      @ unlink($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.article");
      @ unlink($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.content");
      @ unlink($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.alist");
      @ unlink($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.clist");
      $ART->query("delete from ".$REX['TABLE_PREFIX']."article where id='$id'");
      $ART->query("delete from ".$REX['TABLE_PREFIX']."article_slice where article_id='$id'");
      next($CL);
    }

    // --------------------------------------------------- Listen generieren
    rex_generateLists($re_id);

    return $I18N->msg('category_deleted').' '.$I18N->msg('article_deleted');

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

  $CL = $REX['CLANG'];
  reset($CL);
  for ($j = 0; $j < count($CL); $j ++)
  {

    $clang = key($CL);

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
    $fp = fopen($REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.alist", "w");
    fputs($fp, $content);
    fclose($fp);
    @ chmod($REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.alist", 0777);

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
    $fp = fopen($REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.clist", "w");
    fputs($fp, $content);
    fclose($fp);
    @ chmod($REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.clist", 0777);

    next($CL);
  }

}

/**
 * Berechnet die Prios der Kategorien in einer Kategorie neu
 * 
 * @param $re_id    KategorieId der Kategorie, die erneuert werden soll
 * @param $clang    ClangId der Kategorie, die erneuert werden soll
 * @param $new_prio Neue PrioNr der Kategorie 
 * @param $old_prio Alte PrioNr der Kategorie
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

    $gu = new rex_sql;
    $gr = new rex_sql;
    $gr->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$re_id' and clang='$clang' and startpage=1 order by catprior,updatedate $addsql");
    for ($i = 0; $i < $gr->getRows(); $i ++)
    {
      $ipid = $gr->getValue("pid");
      $iprior = $i +1;
      $gu->query("update ".$REX['TABLE_PREFIX']."article set catprior=$iprior where pid='$ipid'");
      $gr->next();
    }
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

    $gu = new rex_sql;
    $gr = new rex_sql;
    $gr->setQuery("select * from ".$REX['TABLE_PREFIX']."article where clang='$clang' and ((startpage<>1 and re_id='$re_id') or (startpage=1 and id=$re_id))order by prior,updatedate $addsql");
    for ($i = 0; $i < $gr->getRows(); $i ++)
    {
      // echo "<br>".$gr->getValue("pid")." ".$gr->getValue("id")." ".$gr->getValue("name");
      $ipid = $gr->getValue("pid");
      $iprior = $i +1;
      $gu->query("update ".$REX['TABLE_PREFIX']."article set prior=$iprior where pid='$ipid'");
      $gr->next();
    }
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
    //$from_sql->debugsql = 1;
    $from_sql->setQuery('select * from '.$REX['TABLE_PREFIX'].'article where clang="'. $clang .'" and startpage<>1 and id="'. $id .'" and re_id="'. $from_cat_id .'"');

    if ($from_sql->getRows() == 1)
    {
      // validierung der to_cat_id
      $to_sql = new rex_sql;
      //$to_sql->debugsql = 1;
      $to_sql->setQuery('select * from '.$REX['TABLE_PREFIX'].'article where clang="'. $clang .'" and startpage=1 and id="'. $to_cat_id .'"');

      if ($to_sql->getRows() == 1)
      {
        $art_sql = new rex_sql;
        //$art_sql->debugsql = 1;

        $art_sql->setTable($REX['TABLE_PREFIX'].'article');
        $art_sql->setValue('re_id', $to_sql->getValue('id'));
        $art_sql->setValue('path', $to_sql->getValue('path').$to_sql->getValue('id').'|');
        $art_sql->setValue('catname', $to_sql->getValue('name'));
        // Artikel als letzten Artikel in die neue Kat einfügen
        $art_sql->setValue('prior', '99999');
        // Kopierter Artikel offline setzen
        $art_sql->setValue('status', '0');
        $art_sql->setValue('updatedate', time());
        $art_sql->setValue('updateuser', addslashes($REX_USER->getValue('login')));

        $art_sql->where('clang="'. $clang .'" and startpage<>1 and id="'. $id .'" and re_id="'. $from_cat_id .'"');
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

		for($i=0;$i<$gcats->getRows();$i++)
		{
			// make update
			$new_path = $to_path.$from_cat."|".str_replace($from_path,"",$gcats->getValue("path"));
			$icid = $gcats->getValue("id");
			$irecid = $gcats->getValue("re_id");
			
			// path aendern und speichern
			$up = new rex_sql;
			// $up->debugsql = 1;
			$up->setTable($REX['TABLE_PREFIX']."article");
			$up->where("id=$icid");
			$up->setValue("path",$new_path);
			$up->update();
			
			// cat in gen eintragen			
			$RC[$icid] = 1;
			
			$gcats->next();
		}		

		// ----- clang holen, max catprio holen und entsprechen updaten
		$CL = $REX['CLANG'];
		reset($CL);
		for ($i = 0; $i < count($CL); $i ++)
		{
			$clang = key($CL);
			$gmax = new rex_sql;
			$gmax->setQuery("select max(catprior) from ".$REX['TABLE_PREFIX']."article where re_id=$to_cat and clang=$clang");
			$catprior = (int) $gmax->getValue("max(catprior)");
			$up = new rex_sql;
			// $up->debugsql = 1;
			$up->setTable($REX['TABLE_PREFIX']."article");
			$up->where("id=$from_cat and clang=$clang ");
			$up->setValue("path",$to_path);
			$up->setValue("re_id",$to_cat);
			$up->setValue("catprior",($catprior+1));
			$up->update();
			next($CL);
		}

		// ----- generiere artikel neu - ohne neue inhaltsgenerierung
		foreach($RC as $id => $key)
		{
			rex_generateArticle($id,false);
		}
		
		$CL = $REX['CLANG'];
		reset($CL);
		for ($j=0;$j<count($CL);$j++)
		{
			$mlang = key($CL);
			rex_newCatPrio($fcat->getValue("re_id"),$mlang,0,1);
			next($CL);
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

      if ($to_sql->getRows() == 1)
      {
        $art_sql = new rex_sql;
        $art_sql->setTable($REX['TABLE_PREFIX'].'article');
        if ($new_id == "") $new_id = $art_sql->setNewId('id');
        $art_sql->setValue('id', $new_id); // neuen auto_incrment erzwingen 
        $art_sql->setValue('re_id', $to_sql->getValue('id'));
        $art_sql->setValue('path', $to_sql->getValue('path').$to_sql->getValue('id').'|');
        $art_sql->setValue('catname', $to_sql->getValue('name'));
        $art_sql->setValue('prior', 99999); // Artikel als letzten Artikel in die neue Kat einfügen
        $art_sql->setValue('status', 0); // Kopierter Artikel offline setzen
        $art_sql->setValue('createdate', time());
        $art_sql->setValue('createuser', addslashes($REX_USER->getValue('login')));
        $art_sql->setValue('startpage', 0);

        // schon gesetzte Felder nicht wieder überschreiben
        $dont_copy = array ('id', 'pid', 're_id', 'catname', 'path', 'prior', 'status', 'createdate', 'createuser', 'startpage');
        
        foreach (array_diff($to_sql->getFieldnames(), $dont_copy) as $fld_name)
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
  // TODO
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
    $uc->where("clang='$to_clang' and id='$to_id'");
    $uc->setValue("updatedate", time());
    $uc->setValue("updateuser", addslashes($REX_USER->getValue("login")));

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

        if (($state = rex_deleteDir($file."/".$filename, $delete_folders)) !== true)
        {
          // Schleife abbrechen, dir_hanlde schließen und danach erst false zurückgeben
          break;
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
function rex_deleteCLang($id)
{
  global $REX;

  if ($id == 0)
    return "";

  $content = "// --- DYN\n\r";

  reset($REX['CLANG']);
  for ($i = 0; $i < count($REX['CLANG']); $i ++)
  {
    $cur = key($REX['CLANG']);
    $val = current($REX['CLANG']);
    if ($cur != $id)
      $content .= "\n\r\$REX['CLANG']['$cur'] = \"$val\";";
    next($REX['CLANG']);
  }
  $content .= "\n\r// --- /DYN";
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";

  $h = fopen($file, "r");
  $fcontent = fread($h, filesize($file));
  $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", $content, $fcontent);
  fclose($h);
  $h = fopen($file, "w+");
  fwrite($h, $fcontent, strlen($fcontent));
  fclose($h);
  @ chmod($file, 0777);

  $del = new rex_sql();
  $del->setQuery("select * from ".$REX['TABLE_PREFIX']."article where clang='$id'");
  for ($i = 0; $i < $del->getRows(); $i ++)
  {
    $aid = $del->getValue("id");
    // rex_deleteArticle($del->getValue("id"),$id,0);
    @ unlink($REX['INCLUDE_PATH']."/generated/articles/$aid.$id.article");
    @ unlink($REX['INCLUDE_PATH']."/generated/articles/$aid.$id.content");
    @ unlink($REX['INCLUDE_PATH']."/generated/articles/$aid.$id.alist");
    @ unlink($REX['INCLUDE_PATH']."/generated/articles/$aid.$id.clist");
    $del->next();
  }

  $del->query("delete from ".$REX['TABLE_PREFIX']."article where clang='$id'");
  $del->query("delete from ".$REX['TABLE_PREFIX']."article_slice where clang='$id'");

  unset ($REX['CLANG'][$id]);
  $del = new rex_sql();
  $del->query("delete from ".$REX['TABLE_PREFIX']."clang where id='$id'");

  // ----- EXTENSION POINT
  rex_register_extension_point('CLANG_DELETED','',array ('id' => $id));
  
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
  $content = "// --- DYN\n\r";
  reset($REX['CLANG']);
  for ($i = 0; $i < count($REX['CLANG']); $i ++)
  {
    $cur = key($REX['CLANG']);
    $val = current($REX['CLANG']);

    $content .= "\n\r\$REX['CLANG']['$cur'] = \"$val\";";
    next($REX['CLANG']);
  }
  $content .= "\n\r// --- /DYN";

  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  $h = fopen($file, "r");
  $fcontent = fread($h, filesize($file));
  $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", $content, $fcontent);
  fclose($h);

  $h = fopen($file, "w+");
  fwrite($h, $fcontent, strlen($fcontent));
  fclose($h);
  @ chmod($file, 0777);

  $add = new rex_sql();
  $add->setQuery("select * from ".$REX['TABLE_PREFIX']."article where clang='0'");
  $fields = $add->getFieldnames();
  for ($i = 0; $i < $add->getRows(); $i ++)
  {
    $adda = new rex_sql;
    // $adda->debugsql = 1;
    $adda->setTable($REX['TABLE_PREFIX']."article");
    reset($fields);
    while (list ($key, $value) = each($fields))
    {

      if ($value == "pid")
        echo ""; // nix passiert
      else
        if ($value == "clang")
          $adda->setValue("clang", $id);
        else
          if ($value == "status")
            $adda->setValue("status", "0"); // Alle neuen Artikel offline 
      else
        $adda->setValue($value, rex_addslashes($add->getValue("$value")));
      //  createuser
      //  updateuser
    }
    $adda->insert();

    $add->next();
  }
  $add = new rex_sql();
  $add->query("insert into ".$REX['TABLE_PREFIX']."clang set id='$id',name='$name'");

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
  $h = fopen($file, "r");
  $cont = fread($h, filesize($file));
  $cont = ereg_replace("(REX\['CLANG'\]\['$id\'].?\=.?)[^;]*", "\\1\"". ($name)."\"", $cont);
  fclose($h);
  $h = fopen($REX['INCLUDE_PATH']."/clang.inc.php", "w+");
  fwrite($h, $cont, strlen($cont));
  fclose($h);
  @ chmod($REX['INCLUDE_PATH']."/clang.inc.php", 0777);
  $edit = new rex_sql;
  $edit->query("update ".$REX['TABLE_PREFIX']."clang set name='$name' where id='$id'");
  
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

  $content = "// --- DYN\n\n";
  foreach ($ADDONS as $cur)
  {
    if (!OOAddon :: isInstalled($cur))
    {
      $REX['ADDON']['install'][$cur] = 0;
    }
    if (!OOAddon :: isActivated($cur))
    {
      $REX['ADDON']['status'][$cur] = 0;
    }

    $content .= "\$REX['ADDON']['install']['$cur'] = ".$REX['ADDON']['install'][$cur].";\n"."\$REX['ADDON']['status']['$cur'] = ".$REX['ADDON']['status'][$cur].";\n\n";
  }
  $content .= "// --- /DYN";

  $file = $REX['INCLUDE_PATH']."/addons.inc.php";
  // Sichergehen, dass die Datei existiert und beschreibbar ist
  if (is_writable($file))
  {

    if (!$h = fopen($file, "r"))
    {
      return 'Konnte Datei "'.$file.'" nicht lesen';
    }
    $fcontent = fread($h, filesize($file));
    $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", $content, $fcontent);
    fclose($h);

    if (!$h = fopen($file, "w+"))
    {
      return 'Konnte Datei "'.$file.'" nicht zum schreiben oeffnen';
    }
    //if (!fwrite($h, $fcontent, strlen($fcontent))) {
    if (!fwrite($h, $fcontent, strlen($fcontent)))
    {
      return 'Konnte Inhalt nicht in Datei "'.$file.'" schreiben';
    }
    fclose($h);

    // alles ist gut gegangen
    return true;
  }
  else
  {
    return 'Datei "'.$file.'" hat keine Schreibrechte';
  }
}

function rex_generateTemplate($template_id)
{
  global $REX;
  
  $sql = new rex_sql();
  $qry = 'SELECT * FROM '. $REX['TABLE_PREFIX']  .'template WHERE id = '.$template_id;
  $sql->setQuery($qry);
  
  if($sql->getRows() == 1)
  {
    if($fp = fopen($REX['INCLUDE_PATH']."/generated/templates/".$template_id.".template", "w"))
    {
      fputs($fp, $sql->getValue('content'));
      fclose($fp);
      @ chmod($REX['INCLUDE_PATH']."/generated/templates/". $template_id .".template", $REX['FILEPERM']);
      return true;
    }
  }
  return false;
}

// ----------------------------------------- generate helpers

/**
 * Escaped einen String
 * 
 * @param $string Zu escapender String 
 */
function rex_addslashes($string)
{
  $string = str_replace("\\", "\\\\", $string);
  $string = str_replace("\"", "\\\"", $string);
  $string = str_replace("'", "\'", $string);

  return $string;

}
?>