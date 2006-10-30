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

unset ($REX_ACTION);

$article = new rex_sql;
$article->setQuery("
		SELECT 
			article.*, template.attributes as template_attributes 
		FROM 
			" . $REX['TABLE_PREFIX'] . "article as article
		LEFT JOIN " . $REX['TABLE_PREFIX'] . "template as template 
      ON template.id=article.template_id    
		WHERE 
			article.id='$article_id' 
			AND clang=$clang");

if ($article->getRows() == 1)
{

  // ----- ctype holen
  $attributes = $article->getValue("template_attributes");
  $REX['CTYPE'] = rex_getAttributes("ctype", $attributes, array ()); // ctypes - aus dem template
  $ctype = rex_request("ctype", "int");
  if (!array_key_exists($ctype, $REX['CTYPE']))
    $ctype = 1; // default = 1

  // ----- Artikel wurde gefunden - Kategorie holen
  if ($article->getValue("startpage") == 1)
    $category_id = $article->getValue("id");
  else
    $category_id = $article->getValue("re_id");

  // ----- category pfad und rechte
  include $REX['INCLUDE_PATH'] . "/functions/function_rex_category.inc.php";
  // $KATout kommt aus dem include
  // $KATPERM

  if ($page == "content" && $article_id > 0)
  {
    $KATout .= "\n" . '<p>';

    if ($article->getValue("startpage") == 1)
      $KATout .= $I18N->msg("start_article") . " : ";
    else
      $KATout .= $I18N->msg("article") . " : ";

    $catname = str_replace(" ", "&nbsp;", $article->getValue("name"));

    $KATout .= '<a href="index.php?page=content&amp;article_id=' . $article_id . '&amp;mode=edit&amp;clang=' . $clang . '">' . $catname . '</a>';
    // $KATout .= " [$article_id]";
    $KATout .= '</p>';
  }

  // ----- Titel anzeigen
  rex_title("Artikel", $KATout);

  // ----- Sprachenblock
  $sprachen_add = '&amp;category_id=' . $category_id . '&amp;article_id=' . $article_id;
  include $REX['INCLUDE_PATH'] . "/functions/function_rex_languages.inc.php";

  if (isset ($_REQUEST["mode"]))
    $mode = $_REQUEST["mode"];
  else
    $mode = "";

  // ----- mode defs
  if ($mode != "meta")
    $mode = "edit";

  // ----------------- HAT USER DIE RECHTE AN DIESEM ARTICLE ODER NICHT
  if (!($KATPERM || $REX_USER->hasPerm('article[' . $article_id . ']')))
  {
    // ----- hat keine rechte an diesem artikel
    echo '<p class="rex-warning">' . $I18N->msg('no_rights_to_edit') . '</p>';

  }
  else
  {
    // ----- hat rechte an diesem artikel

    // ------------------------------------------ Slice add/edit/delete
    if (isset ($function) and isset ($save) and ($function == "add" or $function == "edit" or $function == "delete") and $save == 1)
    {

      // ----- check module

      $CM = new rex_sql;
      if ($function == "edit" || $function == "delete")
      {
        // edit/ delete
        $CM->setQuery("select * from " . $REX['TABLE_PREFIX'] . "article_slice left join " . $REX['TABLE_PREFIX'] . "modultyp on " . $REX['TABLE_PREFIX'] . "article_slice.modultyp_id=" . $REX['TABLE_PREFIX'] . "modultyp.id where " . $REX['TABLE_PREFIX'] . "article_slice.id='$slice_id' and clang=$clang");
        if ($CM->getRows() == 1)
          $module_id = $CM->getValue("" . $REX['TABLE_PREFIX'] . "article_slice.modultyp_id");
      }
      else
      {
        // add
        $CM->setQuery("select * from " . $REX['TABLE_PREFIX'] . "modultyp where id='$module_id'");
      }

      if ($CM->getRows() != 1)
      {
        // ------------- START: MODUL IST NICHT VORHANDEN
        $message = $I18N->msg('module_not_found');
        $slice_id = '';
        $function = '';
        $module_id = '';
        $save = '';
        // ------------- END: MODUL IST NICHT VORHANDEN

      }
      else
      {
        // ------------- MODUL IST VORHANDEN

        // ----- RECHTE AM MODUL ?
        if (!($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('module[' . $module_id . ']') || $REX_USER->hasPerm('module[0]')))
        {
          // ----- RECHTE AM MODUL: NEIN
          $message = $I18N->msg('no_rights_to_this_function');
          $slice_id = '';
          $function = '';
          $module_id = '';
          $save = '';

        }
        else
        {
          // ----- RECHTE AM MODUL: JA
          $message = '';

          // ***********************  daten einlesen
          $REX_ACTION = array ();
          $REX_ACTION['SAVE'] = true;

          foreach ($REX['VARIABLES'] as $obj)
          {
            $REX_ACTION = $obj->getACRequestValues($REX_ACTION);
          }

          // ----- PRE SAVE ACTION [ADD/EDIT/DELETE]

          if ($function == 'edit')
            $modebit = '2'; // pre-action and edit
          elseif ($function == 'delete') $modebit = '4'; // pre-action and delete
          else
            $modebit = '1'; // pre-action and add

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

            eval ("?>" . $iaction);
            if ($REX_ACTION['MSG'] != "")
              $message .= $REX_ACTION['MSG'] . " | ";
            $ga->next();
          }

          // ----- / PRE SAVE ACTION

          // Statusspeicherung für die rex_article Klasse
          $REX['ACTION'] = $REX_ACTION;

          // Werte werden aus den REX_ACTIONS übernommen wenn SAVE=true
          if (!$REX_ACTION['SAVE'])
          {
            // ----- DONT SAVE/UPDATE SLICE

            if ($REX_ACTION['MSG'] != '')
              $message = $REX_ACTION['MSG'];
            elseif ($function == 'delete') $message = 'Block konnte nicht gelöscht werden.';
            else
              $message = 'Eingaben wurde nicht übernommen.';

          }
          else
          {

            // ----- SAVE/UPDATE SLICE

            if ($function == 'add' || $function == 'edit')
            {

              $newsql = new rex_sql;
              $newsql->debugsql = 0;
              $newsql->setTable($REX['TABLE_PREFIX'] . 'article_slice');

              if ($function == 'edit')
              {
                // edit
                $newsql->setWhere('id=' . $slice_id);
              }
              elseif ($function == 'add')
              {
                // add
                $newsql->setValue('re_article_slice_id', $slice_id);
                $newsql->setValue('article_id', $article_id);
                $newsql->setValue('modultyp_id', $module_id);
                $newsql->setValue('clang', $clang);
                $newsql->setValue('ctype', $ctype);
              }

              // ****************** SPEICHERN FALLS NOETIG
              foreach ($REX['VARIABLES'] as $obj)
              {
                $obj->setACValues($newsql, $REX_ACTION, true);
              }

              if ($function == 'edit')
              {
                $newsql->setValue('updatedate', time());
                $newsql->setValue('updateuser', $REX_USER->getValue('login'));
                if ($newsql->update())
                  $message .= $I18N->msg('block_updated');

              }
              elseif ($function == 'add')
              {
                $newsql->setValue('createdate', time());
                $newsql->setValue('createuser', $REX_USER->getValue('login'));
                if ($newsql->insert())
                {
                  $last_id = $newsql->getLastId();
                  if ($newsql->query('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice SET re_article_slice_id=' . $last_id . ' WHERE re_article_slice_id=' . $slice_id . ' AND id<>' . $last_id . ' AND article_id=' . $article_id . ' AND clang=' . $clang))
                  {
                    $message .= $I18N->msg('block_added');
                    $slice_id = $last_id;
                  }
                }
              }
            }
            else
            {
              // make delete
              $re_id = $CM->getValue($REX['TABLE_PREFIX'] . 'article_slice.re_article_slice_id');
              $newsql = new rex_sql;
              $newsql->setQuery('SELECT * FROM ' . $REX['TABLE_PREFIX'] . 'article_slice WHERE re_article_slice_id=' . $slice_id);
              if ($newsql->getRows() > 0)
              {
                $newsql->query('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice SET re_article_slice_id=' . $re_id . ' where id=' . $newsql->getValue('id'));
              }
              $newsql->query('DELETE FROM ' . $REX['TABLE_PREFIX'] . 'article_slice WHERE id=' . $slice_id);
              $message = $I18N->msg('block_deleted');
            }
            // ----- / SAVE SLICE

            // ----- artikel neu generieren
            $EA = new rex_sql;
            $EA->setTable($REX['TABLE_PREFIX'] . "article");
            $EA->setWhere("id='$article_id' and clang=$clang");
            $EA->setValue("updatedate", time());
            $EA->setValue("updateuser", $REX_USER->getValue("login"));
            $EA->update();
            rex_generateArticle($article_id);

            // ----- POST SAVE ACTION [ADD/EDIT/DELETE]

            $ga = new rex_sql;
            $ga->setQuery('SELECT postsave FROM ' . $REX['TABLE_PREFIX'] . 'module_action ma,' . $REX['TABLE_PREFIX'] . 'action a WHERE postsave != "" AND ma.action_id=a.id AND module_id=' . $module_id . ' AND ((a.postsavemode & ' . $modebit . ') = ' . $modebit . ')');

            for ($i = 0; $i < $ga->getRows(); $i++)
            {
              $REX_ACTION['MSG'] = '';
              $iaction = $ga->getValue('postsave');

              // ***************** WERTE ERSETZEN UND POSTACTION AUSFÜHREN
              foreach ($REX['VARIABLES'] as $obj)
              {
                $iaction = $obj->getACOutput($REX_ACTION, $iaction);
              }

              eval ('?>' . $iaction);
              if ($REX_ACTION['MSG'] != '')
                $message .= ' | ' . $REX_ACTION['MSG'];
              $ga->next();
            }

            // ----- / POST SAVE ACTION

            // Update Button wurde gedrückt?
            $btn_update = rex_post('btn_update');
            if ($btn_update == '')
            {
              $function = '';
            }

            $save = '';
          }
        }
      }
    }
    // ------------------------------------------ END: Slice add/edit/delete

    // ------------------------------------------ START: Slice move up/down
    if (isset ($function) and $function == "moveup" || $function == "movedown")
    {
      if ($REX_USER->hasPerm("moveSlice[]"))
      {
        // modul und rechte vorhanden ?

        $CM = new rex_sql;
        $CM->setQuery("select * from " . $REX['TABLE_PREFIX'] . "article_slice left join " . $REX['TABLE_PREFIX'] . "modultyp on " . $REX['TABLE_PREFIX'] . "article_slice.modultyp_id=" . $REX['TABLE_PREFIX'] . "modultyp.id where " . $REX['TABLE_PREFIX'] . "article_slice.id='$slice_id' and clang=$clang");
        if ($CM->getRows() != 1)
        {
          // ------------- START: MODUL IST NICHT VORHANDEN
          $message = $I18N->msg('module_not_found');
          $slice_id = "";
          $function = "";
          $module_id = "";
          $save = "";
          // ------------- END: MODUL IST NICHT VORHANDEN

        }
        else
        {

          // ------------- MODUL IST VORHANDEN
          $module_id = $CM->getValue($REX['TABLE_PREFIX'] . "article_slice.modultyp_id");

          // ----- RECHTE AM MODUL ?
          if ($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("dev[]") || $REX_USER->hasPerm("module[$module_id]") || $REX_USER->hasPerm("module[0]"))
          {
            // rechte sind vorhanden
            // ctype beachten
            // verschieben / vertauschen
            // article regenerieren.

            $slice_id = $CM->getValue($REX['TABLE_PREFIX'] . "article_slice.id");
            $slice_article_id = $CM->getValue("article_id");
            $re_slice_id = $CM->getValue($REX['TABLE_PREFIX'] . "article_slice.re_article_slice_id");
            $slice_ctype = $CM->getValue($REX['TABLE_PREFIX'] . "article_slice.ctype");

            $gs = new rex_sql;
            // $gs->debugsql = 1;
            $gs->setQuery("select * from " . $REX['TABLE_PREFIX'] . "article_slice where article_id='$slice_article_id'");
            for ($i = 0; $i < $gs->getRows(); $i++)
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
                  $gs->query("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $SREID[$SREID[$slice_id]] . "' where id='" . $slice_id . "'");
                  $gs->query("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $slice_id . "' where id='" . $SREID[$slice_id] . "'");
                  if ($SID[$slice_id] > 0)
                    $gs->query("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $SREID[$slice_id] . "' where id='" . $SID[$slice_id] . "'");
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
                  $gs->query("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $SREID[$slice_id] . "' where id='" . $SID[$slice_id] . "'");
                  $gs->query("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $SID[$slice_id] . "' where id='" . $slice_id . "'");
                  if ($SID[$SID[$slice_id]] > 0)
                    $gs->query("update " . $REX['TABLE_PREFIX'] . "article_slice set re_article_slice_id='" . $slice_id . "' where id='" . $SID[$SID[$slice_id]] . "'");
                  $message = $I18N->msg('slice_moved');
                  rex_generateArticle($slice_article_id);
                }
              }
            }
          }
          else
          {
            $message = $I18N->msg('no_rights_to_this_function');
          }
        }
      }
      else
      {
        $message = $I18N->msg('no_rights_to_this_function');
      }
    }
    // ------------------------------------------ END: Slice move up/down

    // ------------------------------------------ START: COPY LANG CONTENT
    if (isset ($function) and $function == "copycontent")
    {
      if ($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("copyContent[]"))
      {
        if (rex_copyContent($article_id, $article_id, $clang_a, $clang_b))
        {
          $message = $I18N->msg('content_contentcopy');
        }
        else
        {
          $message = $I18N->msg('content_errorcopy');
        }
      }
    }
    // ------------------------------------------ END: COPY LANG CONTENT

    // ------------------------------------------ START: MOVE ARTICLE
    if (!empty ($_POST['movearticle']) and $category_id != $article_id)
    {
      $category_id_new = (int) $category_id_new;
      if ($REX_USER->hasPerm("admin[]") || ($REX_USER->hasPerm("moveArticle[]") && ($REX_USER->hasPerm("csw[0]") || $REX_USER->hasPerm("csw[" . $category_id_new . "]"))))
      {
        if (rex_moveArticle($article_id, $category_id, $category_id_new))
        {
          $message = $I18N->msg('content_articlemoved');
          ob_end_clean();
          header("Location: index.php?page=content&article_id=" . $article_id . "&mode=meta&clang=" . $clang . "&ctype=" . $ctype . "&msg=" . urlencode($message));
          exit;
        }
        else
        {
          $message = $I18N->msg('content_errormovearticle');
        }
      }
      else
      {
        $message = $I18N->msg('no_rights_to_this_function');
      }
    }
    // ------------------------------------------ END: MOVE ARTICLE

    // ------------------------------------------ START: COPY ARTICLE
    if (!empty ($_POST['copyarticle']))
    {
      $category_copy_id_new = (int) $category_copy_id_new;
      if ($REX_USER->hasPerm("admin[]") || ($REX_USER->hasPerm("copyArticle[]") && ($REX_USER->hasPerm("csw[0]") || $REX_USER->hasPerm("csw[" . $category_copy_id_new . "]"))))
      {
        if ($new_id = rex_copyArticle($article_id, $category_copy_id_new))
        {
          $message = $I18N->msg('content_articlecopied');
          ob_end_clean();
          header("Location: index.php?page=content&article_id=" . $new_id . "&mode=meta&clang=" . $clang . "&ctype=" . $ctype . "&msg=" . urlencode($message));
          exit;
        }
        else
        {
          $message = $I18N->msg('content_errorcopyarticle');
        }
      }
      else
      {
        $message = $I18N->msg('no_rights_to_this_function');
      }
    }
    // ------------------------------------------ END: COPY ARTICLE

    // ------------------------------------------ START: MOVE CATEGORY
    if (!empty ($_POST['movecategory']))
    {
      $category_id_new = (int) $category_id_new;
      if ($REX_USER->hasPerm("admin[]") || ($REX_USER->hasPerm("moveCategory[]") && (($REX_USER->hasPerm("csw[0]") || $REX_USER->hasPerm("csw[" . $category_id . "]")) && ($REX_USER->hasPerm("csw[0]") || $REX_USER->hasPerm("csw[" . $category_id_new . "]")))))
      {
        if ($category_id != $category_id_new && rex_moveCategory($category_id, $category_id_new))
        {
          $message = $I18N->msg('category_moved');
          ob_end_clean();
          header("Location: index.php?page=content&article_id=" . $category_id . "&mode=meta&clang=" . $clang . "&ctype=" . $ctype . "&msg=" . urlencode($message));
          exit;
        }
        else
        {
          $message = $I18N->msg('content_error_movecategory');
        }
      }
      else
      {
        $message = $I18N->msg('no_rights_to_this_function');
      }
    }
    // ------------------------------------------ END: MOVE CATEGORY

    // ------------------------------------------ START: CONTENT HEAD MENUE
    $num_ctypes = count($REX['CTYPE']);
    $tadd = "";
    if ($num_ctypes > 1)
    {
      $tadd = '
                  <ul>
                    <li>Typen : </li>';
      $i = 1;
      foreach ($REX['CTYPE'] as $key => $val)
      {
        $tadd .= '
                        <li>';
        if ($key == $ctype)
        {
          $tadd .= $val;
        }
        else
        {
          $tadd .= '<a href="index.php?page=content&amp;clang=' . $clang . '&amp;ctype=' . $key . '&amp;category_id=' . $category_id . '&amp;article_id=' . $article_id . '">' . $val . '</a>';
        }
        if ($num_ctypes != $i)
        {
          $tadd .= ' | ';
        }
        $tadd .= '</li>';
        $i++;
      }
      $tadd .= '
                  </ul>';
    }

    $menu = $tadd;

    if ($mode == 'edit')
    {
      $menu_edit = '<span>' . $I18N->msg('edit_mode') . '</span>';
      $menu_meta = '<a href="index.php?page=content&amp;article_id=' . $article_id . '&amp;mode=meta&amp;clang=' . $clang . '&amp;ctype=' . $ctype . '">' . $I18N->msg('metadata') . '</a>';
    }
    else
    {
      $menu_edit = '<a href="index.php?page=content&amp;article_id=' . $article_id . '&amp;mode=edit&amp;clang=' . $clang . '&amp;ctype=' . $ctype . '">' . $I18N->msg('edit_mode') . '</a>';
      $menu_meta = '<span>' . $I18N->msg('metadata') . '</span>';
    }

    $menu .= '
            <ul>
              <li>' . $menu_edit . ' | </li>
              <li>' . $menu_meta . ' | </li>
              <li><a href="../index.php?article_id=' . $article_id . '&amp;clang=' . $clang . '" target="_blank">' . $I18N->msg('show') . '</a></li>
            </ul>';
    // ------------------------------------------ END: CONTENT HEAD MENUE

    // ------------------------------------------ START: AUSGABE
    echo '
            <!-- *** OUTPUT OF ARTICLE-CONTENT - START *** -->
            <div class="rex-cnt-hdr">
              ' . $menu . '
            </div>
            ';

    // ------------------------------------------ WARNING
    if (!isset ($message))
      $message = '';

    if ($mode != 'edit' && $message != '')
    {
      echo '<p class="rex-warning">' . $message . '</p>';
    }

    echo '
            <div class="rex-cnt-bdy">
            ';

    if ($mode == "edit")
    {
      if (!isset ($slice_id))
        $slice_id = '';
      if (!isset ($function))
        $function = '';

      // ------------------------------------------ START: MODULE EDITIEREN/ADDEN ETC.
      echo '
                  <!-- *** OUTPUT OF ARTICLE-CONTENT-EDIT-MODE - START *** -->
                  <div class="rex-cnt-editmode">
                  ';

      $CONT = new rex_article;
      $CONT->message = $message;
      $CONT->setArticleId($article_id);
      $CONT->setSliceId($slice_id);
      $CONT->setMode($mode);
      $CONT->setCLang($clang);
      $CONT->setEval(TRUE);
      $CONT->setFunction($function);
      eval ("?>" . $CONT->getArticle($ctype));

      echo '
                  </div>
                  <!-- *** OUTPUT OF ARTICLE-CONTENT-EDIT-MODE - END *** -->
                  ';
      // ------------------------------------------ END: MODULE EDITIEREN/ADDEN ETC.

    }
    elseif ($mode == "meta")
    {
      // ------------------------------------------ START: META VIEW
      $extens = "";
      if (isset ($save) and $save == "1")
      {
        $meta_sql = new rex_sql;
        $meta_sql->setTable($REX['TABLE_PREFIX'] . "article");
        // $meta_sql->debugsql = 1;
        $meta_sql->setWhere("id='$article_id' and clang=$clang");
        $meta_sql->setValue("keywords", $meta_keywords);
        $meta_sql->setValue("description", $meta_description);
        $meta_sql->setValue("name", $meta_article_name);
        $meta_sql->setValue("updatedate", time());
        $meta_sql->setValue("updateuser", $REX_USER->getValue("login"));

        // -------------------------- FILE UPLOAD META BILD/FILE

        $meta_sql->setValue("file", $REX_MEDIA_1);

        // ----------------------------- / FILE UPLOAD

        $meta_sql->update();

        $article->setQuery("select * from " . $REX['TABLE_PREFIX'] . "article where id='$article_id' and clang='$clang'");
        if (!isset ($message))
          $message = '';
        $err_msg = $I18N->msg("metadata_updated") . $message;

        rex_generateArticle($article_id);

        // ----- EXTENSION POINT
        $message = rex_register_extension_point('ART_META_UPDATED', $message, array (
          "id" => $article_id,
          "clang" => $clang,
          "keywords" => $meta_keywords,
          "description" => $meta_description,
          "name" => $meta_article_name,

          
        ));
      }

      echo '
            	  <div class="rex-cnt-metamode">
                  <form action="index.php" method="post" enctype="multipart/form-data" id="REX_FORM">
                    <fieldset>
                      <legend class="rex-lgnd">' . $I18N->msg('general') . '</legend>
                      <input type="hidden" name="page" value="content" />
                      <input type="hidden" name="article_id" value="' . $article_id . '" />
                      <input type="hidden" name="mode" value="meta" />
                      <input type="hidden" name="save" value="1" />
                      <input type="hidden" name="clang" value="' . $clang . '" />
                      <input type="hidden" name="ctype" value="' . $ctype . '" />
                    ';

      if (isset ($err_msg) and $err_msg != '')
        echo '<p class="rex-warning">' . $err_msg . '</p>';

      echo '
                    <p>
                      <label for="meta_article_name">' . $I18N->msg("name_description") . '</label>
                      <input type="text" id="meta_article_name" name="meta_article_name" value="' . htmlspecialchars($article->getValue("name")) . '" size="30" />
                    </p>
                    <p>
                      <label for="meta_description">' . $I18N->msg("description") . '</label>
                      <textarea name="meta_description" id="meta_description" cols="50" rows="6" >' . htmlspecialchars($article->getValue("description")) . '</textarea>
                    </p>
                    <p>
                      <label for="meta_keywords">' . $I18N->msg("keywords") . '</label>
                      <textarea name="meta_keywords" id="meta_keywords" cols="50" rows="6">' . htmlspecialchars($article->getValue("keywords")) . '</textarea>
                    </p>
                    <p>
                      <label for="REX_MEDIA_1">' . $I18N->msg("metadata_image") . '</label>
                      <input type="hidden" name="REX_MEDIA_DELETE_1" value="0" id="REX_MEDIA_DELETE_1" />
                      <input type="text" size="30" name="REX_MEDIA_1" value="' . $article->getValue("file") . '" id="REX_MEDIA_1" readonly="readonly" />
                      
            	      <a href="#" onclick="openREXMedia(1); return false;"><img src="pics/file_open.gif" width="16" height="16" alt="medienpool" title="medienpool" /></a>
                      <a href="#" onclick="deleteREXMedia(1); return false;"><img src="pics/file_del.gif" width=16 height=16 alt="+" title="-" /></a>
                      <a href="#" onclick="addREXMedia(1); return false;"><img src="pics/file_add.gif" width="16" height="16" alt="-" title="+" /></a>
                    </p>
            
                    ';

      // ----- EXTENSION POINT
      echo rex_register_extension_point('ART_META_FORM', '', array (
        "id" => $article_id,
        "clang" => $clang
      ));

      echo '
                    <p>
                      <input class="rex-sbmt" type="submit" value="' . $I18N->msg("update_metadata") . '" />
                    </p>
                 </fieldset>';

      // ----- EXTENSION POINT
      echo rex_register_extension_point('ART_META_FORM_SECTION', '', array (
        "id" => $article_id,
        "clang" => $clang
      ));

      // --------------------------------------------------- START - FUNKTION ZUM AUSLESEN DER KATEGORIEN  	
      function add_cat_options(& $select, & $cat, & $cat_ids, $groupName = '', $nbsp = '')
      {

        global $REX_USER;
        if (empty ($cat))
        {
          return;
        }

        $cat_ids[] = $cat->getId();
        if ($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("csw[0]") || $REX_USER->hasPerm("csr[" . $cat->getId() . "]") || $REX_USER->hasPerm("csw[" . $cat->getId() . "]"))
        {
          $select->add_option($nbsp . $cat->getName(), $cat->getId());
          $childs = $cat->getChildren();
          if (is_array($childs))
          {
            $nbsp = $nbsp . '&nbsp;&nbsp;&nbsp;';
            foreach ($childs as $child)
            {
              add_cat_options($select, $child, $cat_ids, $cat->getName(), $nbsp);
            }
          }
        }
      }
      // --------------------------------------------------- ENDE - FUNKTION ZUM AUSLESEN DER KATEGORIEN  

      // ------------------------------------------------------------- SONSTIGES START    
      if ($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("moveArticle[]") || $REX_USER->hasPerm("copyArticle[]") || ($REX_USER->hasPerm("copyContent[]") && count($REX['CLANG']) > 1))
      {
        // --------------------------------------------------- INHALTE KOPIEREN START
        if (($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("copyContent[]")) && count($REX['CLANG']) > 1)
        {
          $lang_a = new rex_select;
          $lang_a->set_id("clang_a");
          $lang_a->set_name("clang_a");
          $lang_a->set_size("1");

          foreach ($REX['CLANG'] as $val => $key)
          {
            $lang_a->add_option($key, $val);
          }

          $lang_b = $lang_a;
          $lang_b->set_id("clang_b");
          $lang_b->set_name("clang_b");
          if (isset ($_REQUEST["clang_a"]))
            $lang_a->set_selected($_REQUEST["clang_a"]);
          if (isset ($_REQUEST["clang_b"]))
            $lang_b->set_selected($_REQUEST["clang_b"]);

          echo '
                                <fieldset>
                                  <legend class="rex-lgnd">' . $I18N->msg("content_submitcopycontent") . '</legend>
                                  <p>
                                    <label for="clang_a">' . $I18N->msg("content_contentoflang") . '</label>
                                    ' . $lang_a->out() . '
                                    <label for="clang_b">' . $I18N->msg("content_to") . '</label> ' . $lang_b->out() . '
                                  </p>
                                  <p>
                                    <input class="rex-sbmt" type="submit" name="copycontent" value="' . $I18N->msg("content_submitcopycontent") . '" />
                                  </p>
                                </fieldset>';

        }
        // --------------------------------------------------- INHALTE KOPIEREN ENDE

        // --------------------------------------------------- ARTIKEL VERSCHIEBEN START
        if ($article->getValue("startpage") == 0 && ($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("moveArticle[]")))
        {

          // Wenn Artikel kein Startartikel dann Selectliste darstellen, sonst...
          $move_a = new rex_select;
          $move_a->set_id("category_id_new");
          $move_a->set_name("category_id_new");
          $move_a->set_size("1");

          if ($cats = OOCategory :: getRootCategories())
          {
            foreach ($cats as $cat)
            {
              add_cat_options($move_a, $cat, $cat_ids);
            }
          }

          echo '
                                <fieldset>
                                  <legend class="rex-lgnd">' . $I18N->msg("content_submitmovearticle") . '</legend>
                                  <p>
                                    <label for="category_id_new">' . $I18N->msg("move_article") . '</label>
                                    ' . $move_a->out() . '
                                  </p>
                                  <p>
                                    <input class="rex-sbmt" type="submit" name="movearticle" value="' . $I18N->msg("content_submitmovearticle") . '" />
                                  </p>
                                </fieldset>
                                ';

        }
        // ------------------------------------------------ ARTIKEL VERSCHIEBEN ENDE

        // -------------------------------------------------- ARTIKEL KOPIEREN START
        if ($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("copyArticle[]"))
        {
          $move_a = new rex_select;
          $move_a->set_name("category_copy_id_new");
          $move_a->set_id("category_copy_id_new");
          $move_a->set_size("1");
          $move_a->set_selected($article_id);

          if ($cats = OOCategory :: getRootCategories())
          {
            foreach ($cats as $cat)
            {
              add_cat_options($move_a, $cat, $cat_ids);
            }
          }

          echo '
                                <fieldset>
                                  <legend class="rex-lgnd">' . $I18N->msg("content_submitcopyarticle") . '</legend>
                                  <p>
                                    <label for="category_copy_id_new">' . $I18N->msg("copy_article") . '</label>
                                    ' . $move_a->out() . '
                                  </p>
                                  <p>
                                    <input class="rex-sbmt" type="submit" name="copyarticle" value="' . $I18N->msg("content_submitcopyarticle") . '" />
                                  </p>
                                </fieldset>
                                ';

        }
        // --------------------------------------------------- ARTIKEL KOPIEREN ENDE 

        // --------------------------------------------------- KATEGORIE/STARTARTIKEL VERSCHIEBEN START 
        if ($article->getValue("startpage") == 1 && ($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("moveCategory[]")))
        {
          $move_a = new rex_select;
          $move_a->set_id("category_id_new");
          $move_a->set_name("category_id_new");
          $move_a->set_size("1");
          $move_a->set_selected($article_id);

          if ($cats = OOCategory :: getRootCategories())
          {
            foreach ($cats as $cat)
            {
              add_cat_options($move_a, $cat, $cat_ids, "", "&nbsp;&nbsp;");
            }
          }
          echo '
                                <fieldset>
                                  <legend class="rex-lgnd">' . $I18N->msg("content_submitmovecategory") . '</legend>
                                  <p>
                                    <label for="category_id_new">' . $I18N->msg("move_category") . '</label>
                                    ' . $move_a->out() . '
                                  </p>
                                  <p>
                                    <input class="rex-sbmt" type="submit" name="movecategory" value="' . $I18N->msg("content_submitmovecategory") . '" />
                                  </p>
                                </fieldset>';

        }
        // ------------------------------------------------ KATEGROIE/STARTARTIKEL VERSCHIEBEN ENDE 

      }
      // ------------------------------------------------------------- SONSTIGES ENDE  

      echo '
                  </form>
            	  </div>';

      // ------------------------------------------ END: META VIEW

    }

    echo '
            </div>
            <!-- *** OUTPUT OF ARTICLE-CONTENT - END *** -->
            ';

    // ------------------------------------------ END: AUSGABE

  }
}
?>