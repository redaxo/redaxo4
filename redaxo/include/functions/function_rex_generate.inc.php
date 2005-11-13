<?php

/** 
 * Funktionensammlung für die generierung der Artikel/Templates/Kategorien/Metainfos.. etc. 
 * @package redaxo3 
 * @version $Id$ 
 */ 


// ----------------------------------------- Alles generieren

function rex_generateAll()
{

  global $REX, $I18N;

  // alles existiert schon
  // -> generiere templates
  // -> generiere article und listen
  // -> generiere file meta


  // ----------------------------------------------------------- generiere templates
  rex_deleteDir($REX['INCLUDE_PATH']."/generated/templates",0);
  // mkdir($REX['INCLUDE_PATH']."/generated/templates",$REX['FILEPERM']);
  $gt = new sql;
  $gt->setQuery("select * from rex_template");
  for ($i=0;$i<$gt->getRows();$i++)
  {
    $fp = fopen ($REX['INCLUDE_PATH']."/generated/templates/".$gt->getValue("rex_template.id").".template", "w");
    fputs($fp,$gt->getValue("rex_template.content"));
    fclose($fp);
    @chmod($REX['INCLUDE_PATH']."/generated/templates/".$gt->getValue("rex_template.id").".template",0777);
    $gt->next();
  }


  // ----------------------------------------------------------- generiere artikel
  rex_deleteDir($REX['INCLUDE_PATH']."/generated/articles",0);
  // mkdir($REX['INCLUDE_PATH']."/generated/articles",$REX['FILEPERM']);
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
    $content .= "\n\r\$REX['CLANG']['$id'] = \"$name\";";
    $lg->next();
  }
  $content .= "\n\r// --- /DYN";
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  $h = fopen($file,"r");
  $fcontent = fread($h,filesize($file));
  $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)",$content,$fcontent);
  fclose($h);
  $h = fopen($file,"w+");
  fwrite($h,$fcontent,strlen($fcontent));
  fclose($h);
  @chmod($file,0777);


  // ----------------------------------------------------------- generiere filemetas ...
  // **********************



  // ----------------------------------------------------------- message
  $MSG = $I18N->msg('articles_generated')." ".$I18N->msg('old_articles_deleted');

  // ----- EXTENSION POINT
  $MSG = rex_register_extension_point('ALL_GENERATED', $MSG);
  
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

  // --------------------------------------------------- generiere generated/articles/xx.article

  $CL = $REX['CLANG'];
  reset($CL);
  for ($i=0;$i<count($CL);$i++)
  {

    $clang = key($CL);
    $REX['RC'] = true; // keine Ausgabe als eval(CONTENT) sondern nur speichern in datei
    $CONT = new article;
    $CONT->setCLang($clang);
    $CONT->setArticleId($id);
    $article_content = "?>".$CONT->getArticle();

    // --------------------------------------------------- Artikelparameter speichern
    $article = "<?php\n".
          "\n\$REX['ART']['$id']['article_id']['$clang'] = \"$id\";".
          "\n\$REX['ART']['$id']['re_id']['$clang'] = \"".rex_addslashes($CONT->getValue("re_id"))."\";".
          "\n\$REX['ART']['$id']['name']['$clang'] = \"".rex_addslashes($CONT->getValue("name"))."\";".
          "\n\$REX['ART']['$id']['catname']['$clang'] = \"".rex_addslashes($CONT->getValue("catname"))."\";".
          "\n\$REX['ART']['$id']['cattype']['$clang'] = \"".rex_addslashes($CONT->getValue("name"))."\";".
          "\n\$REX['ART']['$id']['alias']['$clang'] = \"".rex_addslashes($CONT->getValue("name"))."\";".
          "\n\$REX['ART']['$id']['description']['$clang'] = \"".rex_addslashes($CONT->getValue("description"))."\";".
          "\n\$REX['ART']['$id']['attribute']['$clang'] = \"".rex_addslashes($CONT->getValue("attribute"))."\";".
          "\n\$REX['ART']['$id']['file']['$clang'] = \"".rex_addslashes($CONT->getValue("file"))."\";".
          "\n\$REX['ART']['$id']['type_id']['$clang'] = \"".rex_addslashes($CONT->getValue("type_id"))."\";".
          "\n\$REX['ART']['$id']['teaser']['$clang'] = \"".rex_addslashes($CONT->getValue("teaser"))."\";".
          "\n\$REX['ART']['$id']['startpage']['$clang'] = \"".rex_addslashes($CONT->getValue("startpage"))."\";".
          "\n\$REX['ART']['$id']['prior']['$clang'] = \"".rex_addslashes($CONT->getValue("prior"))."\";".
          "\n\$REX['ART']['$id']['path']['$clang'] = \"".rex_addslashes($CONT->getValue("path"))."\";".
          "\n\$REX['ART']['$id']['status']['$clang'] = \"".rex_addslashes($CONT->getValue("status"))."\";".
          "\n\$REX['ART']['$id']['online_from']['$clang'] = \"".rex_addslashes($CONT->getValue("online_from"))."\";".
          "\n\$REX['ART']['$id']['online_to']['$clang'] = \"".rex_addslashes($CONT->getValue("online_to"))."\";".
          "\n\$REX['ART']['$id']['createdate']['$clang'] = \"".rex_addslashes($CONT->getValue("createdate"))."\";".
          "\n\$REX['ART']['$id']['updatedate']['$clang'] = \"".rex_addslashes($CONT->getValue("updatedate"))."\";".
          "\n\$REX['ART']['$id']['keywords']['$clang'] = \"".rex_addslashes($CONT->getValue("keywords"))."\";".
          "\n\$REX['ART']['$id']['template_id']['$clang'] = \"".rex_addslashes($CONT->getValue("template_id"))."\";".
          "\n\$REX['ART']['$id']['createuser']['$clang'] = \"".rex_addslashes($CONT->getValue("createuser"))."\";".
          "\n\$REX['ART']['$id']['updateuser']['$clang'] = \"".rex_addslashes($CONT->getValue("updateuser"))."\";".
          "\n\$REX['ART']['$id']['last_update_stamp']['$clang'] = \"".time()."\";".
          "\n?>";
    if ($fp = @fopen ($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.article", "w"))
    {
      fputs($fp,$article);
      fclose($fp);
      @chmod($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.article",0777);
    }else
    {
      $MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
    }


    // --------------------------------------------------- Artikelcontent speichern
    if ($fp = @fopen ($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.content", "w"))
    {
      fputs($fp,$article_content);
      fclose($fp);
      @chmod($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.content",0777);
    }else
    {
      $MSG = $I18N->msg('article_could_not_be_generated')." ".$I18N->msg('check_rights_in_directory').$REX['INCLUDE_PATH']."/generated/articles/";
    }
    if (isset($MSG) and $MSG != "") echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=warning>$MSG</td></tr></table>";
    $REX['RC'] = false;


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

  if ($id == $REX['STARTARTIKEL_ID']) {
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

    $CL = $REX['CLANG'];
    reset($CL);
    for ($i=0;$i<count($CL);$i++)
    {
      $clang = key($CL);
      @unlink($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.article");
      @unlink($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.content");
      @unlink($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.alist");
      @unlink($REX['INCLUDE_PATH']."/generated/articles/$id.$clang.clist");
      $ART->query("delete from rex_article where id='$id'");
      $ART->query("delete from rex_article_slice where article_id='$id'");
      next($CL);
    }


    // --------------------------------------------------- Listen generieren
    rex_generateLists($re_id);


    return $I18N->msg('category_deleted').' '.$I18N->msg('article_deleted');

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

  $CL = $REX['CLANG'];
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
      $content .= "\$REX['RE_ID']['$re_id']['$i'] = \"".$GC->getValue("id")."\";\n";
      $GC->next();
    }
    $content .= "\n?>";
    $fp = fopen ($REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.alist", "w");
    fputs($fp,$content);
    fclose($fp);
    @chmod($REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.alist",0777);

    // --------------------------------------- CAT LIST

    $GC = new sql;
    $GC->setQuery("select * from rex_article where re_id=$re_id and clang=$clang and startpage=1 order by catprior,name");
    $content = "<?php\n";
    for ($i=0;$i<$GC->getRows();$i++)
    {
      $id = $GC->getValue("id");
      $content .= "\$REX['RE_CAT_ID']['$re_id']['$i'] = \"".$GC->getValue("id")."\";\n";
      $GC->next();
    }
    $content .= "\n?>";
    $fp = fopen ($REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.clist", "w");
    fputs($fp,$content);
    fclose($fp);
    @chmod($REX['INCLUDE_PATH']."/generated/articles/$re_id.$clang.clist",0777);

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
    $gr->setQuery("select * from rex_article where re_id='$re_id' and clang='$clang' and startpage=1 order by catprior,updatedate $addsql");
    for ($i=0;$i<$gr->getRows();$i++)
    {
      $ipid = $gr->getValue("pid");
      $iprior = $i+1;
      $gu->query("update rex_article set catprior=$iprior where pid='$ipid'");
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
    $gr->setQuery("select * from rex_article where clang='$clang' and ((startpage<>1 and re_id='$re_id') or (startpage=1 and id=$re_id))order by prior,updatedate $addsql");
    for ($i=0;$i<$gr->getRows();$i++)
    {
      // echo "<br>".$gr->getValue("pid")." ".$gr->getValue("id")." ".$gr->getValue("name");
      $ipid = $gr->getValue("pid");
      $iprior = $i+1;
      $gu->query("update rex_article set prior=$iprior where pid='$ipid'");
      $gr->next();
    }
    rex_generateLists($re_id);
  }
}

function rex_moveArticle($id,$to_cat_id,$from_cat_id)
{
 // TODO
}

function rex_copyArticle($id,$to_cat_id)
{
 // TODO
}

function rex_copyCategory($which_id,$to_cat)
{
 // TODO
}

function rex_copyContent($from_id,$to_id,$from_clang = 0,$to_clang = 0,$from_re_sliceid = 0)
{
	global $REX,$REX_USER;
	
	if ($from_id == $to_id && $from_clang == $to_clang) return false;
	
	$gc = new sql;
	$gc->setQuery("select * from rex_article_slice where re_article_slice_id='$from_re_sliceid' and article_id='$from_id' and clang='$from_clang'");
	
	if ($gc->getRows()==1)
	{
		
		// letzt slice_id des ziels holen ..
		$glid = new sql;
		$glid->setQuery("select 
			r1.id, r1.re_article_slice_id
		from 
			rex_article_slice as r1 
		left join 
			rex_article_slice as r2 on r1.id=r2.re_article_slice_id 
		where 
			r1.article_id=$to_id 
			and r1.clang=$to_clang 
			and r2.id is NULL;");
		if ($glid->getRows() == 1) $to_last_slice_id = $glid->getValue("r1.id");
		else $to_last_slice_id = 0;

		$ins = new sql;
		// $ins->debugsql = 1;
		$ins->setTable("rex_article_slice");
		
		$cols = new sql;
		// $cols->debugsql = 1;
		$cols->setquery("SHOW COLUMNS FROM rex_article_slice");
		for($j=0;$j<$cols->rows;$j++,$cols->next())
		{
			$colname = $cols->getvalue("Field");
			if ($colname == "clang") $value = $to_clang;
			elseif ($colname == "re_article_slice_id") $value = $to_last_slice_id;
			elseif ($colname == "article_id") $value = $to_id;
			elseif ($colname == "createdate") $value = time();
			elseif ($colname == "updatedate") $value = time();
			elseif ($colname == "createuser") $value = $REX_USER->getValue("login");
			elseif ($colname == "updateuser") $value = $REX_USER->getValue("login");
			else $value = addslashes($gc->getValue("$colname"));

			if ($colname != "id") $ins->setValue($colname,$value);
		}
		$ins->insert();

		// id holen und als re setzen und weitermachen..
		rex_copyContent($from_id,$to_id,$from_clang,$to_clang,$gc->getValue("id"));
		return true;
	}

	rex_generateArticle($to_id);
		
	return true;
}


// ----------------------------------------- CTYPE




// ----------------------------------------- FILE

function rex_deleteDir($file,$what = 0)
{
  if (file_exists($file))
  {
    // @chmod($file,0775);
    if (is_dir($file))
    {
      $handle = opendir($file);
      while($filename = readdir($handle))
      {
        if ( $filename == '_readme.txt' || $filename == '.cvsignore') return;
        
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

}




// ----------------------------------------- CLANG

function rex_deleteCLang($id)
{
  global $REX;
  
  if ($id==0) return "";
  
  $content = "// --- DYN\n\r";
  
  reset($REX['CLANG']);
  for ($i=0;$i<count($REX['CLANG']);$i++)
  {
    $cur = key($REX['CLANG']);
    $val = current($REX['CLANG']);
    if ($cur != $id) $content .= "\n\r\$REX['CLANG']['$cur'] = \"$val\";";
    next($REX['CLANG']);
  }
  $content .= "\n\r// --- /DYN";
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  
  $h = fopen($file,"r");
  $fcontent = fread($h,filesize($file));
  $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)",$content,$fcontent);
  fclose($h);
  $h = fopen($file,"w+");
  fwrite($h,$fcontent,strlen($fcontent));
  fclose($h);
  @chmod($file,0777);
  
  $del = new sql();
  $del->setQuery("select * from rex_article where clang='$id'");
  for($i=0;$i<$del->getRows();$i++)
  {
    $aid = $del->getValue("id");
    // rex_deleteArticle($del->getValue("id"),$id,0);
    @unlink($REX['INCLUDE_PATH']."/generated/articles/$aid.$id.article");
    @unlink($REX['INCLUDE_PATH']."/generated/articles/$aid.$id.content");
    @unlink($REX['INCLUDE_PATH']."/generated/articles/$aid.$id.alist");
    @unlink($REX['INCLUDE_PATH']."/generated/articles/$aid.$id.clist");
    $del->next();
  }
  
  $del->query("delete from rex_article where clang='$id'");
  $del->query("delete from rex_article_slice where clang='$id'");

  unset($REX['CLANG'][$id]);
  $del = new sql();
  $del->query("delete from rex_clang where id='$id'");
  
  rex_generateAll();
}

function rex_addCLang($id,$name)
{
  global $REX;
  $REX['CLANG'][$id] = $name;
  $content = "// --- DYN\n\r";
  reset($REX['CLANG']);
  for ($i=0;$i<count($REX['CLANG']);$i++)
  {
    $cur = key($REX['CLANG']);
    $val = current($REX['CLANG']);

    $content .= "\n\r\$REX['CLANG']['$cur'] = \"$val\";";
    next($REX['CLANG']);
  }
  $content .= "\n\r// --- /DYN";

  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  $h = fopen($file,"r");
  $fcontent = fread($h,filesize($file));
  $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)",$content,$fcontent);
  fclose($h);

  $h = fopen($file,"w+");
  fwrite($h,$fcontent,strlen($fcontent));
  fclose($h);
  @chmod($file,0777);

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
            else if ($value == "status") $adda->setValue("status", "0"); // Alle neuen Artikel offline 
      else $adda->setValue($value,rex_addslashes($add->getValue("$value")));
      //  createuser
      //  updateuser
    }
    $adda->insert();

    $add->next();
  }
  $add = new sql();
  $add->query("insert into rex_clang set id='$id',name='$name'");

  rex_generateAll();
}

function rex_editCLang($id,$name)
{
  global $REX;

  $REX['CLANG'][$id] = $name;
  $file = $REX['INCLUDE_PATH']."/clang.inc.php";
  $h = fopen($file,"r");
  $cont = fread($h,filesize($file));
  $cont = ereg_replace("(REX\['CLANG'\]\['$id\'].?\=.?)[^;]*","\\1\"".($name)."\"",$cont);
  fclose($h);
  $h = fopen($REX['INCLUDE_PATH']."/clang.inc.php","w+");
  fwrite($h,$cont,strlen($cont));
  fclose($h);
  @chmod($REX['INCLUDE_PATH']."/clang.inc.php",0777);
  $edit = new sql;
  $edit->query("update rex_clang set name='$name' where id='$id'");
}


// ----------------------------------------- generate helpers

function rex_addslashes($string)
{

  $string = str_replace("\\","\\\\",$string);
  $string = str_replace("\"","\\\"",$string);
  
  return $string;
  
}



?>