<?php


/**
 * Verwaltung der Inhalte. EditierModul / Metadaten ...
 * @package redaxo4
 * @version $Id: content.inc.php,v 1.7 2008/02/25 09:52:16 kills Exp $
 */

/*
// TODOS:
// - alles vereinfachen
// - <? ?> $ Problematik bei REX_ACTION
*/

require $REX['INCLUDE_PATH'].'/functions/function_rex_content.inc.php';

unset ($REX_ACTION);

$slice_id = rex_request('slice_id', 'int', '');
$article_id = rex_request('article_id', 'int');
$category_id = rex_request('category_id', 'int');
$function = rex_request('function', 'string');

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
  $attributes = $article->getValue('template_attributes');

  // Für Artikel ohne Template
  if($attributes === null) $attributes = '';

  $REX['CTYPE'] = rex_getAttributes('ctype', $attributes, array ()); // ctypes - aus dem template
  $ctype = rex_request('ctype', 'int');
  if (!array_key_exists($ctype, $REX['CTYPE']))
    $ctype = 1; // default = 1

  // ----- Artikel wurde gefunden - Kategorie holen
  if ($article->getValue('startpage') == 1)
    $category_id = $article->getValue('id');
  else
    $category_id = $article->getValue('re_id');

  // ----- category pfad und rechte
  require $REX['INCLUDE_PATH'] . '/functions/function_rex_category.inc.php';
  // $KATout kommt aus dem include
  // $KATPERM

  if ($page == 'content' && $article_id > 0)
  {
    $KATout .= "\n" . '<p>';

    if ($article->getValue('startpage') == 1)
      $KATout .= $I18N->msg('start_article') . ' : ';
    else
      $KATout .= $I18N->msg('article') . ' : ';

    $catname = str_replace(' ', '&nbsp;', $article->getValue('name'));

    $KATout .= '<a href="index.php?page=content&amp;article_id=' . $article_id . '&amp;mode=edit&amp;clang=' . $clang . '"'. rex_tabindex() .'>' . $catname . '</a>';
    // $KATout .= " [$article_id]";
    $KATout .= '</p>';
  }

  // ----- Titel anzeigen
  rex_title($I18N->msg('content'), $KATout);

  // ----- Request Parameter
  $mode = rex_request('mode', 'string');
  $function = rex_request('function', 'string');
  $message = rex_request('message', 'string');

  // ----- mode defs
  if ($mode != 'meta')
    $mode = 'edit';

  // ----- Sprachenblock
  $sprachen_add = '&amp;mode='. $mode .'&amp;category_id=' . $category_id . '&amp;article_id=' . $article_id;
  require $REX['INCLUDE_PATH'] . '/functions/function_rex_languages.inc.php';

  // ----- EXTENSION POINT
  echo rex_register_extension_point('PAGE_CONTENT_HEADER', '',
    array(
      'article_id' => $article_id,
      'clang' => $clang,
      'function' => $function,
      'mode' => $mode,
      'slice_id' => $slice_id
    )
  );

  // ----------------- HAT USER DIE RECHTE AN DIESEM ARTICLE ODER NICHT
  if (!($KATPERM || $REX_USER->hasPerm('article[' . $article_id . ']')))
  {
    // ----- hat keine rechte an diesem artikel
    echo rex_warning($I18N->msg('no_rights_to_edit'));
  }
  else
  {
    // ----- hat rechte an diesem artikel

    // ------------------------------------------ Slice add/edit/delete
    if (rex_request('save', 'boolean') && ($function == 'add' || $function == 'edit' || $function == 'delete'))
    {
      // ----- check module

      $CM = new rex_sql;
      if ($function == 'edit' || $function == 'delete')
      {
        // edit/ delete
        $CM->setQuery("SELECT * FROM " . $REX['TABLE_PREFIX'] . "article_slice LEFT JOIN " . $REX['TABLE_PREFIX'] . "module ON " . $REX['TABLE_PREFIX'] . "article_slice.modultyp_id=" . $REX['TABLE_PREFIX'] . "module.id WHERE " . $REX['TABLE_PREFIX'] . "article_slice.id='$slice_id' AND clang=$clang");
        if ($CM->getRows() == 1)
          $module_id = $CM->getValue("" . $REX['TABLE_PREFIX'] . "article_slice.modultyp_id");
      }
      else
      {
        // add
        $module_id = rex_post('module_id', 'int');
        $CM->setQuery("SELECT * FROM " . $REX['TABLE_PREFIX'] . "module WHERE id='$module_id'");
      }

      if ($CM->getRows() != 1)
      {
        // ------------- START: MODUL IST NICHT VORHANDEN
        $message = $I18N->msg('module_not_found');
        $slice_id = '';
        $function = '';
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
          list($action_message, $REX_ACTION) = rex_execPreSaveAction($module_id, $function, $REX_ACTION);
          $message .= $action_message;
          // ----- / PRE SAVE ACTION

          // Statusspeicherung für die rex_article Klasse
          $REX['ACTION'] = $REX_ACTION;

          // Werte werden aus den REX_ACTIONS übernommen wenn SAVE=true
          if (!$REX_ACTION['SAVE'])
          {
            // ----- DONT SAVE/UPDATE SLICE

            if ($REX_ACTION['MSG'] != '')
              $message = $REX_ACTION['MSG'];
            elseif ($function == 'delete')
            	$message = $I18N->msg('slice_deleted_error');
            else
              $message = $I18N->msg('slice_saved_error');

          }
          else
          {
            // ----- SAVE/UPDATE SLICE

            if ($function == 'add' || $function == 'edit')
            {

              $newsql = new rex_sql;
              // $newsql->debugsql = true;
              $sliceTable = $REX['TABLE_PREFIX'] . 'article_slice';
              $newsql->setTable($sliceTable);

              if ($function == 'edit')
              {
                // edit
                $newsql->setWhere('id=' . $slice_id);
              }
              elseif ($function == 'add')
              {
                // add
                $newsql->setValue($sliceTable .'.re_article_slice_id', $slice_id);
                $newsql->setValue($sliceTable .'.article_id', $article_id);
                $newsql->setValue($sliceTable .'.modultyp_id', $module_id);
                $newsql->setValue($sliceTable .'.clang', $clang);
                $newsql->setValue($sliceTable .'.ctype', $ctype);
              }

              // ****************** SPEICHERN FALLS NOETIG
              foreach ($REX['VARIABLES'] as $obj)
              {
                $obj->setACValues($newsql, $REX_ACTION, true);
              }

              if ($function == 'edit')
              {
                $newsql->addGlobalUpdateFields();
                if ($newsql->update())
                  $message .= $I18N->msg('block_updated');
                else
                  $message .= $newsql->getError();

              }
              elseif ($function == 'add')
              {
                $newsql->addGlobalUpdateFields();
                $newsql->addGlobalCreateFields();
                if ($newsql->insert())
                {
                  $last_id = $newsql->getLastId();
                  if ($newsql->setQuery('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice SET re_article_slice_id=' . $last_id . ' WHERE re_article_slice_id=' . $slice_id . ' AND id<>' . $last_id . ' AND article_id=' . $article_id . ' AND clang=' . $clang))
                  {
                    $message .= $I18N->msg('block_added');
                    $slice_id = $last_id;
                  }
                  $function = "";
                }
                else
                {
                  $message .= $newsql->getError();
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
                $newsql->setQuery('UPDATE ' . $REX['TABLE_PREFIX'] . 'article_slice SET re_article_slice_id=' . $re_id . ' where id=' . $newsql->getValue('id'));
              }
              $newsql->setQuery('DELETE FROM ' . $REX['TABLE_PREFIX'] . 'article_slice WHERE id=' . $slice_id);
              $message = $I18N->msg('block_deleted');
            }
            // ----- / SAVE SLICE

            // ----- artikel neu generieren
            $EA = new rex_sql;
            $EA->setTable($REX['TABLE_PREFIX'] . 'article');
            $EA->setWhere('id='. $article_id .' AND clang='. $clang);
            $EA->addGlobalUpdateFields();
            $EA->update();
            rex_generateArticle($article_id);

            // ----- POST SAVE ACTION [ADD/EDIT/DELETE]
            $message .= rex_execPostSaveAction($module_id, $function, $REX_ACTION);
            // ----- / POST SAVE ACTION

            // Update Button wurde gedrückt?
            // TODO: Workaround, da IE keine Button Namen beim
            // drücken der Entertaste übermittelt
            if (rex_post('btn_save', 'string'))
            {
              $function = '';
            }
          }
        }
      }
    }
    // ------------------------------------------ END: Slice add/edit/delete

    // ------------------------------------------ START: Slice move up/down
    if ($function == 'moveup' || $function == 'movedown')
    {
      if ($REX_USER->hasPerm('moveSlice[]'))
      {
        // modul und rechte vorhanden ?

        $CM = new rex_sql;
        $CM->setQuery("select * from " . $REX['TABLE_PREFIX'] . "article_slice left join " . $REX['TABLE_PREFIX'] . "module on " . $REX['TABLE_PREFIX'] . "article_slice.modultyp_id=" . $REX['TABLE_PREFIX'] . "module.id where " . $REX['TABLE_PREFIX'] . "article_slice.id='$slice_id' and clang=$clang");
        if ($CM->getRows() != 1)
        {
          // ------------- START: MODUL IST NICHT VORHANDEN
          $message = $I18N->msg('module_not_found');
          $slice_id = "";
          $function = "";
          // ------------- END: MODUL IST NICHT VORHANDEN
        }
        else
        {
        	$module_id = (int) $CM->getValue($REX['TABLE_PREFIX']."article_slice.modultyp_id");

          // ----- RECHTE AM MODUL ?
          if ($REX_USER->hasPerm("admin[]") || $REX_USER->hasPerm("module[$module_id]") || $REX_USER->hasPerm("module[0]"))
          {
            // rechte sind vorhanden

            if ($function == "moveup" || $function == "movedown")
            {
              list($success, $message) = rex_moveSlice($slice_id, $clang, $function);
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

		// ------------------------------------------ START: ARTICLE2STARTARTICLE
    if (rex_post('article2startpage', 'string'))
    {
      if ($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('article2startpage[]'))
      {
        if (rex_article2startpage($article_id))
        {
          $message = $I18N->msg('content_tostartarticle_ok');
          header("Location:index.php?page=content&mode=meta&clang=$clang&ctype=$ctype&article_id=$article_id&message=".urlencode($message));
          exit;
        }
        else
        {
          $message = $I18N->msg('content_tostartarticle_failed');
        }
      }
    }
    // ------------------------------------------ END: COPY LANG CONTENT


    // ------------------------------------------ START: COPY LANG CONTENT
    if (rex_post('copycontent', 'string'))
    {
      if ($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('copyContent[]'))
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
    if (rex_post('movearticle', 'string') && $category_id != $article_id)
    {

      $category_id_new = rex_post('category_id_new', 'int');
      if ($REX_USER->hasPerm('admin[]') || ($REX_USER->hasPerm('moveArticle[]') && ($REX_USER->hasPerm('csw[0]') || $REX_USER->hasPerm('csw[' . $category_id_new . ']'))))
      {
        if (rex_moveArticle($article_id, $category_id, $category_id_new))
        {
          $message = $I18N->msg('content_articlemoved');
          ob_end_clean();
          header('Location: index.php?page=content&article_id=' . $article_id . '&mode=meta&clang=' . $clang . '&ctype=' . $ctype . '&message=' . urlencode($message));
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
    if (rex_post('copyarticle', 'string'))
    {
    	$category_copy_id_new = rex_post('category_copy_id_new', 'int');
      if ($REX_USER->hasPerm('admin[]') || ($REX_USER->hasPerm('copyArticle[]') && ($REX_USER->hasPerm('csw[0]') || $REX_USER->hasPerm('csw[' . $category_copy_id_new . ']'))))
      {
        if (($new_id = rex_copyArticle($article_id, $category_copy_id_new)) !== false)
        {
          $message = $I18N->msg('content_articlecopied');
          ob_end_clean();
          header('Location: index.php?page=content&article_id=' . $new_id . '&mode=meta&clang=' . $clang . '&ctype=' . $ctype . '&message=' . urlencode($message));
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
    if (rex_post('movecategory', 'string'))
    {
    	$category_id_new = rex_post('category_id_new', 'int');
      if ($REX_USER->hasPerm('admin[]') || ($REX_USER->hasPerm('moveCategory[]') && (($REX_USER->hasPerm('csw[0]') || $REX_USER->hasPerm('csw[' . $category_id . ']')) && ($REX_USER->hasPerm('csw[0]') || $REX_USER->hasPerm('csw[' . $category_id_new . ']')))))
      {
        if ($category_id != $category_id_new && rex_moveCategory($category_id, $category_id_new))
        {
          $message = $I18N->msg('category_moved');
          ob_end_clean();
          header('Location: index.php?page=content&article_id=' . $category_id . '&mode=meta&clang=' . $clang . '&ctype=' . $ctype . '&message=' . urlencode($message));
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

    // ------------------------------------------ START: SAVE METADATA
    if (rex_post('savemeta', 'string'))
    {
      $meta_sql = new rex_sql;
      $meta_sql->setTable($REX['TABLE_PREFIX'] . "article");
      // $meta_sql->debugsql = 1;
      $meta_sql->setWhere("id='$article_id' AND clang=$clang");
      $meta_sql->setValue('name', $meta_article_name);
      $meta_sql->addGlobalUpdateFields();

      if($meta_sql->update())
      {
        $article->setQuery("SELECT * FROM " . $REX['TABLE_PREFIX'] . "article WHERE id='$article_id' AND clang='$clang'");

        $message = $I18N->msg("metadata_updated") . $message;

        rex_generateArticle($article_id);

        // ----- EXTENSION POINT
        $message = rex_register_extension_point('ART_META_UPDATED', $message, array (
          'id' => $article_id,
          'clang' => $clang,
          'name' => $meta_article_name,
        ));
      }
      else
      {
        $message .= $meta_sql->getError();
      }
    }
    // ------------------------------------------ END: SAVE METADATA

    // ------------------------------------------ START: CONTENT HEAD MENUE
    $num_ctypes = count($REX['CTYPE']);

    $ctype_menu = '';
    if ($num_ctypes > 0)
    {
      $listElements = array();

      if ($num_ctypes > 1)
        $listElements[] = $I18N->msg('content_types').': ';
      else
        $listElements[] = $I18N->msg('content_type').': ';

      $i = 1;
      foreach ($REX['CTYPE'] as $key => $val)
      {
        $s = '';
        $class = '';

        if ($key == $ctype && $mode == 'edit')
        {
        	$class = ' class="rex-active"';
        }

        $val = rex_translate($val);
        $s .= '<a href="index.php?page=content&amp;clang=' . $clang . '&amp;ctype=' . $key . '&amp;category_id=' . $category_id . '&amp;article_id=' . $article_id . '"'. $class .''. rex_tabindex() .'>' . $val . '</a>';

        if ($num_ctypes != $i)
        {
          $s .= ' | ';
        }

        $listElements[] = $s;
        $i++;
      }

      // ----- EXTENSION POINT
      $listElements = rex_register_extension_point('PAGE_CONTENT_CTYPE_MENU', $listElements,
        array(
          'article_id' => $article_id,
          'clang' => $clang,
          'function' => $function,
          'mode' => $mode,
          'slice_id' => $slice_id
        )
      );

      $ctype_menu .= "\n".'<ul id="rex-navi-ctype">';
      foreach($listElements as $listElement)
      {
        $ctype_menu .= '<li>'.$listElement.'</li>';
      }
      $ctype_menu .= '</ul>';
    }

    $menu = $ctype_menu;
    $listElements = array();

    if ($mode == 'edit')
    {
      $listElements[] = '<a href="index.php?page=content&amp;article_id=' . $article_id . '&amp;mode=edit&amp;clang=' . $clang . '&amp;ctype=' . $ctype . '" class="rex-active"'. rex_tabindex() .'>' . $I18N->msg('edit_mode') . '</a>';
      $listElements[] = '<a href="index.php?page=content&amp;article_id=' . $article_id . '&amp;mode=meta&amp;clang=' . $clang . '&amp;ctype=' . $ctype . '"'. rex_tabindex() .'>' . $I18N->msg('metadata') . '</a>';
    }
    else
    {
      $listElements[] = '<a href="index.php?page=content&amp;article_id=' . $article_id . '&amp;mode=edit&amp;clang=' . $clang . '&amp;ctype=' . $ctype . '"'. rex_tabindex() .'>' . $I18N->msg('edit_mode') . '</a>';
      $listElements[] = '<a href="index.php?page=content&amp;article_id=' . $article_id . '&amp;mode=meta&amp;clang=' . $clang . '&amp;ctype=' . $ctype . '" class="rex-active"'. rex_tabindex() .'>' . $I18N->msg('metadata') . '</a>';
    }

    $listElements[] = '<a href="../index.php?article_id=' . $article_id . '&amp;clang=' . $clang . '" onclick="window.open(this.href); return false;" '. rex_tabindex() .'>' . $I18N->msg('show') . '</a>';

    // ----- EXTENSION POINT
    $listElements = rex_register_extension_point('PAGE_CONTENT_MENU', $listElements,
      array(
        'article_id' => $article_id,
        'clang' => $clang,
        'function' => $function,
        'mode' => $mode,
        'slice_id' => $slice_id
      )
    );

    $menu .= "\n".'<ul class="rex-navi-content">';
    $num_elements = count($listElements);
    for($i = 0; $i < $num_elements; $i++)
    {
      $lastElement = ($i == ($num_elements -1));
      $menu .= '<li>'. $listElements[$i] . ($lastElement ? '' : ' | ') .'</li>';
    }
    $menu .= '</ul>';

    // ------------------------------------------ END: CONTENT HEAD MENUE

    // ------------------------------------------ START: AUSGABE
    echo '
            <!-- *** OUTPUT OF ARTICLE-CONTENT - START *** -->
            <div class="rex-content-header">
            <div class="rex-content-header-2">
              ' . $menu . '
              <div class="rex-clearer"></div>
            </div>
            </div>
            ';

    // ------------------------------------------ WARNING
    if ($mode != 'edit' && $message != '')
    {
      echo rex_warning($message);
    }

    echo '
            <div class="rex-content-body">
            ';

    if ($mode == 'edit')
    {
      // ------------------------------------------ START: MODULE EDITIEREN/ADDEN ETC.

      echo '
                  <!-- *** OUTPUT OF ARTICLE-CONTENT-EDIT-MODE - START *** -->
                  <div class="rex-content-editmode">
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
    elseif ($mode == 'meta')
    {
      // ------------------------------------------ START: META VIEW

      echo '
    	  <div class="rex-form rex-content-metamode">
          <form action="index.php" method="post" enctype="multipart/form-data" id="REX_FORM">
            <fieldset>
              <legend class="rex-legend">' . $I18N->msg('general') . '</legend>

				      <div class="rex-fieldset-wrapper">

						  <input type="hidden" name="page" value="content" />
						  <input type="hidden" name="article_id" value="' . $article_id . '" />
						  <input type="hidden" name="mode" value="meta" />
						  <input type="hidden" name="save" value="1" />
						  <input type="hidden" name="clang" value="' . $clang . '" />
						  <input type="hidden" name="ctype" value="' . $ctype . '" />

						<p>
						  <label for="meta_article_name">' . $I18N->msg("name_description") . '</label>
						  <input type="text" id="meta_article_name" name="meta_article_name" value="' . htmlspecialchars($article->getValue("name")) . '" size="30"'. rex_tabindex() .' />
						</p>';

      // ----- EXTENSION POINT
      echo rex_register_extension_point('ART_META_FORM', '', array (
        'id' => $article_id,
        'clang' => $clang,
        'article' => $article
      ));

      echo '
								<p>
								  <input class="rex-sbmt" type="submit" name="savemeta" value="' . $I18N->msg("update_metadata") . '"'. rex_accesskey($I18N->msg('update_metadata'), $REX['ACKEY']['SAVE']) . rex_tabindex() .' />
								</p>
	            </div>
	         </fieldset>';

      // ----- EXTENSION POINT
      echo rex_register_extension_point('ART_META_FORM_SECTION', '', array (
        'id' => $article_id,
        'clang' => $clang
      ));

      // ------------------------------------------------------------- SONSTIGES START
      if ($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('article2startpage[]') || $REX_USER->hasPerm('moveArticle[]') || $REX_USER->hasPerm('copyArticle[]') || ($REX_USER->hasPerm('copyContent[]') && count($REX['CLANG']) > 1))
      {

				// --------------------------------------------------- ZUM STARTARTICLE MACHEN START
				if ($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('article2startpage[]'))
				{
					echo '
                <fieldset>
                  <legend class="rex-lgnd">' . $I18N->msg('content_startarticle') . '</legend>
  							  <div class="rex-fldst-wrppr">
									  <p>';

					if ($article->getValue('startpage')==0 && $article->getValue('re_id')==0)
						echo $I18N->msg('content_nottostartarticle');
					else if ($article->getValue('startpage')==1)
						echo $I18N->msg('content_isstartarticle');
					else
						echo '<input class="rex-sbmt" type="submit" name="article2startpage" value="' . $I18N->msg('content_tostartarticle') . '"'. rex_tabindex() .' onclick="return confirm(\'' . $I18N->msg('content_tostartarticle') . '?\')" />';

					echo '
									  </p>
								  </div>
                </fieldset>';
				}
				// --------------------------------------------------- ZUM STARTARTICLE MACHEN END


        // --------------------------------------------------- INHALTE KOPIEREN START
        if (($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('copyContent[]')) && count($REX['CLANG']) > 1)
        {
          $lang_a = new rex_select;
          $lang_a->setId('clang_a');
          $lang_a->setName('clang_a');
          $lang_a->setSize('1');
          $lang_a->setAttribute('tabindex', rex_tabindex(false));
          foreach ($REX['CLANG'] as $key => $val)
          {
            $val = rex_translate($val);
            $lang_a->addOption($val, $key);
          }

          $lang_b = new rex_select;
          $lang_b->setId('clang_b');
          $lang_b->setName('clang_b');
          $lang_b->setSize('1');
          $lang_b->setAttribute('tabindex', rex_tabindex(false));
          foreach ($REX['CLANG'] as $key => $val)
          {
            $val = rex_translate($val);
            $lang_b->addOption($val, $key);
          }

          $lang_a->setSelected(rex_request('clang_a', 'int', null));
          $lang_b->setSelected(rex_request('clang_b', 'int', null));

          echo '
                <fieldset>
                  <legend class="rex-lgnd">' . $I18N->msg('content_submitcopycontent') . '</legend>
  							  <div class="rex-fldst-wrppr">
									  <p>
											<label for="clang_a">' . $I18N->msg('content_contentoflang') . '</label>
											' . $lang_a->get() . '
											<label for="clang_b">' . $I18N->msg('content_to') . '</label>
											' . $lang_b->get() . '
									  </p>
									  <p>
											<input class="rex-sbmt" type="submit" name="copycontent" value="' . $I18N->msg('content_submitcopycontent') . '"'. rex_tabindex() .' onclick="return confirm(\'' . $I18N->msg('content_submitcopycontent') . '?\')" />
									  </p>
								  </div>
                </fieldset>';

        }
        // --------------------------------------------------- INHALTE KOPIEREN ENDE

        // --------------------------------------------------- ARTIKEL VERSCHIEBEN START
        if ($article->getValue('startpage') == 0 && ($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('moveArticle[]')))
        {

          // Wenn Artikel kein Startartikel dann Selectliste darstellen, sonst...
          $move_a = new rex_category_select();
          $move_a->setId('category_id_new');
          $move_a->setName('category_id_new');
          $move_a->setSize('1');
          $move_a->setAttribute('tabindex', rex_tabindex(false));
          $move_a->setSelected($category_id);

          echo '
                <fieldset>
                  <legend class="rex-lgnd">' . $I18N->msg('content_submitmovearticle') . '</legend>
						      <div class="rex-fldst-wrppr">
									  <p>
											<label for="category_id_new">' . $I18N->msg('move_article') . '</label>
											' . $move_a->get() . '
									  </p>
									  <p>
											<input class="rex-sbmt" type="submit" name="movearticle" value="' . $I18N->msg('content_submitmovearticle') . '"'. rex_tabindex() .' onclick="return confirm(\'' . $I18N->msg('content_submitmovearticle') . '?\')" />
									  </p>
								  </div>
                </fieldset>';

        }
        // ------------------------------------------------ ARTIKEL VERSCHIEBEN ENDE

        // -------------------------------------------------- ARTIKEL KOPIEREN START
        if ($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('copyArticle[]'))
        {
          $move_a = new rex_category_select();
          $move_a->setName('category_copy_id_new');
          $move_a->setId('category_copy_id_new');
          $move_a->setSize('1');
          $move_a->setSelected($category_id);
          $move_a->setAttribute('tabindex', rex_tabindex(false));

          echo '
                  <fieldset>
                    <legend class="rex-lgnd">' . $I18N->msg('content_submitcopyarticle') . '</legend>
    							  <div class="rex-fldst-wrppr">
										  <p>
												<label for="category_copy_id_new">' . $I18N->msg('copy_article') . '</label>
												' . $move_a->get() . '
										  </p>
										  <p>
												<input class="rex-sbmt" type="submit" name="copyarticle" value="' . $I18N->msg('content_submitcopyarticle') . '"'. rex_tabindex() .' onclick="return confirm(\'' . $I18N->msg('content_submitcopyarticle') . '?\')" />
										  </p>
									  </div>
                  </fieldset>';

        }
        // --------------------------------------------------- ARTIKEL KOPIEREN ENDE

        // --------------------------------------------------- KATEGORIE/STARTARTIKEL VERSCHIEBEN START
        if ($article->getValue('startpage') == 1 && ($REX_USER->hasPerm('admin[]') || $REX_USER->hasPerm('moveCategory[]')))
        {
          $move_a = new rex_category_select();
          $move_a->setId('category_id_new');
          $move_a->setName('category_id_new');
          $move_a->setSize('1');
          $move_a->setSelected($article_id);
          $move_a->setAttribute('tabindex', rex_tabindex(false));

          echo '
                  <fieldset>
                    <legend class="rex-lgnd">' . $I18N->msg('content_submitmovecategory') . '</legend>
    							  <div class="rex-fldst-wrppr">
										  <p>
												<label for="category_id_new">' . $I18N->msg('move_category') . '</label>
												' . $move_a->get() . '
										  </p>
										  <p>
												<input class="rex-sbmt" type="submit" name="movecategory" value="' . $I18N->msg('content_submitmovecategory') . '"'. rex_tabindex() .' onclick="return confirm(\'' . $I18N->msg('content_submitmovecategory') . '?\')" />
										  </p>
									  </div>
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