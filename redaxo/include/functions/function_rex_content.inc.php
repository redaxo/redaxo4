<?php

/**
 * Verschiebt den Slice mit der Id $slice_id der Sprache $clang nach oben
 */
function rex_moveSliceUp($slice_id, $clang)
{
  return rex_moveSlice($slice_id, $clang, 'moveup');
}

/**
 * Verschiebt den Slice mit der Id $slice_id der Sprache $clang nach unten
 */
function rex_moveSliceDown($slice_id, $clang)
{
  return rex_moveSlice($slice_id, $clang, 'movedown');
}

/**
 * Verschiebt den Slice mit der Id $slice_id der Sprache $clang in die Richtung
 * $direction
 */
function rex_moveSlice($slice_id, $clang, $direction)
{
  global $REX, $I18N;

  // ctype beachten
  // verschieben / vertauschen
  // article regenerieren.

  $success = false;
  $message = $I18N->msg('slice_moved_error');

  $CM = new rex_sql;
  $CM->setQuery("select * from " . $REX['TABLE_PREFIX'] . "article_slice left join " . $REX['TABLE_PREFIX'] . "module on " . $REX['TABLE_PREFIX'] . "article_slice.modultyp_id=" . $REX['TABLE_PREFIX'] . "module.id where " . $REX['TABLE_PREFIX'] . "article_slice.id='$slice_id' and clang=$clang");
  if ($CM->getRows() == 1)
  {
    $slice_id = $CM->getValue($REX['TABLE_PREFIX'] . "article_slice.id");
    $slice_article_id = $CM->getValue("article_id");
    $re_slice_id = $CM->getValue($REX['TABLE_PREFIX'] . "article_slice.re_article_slice_id");
    $slice_ctype = $CM->getValue($REX['TABLE_PREFIX'] . "article_slice.ctype");

    $gs = new rex_sql;
    // $gs->debugsql = 1;
    $gs->setQuery("select * from " . $REX['TABLE_PREFIX'] . "article_slice where article_id='$slice_article_id'");
    $SID = array();
    $SREID = array();
    $SCTYPE = array();
    for ($i = 0; $i < $gs->getRows(); $i++)
    {
      $SID[$gs->getValue("re_article_slice_id")] = $gs->getValue("id");
      $SREID[$gs->getValue("id")] = $gs->getValue("re_article_slice_id");
      $SCTYPE[$gs->getValue("id")] = $gs->getValue("ctype");
      $gs->next();
    }

    // ------ moveup
    if ($direction == "moveup")
    {
      if ($SREID[$slice_id] > 0)
      {
        if ($SCTYPE[$SREID[$slice_id]] == $slice_ctype)
        {
          $gs->setQuery("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $SREID[$SREID[$slice_id]] . "' where id='" . $slice_id . "'");
          $gs->setQuery("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $slice_id . "' where id='" . $SREID[$slice_id] . "'");
          if ($SID[$slice_id] > 0)
            $gs->setQuery("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $SREID[$slice_id] . "' where id='" . $SID[$slice_id] . "'");
          rex_generateArticle($slice_article_id);
          $message = $I18N->msg('slice_moved');
          $success = true;
        }
      }
    }

    // ------ movedown
    else if ($direction == "movedown")
    {
      if ($SID[$slice_id] > 0)
      {
        if ($SCTYPE[$SID[$slice_id]] == $slice_ctype)
        {
          $gs->setQuery("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $SREID[$slice_id] . "' where id='" . $SID[$slice_id] . "'");
          $gs->setQuery("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $SID[$slice_id] . "' where id='" . $slice_id . "'");
          if ($SID[$SID[$slice_id]] > 0)
            $gs->setQuery("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $slice_id . "' where id='" . $SID[$SID[$slice_id]] . "'");
          rex_generateArticle($slice_article_id);
          $message = $I18N->msg('slice_moved');
          $success = true;
        }
      }
    }
    else
    {
      trigger_error('Unsupported direction "'. $direction .'"!', E_USER_ERROR);
    }
  }

  return array($success, $message);
}

/**
 * F�hrt alle pre-save Aktionen des Moduls $module_id im Modus $function aus und
 * f�llt dabei das $REX_ACTION Array
 */
function rex_execPreSaveAction($module_id, $function, $REX_ACTION)
{
  global $REX;
  $modebit = rex_getActionModeBit($function);

  $ga = new rex_sql;
  $ga->setQuery('SELECT presave FROM ' . $REX['TABLE_PREFIX'] . 'module_action ma,' . $REX['TABLE_PREFIX'] . 'action a WHERE presave != "" AND ma.action_id=a.id AND module_id=' . $module_id . ' AND ((a.presavemode & ' . $modebit . ') = ' . $modebit . ')');

  for ($i = 0; $i < $ga->getRows(); $i++)
  {
    $REX_ACTION['MSG'] = '';
    $iaction = $ga->getValue('presave');

    // *********************** WERTE ERSETZEN
    foreach ($REX['VARIABLES'] as $obj)
    {
      $iaction = $obj->getACOutput($REX_ACTION, $iaction);
    }

    eval ('?>' . $iaction);

    if ($REX_ACTION['MSG'] != '')
      $message .= $REX_ACTION['MSG'] . ' | ';

    $ga->next();
  }
  return array($message, $REX_ACTION);
}

/**
 * F�hrt alle post-save Aktionen des Moduls $module_id im Modus $function aus
 * und f�llt dabei das $REX_ACTION Array
 */
function rex_execPostSaveAction($module_id, $function, $REX_ACTION)
{
  global $REX;
  $modebit = rex_getActionModeBit($function);

  $ga = new rex_sql;
  $ga->setQuery('SELECT postsave FROM ' . $REX['TABLE_PREFIX'] . 'module_action ma,' . $REX['TABLE_PREFIX'] . 'action a WHERE postsave != "" AND ma.action_id=a.id AND module_id=' . $module_id . ' AND ((a.postsavemode & ' . $modebit . ') = ' . $modebit . ')');

  for ($i = 0; $i < $ga->getRows(); $i++)
  {
    $REX_ACTION['MSG'] = '';
    $iaction = $ga->getValue('postsave');

    // ***************** WERTE ERSETZEN UND POSTACTION AUSF�HREN
    foreach ($REX['VARIABLES'] as $obj)
    {
      $iaction = $obj->getACOutput($REX_ACTION, $iaction);
    }

    eval ('?>' . $iaction);

    if ($REX_ACTION['MSG'] != '')
      $message .= ' | ' . $REX_ACTION['MSG'];

    $ga->next();
  }
  return $message;
}

/**
 * �bersetzt den Modus $function in das dazugeh�rige Bitwort
 */
function rex_getActionModeBit($function)
{
  if ($function == 'edit')
    $modebit = '2'; // pre-action and edit
  elseif ($function == 'delete')
    $modebit = '4'; // pre-action and delete
  else
    $modebit = '1'; // pre-action and add

  return $modebit;
}

/**
 * Konvertiert einen Artikel zum Startartikel der eigenen Kategorie
 *
 * @param $aid  Artikel ID
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
      $neu_value = $neu->escape($neu->getValue($param));
      $alt_value = $alt->escape($alt->getValue($param));
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
 * Kopiert eine Kategorie in eine andere
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
  global $REX;

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
      $uc->setValue($value, $gc->escape($gc->getValue($value)));
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
      $colname = $cols->getValue("Field");
      if ($colname == "clang") $value = $to_clang;
      elseif ($colname == "re_article_slice_id") $value = $to_last_slice_id;
      elseif ($colname == "article_id") $value = $to_id;
      elseif ($colname == "createdate") $value = time();
      elseif ($colname == "updatedate") $value = time();
      elseif ($colname == "createuser") $value = $REX_USER->getValue("login");
      elseif ($colname == "updateuser") $value = $REX_USER->getValue("login");
      else
        $value = $gc->getValue($colname);

      if ($colname != "id")
        $ins->setValue($colname, $ins->escape($value));
    }
    $ins->insert();

    // id holen und als re setzen und weitermachen..
    rex_copyContent($from_id, $to_id, $from_clang, $to_clang, $gc->getValue("id"));
    return true;
  }

  rex_generateArticle($to_id);

  return true;
}

/**
 * Kopieren eines Artikels von einer Kategorie in eine andere
 *
 * @param $id          ArtikelId des zu kopierenden Artikels
 * @param $to_cat_id   KategorieId in die der Artikel kopiert werden soll
 */
function rex_copyArticle($id, $to_cat_id)
{
  global $REX;

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
        $art_sql->setValue('catname', $art_sql->escape($catname));
        $art_sql->setValue('catprior', 0);
        $art_sql->setValue('prior', 99999); // Artikel als letzten Artikel in die neue Kat einf�gen
        $art_sql->setValue('status', 0); // Kopierter Artikel offline setzen
        $art_sql->setValue('startpage', 0);
        $art_sql->addGlobalCreateFields();

        // schon gesetzte Felder nicht wieder �berschreiben
        $dont_copy = array ('id', 'pid', 're_id', 'catname', 'catprior', 'path', 'prior', 'status', 'createdate', 'createuser', 'startpage');

        foreach (array_diff($from_sql->getFieldnames(), $dont_copy) as $fld_name)
        {
          $art_sql->setValue($fld_name, $art_sql->escape($from_sql->getValue($fld_name)));
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

  // Generated der Kategorien neu erzeugen, da sich derin befindliche Artikel ge�ndert haben
  rex_generateArticle($to_cat_id,false);

  return $new_id;
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
  global $REX;

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
          $catname = $from_sql->getValue('name');
        }

        $art_sql = new rex_sql;
        //$art_sql->debugsql = 1;

        $art_sql->setTable($REX['TABLE_PREFIX'].'article');
        $art_sql->setValue('re_id', $re_id);
        $art_sql->setValue('path', $path);
        $art_sql->setValue('catname', $art_sql->escape($catname));
        // Artikel als letzten Artikel in die neue Kat einf�gen
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

  // Generated der Kategorien neu erzeugen, da sich derin befindliche Artikel ge�ndert haben
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
  }
  else
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
    }
    else
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
      }
      else
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

?>