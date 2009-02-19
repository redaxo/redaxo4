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

  // ----------------------------------------------------------- generated löschen
  rex_deleteDir($REX['INCLUDE_PATH'].'/generated', FALSE);
  
  // ----------------------------------------------------------- generiere clang
  $lg = new rex_sql();
  $lg->setQuery("select * from ".$REX['TABLE_PREFIX']."clang order by id");
  
  $REX['CLANG'] = array();
  while($lg->hasNext())
  {
    $REX['CLANG'][$lg->getValue("id")] = $lg->getValue("name"); 
    $lg->next();
  }
  
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  rex_replace_dynamic_contents($file, "\$REX['CLANG'] = ". var_export($REX['CLANG'], true) .";\n");

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
    if($clang !== null && $clang != $_clang)
      continue;
      
    @unlink($REX['INCLUDE_PATH'].'/generated/articles/'. $id .'.'. $_clang .'.article');
    @unlink($REX['INCLUDE_PATH'].'/generated/articles/'. $id .'.'. $_clang .'.content');
    @unlink($REX['INCLUDE_PATH'].'/generated/articles/'. $id .'.'. $_clang .'.alist');
    @unlink($REX['INCLUDE_PATH'].'/generated/articles/'. $id .'.'. $_clang .'.clist');
  }
}


function rex_generateArticleMeta($article_id, $clang = null)
{
  global $REX, $I18N;
  
  foreach($REX['CLANG'] as $_clang => $clang_name)
  {
    if($clang !== null && $clang != $_clang)
      continue;
    
    $CONT = new rex_article;
    $CONT->setCLang($_clang);
    $CONT->getContentAsQuery(); // Content aus Datenbank holen, no cache
    $CONT->setEval(FALSE); // Content nicht ausfŸhren, damit in Cachedatei gespeichert werden kann
    if (!$CONT->setArticleId($article_id)) return false;

    // --------------------------------------------------- Artikelparameter speichern
    $params = array(
      'article_id' => $article_id,
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
      $content .='$REX[\'ART\']['. $article_id .'][\''. $name .'\']['. $_clang .'] = \''. rex_addslashes($value,'\\\'') .'\';'."\n";
    }
    $content .= '?>';
    
    $article_file = $REX['INCLUDE_PATH']."/generated/articles/$article_id.$_clang.article";
    if (rex_put_file_contents($article_file, $content) === false)
    {
      return $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
    }
    
    // damit die aktuellen änderungen sofort wirksam werden, einbinden!
    require ($article_file);
  }
  
  return true;
}

function rex_generateArticleContent($article_id, $clang = null)
{
  global $REX, $I18N;
  
  foreach($REX['CLANG'] as $_clang => $clang_name)
  {
    if($clang !== null && $clang != $_clang)
      continue;
      
    $CONT = new rex_article;
    $CONT->setCLang($_clang);
    $CONT->getContentAsQuery(); // Content aus Datenbank holen, no cache
    $CONT->setEval(FALSE); // Content nicht ausführen, damit in Cachedatei gespeichert werden kann
    if (!$CONT->setArticleId($article_id)) return false;
  
    // --------------------------------------------------- Artikelcontent speichern
    $article_content_file = $REX['INCLUDE_PATH']."/generated/articles/$article_id.$_clang.content";
    $article_content = "?>".$CONT->getArticle();
  
    // ----- EXTENSION POINT
    $article_content = rex_register_extension_point('GENERATE_FILTER', $article_content,
      array (
        'id' => $article_id,
        'clang' => $_clang,
        'article' => $CONT
      )
    );
  
    if (rex_put_file_contents($article_content_file, $article_content) === false)
    {
      return $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
    }
  }
  
  return true;
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

  foreach($REX['CLANG'] as $clang => $clang_name)
  {
    $CONT = new rex_article;
    $CONT->setCLang($clang);
    $CONT->getContentAsQuery(); // Content aus Datenbank holen, no cache
    $CONT->setEval(FALSE); // Content nicht ausführen, damit in Cachedatei gespeichert werden kann
    if (!$CONT->setArticleId($id)) return false;
      
    // ----------------------- generiere generated/articles/xx.article
    $MSG = rex_generateArticleMeta($id, $clang);
    if($MSG === false) return false;
    
    if($refreshall)
    {
      // ----------------------- generiere generated/articles/xx.content
      $MSG = rex_generateArticleContent($id, $clang);
      if($MSG === false) return false;
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
function rex_deleteArticle($id)
{
  global $I18N;

  $result = _rex_deleteArticle($id);

  return $result === true ? $I18N->msg('category_deleted').' '.$I18N->msg('article_deleted') : $result;
}

function _rex_deleteArticle($id)
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
        $allowDelete = _rex_deleteArticle($id);
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
function rex_generateLists($re_id, $clang = null)
{
  global $REX, $I18N;

  // generiere listen
  //
  //
  // -> je nach clang
  // --> artikel listen
  // --> catgorie listen
  //

  foreach($REX['CLANG'] as $_clang => $clang_name)
  {
    if($clang !== null && $clang != $_clang)
      continue;
        
    // --------------------------------------- ARTICLE LIST

    $GC = new rex_sql;
    // $GC->debugsql = 1;
    $GC->setQuery("select * from ".$REX['TABLE_PREFIX']."article where (re_id=$re_id and clang=$_clang and startpage=0) OR (id=$re_id and clang=$_clang and startpage=1) order by prior,name");
    $content = "<?php\n";
    for ($i = 0; $i < $GC->getRows(); $i ++)
    {
      $id = $GC->getValue("id");
      $content .= "\$REX['RE_ID']['$re_id']['$i'] = \"".$GC->getValue("id")."\";\n";
      $GC->next();
    }
    $content .= "\n?>";

    $article_list_file = $REX['INCLUDE_PATH']."/generated/articles/$re_id.$_clang.alist";
    if (rex_put_file_contents($article_list_file, $content) === false)
    {
      return $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
    }

    // --------------------------------------- CAT LIST

    $GC = new rex_sql;
    $GC->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id=$re_id and clang=$_clang and startpage=1 order by catprior,name");
    $content = "<?php\n";
    for ($i = 0; $i < $GC->getRows(); $i ++)
    {
      $id = $GC->getValue("id");
      $content .= "\$REX['RE_CAT_ID']['$re_id']['$i'] = \"".$GC->getValue("id")."\";\n";
      $GC->next();
    }
    $content .= "\n?>";

    $article_categories_file = $REX['INCLUDE_PATH']."/generated/articles/$re_id.$_clang.clist";
    if (rex_put_file_contents($article_categories_file, $content) === false)
    {
      return $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
    }
  }
  
  return true;
}

/**
 * Löscht einen Ordner/Datei mit Unterordnern
 *
 * @param $file Zu löschender Ordner/Datei
 * @param $delete_folders Ordner auch löschen? false => nein, true => ja
 */
function rex_deleteDir($file, $delete_folders = false)
{
  $state = true;
  
  $file = rtrim($file, DIRECTORY_SEPARATOR);

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

        if (!rex_deleteDir($file.DIRECTORY_SEPARATOR.$filename, $delete_folders))
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

  if ($clang == 0 || !isset($REX['CLANG'][$clang]))
    return false;

  $clangName = $REX['CLANG'][$clang];
  unset ($REX['CLANG'][$clang]);

  $del = new rex_sql();
  $del->setQuery("delete from ".$REX['TABLE_PREFIX']."article where clang='$clang'");
  $del->setQuery("delete from ".$REX['TABLE_PREFIX']."article_slice where clang='$clang'");
  $del->setQuery("delete from ".$REX['TABLE_PREFIX']."clang where id='$clang'");
  
  // ----- EXTENSION POINT
  rex_register_extension_point('CLANG_DELETED','',
    array (
      'id' => $clang,
      'name' => $clangName,
    )
  );

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
  
  if(isset($REX['CLANG'][$id])) return false;

  $REX['CLANG'][$id] = $name;
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  rex_replace_dynamic_contents($file, "\$REX['CLANG'] = ". var_export($REX['CLANG'], true) .";\n");
  
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
        $adda->setValue($value, $add->escape($add->getValue($value)));
    }

    $adda->insert();
    $add->next();
  }

  $add = new rex_sql();
  $add->setQuery("insert into ".$REX['TABLE_PREFIX']."clang set id='$id',name='$name'");

  // ----- EXTENSION POINT
  rex_register_extension_point('CLANG_ADDED','',array ('id' => $id, 'name' => $name));
  
  return true;
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
  
  if(!isset($REX['CLANG'][$id])) return false;

  $REX['CLANG'][$id] = $name;
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  rex_replace_dynamic_contents($file, "\$REX['CLANG'] = ". var_export($REX['CLANG'], true) .";\n");

  $edit = new rex_sql;
  $edit->setQuery("update ".$REX['TABLE_PREFIX']."clang set name='$name' where id='$id'");

  // ----- EXTENSION POINT
  rex_register_extension_point('CLANG_UPDATED','',array ('id' => $id, 'name' => $name));
  
  return true;
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
  if(rex_replace_dynamic_contents($file, $content) === false)
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
    if(rex_put_file_contents($templateFile, $content) !== false)
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