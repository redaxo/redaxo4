<?php

/** 
 * Verwaltung der Inhalte. EditierModul / Metadaten ... 
 * @package redaxo3 
 * @version $Id$ 
 */ 


/*
// TODOS:
// - alles vereinfachen
// - <? ?> $ Problematik bei REX_ACTION
*/

unset($REX_ACTION);

$article = new sql;
$article->setQuery("select * from rex_article where id='$article_id' and clang=$clang");

if ($article->getRows() == 1)
{

  // ----- ctype wird in der functions überprüft.

  // ----- Artikel wurde gefunden - Kategorie holen
  if ($article->getValue("startpage") == 1) $category_id = $article->getValue("id");
  else $category_id = $article->getValue("re_id");

  // ----- category pfad und rechte
  include $REX['INCLUDE_PATH']."/functions/function_rex_category.inc.php";
  // $KATout kommt aus dem include
  // $KATPERM
  if ($page == "content" && $article_id > 0)
  {
    if ($article->getValue("startpage")==1) $KATout .= " &nbsp;&nbsp;&nbsp;".$I18N->msg("start_article")." : ";
    else $KATout .= " &nbsp;&nbsp;&nbsp;".$I18N->msg("article")." : ";
    $KATout .= "<a href=index.php?page=content&article_id=$article_id&mode=edit&clang=$clang>".str_replace(" ","&nbsp;",$article->getValue("name"))."</a>";
    // $KATout .= " [$article_id]";
  }

  // ----- Titel anzeigen
  rex_title("Artikel",$KATout);

  // ----- Sprachenblock
  $sprachen_add = "&category_id=$category_id&article_id=$article_id";
  include $REX['INCLUDE_PATH']."/functions/function_rex_languages.inc.php";

  if (isset($_REQUEST["mode"])) $mode = $_REQUEST["mode"];
  else $mode = "";


  // ----- mode defs
  if ($mode != "meta") $mode = "edit";

  // ----------------- HAT USER DIE RECHTE AN DIESEM ARTICLE ODER NICHT
  if ( !( $KATPERM || $REX_USER->isValueOf("rights","article[$article_id]") ) )
  {
    // ----- hat keine rechte an diesem artikel
      echo "<table border=1 cellpadding=6 cellspacing=0 width=770 bgcolor=#eeeeee><tr bgcolor='#eeeeee'><td class=warning><br><br>&nbsp;&nbsp;".$I18N->msg("no_rights_to_edit")."<br><br><br></td></tr></table>";

  }else
  {
    // ----- hat rechte an diesem artikel

    // ------------------------------------------ Slice add/edit/delete
    if ( isset ($function) and isset ($save) and
        (
          $function == "add" or 
          $function == "edit" or 
          $function == "delete" 
        ) and 
        $save == 1
      )
    {     
      
      // ----- check module

      $CM = new sql;
      if ($function == "edit" || $function == "delete")
      {
        // edit/ delete
        $CM->setQuery("select * from rex_article_slice left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id where rex_article_slice.id='$slice_id' and clang=$clang");
        if ($CM->getRows()==1) $module_id = $CM->getValue("rex_article_slice.modultyp_id");
      }else
      {
        // add
        $CM->setQuery("select * from rex_modultyp where id='$module_id'");
      }

      if ($CM->getRows()!=1)
      {
        // ------------- START: MODUL IST NICHT VORHANDEN
        $message = $I18N->msg('module_not_found');
        $slice_id = "";
        $function = "";
        $module_id = "";
        $save = "";
        // ------------- END: MODUL IST NICHT VORHANDEN

      }else
      {

        // ------------- MODUL IST VORHANDEN

        // ----- RECHTE AM MODUL ?
        if ( !($REX_USER->isValueOf("rights","admin[]") || 
            $REX_USER->isValueOf("rights","dev[]") || 
            $REX_USER->isValueOf("rights","module[$module_id]") || 
            $REX_USER->isValueOf("rights","module[0]"))
          )
        {
          // ----- RECHTE AM MODUL: NEIN
          $message = $I18N->msg('no_rights_to_this_function');
          $slice_id = "";
          $function = "";
          $module_id = "";
          $save = "";

        } else
        {
          // ----- RECHTE AM MODUL: JA
          $message = "";
          
          for ($i=1; $i<11; $i++)
          {
            // pruefe Vorhandensein der Variablen
            if (!isset($VALUE[$i])) $VALUE[$i] = '';
            if (!isset($LINK[$i])) $LINK[$i] = '';
            
            $REX_ACTION['VALUE'][$i] = $VALUE[$i];
            $REX_ACTION['LINK'][$i] = $LINK[$i];
            $FILENAME = "REX_MEDIA_$i";
            if (!isset($$FILENAME)) $$FILENAME = '';
            $REX_ACTION['FILE'][$i] = $$FILENAME;
            $LINKLIST = "REX_LINKLIST_$i";
            if (!isset($$LINKLIST)) $$LINKLIST = '';
            $REX_ACTION['LINKLIST'][$i] = $$LINKLIST;
            $MEDIALIST = "REX_MEDIALIST_$i";
            if (!isset($$MEDIALIST)) $$MEDIALIST = '';
            $REX_ACTION['MEDIALIST'][$i] = $$MEDIALIST;
          }

          if (!isset($INPUT_HTML)) $INPUT_HTML = '';
          if (!isset($INPUT_PHP)) $INPUT_PHP = '';
          $REX_ACTION['HTML'] = $INPUT_HTML;
          $REX_ACTION['PHP'] = $INPUT_PHP;

          // ----- PRE ACTION [ADD/EDIT/DELETE]

          $REX_ACTION['SAVE'] = true;

          if ($function == "edit") $addsql = " and rex_action.prepost=0 and rex_action.sedit=1"; // pre-action and edit
          elseif($function == "delete") $addsql = " and rex_action.prepost=0 and rex_action.sdelete=1"; // pre-action and delete
          else $addsql = " and rex_action.prepost=0 and rex_action.sadd=1"; // pre-action and add

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
            $iaction = str_replace("REX_PHP",$REX_ACTION['PHP'],$iaction);
            $iaction = str_replace("REX_HTML",$REX_ACTION['HTML'],$iaction);

            for ($j=1;$j<11;$j++)
            {
              $iaction = str_replace("REX_VALUE[$j]",$REX_ACTION['VALUE'][$j],$iaction);
              $iaction = str_replace("REX_LINK[$j]",$REX_ACTION['LINK'][$j],$iaction);
              $iaction = str_replace("REX_FILE[$j]",$REX_ACTION['FILE'][$j],$iaction);
              $iaction = str_replace("REX_LINKLIST[$j]",$REX_ACTION['LINKLIST'][$j],$iaction);
              $iaction = str_replace("REX_MEDIALIST[$j]",$REX_ACTION['MEDIALIST'][$j],$iaction);
            }

            eval("?>".$iaction);
            if (isset($REX_ACTION['MSG']) and $REX_ACTION['MSG'] != "" ) $message .= $REX_ACTION['MSG']." | ";
            $ga->next();
          }

          // ----- / PRE ACTION


          if (!$REX_ACTION['SAVE'])
          {
            // ----- DOWN SAVE/UPDATE SLICE
          
            if ($REX_ACTION['MSG']!="") $message = $REX_ACTION['MSG'];
            elseif ($function == "delete") $message = "Block konnte nicht gelöscht werden.";
            else $message = "Eingaben wurde nicht übernommen.";
          
          }else
          {
  
            // ----- SAVE/UPDATE SLICE
            
            if ($function == "add" || $function == "edit")
            {
              
              $newsql = new sql;
              // $newsql->debugsql = 1;
              $newsql->setTable("rex_article_slice");
    
              if ($function == "edit")
              {
                // edit
                $newsql->where("id='$slice_id'");
              }elseif($function == "add")
              {
                // add
                $newsql->setValue("re_article_slice_id",$slice_id);
                $newsql->setValue("article_id",$article_id);
                $newsql->setValue("modultyp_id",$module_id);
                $newsql->setValue("clang",$clang);
                $newsql->setValue("ctype",$ctype);
              }
              
              for ($i=1;$i<11;$i++)
              {
                $newsql->setValue("value$i",$REX_ACTION['VALUE'][$i]);
              }
    
              $newsql->setValue("html",$REX_ACTION['HTML']);
              $newsql->setValue("php",$REX_ACTION['PHP']);
    
              // ---------------------------- REX_MEDIA
              for ($fi=1;$fi<11;$fi++)
              {
                
                // ----- link
                if ($REX_ACTION['LINK'][$fi]=="delete link" or $REX_ACTION['LINK'][$fi]=="")
                {
                  $newsql->setValue("link$fi","");
                }else
                {
                  $newsql->setValue("link$fi",$REX_ACTION['LINK'][$fi]);
                }

    
                // ----- file
                $FILENAME = $REX_ACTION['FILE'][$fi];
                if ($FILENAME == "")
                {
                  $newsql->setValue("file".$fi,"");
                }else
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
                
                // ----- linklist
                $newsql->setValue("linklist$fi",$REX_ACTION['LINKLIST'][$fi]);

                // ----- medialist
                $newsql->setValue("filelist$fi",$REX_ACTION['MEDIALIST'][$fi]);
              }
    
              $newsql->setValue("updatedate",time());
              $newsql->setValue("updateuser",$REX_USER->getValue("login"));
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
            }else
            {
              // make delete
              $re_id  = $CM->getValue("rex_article_slice.re_article_slice_id");
              $newsql = new sql;
              $newsql->setQuery("select * from rex_article_slice where re_article_slice_id='$slice_id'");
              if ($newsql->getRows()>0)
              {
                $newsql->query("update rex_article_slice set re_article_slice_id='$re_id' where id='".$newsql->getValue("id")."'");
              }
              $newsql->query("delete from rex_article_slice where id='$slice_id'");
              $message = $I18N->msg('block_deleted');
            } 
            // ----- / SAVE SLICE
  
  
            // ----- POST ACTION [ADD AND EDIT]
            if ($function == "edit") $addsql = " and rex_action.prepost=1 and rex_action.sedit=1"; // post-action and edit
            elseif ($function == "delete") $addsql = " and rex_action.prepost=1 and rex_action.sdelete=1"; // post-action and delete
            else $addsql = " and rex_action.prepost=1 and rex_action.sadd=1"; // post-action and add

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
              $iaction = str_replace("REX_PHP",$REX_ACTION['PHP'],$iaction);
              $iaction = str_replace("REX_HTML",$REX_ACTION['HTML'],$iaction);
              for ($j=1;$j<11;$j++)
              {
                $iaction = str_replace("REX_VALUE[$j]",$REX_ACTION['VALUE'][$j],$iaction);
                $iaction = str_replace("REX_LINK[$j]",$REX_ACTION['LINK'][$j],$iaction);
                $iaction = str_replace("REX_LINKLIST[$j]",$REX_ACTION['LINKLIST'][$j],$iaction);
                $iaction = str_replace("REX_FILE[$j]",$REX_ACTION['FILE'][$j],$iaction);
                $iaction = str_replace("REX_MEDIALIST[$j]",$REX_ACTION['MEDIALIST'][$j],$iaction);
              }
              eval("?>".$iaction);
              if (isset($REX_ACTION['MSG']) and $REX_ACTION['MSG'] != "") $message .= " | ".$REX_ACTION['MSG'];
              $REX_ACTION['MSG'] = "";
              $ga->next();
            }
            // ----- / POST ACTION
            if (!(isset($update) and $update == 1)){
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

          }
        }
      }
    }
    // ------------------------------------------ END: Slice add/edit/delete


    // ------------------------------------------ START: Slice move up/down
    if (isset($function) and $function == "moveup" || $function == "movedown")
    {
      if ($REX_USER->isValueOf("rights","moveSlice[]"))
      {
        // modul und rechte vorhanden ?
        
        $CM = new sql;
        $CM->setQuery("select * from rex_article_slice left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id where rex_article_slice.id='$slice_id' and clang=$clang");
        if ($CM->getRows()!=1)
        {
          // ------------- START: MODUL IST NICHT VORHANDEN
          $message = $I18N->msg('module_not_found');
          $slice_id = "";
          $function = "";
          $module_id = "";
          $save = "";
          // ------------- END: MODUL IST NICHT VORHANDEN

        }else
        {

          // ------------- MODUL IST VORHANDEN
          $module_id = $CM->getValue("rex_article_slice.modultyp_id");

          // ----- RECHTE AM MODUL ?
          if ( $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]") || $REX_USER->isValueOf("rights","module[$module_id]") || $REX_USER->isValueOf("rights","module[0]") )
          {
            // rechte sind vorhanden
            // ctype beachten
            // verschieben / vertauschen
            // article regenerieren.
            
            $slice_id = $CM->getValue("rex_article_slice.id");
            $slice_article_id = $CM->getValue("article_id");
            $re_slice_id = $CM->getValue("rex_article_slice.re_article_slice_id");
            $slice_ctype = $CM->getValue("rex_article_slice.ctype");

            $gs = new sql;
            // $gs->debugsql = 1;
            $gs->setQuery("select * from rex_article_slice where article_id='$slice_article_id'");
            for ($i=0;$i<$gs->getRows();$i++)
            {
              $SID[$gs->getValue("re_article_slice_id")] = $gs->getValue("id");
              $SREID[$gs->getValue("id")] = $gs->getValue("re_article_slice_id");
              $SCTYPE[$gs->getValue("id")] = $gs->getValue("ctype");
              $gs->next();  
            }


            $message = $I18N->msg('slice_moved_error');
            // ------ moveup
            if ($function == "moveup")
            {
              if ($SREID[$slice_id] > 0)
              {
                if ($SCTYPE[$SREID[$slice_id]] == $slice_ctype) 
                {
                  $gs->query("update rex_article_slice set re_article_slice_id='".$SREID[$SREID[$slice_id]]."' where id='".$slice_id."'");
                  $gs->query("update rex_article_slice set re_article_slice_id='".$slice_id."' where id='".$SREID[$slice_id]."'");
                  if ($SID[$slice_id]>0) $gs->query("update rex_article_slice set re_article_slice_id='".$SREID[$slice_id]."' where id='".$SID[$slice_id]."'");
                  $message = $I18N->msg('slice_moved');
                  rex_generateArticle($slice_article_id);
                }
              }
            }

            // ------ movedown
            if ($function == "movedown")
            {
              if ($SID[$slice_id] > 0)
              {
                if ($SCTYPE[$SID[$slice_id]] == $slice_ctype) 
                {
                  $gs->query("update rex_article_slice set re_article_slice_id='".$SREID[$slice_id]."' where id='".$SID[$slice_id]."'");
                  $gs->query("update rex_article_slice set re_article_slice_id='".$SID[$slice_id]."' where id='".$slice_id."'");
                  if ($SID[$SID[$slice_id]]>0) $gs->query("update rex_article_slice set re_article_slice_id='".$slice_id."' where id='".$SID[$SID[$slice_id]]."'");
                  $message = $I18N->msg('slice_moved');
                  rex_generateArticle($slice_article_id);
                }
              }
            }
          }else
          {
            $message = $I18N->msg('no_rights_to_this_function');
          }
        } 
      }else
      {
        $message = $I18N->msg('no_rights_to_this_function');
      }
    }
    // ------------------------------------------ END: Slice move up/down

  // ------------------------------------------ START: COPY LANG CONTENT
  if (isset($function) and $function == "copycontent")
  {
    if($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","copyContent[]"))
        {
      if (rex_copyContent($article_id,$article_id,$clang_a,$clang_b))
      {
        $message = $I18N->msg('content_contentcopy');
      }else
      {
        $message = $I18N->msg('content_errorcopy');
      }
    }
  }
  // ------------------------------------------ END: COPY LANG CONTENT
  

  // ------------------------------------------ START: MOVE ARTICLE
  if (isset($function) and $function == "movearticle")
  {
    if($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","moveArticle[]"))
    {
      if (rex_moveArticle($article_id, $category_id_old, $category_id_new))
      {
        $message = $I18N->msg('content_articlemoved');
      }else
      {
        $message = $I18N->msg('content_errormovearticle');
      }
    }
  }
  // ------------------------------------------ END: MOVE ARTICLE
  

  // ------------------------------------------ START: COPY ARTICLE
  if (isset($function) and $function == "copyarticle")
  {
    if($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","copyArticle[]"))
    {
      if (rex_copyArticle($article_id, $category_copy_id_old, $category_copy_id_new))
      {
        $message = $I18N->msg('content_articlecopied');
      }else
      {
        $message = $I18N->msg('content_errorcopyarticle');
      }
    }
  }
  // ------------------------------------------ END: COPY ARTICLE
  

  // ------------------------------------------ START: MOVE CATEGORY
  if (isset($function) and $function == "movecategory")
  {
    if($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","moveCategory[]"))
    {
      $category_id_new = (int) $category_id_new;
      if (rex_moveCategory($category_id, $category_id_new))
      {
        $message = $I18N->msg('content_category_moved');
        // ausgabe stoppen
        // header neu setzen
        // in gemoved category springen..
        
        ob_end_clean();

		header("Location: index.php?page=content&article_id=".$category_id."&mode=meta&clang=".$clang."&ctype=".$ctype);
		exit;
        
      }else
      {
        $message = $I18N->msg('content_error_movecategory');
      }
    }
  }
  // ------------------------------------------ END: MOVE CATEGORY



    // ------------------------------------------ START: CONTENT HEAD MENUE
    reset($REX['CTYPE']);
    $tadd = "";
    if (count($REX['CTYPE'])>1)
    {
      $tadd = "<b>Typen:</b> | ";
      while( list($key,$val) = each($REX['CTYPE']) )
      {
        if ($key==$ctype) $tadd .= "$val | ";
        else $tadd .= "<a href=index.php?page=content&clang=$clang&ctype=$key&category_id=$category_id&article_id=$article_id>$val</a> | ";
      }
      $tadd .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }
    $menu = $tadd." <a href=../index.php?article_id=$article_id&clang=$clang&ctype=$ctype class=blue target=_blank>".$I18N->msg('show')."</a>";
    
    $edit_mode_css_class = 'blue';    
    $meta_css_class = 'black';
    if ($mode=="edit") 
    {
      $edit_mode_css_class = 'black';    
      $meta_css_class = 'blue';    
    }
    $menu.= " | <a href=index.php?page=content&article_id=$article_id&mode=edit&clang=$clang&ctype=$ctype class=$edit_mode_css_class>".$I18N->msg('edit_mode')."</a> | <a href=index.php?page=content&article_id=$article_id&mode=meta&clang=$clang&ctype=$ctype class=$meta_css_class>".$I18N->msg('metadata')."</a>";
    // ------------------------------------------ END: CONTENT HEAD MENUE
    

    // ------------------------------------------ START: AUSGABE
    echo "  <table border=0 cellpadding=0 cellspacing=1 width=770>
        <tr>
          <td align=center class=grey width=30><img src=pics/document.gif width=16 height=16 border=0 vspace=5 hspace=12></td>
          <td align=left class=grey>&nbsp;$menu</td>
          <td align=left class=grey width=153><img src=pics/leer.gif width=153 height=20></td>
        </tr>";
    // ------------------------------------------ WARNING       
    if (isset($message) and $message != ""){ echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16 vspace=4></td><td class=warning>&nbsp;&nbsp;$message</td><td class=lgrey>&nbsp;</td></tr>"; }

    echo "  <tr>
          <td class=lgrey>&nbsp;</td>
          <td valign=top class=lblue>";


    if ($mode == "edit")
    {
      if (!isset($slice_id)) $slice_id = '';
      if (!isset($function)) $function = '';
      
      // ------------------------------------------ START: MODULE EDITIEREN/ADDEN ETC.
      $CONT = new article;
      $CONT->setArticleId($article_id);
      $CONT->setSliceId($slice_id);
      $CONT->setMode($mode);
      $CONT->setCLang($clang);
      $CONT->setCType($ctype);
      $CONT->setEval(TRUE);
      $CONT->setFunction($function);
      eval("?>".$CONT->getArticle());
      // ------------------------------------------ END: MODULE EDITIEREN/ADDEN ETC.

    }elseif ($mode == "meta")
    {
      // ------------------------------------------ START: META VIEW
      $extens = "";
      if (isset($save) and $save == "1")
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
        if (!isset ($meta_teaser)) $meta_teaser = 0;
        $meta_sql->setValue("teaser",$meta_teaser);
        $meta_sql->setValue("updatedate",time());
        $meta_sql->setValue("updateuser",$REX_USER->getValue("login"));

        // -------------------------- FILE UPLOAD META BILD/FILE

        $meta_sql->setValue("file",$REX_MEDIA_1);

        // ----------------------------- / FILE UPLOAD

        $meta_sql->update();

        $article->setQuery("select * from rex_article where id='$article_id' and clang='$clang'");
        if (!isset ($message)) $message = '';
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
      // Artikeltyp-Auswahl nur anzeigen, wenn mehr als ein Typ vorhanden ist
      if ($typesql->getRows() <=1 ) $out = "<input type=hidden name=type_id value=0>";
      else $out = "<tr><td class=grey>".$I18N->msg("article_type_list_name")."</td><td class=grey>".$typesel->out()."</td></tr>";

      echo "  <table border=0 cellpadding=5 cellspacing=1 width=100%>
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

      if (isset($err_msg) and $err_msg != "") echo '<tr><td colspan="2" class="warning"><font class="warning">'.$err_msg.'</font></td></tr>';

      function selectdate($date,$extens){

        $date = date("Ymd",$date);
        $ausgabe = "<select name=jahr$extens size=1>\n";
        for ($i=2005;$i<2011;$i++){
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
          <td class=grey><input type=text name=meta_article_name value=\"".htmlspecialchars($article->getValue("name"))."\" size=30 style=\"width:100%;\"></td>
        </tr>
        <tr>
          <td class=grey>".$I18N->msg("description")."</td>
          <td class=grey>
                      <textarea name=meta_description id=meta_description cols=30 rows=5 style='width:100%; height: 80px;'>".htmlspecialchars($article->getValue("description"))."</textarea>
                    </td>
        </tr>
        <tr>
          <td class=grey>".$I18N->msg("keywords")."</td>
          <td class=grey>
                      <textarea name=meta_keywords id=meta_keywords cols=30 rows=5 style='width:100%; height: 80px;'>".htmlspecialchars($article->getValue("keywords"))."</textarea>
                    </td>
        </tr>";

      echo "<tr><td class=grey>".$I18N->msg("metadata_image")."</td><td class=grey>";
            
      echo "  <table>
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
      else echo "<td align=right class=grey><input type=checkbox id=meta_teaser name=meta_teaser value=1></td>";
      echo "  <td class=grey><label for=meta_teaser>".$I18N->msg("teaser")."</label></td>
        </tr>";

      echo "  </tr>
        $out
         ";

      echo "
        <tr>
          <td class=grey>&nbsp;</td>
          <td class=grey><input type=submit value='".$I18N->msg("update_metadata")."' size=8></td>
        </tr>
        </form>
        </table>";
        
        
      // START - FUNKTION ZUM AUSLESEN DER KATEGORIEN ---------------------------------------------------  	
      function add_cat_options( &$select, &$cat, &$cat_ids, $groupName = '', $nbsp = '')
      {

      	global $REX_USER;
      	if (empty($cat)) {
      		return;
      	}

      	$cat_ids[] = $cat->getId();
      	if( $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","csr[".$cat->getId()."]") || $REX_USER->isValueOf("rights","csw[".$cat->getId()."]") ) {
      		$select->add_option($nbsp.$cat->getName(),$cat->getId());
      		$childs = $cat->getChildren();
      		if (is_array($childs)) {
      			$nbsp = $nbsp.'&nbsp;&nbsp;&nbsp;';
      			foreach ( $childs as $child) {
      				add_cat_options( $select, $child, $cat_ids, $cat->getName(), $nbsp);
      			}
      		}
      	}
      }
      // ENDE - FUNKTION ZUM AUSLESEN DER KATEGORIEN ---------------------------------------------------  


      // SONSTIGES START -------------------------------------------------------------    
        if ($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","moveArticle[]") || $REX_USER->isValueOf("rights","copyArticle[]") || ($REX_USER->isValueOf("rights","copyContent[]") && count($REX['CLANG']) > 1))
        {
          echo "<table border=0 cellpadding=5 cellspacing=1 width=100%>
          <tr>
            <td colspan=3>".$I18N->msg("other_functions")."</td>
          </tr>";
		  
          // INHALTE KOPIEREN START ---------------------------------------------------
        if(($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","copyContent[]")) && count($REX['CLANG']) > 1)
        {
          echo "
          <form action=index.php method=get>
          <input type=hidden name=page value=content>
          <input type=hidden name=article_id value='$article_id'>
          <input type=hidden name=mode value='meta'>
          <input type=hidden name=clang value=$clang>
          <input type=hidden name=ctype value=$ctype>
          <input type=hidden name=function value=copycontent>";

        $lang_a = new select;
        $lang_a->set_name("clang_a");
        $lang_a->set_style("width:100px;");
        $lang_a->set_size(1);

        foreach($REX['CLANG'] as $val => $key)
        {
          $lang_a->add_option($key,$val);
        }
      
        $lang_b = $lang_a;
        $lang_b->set_name("clang_b");
        if (isset($_REQUEST["clang_a"])) $lang_a->set_selected($_REQUEST["clang_a"]);
        if (isset($_REQUEST["clang_b"])) $lang_b->set_selected($_REQUEST["clang_b"]);
          
        echo "<tr><td class=grey width=150>".$I18N->msg("content_contentoflang")."</td><td class=grey>".$lang_a->out()." ".$I18N->msg("content_to")." ".$lang_b->out()." ". $I18N->msg("content_copy")."</td></tr>";
        
        echo "<tr>
          <td class=grey>&nbsp;</td>
          <td class=grey><input type=submit value='".$I18N->msg("content_submitcopycontent")."' size=8></td>
          </tr>";
        echo "</form>";
        }
        
          // INHALTE KOPIEREN ENDE ---------------------------------------------------

        	// ARTIKEL VERSCHIEBEN START ---------------------------------------------------
			if ($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","moveArticle[]")) {
				print "<form action=index.php method=get>
						<input type=hidden name=page value=content>
						<input type=hidden name=article_id value='$article_id'>
						<input type=hidden name=category_id_old value='$category_id'>
						<input type=hidden name=mode value='meta'>
						<input type=hidden name=clang value=$clang>
						<input type=hidden name=ctype value=$ctype>
						<input type=hidden name=function value=movearticle>";

				// Wenn Artikel kein Startartikel dann Selectliste darstellen, sonst...
		  		if ($article->getValue("startpage") == 0) {
					$move_a = new select;
					$move_a->set_name("category_id_new");
					$move_a->set_style("width:100%;");
					$move_a->set_size(1);
			
					if ($cats = OOCategory::getRootCategories()) {
						foreach( $cats as $cat) {
							add_cat_options( $move_a, $cat, $cat_ids);
						}
					}
				
					echo "<tr>
							<td class=grey width=150>".$I18N->msg("move_article")."</td>
							<td class=grey>".$move_a->out()."</td>
						  </tr>
						  <tr>
						    <td class=grey>&nbsp;</td>
							<td class=grey><input type=submit value='".$I18N->msg("content_submitmovearticle")."' size=8></td>
						</tr>";
				}
				// ...Hinweis ausgeben, das der Artikel ein Startartikel ist 
				// und nicht verschoben werden kann.
				else {
					echo "<tr>
							<td class=grey width=150>".$I18N->msg("move_article")."</td>
							<td class=grey>".$I18N->msg("content_movearticle_no_startpage")."</td>
						</tr>";
				}
				print '</form>';
			}   
			// ARTIKEL VERSCHIEBEN ENDE ------------------------------------------------
			
			
			
			// ARTIKEL KOPIEREN START --------------------------------------------------
			if ($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","copyArticle[]")) {
				print "<form action=index.php method=get>
						<input type=hidden name=page value=content>
						<input type=hidden name=article_id value='$article_id'>
						<input type=hidden name=category_copy_id_old value='$category_id'>
						<input type=hidden name=mode value='meta'>
						<input type=hidden name=clang value=$clang>
						<input type=hidden name=ctype value=$ctype>
						<input type=hidden name=function value=copyarticle>";

				$move_a = new select;
				$move_a->set_name("category_copy_id_new");
				$move_a->set_style("width:100%;");
				$move_a->set_size(1);
			
				if ($cats = OOCategory::getRootCategories()) {
					foreach( $cats as $cat) {
						add_cat_options( $move_a, $cat, $cat_ids);
					}
				}
				
				echo "<tr>
						<td class=grey width=150>".$I18N->msg("copy_article")."</td>
						<td class=grey>".$move_a->out()."</td>
					</tr>
					<tr>
						<td class=grey>&nbsp;</td>
						<td class=grey><input type=submit value='".$I18N->msg("content_submitcopyarticle")."' size=8></td>
					</tr>";
				
				print '</form>';
			}
			// ARTIKEL KOPIEREN ENDE ---------------------------------------------------



			// KATEGORIE/STARTARTIKEL VERSCHIEBEN START ---------------------------------------------------
			if ($REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","moveCategory[]") && $article->getValue("startpage") == 1)
			{
				
				print "<form action=index.php method=get>
						<input type=hidden name=page value=content>
						<input type=hidden name=article_id value='$article_id'>
						<input type=hidden name=mode value='meta'>
						<input type=hidden name=clang value=$clang>
						<input type=hidden name=ctype value=$ctype>
						<input type=hidden name=function value=movecategory>";

				$move_a = new select;
				$move_a->set_name("category_id_new");
				$move_a->set_style("width:100%;");
				$move_a->set_size(1);
		
				if ($cats = OOCategory::getRootCategories()) {
					foreach( $cats as $cat) {
						add_cat_options( $move_a, $cat, $cat_ids);
					}
				}
			
				echo "<tr>
						<td class=grey width=150>".$I18N->msg("move_category")."</td>
						<td class=grey>".$move_a->out()."</td>
					  </tr>
					  <tr>
					    <td class=grey>&nbsp;</td>
						<td class=grey><input type=submit value='".$I18N->msg("content_submitmovecategory")."' size=8></td>
					</tr>";

				print '</form>';
			}
			// KATEGROIE/STARTARTIKEL VERSCHIEBEN ENDE ------------------------------------------------











        echo "</table>";
        }
// SONSTIGES ENDE ------------------------------------------------------------- 


      // ------------------------------------------ END: META VIEW

    }

    echo "    </td>
    	<td class=lgrey>&nbsp;</td>
        </tr>
        </table>";

    // ------------------------------------------ END: AUSGABE

  }
}

?>
