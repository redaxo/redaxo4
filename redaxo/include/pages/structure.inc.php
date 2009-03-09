<?php

/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// request vars
$category_id = rex_request('category_id', 'rex-category-id');
$article_id  = rex_request('article_id',  'rex-article-id');
$clang       = rex_request('clang',       'rex-clang-id', $REX['START_CLANG_ID']);
$ctype       = rex_request('ctype',       'rex-ctype-id');
$edit_id     = rex_request('edit_id',     'rex-category-id');
$function    = rex_request('function',    'string');

$info = '';
$warning = '';

require $REX['INCLUDE_PATH'].'/functions/function_rex_category.inc.php';
require $REX['INCLUDE_PATH'].'/functions/function_rex_content.inc.php';


// --------------------------------------------- TITLE

rex_title($I18N->msg('title_structure'), $KATout);

$sprachen_add = '&amp;category_id='. $category_id;
require $REX['INCLUDE_PATH'].'/functions/function_rex_languages.inc.php';

// -------------- STATUS_TYPE Map
$catStatusTypes = rex_categoryStatusTypes();
$artStatusTypes = rex_articleStatusTypes();

// --------------------------------------------- KATEGORIE FUNKTIONEN
if (rex_post('catedit_function', 'boolean') && $edit_id != '' && $KATPERM)
{
  // --------------------- KATEGORIE EDIT
  $data = array();
  $data['catprior'] = rex_post('Position_Category', 'int');
  $data['catname']  = rex_post('kat_name', 'string');
  $data['path']     = $KATPATH;

  list($success, $message) = rex_editCategory($edit_id, $clang, $data);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
elseif ($function == 'catdelete_function' && $edit_id != '' && $KATPERM && !$REX['USER']->hasPerm('editContentOnly[]'))
{
  // --------------------- KATEGORIE DELETE
  list($success, $message) = rex_deleteCategoryReorganized($edit_id);

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
        && ($REX['USER']->hasPerm('admin[]') || $KATPERM && $REX['USER']->hasPerm('publishArticle[]')))
{
  // --------------------- KATEGORIE STATUS
  list($success, $message) = rex_categoryStatus($edit_id, $clang);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
elseif (rex_post('catadd_function', 'boolean') && $KATPERM && !$REX['USER']->hasPerm('editContentOnly[]'))
{
  // --------------------- KATEGORIE ADD
  $data = array();
  $data['catprior'] = rex_post('Position_New_Category', 'int');
  $data['catname']  = rex_post('category_name', 'string');
  $data['path']     = $KATPATH;

  list($success, $message) = rex_addCategory($category_id, $data);

  if($success)
    $info = $message;
  else
    $warning = $message;
}

// --------------------------------------------- ARTIKEL FUNKTIONEN

if ($function == 'status_article' && $article_id != ''
    && ($REX['USER']->hasPerm('admin[]') || $KATPERM && $REX['USER']->hasPerm('publishArticle[]')))
{
  // --------------------- ARTICLE STATUS
  list($success, $message) = rex_articleStatus($article_id, $clang);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
// Hier mit !== vergleichen, da 0 auch einen gültige category_id ist (RootArtikel)
elseif (rex_post('artadd_function', 'boolean') && $category_id !== '' && $KATPERM &&  !$REX['USER']->hasPerm('editContentOnly[]'))
{
  // --------------------- ARTIKEL ADD
  $data = array();
  $data['prior']       = rex_post('Position_New_Article', 'int');
  $data['name']        = rex_post('article_name', 'string');
  $data['template_id'] = rex_post('template_id', 'rex-template-id');
  $data['category_id'] = $category_id;
  $data['path']        = $KATPATH;

  list($success, $message) = rex_addArticle($data);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
elseif (rex_post('artedit_function', 'boolean') && $article_id != '' && $KATPERM)
{
  // --------------------- ARTIKEL EDIT
  $data = array();
  $data['prior']       = rex_post('Position_Article', 'int');
  $data['name']        = rex_post('article_name', 'string');
  $data['template_id'] = rex_post('template_id', 'rex-template-id');
  $data['category_id'] = $category_id;
  $data['path']        = $KATPATH;

  list($success, $message) = rex_editArticle($article_id, $clang, $data);

  if($success)
    $info = $message;
  else
    $warning = $message;
}
elseif ($function == 'artdelete_function' && $article_id != '' && $KATPERM && !$REX['USER']->hasPerm('editContentOnly[]'))
{
  // --------------------- ARTIKEL DELETE
  list($success, $message) = rex_deleteArticleReorganized($article_id);

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
if ($KATPERM && !$REX['USER']->hasPerm('editContentOnly[]'))
{
  $add_category = '<a class="rex-i-element rex-i-category-add" href="index.php?page=structure&amp;category_id='.$category_id.'&amp;function=add_cat&amp;clang='.$clang.'"'. rex_accesskey($I18N->msg('add_category'), $REX['ACKEY']['ADD']) .'><span class="rex-i-element-text">'.$I18N->msg("add_category").'</span></a>';
}

$add_header = '';
$add_col = '';
$data_colspan = 4;
if ($REX['USER']->hasPerm('advancedMode[]'))
{
  $add_header = '<th class="rex-small">'.$I18N->msg('header_id').'</th>';
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

  $legend = $I18N->msg('add_category');
  if ($function == 'edit_cat')
    $legend = $I18N->msg('edit_category');
    
  echo '
  <div class="rex-form" id="rex-form-structure-category">
  <form action="index.php" method="post">
    <fieldset>
      <legend><span>'.$legend .'</span></legend>
      <input type="hidden" name="page" value="structure" />';

  if ($function == 'edit_cat')
    echo '<input type="hidden" name="edit_id" value="'. $edit_id .'" />';

  echo '
      <input type="hidden" name="category_id" value="'. $category_id .'" />
      <input type="hidden" name="clang" value="'. $clang .'" />';
}

echo '
      <table class="rex-table rex-table-mrgn" summary="'. htmlspecialchars($I18N->msg('structure_categories_summary', $cat_name)) .'">
        <caption>'. htmlspecialchars($I18N->msg('structure_categories_caption', $cat_name)) .'</caption>
        <colgroup>
          <col width="40" />
          '. $add_col .'
          <col width="*" />
          <col width="40" />
          <col width="51" />
          <col width="50" />
          <col width="50" />
        </colgroup>
        <thead>
          <tr>
            <th class="rex-icon">'. $add_category .'</th>
            '. $add_header .'
            <th>'.$I18N->msg('header_category').'</th>
            <th>'.$I18N->msg('header_priority').'</th>
            <th colspan="3">'.$I18N->msg('header_status').'</th>
          </tr>
        </thead>
        <tbody>';
if ($category_id != 0 && ($category = OOCategory::getCategoryById($category_id)))
{
  echo '<tr>
          <td class="rex-icon">&nbsp;</td>';
  if ($REX['USER']->hasPerm('advancedMode[]'))
  {
    echo '<td class="rex-small">-</td>';
  }

	echo '<td><a href="index.php?page=structure&amp;category_id='. $category->getParentId() .'&amp;clang='. $clang .'">..</a></td>';
	echo '<td>&nbsp;</td>';
	echo '<td colspan="3">&nbsp;</td>';
	echo '</tr>';

}

// --------------------- KATEGORIE ADD FORM

if ($function == 'add_cat' && $KATPERM && !$REX['USER']->hasPerm('editContentOnly[]'))
{
  $add_td = '';
  if ($REX['USER']->hasPerm('advancedMode[]'))
  {
    $add_td = '<td class="rex-small">-</td>';
  }

  $meta_buttons = rex_register_extension_point('CAT_FORM_BUTTONS', "" );
  $add_buttons = '<input type="submit" class="rex-form-submit" name="catadd_function" value="'. $I18N->msg('add_category') .'"'. rex_accesskey($I18N->msg('add_category'), $REX['ACKEY']['SAVE']) .' />';

  $class = 'rex-table-row-activ';
  if($meta_buttons != "")
    $class .= ' rex-has-metainfo';

  echo '
        <tr class="'. $class .'">
          <td class="rex-icon"><span class="rex-i-element rex-i-category-add"><span class="rex-i-element-text">'. $I18N->msg('add_category') .'</span></span></td>
          '. $add_td .'
          <td><input type="text" id="rex-form-field-name" name="category_name" />'. $meta_buttons .'</td>
          <td><input type="text" id="rex-form-field-prior" name="Position_New_Category" value="100" /></td>
          <td colspan="3">'. $add_buttons .'</td>
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
// $KAT->debugsql = true;
$KAT->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'article WHERE re_id='. $category_id .' AND startpage=1 AND clang='. $clang .' ORDER BY catprior');

for ($i = 0; $i < $KAT->getRows(); $i++)
{
  $i_category_id = $KAT->getValue('id');
  $kat_link = 'index.php?page=structure&amp;category_id='. $i_category_id .'&amp;clang='. $clang;
  $kat_icon_td = '<td class="rex-icon"><a class="rex-i-element rex-i-category" href="'. $kat_link .'"><span class="rex-i-element-text">'. htmlspecialchars($KAT->getValue("catname")). '</span></a></td>';

  $kat_status = $catStatusTypes[$KAT->getValue('status')][0];
  $status_class = $catStatusTypes[$KAT->getValue('status')][1];

  if ($KATPERM)
  {
    if ($REX['USER']->hasPerm('admin[]') || $KATPERM && $REX['USER']->hasPerm('publishCategory[]'))
    {
      $kat_status = '<a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;edit_id='. $i_category_id .'&amp;function=status&amp;clang='. $clang .'" class="'. $status_class .'">'. $kat_status .'</a>';
    }

    if (isset ($edit_id) and $edit_id == $i_category_id and $function == 'edit_cat')
    {
      // --------------------- KATEGORIE EDIT FORM
      $add_td = '';
      if ($REX['USER']->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-small">'. $i_category_id .'</td>';
      }

      $meta_buttons = rex_register_extension_point('CAT_FORM_BUTTONS', "" );
      $add_buttons = '<input type="submit" class="rex-form-submit" name="catedit_function" value="'. $I18N->msg('save_category'). '"'. rex_accesskey($I18N->msg('save_category'), $REX['ACKEY']['SAVE']) .' />';

		  $class = 'rex-table-row-activ';
 		 	if($meta_buttons != "")
    		$class .= ' rex-has-metainfo';

      echo '
        <tr class="'. $class .'">
          '. $kat_icon_td .'
          '. $add_td .'
          <td><input type="text" class="rex-form-text" id="rex-form-field-name" name="kat_name" value="'. htmlspecialchars($KAT->getValue("catname")). '" />'. $meta_buttons .'</td>
          <td><input type="text" class="rex-form-text" id="rex-form-field-prior" name="Position_Category" value="'. htmlspecialchars($KAT->getValue("catprior")) .'" /></td>
          <td colspan="3">'. $add_buttons .'</td>
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
		  
    }
    else
    {
      // --------------------- KATEGORIE WITH WRITE

      $add_td = '';
      if ($REX['USER']->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-small">'. $i_category_id .'</td>';
      }
    
			if (!$REX['USER']->hasPerm('editContentOnly[]'))
			{
				$category_delete = '<a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;edit_id='. $i_category_id .'&amp;function=catdelete_function&amp;clang='. $clang .'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg('delete').'</a>';
			}
			else
			{
				$category_delete = '<span class="rex-strike">'. $I18N->msg('delete') .'</span>';
			}

      echo '
        <tr>
          '. $kat_icon_td .'
          '. $add_td .'
          <td><a href="'. $kat_link .'">'. htmlspecialchars($KAT->getValue("catname")) .'</a></td>
          <td>'. htmlspecialchars($KAT->getValue("catprior")) .'</td>
          <td><a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;edit_id='. $i_category_id .'&amp;function=edit_cat&amp;clang='. $clang .'">'. $I18N->msg('change') .'</a></td>
          <td>'. $category_delete .'</td>
          <td>'. $kat_status .'</td>
        </tr>';
    }

  }
  elseif ($REX['USER']->hasPerm('csr['. $i_category_id .']') || $REX['USER']->hasPerm('csw['. $i_category_id .']'))
  {
      // --------------------- KATEGORIE WITH READ
      $add_td = '';
      if ($REX['USER']->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-small">'. $i_category_id .'</td>';
      }

      echo '
        <tr>
          '. $kat_icon_td .'
          '. $add_td .'
          <td><a href="'. $kat_link .'">'.$KAT->getValue("catname").'</a></td>
          <td>'.htmlspecialchars($KAT->getValue("catprior")).'</td>
          <td><span class="rex-strike">'. $I18N->msg('change') .'</span></td>
          <td><span class="rex-strike">'. $I18N->msg('delete') .'</span></td>
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
  echo '
    <script type="text/javascript">
      <!--
      jQuery(function($){
        $("#rex-form-field-name").focus();
      });
      //-->
    </script>
  </fieldset>
</form>
</div>';
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
  $TMPL_SEL->setId('rex-form-template');
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
  if ($KATPERM && !$REX['USER']->hasPerm('editContentOnly[]'))
  {
    $art_add_link = '<a class="rex-i-element rex-i-article-add" href="index.php?page=structure&amp;category_id='. $category_id .'&amp;function=add_art&amp;clang='. $clang .'"'. rex_accesskey($I18N->msg('article_add'), $REX['ACKEY']['ADD_2']) .'><span class="rex-i-element-text">'. $I18N->msg('article_add') .'</span></a>';
  }

  $add_head = '';
  $add_col  = '';
  if ($REX['USER']->hasPerm('advancedMode[]'))
  {
    $add_head = '<th class="rex-small">'. $I18N->msg('header_id') .'</th>';
    $add_col  = '<col width="40" />';
  }

  if($function == 'add_art' || $function == 'edit_art')
  {

    $legend = $I18N->msg('article_add');
    if ($function == 'edit_art')
      $legend = $I18N->msg('article_edit');
    
    echo '
    <div class="rex-form" id="rex-form-structure-article">
    <form action="index.php" method="post">
      <fieldset>
        <legend><span>'.$legend .'</span></legend>
        <input type="hidden" name="page" value="structure" />
        <input type="hidden" name="category_id" value="'. $category_id .'" />';
    if ($article_id != "") echo '<input type="hidden" name="article_id" value="'. $article_id .'" />';
    echo '
        <input type="hidden" name="clang" value="'. $clang .'" />';
  }

  // READ DATA
  $sql = new rex_sql;
  // $sql->debugsql = true;
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
        <caption>'. htmlspecialchars($I18N->msg('structure_articles_caption', $cat_name)).'</caption>
        <colgroup>
          <col width="40" />
          '. $add_col .'
          <col width="*" />
          <col width="40" />
          <col width="200" />
          <col width="115" />
          <col width="51" />
          <col width="50" />
          <col width="50" />
        </colgroup>
        <thead>
          <tr>
            <th class="rex-icon">'. $art_add_link .'</th>
            '. $add_head .'
            <th>'.$I18N->msg('header_article_name').'</th>
            <th>'.$I18N->msg('header_priority').'</th>
            <th>'.$I18N->msg('header_template').'</th>
            <th>'.$I18N->msg('header_date').'</th>
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
  if ($function == 'add_art' && $KATPERM && !$REX['USER']->hasPerm('editContentOnly[]'))
  {
    if($REX['DEFAULT_TEMPLATE_ID'] > 0)
    {
      $TMPL_SEL->setSelected($REX['DEFAULT_TEMPLATE_ID']);
    }
    else
    {
      // template_id vom Startartikel erben
      $sql2 = new rex_sql;
      // $sql2->debugsql = true;
      $sql2->setQuery('SELECT template_id FROM '.$REX['TABLE_PREFIX'].'article WHERE id='. $category_id .' AND clang='. $clang .' AND startpage=1');
      
      if ($sql2->getRows() == 1)
      {
        $TMPL_SEL->setSelected($sql2->getValue('template_id'));
      }
    }

    $add_td = '';
    if ($REX['USER']->hasPerm('advancedMode[]'))
    {
      $add_td = '<td class="rex-small">-</td>';
    }

    echo '<tr class="rex-table-row-activ">
            <td class="rex-icon"><span class="rex-i-element rex-i-article"><span class="rex-i-element-text">'.$I18N->msg('article_add') .'</span></span></td>
            '. $add_td .'
            <td><input type="text" class="rex-form-text" id="rex-form-field-name" name="article_name" /></td>
            <td><input type="text" class="rex-form-text" id="rex-form-field-prior" name="Position_New_Article" value="100" /></td>
            <td>'. $TMPL_SEL->get() .'</td>
            <td>'. rex_formatter :: format(time(), 'strftime', 'date') .'</td>
            <td colspan="3"><input type="submit" class="rex-form-submit" name="artadd_function" value="'.$I18N->msg('article_add') .'"'. rex_accesskey($I18N->msg('article_add'), $REX['ACKEY']['SAVE']) .' /></td>
          </tr>
          ';
  }

  // --------------------- ARTIKEL LIST

  for ($i = 0; $i < $sql->getRows(); $i++)
  {

    if ($sql->getValue('startpage') == 1)
    {
      $class = 'rex-i-article-startpage';
    }
    else
    {
      $class = 'rex-i-article';
    }

    // --------------------- ARTIKEL EDIT FORM

    if ($function == 'edit_art' && $sql->getValue('id') == $article_id && $KATPERM)
    {
      $add_td = '';
      if ($REX['USER']->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-small">'. $sql->getValue("id") .'</td>';
      }

      $TMPL_SEL->setSelected($sql->getValue('template_id'));

      echo '<tr class="rex-table-row-activ">
              <td class="rex-icon"><a class="rex-i-element '.$class.'" href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;clang='. $clang .'"><span class="rex-i-element-text">' .htmlspecialchars($sql->getValue("name")).'</span></a></td>
              '. $add_td .'
              <td><input type="text" class="rex-form-text" id="rex-form-field-name" name="article_name" value="' .htmlspecialchars($sql->getValue('name')).'" /></td>
              <td><input type="text" class="rex-form-text" id="rex-form-field-prior" name="Position_Article" value="'. htmlspecialchars($sql->getValue('prior')).'" /></td>
              <td>'. $TMPL_SEL->get() .'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
              <td colspan="3"><input type="submit" class="rex-form-submit" name="artedit_function" value="'. $I18N->msg('article_save') .'"'. rex_accesskey($I18N->msg('article_save'), $REX['ACKEY']['SAVE']) .' /></td>
            </tr>
            ';

    }elseif ($KATPERM)
    {
      // --------------------- ARTIKEL NORMAL VIEW | EDIT AND ENTER

      $add_td = '';
      if ($REX['USER']->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-small">'. $sql->getValue('id') .'</td>';
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
        if ($REX['USER']->hasPerm('admin[]') || $KATPERM && $REX['USER']->hasPerm('publishArticle[]'))
        {
            $article_status = '<a href="index.php?page=structure&amp;article_id='. $sql->getValue('id') .'&amp;function=status_article&amp;category_id='. $category_id .'&amp;clang='. $clang .'" class="rex-status-link '. $article_class .'">'. $article_status .'</a>';
        }

        if (!$REX['USER']->hasPerm('editContentOnly[]'))
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
              <td class="rex-icon"><a class="rex-i-element '.$class.'" href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;mode=edit&amp;clang='. $clang .'"><span class="rex-i-element-text">' .htmlspecialchars($sql->getValue('name')).'</span></a></td>
              '. $add_td .'
              <td><a href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;mode=edit&amp;clang='. $clang .'">'. htmlspecialchars($sql->getValue('name')) . '</a></td>
              <td>'. htmlspecialchars($sql->getValue('prior')) .'</td>
              <td>'. $TEMPLATE_NAME[$sql->getValue('template_id')] .'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
              <td><a href="index.php?page=structure&amp;article_id='. $sql->getValue('id') .'&amp;function=edit_art&amp;category_id='. $category_id.'&amp;clang='. $clang .'">'. $I18N->msg('change') .'</a></td>
              '. $add_extra .'
            </tr>
            ';

    }else
    {
      // --------------------- ARTIKEL NORMAL VIEW | NO EDIT NO ENTER

      $add_td = '';
      if ($REX['USER']->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-small">'. $sql->getValue('id') .'</td>';
      }

      $art_status = $artStatusTypes[$sql->getValue('status')][0];
      $art_status_class = $artStatusTypes[$sql->getValue('status')][1];

      echo '<tr>
              <td class="rex-icon"><span class="rex-i-element '.$class.'"><span class="rex-i-element-text">' .htmlspecialchars($sql->getValue('name')).'"</span></span></td>
              '. $add_td .'
              <td>'. htmlspecialchars($sql->getValue('name')).'</td>
              <td>'. htmlspecialchars($sql->getValue('prior')).'</td>
              <td>'. $TEMPLATE_NAME[$sql->getValue('template_id')].'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
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
        jQuery(function($){
          $("#rex-form-field-name").focus();
        });
        //-->
      </script>
    </fieldset>
  </form>
  </div>';
  }
}


echo '
<!-- *** OUTPUT ARTICLES - END *** -->
';