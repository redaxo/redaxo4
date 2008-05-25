<?php

/**
 *
 * @package redaxo4
 * @version $Id: structure.inc.php,v 1.7 2008/03/21 19:45:17 kristinus Exp $
 */

$info = '';
$warning = '';

// --------------------------------------------- EXISTIERT DIESER ZU EDITIERENDE ARTIKEL ?
$edit_id = rex_request('edit_id', 'int');
if ($edit_id)
{
  $thisCat = new rex_sql;
  $thisCat->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'article WHERE id ='.$edit_id.' and clang ='.$clang);

  if ($thisCat->getRows() != 1)
  {
    unset ($edit_id, $thisCat);
  }
}
else
{
  unset ($edit_id);
}

// --------------------------------------------- EXISTIERT DIESER ARTIKEL ?
$article_id = rex_request('article_id', 'int');
if ($article_id)
{
  $thisArt = new rex_sql;
  $thisArt->setQuery('select * from '.$REX['TABLE_PREFIX'].'article where id='.$article_id.' and clang='. $clang);

  if ($thisArt->getRows() != 1)
  {
    unset ($article_id, $thisArt);
  }
}
else
{
  unset ($article_id);
}

$function = rex_request('function', 'string');
$category_id = rex_request('category_id', 'int');

// --------------------------------------------- KATEGORIE PFAD UND RECHTE WERDEN ÜBERPRÜFT

require $REX['INCLUDE_PATH'].'/functions/function_rex_category.inc.php';

// --------------------------------------------- TITLE

rex_title($I18N->msg('title_structure'), $KATout);

$sprachen_add = '&amp;category_id='. $category_id;
require $REX['INCLUDE_PATH'].'/functions/function_rex_languages.inc.php';

// -------------- STATUS_TYPE Map
$catStatusTypes = rex_structure_categoryStatusTypes();
$artStatusTypes = rex_structure_articleStatusTypes();



// --------------------------------------------- KATEGORIE FUNKTIONEN
if (!empty($catedit_function) && $edit_id != '' && $KATPERM)
{
  // --------------------- KATEGORIE EDIT
  $data = array();
  $data['catprior'] = (int) $Position_Category;
  $data['catname'] = $kat_name;
  $data['path'] = $KATPATH;

  list($success, $message) = rex_structure_editCategory($edit_id, $clang, $data);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
elseif (!empty($catdelete_function) && $edit_id != "" && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  // --------------------- KATEGORIE DELETE
  list($success, $message) = rex_structure_deleteCategory($edit_id, $clang);

  if($success)
  {
    $info = $message;
  }
  else
  {
    $warning = $message;
    $function = 'edit';
  }
}
elseif ($function == 'status' && $edit_id != ''
       && ($REX_USER->hasPerm('admin[]') || $KATPERM && $REX_USER->hasPerm('publishArticle[]')))
{
  // --------------------- KATEGORIE STATUS
  list($success, $message) = rex_structure_categoryStatus($edit_id, $clang);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
elseif (!empty($catadd_function) && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  // --------------------- KATEGORIE ADD

  $data = array();
  $data['catprior'] = (int) $Position_New_Category;
  $data['catname'] = $category_name;
  $data['path'] = $KATPATH;

  list($success, $message) = rex_structure_addCategory($category_id, $clang, $data);

  if($success)
    $info = $message;
  else
    $warning = $message;
}

// --------------------------------------------- ARTIKEL FUNKTIONEN

if ($function == 'status_article' && $article_id != ''
    && ($REX_USER->hasPerm('admin[]') || $KATPERM && $REX_USER->hasPerm('publishArticle[]')))
{
  // --------------------- ARTICLE STATUS
  list($success, $message) = rex_strucutre_articleStatus($article_id, $clang);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
// Hier mit !== vergleichen, da 0 auch einen gültige category_id ist (RootArtikel)
elseif (!empty($artadd_function) && $category_id !== '' && $KATPERM &&  !$REX_USER->hasPerm('editContentOnly[]'))
{
  // --------------------- ARTIKEL ADD

  $data = array();
  $data['prior'] = (int) $Position_New_Article;
  $data['category_id'] = $category_id;
  $data['name'] = $article_name;
  $data['template_id'] = $template_id;
  $data['path'] = $KATPATH;

  list($success, $message) = rex_structure_addArticle($category_id, $clang, $data);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
elseif (!empty($artedit_function) && $article_id != '' && $KATPERM)
{
  // --------------------- ARTIKEL EDIT
  $data = array();
  $data['prior'] = (int) $Position_Article;
  $data['name'] = $article_name;
  $data['template_id'] = $template_id;
  $data['category_id'] = $category_id;
  $data['path'] = $KATPATH;

  list($success, $message) = rex_structure_editArticle($article_id, $clang, $data);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
elseif ($function == 'artdelete_function' && $article_id != '' && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  // --------------------- ARTIKEL DELETE
  list($success, $message) = rex_structure_deleteArticle($article_id);

  if($success)
    $info = $message;
  else
    $warning = $message;
}

// --------------------------------------------- KATEGORIE LISTE

if ($warning != "")
  echo rex_warning($warning);

if ($info != "")
  echo rex_info($info);

$cat_name = 'Homepage';
$category = OOCategory::getCategoryById($category_id, $clang);
if($category)
  $cat_name = $category->getName();

$add_category = '';
if ($KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  $add_category = '<a href="index.php?page=structure&amp;category_id='.$category_id.'&amp;function=add_cat&amp;clang='.$clang.'"'. rex_accesskey($I18N->msg('add_category'), $REX['ACKEY']['ADD']) .'><img src="media/folder_plus.gif" alt="'.$I18N->msg("add_category").'" /></a>';
}

$add_header = '';
$add_col = '';
$data_colspan = 4;
if ($REX_USER->hasPerm('advancedMode[]'))
{
  $add_header = '<th class="rex-icon">'.$I18N->msg('header_id').'</th>';
  $add_col = '<col width="40" />';
  $data_colspan = 5;
}

echo rex_register_extension_point('PAGE_STRUCTURE_HEADER', '',
  array(
    'category_id' => $category_id,
    'clang' => $clang
  )
);

echo '
<!-- *** OUTPUT CATEGORIES - START *** -->';

if($function == 'add_cat' || $function == 'edit_cat')
{
  echo '
  <form action="index.php" method="post">
    <fieldset>
      <legend><span class="rex-hide">'.$I18N->msg('add_category') .'</span></legend>
      <input type="hidden" name="page" value="structure" />';

  if ($function == 'edit_cat')
    echo '<input type="hidden" name="edit_id" value="'. $edit_id .'" />';

  echo '
      <input type="hidden" name="category_id" value="'. $category_id .'" />
      <input type="hidden" name="clang" value="'. $clang .'" />';
}

echo '
      <table class="rex-table rex-table-mrgn" summary="'. htmlspecialchars($I18N->msg('structure_categories_summary', $cat_name)) .'">
        <caption class="rex-hide">'. htmlspecialchars($I18N->msg('structure_categories_caption', $cat_name)) .'</caption>
        <colgroup>
          <col width="40" />
          '. $add_col .'
          <col width="*" />
          <col width="40" />
          <col width="315" />
          <col width="153" />
        </colgroup>
        <thead>
          <tr>
            <th class="rex-icon">'. $add_category .'</th>
            '. $add_header .'
            <th>'.$I18N->msg('header_category').'</th>
            <th>'.$I18N->msg('header_priority').'</th>
            <th>'.$I18N->msg('header_edit_category').'</th>
            <th>'.$I18N->msg('header_status').'</th>
          </tr>
        </thead>
        <tbody>';

if ($category_id != 0 && ($category = OOCategory::getCategoryById($category_id)))
{
  echo '<tr>
          <td class="rex-icon">&nbsp;</td>';
  if ($REX_USER->hasPerm('advancedMode[]'))
  {
    echo '<td class="rex-icon">-</td>';
  }

	echo '<td><a href="index.php?page=structure&amp;category_id='. $category->getParentId() .'&amp;clang='. $clang .'">..</a></td>';
	echo '<td>&nbsp;</td>';
	echo '<td>&nbsp;</td>';
	echo '<td>&nbsp;</td>';
	echo '</tr>';

}

// --------------------- KATEGORIE ADD FORM

if ($function == 'add_cat' && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  $add_td = '';
  if ($REX_USER->hasPerm('advancedMode[]'))
  {
    $add_td = '<td class="rex-icon">-</td>';
  }

  $add_buttons = rex_register_extension_point('CAT_FORM_BUTTONS', "" );
  $add_buttons .= '<input type="submit" class="rex-fsubmit" name="catadd_function" value="'. $I18N->msg('add_category') .'"'. rex_accesskey($I18N->msg('add_category'), $REX['ACKEY']['SAVE']) .' />';

  echo '
        <tr class="rex-trow-actv">
          <td class="rex-icon"><img src="media/folder.gif" title="'. $I18N->msg('add_category') .'" alt="'. $I18N->msg('add_category') .'" /></td>
          '. $add_td .'
          <td><input type="text" id="category_name" name="category_name" /></td>
          <td><input type="text" id="Position_New_Category" name="Position_New_Category" value="100" /></td>
          <td>'. $add_buttons .'</td>
          <td class="rex-offline">'. $I18N->msg('status_offline') .'</td>
        </tr>';

  // ----- EXTENSION POINT
  echo rex_register_extension_point('CAT_FORM_ADD', '', array (
      'id' => $category_id,
      'clang' => $clang,
      'data_colspan' => ($data_colspan+1),
		));
}

// --------------------- KATEGORIE LIST

$KAT = new rex_sql;
//$KAT->debugsql = true;
$KAT->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'article WHERE re_id='. $category_id .' AND startpage=1 AND clang='. $clang .' ORDER BY catprior');

for ($i = 0; $i < $KAT->getRows(); $i++)
{
  $i_category_id = $KAT->getValue('id');
  $kat_link = 'index.php?page=structure&amp;category_id='. $i_category_id .'&amp;clang='. $clang;
  $kat_icon_td = '<td class="rex-icon"><a href="'. $kat_link .'"><img src="media/folder.gif" alt="'. htmlspecialchars($KAT->getValue("catname")). '" title="'. htmlspecialchars($KAT->getValue("catname")). '"/></a></td>';

  $kat_status = $catStatusTypes[$KAT->getValue('status')][0];
  $status_class = $catStatusTypes[$KAT->getValue('status')][1];

  if ($KATPERM)
  {

    if ($REX_USER->hasPerm('admin[]') || $KATPERM && $REX_USER->hasPerm('publishCategory[]'))
    {
      $kat_status = '<a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;edit_id='. $i_category_id .'&amp;function=status&amp;clang='. $clang .'" class="'. $status_class .'">'. $kat_status .'</a>';
    }

    if (isset ($edit_id) and $edit_id == $i_category_id and $function == 'edit_cat')
    {
      // --------------------- KATEGORIE EDIT FORM

      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $i_category_id .'</td>';
      }

      $add_buttons = rex_register_extension_point('CAT_FORM_BUTTONS', "" );

      $add_buttons .= '<input type="submit" class="rex-fsubmit" name="catedit_function" value="'. $I18N->msg('save_category'). '"'. rex_accesskey($I18N->msg('save_category'), $REX['ACKEY']['SAVE']) .' />';
      if (!$REX_USER->hasPerm('editContentOnly[]'))
      {
        $add_buttons .= '<input type="submit" class="rex-fsubmit" name="catdelete_function" value="'. $I18N->msg('delete_category'). '"'. rex_accesskey($I18N->msg('delete_category'), $REX['ACKEY']['DELETE']) .' onclick="return confirm(\''. $I18N->msg('delete') .' ?\')" />';
      }


      echo '
        <tr class="rex-trow-actv">
          '. $kat_icon_td .'
          '. $add_td .'
          <td><input type="text" id="kat_name" name="kat_name" value="'. htmlspecialchars($KAT->getValue("catname")). '" /></td>
          <td><input type="text" id="Position_Category" name="Position_Category" value="'. htmlspecialchars($KAT->getValue("catprior")) .'" /></td>
          <td>'. $add_buttons .'</td>
          <td>'. $kat_status .'</td>
        </tr>';

      // ----- EXTENSION POINT
  		echo rex_register_extension_point('CAT_FORM_EDIT', '', array (
      	'id' => $edit_id,
      	'clang' => $clang,
        'category' => $KAT,
      	'catname' => $KAT->getValue('catname'),
      	'catprior' => $KAT->getValue('catprior'),
      	'data_colspan' => ($data_colspan+1),
				));

    }else
    {
      // --------------------- KATEGORIE WITH WRITE

      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $i_category_id .'</td>';
      }

      $add_text = $I18N->msg('category_edit_delete');
      if ($REX_USER->hasPerm('editContentOnly[]'))
      {
        $add_text = $I18N->msg('edit_category');
      }

      echo '
        <tr>
          '. $kat_icon_td .'
          '. $add_td .'
          <td><a href="'. $kat_link .'">'. htmlspecialchars($KAT->getValue("catname")) .'</a></td>
          <td>'. htmlspecialchars($KAT->getValue("catprior")) .'</td>
          <td><a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;edit_id='. $i_category_id .'&amp;function=edit_cat&amp;clang='. $clang .'">'. $add_text .'</a></td>
          <td>'. $kat_status .'</td>
        </tr>';
    }

  }elseif ($REX_USER->hasPerm('csr['. $i_category_id .']') || $REX_USER->hasPerm('csw['. $i_category_id .']'))
  {
      // --------------------- KATEGORIE WITH READ
      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $i_category_id .'</td>';
      }

      echo '
        <tr>
          '. $kat_icon_td .'
          '. $add_td .'
          <td><a href="'. $kat_link .'">'.$KAT->getValue("catname").'</a></td>
          <td>'.htmlspecialchars($KAT->getValue("catprior")).'</td>
          <td>'.$I18N->msg("no_permission_to_edit").'</td>
          <td>'. $kat_status .'</td>
        </tr>';
  }

  $KAT->next();
}

echo '
      </tbody>
    </table>';

if($function == 'add_cat' || $function == 'edit_cat')
{
  $fieldId = $function == 'add_cat' ? 'category_name' :  'kat_name';
  echo '
    <script type="text/javascript">
       <!--
       var needle = new getObj("'. $fieldId .'");
       needle.obj.focus();
       //-->
    </script>
  </fieldset>
</form>';
}

echo '
<!-- *** OUTPUT CATEGORIES - END *** -->
';


// --------------------------------------------- ARTIKEL LISTE

echo '
<!-- *** OUTPUT ARTICLES - START *** -->';

// --------------------- READ TEMPLATES

if ($category_id > -1)
{
  $TEMPLATES = new rex_sql;
  $TEMPLATES->setQuery('select * from '.$REX['TABLE_PREFIX'].'template order by name');
  $TMPL_SEL = new rex_select;
  $TMPL_SEL->setName('template_id');
  $TMPL_SEL->setId('template_id');
  $TMPL_SEL->setSize(1);
  $TMPL_SEL->addOption($I18N->msg('option_no_template'), '0');

  for ($i = 0; $i < $TEMPLATES->getRows(); $i++)
  {
    if ($TEMPLATES->getValue('active') == 1)
    {
      $TMPL_SEL->addOption(rex_translate($TEMPLATES->getValue('name'), null, false), $TEMPLATES->getValue('id'));
    }
    $TEMPLATE_NAME[$TEMPLATES->getValue('id')] = rex_translate($TEMPLATES->getValue('name'));
    $TEMPLATES->next();
  }
  $TEMPLATE_NAME[0] = $I18N->msg('template_default_name');

  // --------------------- ARTIKEL LIST
  $art_add_link = '';
  if ($KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
  {
    $art_add_link = '<a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;function=add_art&amp;clang='. $clang .'"'. rex_accesskey($I18N->msg('article_add'), $REX['ACKEY']['ADD_2']) .'><img src="media/document_plus.gif" alt="'. $I18N->msg('article_add') .'" /></a>';
  }

  $add_head = '';
  $add_col  = '';
  if ($REX_USER->hasPerm('advancedMode[]'))
  {
    $add_head = '<th class="rex-icon">'. $I18N->msg('header_id') .'</th>';
    $add_col  = '<col width="40" />';
  }

  if($function == 'add_art' || $function == 'edit_art')
  {
    echo '
    <form action="index.php" method="post">
      <fieldset>
        <legend><span class="rex-hide">'.$I18N->msg('article_add') .'</span></legend>
        <input type="hidden" name="page" value="structure" />
        <input type="hidden" name="category_id" value="'. $category_id .'" />';
    if (isset($article_id)) echo '<input type="hidden" name="article_id" value="'. $article_id .'" />';
    echo '
        <input type="hidden" name="clang" value="'. $clang .'" />';
  }

  // READ DATA

  $sql = new rex_sql;
  $sql->debugsql = 0;
  $sql->setQuery('SELECT *
        FROM
          '.$REX['TABLE_PREFIX'].'article
        WHERE
          ((re_id='. $category_id .' AND startpage=0) OR (id='. $category_id .' AND startpage=1))
          AND clang='. $clang .'
        ORDER BY
          prior, name');

  echo '
      <table class="rex-table" summary="'. htmlspecialchars($I18N->msg('structure_articles_summary', $cat_name)) .'">
        <caption class="rex-hide">'. htmlspecialchars($I18N->msg('structure_articles_caption', $cat_name)).'</caption>
        <colgroup>
          <col width="40" />
          '. $add_col .'
          <col width="*" />
          <col width="40" />
          <col width="105" />
          <col width="105" />
          <col width="105" />
          <col width="51" />
          <col width="51" />
          <col width="51" />
        </colgroup>
        <thead>
          <tr>
            <th class="rex-icon">'. $art_add_link .'</th>
            '. $add_head .'
            <th>'.$I18N->msg('header_article_name').'</th>
            <th>'.$I18N->msg('header_priority').'</th>
            <th>'.$I18N->msg('header_template').'</th>
            <th>'.$I18N->msg('header_date').'</th>
            <th>'.$I18N->msg('header_article_type').'</th>
            <th colspan="3">'.$I18N->msg('header_status').'</th>
          </tr>
        </thead>
        ';

  // tbody nur anzeigen, wenn später auch inhalt drinnen stehen wird
  if($sql->getRows() > 0 || $function == 'add_art')
  {
    echo '<tbody>
          ';
  }

  // --------------------- ARTIKEL ADD FORM

  if ($function == 'add_art' && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
  {
    if (empty($template_id))
    {
      $sql2 = new rex_sql;
      // $sql2->debugsql = true;
      $sql2->setQuery('SELECT template_id FROM '.$REX['TABLE_PREFIX'].'article WHERE id='. $category_id .' AND clang='. $clang .' AND startpage=1');
      if ($sql2->getRows() == 1)
      {
        $TMPL_SEL->setSelected($sql2->getValue('template_id'));
      }
      else
      {
        $TMPL_SEL->setSelected($REX['DEFAULT_TEMPLATE_ID']);
      }
    }

    $add_td = '';
    if ($REX_USER->hasPerm('advancedMode[]'))
    {
      $add_td = '<td class="rex-icon">-</td>';
    }

    echo '<tr class="rex-trow-actv">
            <td class="rex-icon"><img src="media/document.gif" alt="'.$I18N->msg('article_add') .'" title="'.$I18N->msg('article_add') .'" /></td>
            '. $add_td .'
            <td><input type="text" id="article_name" name="article_name" /></td>
            <td><input type="text" id="Position_New_Article" name="Position_New_Article" value="100" /></td>
            <td>'. $TMPL_SEL->get() .'</td>
            <td>'. rex_formatter :: format(time(), 'strftime', 'date') .'</td>
            <td>'. $I18N->msg("article") .'</td>
            <td colspan="3"><input type="submit" class="rex-fsubmit" name="artadd_function" value="'.$I18N->msg('article_add') .'"'. rex_accesskey($I18N->msg('article_add'), $REX['ACKEY']['SAVE']) .' /></td>
          </tr>
          ';
  }

  // --------------------- ARTIKEL LIST

  for ($i = 0; $i < $sql->getRows(); $i++)
  {

    if ($sql->getValue('startpage') == 1)
    {
      $startpage = $I18N->msg('start_article');
      $icon = 'liste.gif';
    }
    else
    {
      $startpage = $I18N->msg('article');
      $icon = 'document.gif';
    }

    // --------------------- ARTIKEL EDIT FORM

    if ($function == 'edit_art' && isset ($article_id) && $sql->getValue('id') == $article_id && $KATPERM)
    {
      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $sql->getValue("id") .'</td>';
      }

      $TMPL_SEL->setSelected($sql->getValue('template_id'));

      echo '<tr class="rex-trow-actv">
              <td class="rex-icon"><a href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;clang='. $clang .'"><img src="media/'. $icon .'" alt="' .htmlspecialchars($sql->getValue("name")).'" title="' .htmlspecialchars($sql->getValue("name")).'" /></a></td>
              '. $add_td .'
              <td><input type="text" id="article_name" name="article_name" value="' .htmlspecialchars($sql->getValue('name')).'" /></td>
              <td><input type="text" id="Position_Article" name="Position_Article" value="'. htmlspecialchars($sql->getValue('prior')).'" /></td>
              <td>'. $TMPL_SEL->get() .'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
              <td>'. $startpage .'</td>
              <td colspan="3"><input type="submit" class="rex-fsubmit" name="artedit_function" value="'. $I18N->msg('article_save') .'"'. rex_accesskey($I18N->msg('article_save'), $REX['ACKEY']['SAVE']) .' /></td>
            </tr>
            ';

    }elseif ($KATPERM)
    {
      // --------------------- ARTIKEL NORMAL VIEW | EDIT AND ENTER

      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $sql->getValue('id') .'</td>';
      }

      $article_status = $artStatusTypes[$sql->getValue('status')][0];
      $article_class = $artStatusTypes[$sql->getValue('status')][1];

      $add_extra = '';
      if ($sql->getValue('startpage') == 1)
      {
        $add_extra = '<td><span class="rex-strike">'. $I18N->msg('delete') .'</span></td>
                      <td class="'. $article_class .'"><span class="rex-strike">'. $article_status .'</span></td>';
      }else
      {
        if ($REX_USER->hasPerm('admin[]') || $KATPERM && $REX_USER->hasPerm('publishArticle[]'))
        {
            $article_status = '<a href="index.php?page=structure&amp;article_id='. $sql->getValue('id') .'&amp;function=status_article&amp;category_id='. $category_id .'&amp;clang='. $clang .'" class="'. $article_class .'">'. $article_status .'</a>';
        }

        if (!$REX_USER->hasPerm('editContentOnly[]'))
        {
        	$article_delete = '<a href="index.php?page=structure&amp;article_id='. $sql->getValue('id') .'&amp;function=artdelete_function&amp;category_id='. $category_id .'&amp;clang='.$clang .'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg('delete').'</a>';
        }else
        {
        	$article_delete = '<span class="rex-strike">'. $I18N->msg('delete') .'</span>';
        }

        $add_extra = '<td>'. $article_delete .'</td>
                      <td>'. $article_status .'</td>';
      }

      echo '<tr>
              <td class="rex-icon"><a href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;mode=edit&amp;clang='. $clang .'"><img src="media/'. $icon .'" alt="' .htmlspecialchars($sql->getValue('name')).'" title="' .htmlspecialchars($sql->getValue('name')).'" /></a></td>
              '. $add_td .'
              <td><a href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;mode=edit&amp;clang='. $clang .'">'. htmlspecialchars($sql->getValue('name')) . '</a></td>
              <td>'. htmlspecialchars($sql->getValue('prior')) .'</td>
              <td>'. htmlspecialchars($TEMPLATE_NAME[$sql->getValue('template_id')]) .'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
              <td>'. $startpage .'</td>
              <td><a href="index.php?page=structure&amp;article_id='. $sql->getValue('id') .'&amp;function=edit_art&amp;category_id='. $category_id.'&amp;clang='. $clang .'">'. $I18N->msg('change') .'</a></td>
              '. $add_extra .'
            </tr>
            ';

    }else
    {
      // --------------------- ARTIKEL NORMAL VIEW | NO EDIT NO ENTER

      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $sql->getValue('id') .'</td>';
      }

      $art_status = $artStatusTypes[$sql->getValue('status')][0];
      $art_status_class = $artStatusTypes[$sql->getValue('status')][1];

      echo '<tr>
              <td class="rex-icon"><img src="media/'. $icon .'" alt="' .htmlspecialchars($sql->getValue('name')).'" title="' .htmlspecialchars($sql->getValue('name')).'" /></td>
              '. $add_td .'
              <td>'. htmlspecialchars($sql->getValue('name')).'</td>
              <td>'. htmlspecialchars($sql->getValue('prior')).'</td>
              <td>'. $TEMPLATE_NAME[$sql->getValue('template_id')].'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
              <td>'. $startpage .'</td>
              <td><span class="rex-strike">'.$I18N->msg('change').'</span></td>
              <td><span class="rex-strike">'.$I18N->msg('delete').'</span></td>
              <td class="'. $art_status_class .'"><span class="rex-strike">'. $art_status .'</span></td>
            </tr>
            ';
    }

    $sql->counter++;
  }

  // tbody nur anzeigen, wenn später auch inhalt drinnen stehen wird
  if($sql->getRows() > 0 || $function == 'add_art')
  {
    echo '
        </tbody>';
  }

  echo '
      </table>';

  if($function == 'add_art' || $function == 'edit_art')
  {
    echo '
      <script type="text/javascript">
         <!--
         var needle = new getObj("article_name");
         needle.obj.focus();
         //-->
      </script>
    </fieldset>
  </form>';
  }
}


echo '
<!-- *** OUTPUT ARTICLES - END *** -->
';
?>