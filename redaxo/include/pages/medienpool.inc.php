<?php

/**
 *
 * @package redaxo4
 * @version $Id: medienpool.inc.php,v 1.19 2008/03/26 16:19:38 kills Exp $
 */

// TODOS
// - wysiwyg image pfade anschauen und kontrollieren
// - import checken
// - mehrere ebenen in kategorienedit  einbauen

// KOMMT NOCH
// - only types einbauen (only .gif/.pdf/.xxx ..)


// ----- opener_input_field setzen
$opener_input_field = rex_request('opener_input_field', 'string', rex_session('media[opener_input_field]', 'string'));
rex_set_session('media[opener_input_field]', $opener_input_field);
$args = rex_request('args', 'array');

$arg_url = '';
$arg_fields = '';
foreach($args as $arg_name => $arg_value)
{
  $arg_url .= '&amp;args['. urlencode($arg_name) .']='. urlencode($arg_value);
  $arg_fields .= '<input type="hidden" name="args['. $arg_name .']" value="'. $arg_value .'" />'. "\n";
}


// *************************************** PERMS
$PERMALL = false;
if ($REX_USER->hasPerm('admin[]') or $REX_USER->hasPerm('media[0]')) $PERMALL = true;





// *************************************** CONFIG

$thumbs = true;
$thumbsresize = true;
if (!OOAddon::isAvailable('image_resize')) $thumbsresize = false;





// *************************************** CAT ID IN SESSION SPEICHERN
$sql = new rex_sql();
$file_name = rex_get('file_name', 'string');
if ($file_name != "") $sql->setQuery("select * from ".$REX['TABLE_PREFIX']."file where filename='$file_name'");
if ($sql->getRows()==1)
{
  $file_id = $sql->getValue("file_id");
  $rex_file_category = $sql->getValue("category_id");
}
else
{
  $rex_file_category = rex_request('rex_file_category', 'int', -1);
}

if($rex_file_category == -1)
{
  $rex_file_category = rex_session('media[rex_file_category]', 'int');
}


$gc = new rex_sql;
$gc->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'file_category WHERE id='. $rex_file_category);
if ($gc->getRows() != 1)
{
  $rex_file_category = 0;
  $rex_file_category_name = $I18N->msg('pool_kats_no');
}else
{
  $rex_file_category_name = $gc->getValue('name');
}

rex_set_session('media[rex_file_category]', $rex_file_category);





// *************************************** HEADER

?>

<script type="text/javascript">
<!--

function selectMedia(filename)
{
  <?php
  if ($opener_input_field!='')
  {
    echo 'opener.document.getElementById("'.$opener_input_field.'").value = filename;';
  }
  ?>
  self.close();
}

function selectMedialist(filename)
{
  <?php
    if (substr($opener_input_field,0,14) == 'REX_MEDIALIST_')
    {
      $id = substr($opener_input_field,14,strlen($opener_input_field));
      echo 'var medialist = "REX_MEDIALIST_SELECT_'. $id .'";

						var source = opener.document.getElementById(medialist);
						var sourcelength = source.options.length;

            option = opener.document.createElement("OPTION");
            option.text = filename;
            option.value = filename;

						source.options.add(option, sourcelength);
						opener.writeREXMedialist('. $id .');';

    }
  ?>
}

function SetAllCheckBoxes(FieldName, mthis)
{
  var CheckValue;

  if (mthis.checked) CheckValue=true;
  else CheckValue=false;

  var objCheckBoxes = new getObjArray(FieldName);
  if(!objCheckBoxes) return;

  var countCheckBoxes = objCheckBoxes.length;
  if(!countCheckBoxes) objCheckBoxes.checked = CheckValue;
  else
    // set the check value for all check boxes
    for(var i = 0; i < countCheckBoxes; i++)
      objCheckBoxes[i].checked = CheckValue;
}

function insertImage(src,alt)
{
  window.opener.insertImage('files/' + src, alt);
  self.close();
}

function insertLink(src)
{
  window.opener.insertFileLink('files/' + src);
  self.close();
}

//-->
</script>

<?php

// *************************************** request vars

$subpage = rex_request('subpage', 'string');
$msg = rex_request('msg', 'string');
$media_method = rex_request('media_method', 'string');

$subline = array(
  array('', $I18N->msg('pool_file_list')),
  array('add_file', $I18N->msg('pool_file_insert')),
);

if($PERMALL)
{
  $subline[] = array('categories', $I18N->msg('pool_cat_list'));
  $subline[] = array('sync', $I18N->msg('pool_sync_files'));
}

// Arg Url an Menulinks anhaengen
foreach($subline as $key => $item)
{
  $subline[$key][2] = '';
  $subline[$key][3] = $arg_url;
}

// ----- EXTENSION POINT
$subline = rex_register_extension_point('PAGE_MEDIENPOOL_MENU', $subline,
  array(
    'subpage' => $subpage,
    'category_id' => $rex_file_category
  )
);

$title = $I18N->msg('pool_media');
rex_title($title, $subline);


// *************************************** MESSAGES
if ($msg != '')
{
  echo rex_warning($msg)."\n";
  $msg = '';
}


// *************************************** KATEGORIEN CHECK UND AUSWAHL

// ***** kategorie auswahl
$db = new rex_sql();
$file_cat = $db->getArray('SELECT * FROM '.$REX['TABLE_PREFIX'].'file_category ORDER BY name ASC');

// ***** select bauen
$sel_media = new rex_select;
$sel_media->setId("rex_file_category");
$sel_media->setName("rex_file_category");
$sel_media->setSize(1);
$sel_media->setStyle('class="rex-form-select"');
$sel_media->setSelected($rex_file_category);
$sel_media->setAttribute('onchange', 'this.form.submit();');
$sel_media->addOption($I18N->msg('pool_kats_no'),"0");

$mediacat_ids = array();
if ($rootCats = OOMediaCategory::getRootCategories())
{
    foreach( $rootCats as $rootCat) {
        rex_medienpool_addMediacatOptions( $sel_media, $rootCat, $mediacat_ids);
    }
}

// ----- EXTENSION POINT
echo rex_register_extension_point('PAGE_MEDIENPOOL_HEADER', '',
  array(
    'subpage' => $subpage,
    'category_id' => $rex_file_category
  )
);


// ***** formular
$cat_out = '<div class="rex-form" id="rex-form-medienpool-selectcategory">
              <form action="index.php" method="post">
                <fieldset class="rex-form-col-1">
                  <legend>'. $I18N->msg('pool_select_cat') .'</legend>
                  
                  <div class="rex-form-wrapper">
                    <input type="hidden" name="page" value="medienpool" />
                    '. $arg_fields .'
                    
                    <div class="rex-form-row">
                      <p class="rex-form-select">
                        <label for="rex_file_category">'. $I18N->msg('pool_kats') .'</label>
                        '. $sel_media->get() .'
                        <input class="rex-form-submit" type="submit" value="'. $I18N->msg('pool_search') .'" />
                      </p>
                    </div>
                  </div>
                </fieldset>
              </form>
            </div>
';

// ----- EXTENSION POINT
$cat_out = rex_register_extension_point('MEDIA_LIST_TOOLBAR', $cat_out,
  array(
    'subpage' => $subpage,
    'category_id' => $rex_file_category
  )
);

// *************************************** SUBPAGE: KATEGORIEN

if ($PERMALL && $subpage == "categories")
{
  $edit_id = rex_request('edit_id', 'int');
  $msg = "";
  if ($media_method == 'edit_file_cat')
  {
		$cat_name = rex_request('cat_name', 'string');
    $db = new rex_sql;
    $db->setTable($REX['TABLE_PREFIX'].'file_category');
    $db->setWhere('id='.$edit_id);
    $db->setValue('name',$cat_name);
    $db->addGlobalUpdateFields();

    if($db->update())
    {
      $msg = $I18N->msg('pool_kat_updated',$cat_name);
    }
    else
    {
      $msg = $db->getError();
    }

  } elseif ($media_method == 'delete_file_cat')
  {
    $gf = new rex_sql;
    $gf->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'file WHERE category_id='.$edit_id);
    $gd = new rex_sql;
    $gd->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'file_category WHERE re_id='.$edit_id);
    if ($gf->getRows()==0 && $gd->getRows()==0)
    {
      $gf->setQuery('DELETE FROM '.$REX['TABLE_PREFIX'].'file_category WHERE id='. $edit_id);
      $msg = $I18N->msg('pool_kat_deleted');
    }else
    {
      $msg = $I18N->msg('pool_kat_not_deleted');
    }
  } elseif ($media_method == 'add_file_cat')
  {
    $db = new rex_sql;
    $db->setTable($REX['TABLE_PREFIX'].'file_category');
    $db->setValue('name',rex_request('catname', 'string'));
    $db->setValue('re_id', rex_request('cat_id', 'int'));
    $db->setValue('path', rex_request('catpath', 'string'));
    // TODO Update + Create zugleich?
    $db->addGlobalCreateFields();
    $db->addGlobalUpdateFields();

    if($db->insert())
    {
      $msg .= $I18N->msg('pool_kat_saved', stripslashes(rex_request('catname')));
    }
    else
    {
      $msg .= $db->getError();
    }
  }

  $link = 'index.php?page=medienpool&amp;subpage=categories&amp;cat_id=';

  $textpath = '<li> : <a href="'.$link.'0">Start</a></li>';
  if (!isset($cat_id) or $cat_id == '') $cat_id = 0;
  if ($cat_id == 0 || !($OOCat = OOMediaCategory::getCategoryById($cat_id)))
  {
    $OOCats = OOMediaCategory::getRootCategories();
    $cat_id = 0;
    $catpath = "|";
  }else
  {
    $OOCats = $OOCat->getChildren();

    $paths = explode("|",$OOCat->getPath());

    for ($i=1;$i<count($paths);$i++)
    {
      $iid = current($paths);
      if ($iid != "")
      {
        $icat = OOMediaCategory::getCategoryById($iid);
        $textpath .= '<li> : <a href="'.$link.$iid.'">'.$icat->getName().'</a></li>';
      }
      next($paths);
    }
    $textpath .= '<li> : <a href="'.$link.$cat_id.'">'.$OOCat->getName().'</a></li>';
    $catpath = $OOCat->getPath()."$cat_id|";
  }

  echo '<div class="rex-mpl-cat-pth"><ul><li>'. $I18N->msg('pool_kat_path') .'</li> '. $textpath .'</ul></div>';

  if ($msg!='')
  {
    echo rex_warning($msg);
  }

  if ($media_method == 'add_cat' || $media_method == 'update_file_cat')
  {
    $add_mode = $media_method == 'add_cat';
    $legend = $add_mode ? $I18N->msg('pool_kat_create_label') : $I18N->msg('pool_kat_edit');
    $method = $add_mode ? 'add_file_cat' : 'edit_file_cat';

    echo '
	  <div class="rex-form" id="rex-form-medienpool-categories">
      <form action="index.php" method="post">
        <fieldset class="rex-form-col-1">
          <legend>'. $legend .'</legend>
          
          <div class="rex-form-wrapper">
            <input type="hidden" name="page" value="medienpool" />
            <input type="hidden" name="subpage" value="categories" />
            <input type="hidden" name="media_method" value="'. $method .'" />
            <input type="hidden" name="cat_id" value="'. $cat_id .'" />
            <input type="hidden" name="catpath" value="'. $catpath .'" />
            '. $arg_fields .'
    ';
  }

  echo '<table class="rex-table" summary="'.htmlspecialchars($I18N->msg('pool_kat_summary')).'">
          <caption class="rex-hide">'.$I18N->msg('pool_kat_caption').'</caption>
          <colgroup>
            <col width="40" />
            <col width="40" />
            <col width="*" />
            <col width="77" />
            <col width="76" />
          </colgroup>
          <thead>
            <tr>
              <th class="rex-icon"><a href="'. $link . $cat_id .'&amp;media_method=add_cat"'. rex_accesskey($I18N->msg('pool_kat_create'), $REX['ACKEY']['ADD']) .'><img src="media/folder_plus.gif" alt="'. $I18N->msg('pool_kat_create') .'" title="'. $I18N->msg('pool_kat_create') .'" /></a></th>
              <th class="rex-small">ID</th>
              <th>'. $I18N->msg('pool_kat_name') .'</th>
              <th colspan="2">'. $I18N->msg('pool_kat_function') .'</th>
            </tr>
          </thead>
          <tbody>';

  if ($media_method == 'add_cat')
  {
    echo '
      <tr class="rex-table-row-activ">
        <td class="rex-icon"><img src="media/folder.gif" alt="'.$I18N->msg('pool_kat_create').'" title="'.$I18N->msg('pool_kat_create').'" /></td>
        <td class="rex-small">-</td>
        <td>
          <label class="rex-form-hidden-label" for="rex-form-field-name">'. $I18N->msg('pool_kat_name') .'</label>
          <input class="rex-form-text" type="text" size="10" id="rex-form-field-name" name="catname" value="" />
        </td>
        <td colspan="2">
          <input type="submit" class="rex-form-submit" value="'. $I18N->msg('pool_kat_create'). '"'. rex_accesskey($I18N->msg('pool_kat_create'), $REX['ACKEY']['SAVE']) .' />
        </td>
      </tr>
    ';
  }

  foreach( $OOCats as $OOCat) {

    $iid = $OOCat->getId();
    $iname = $OOCat->getName();

    if ($media_method == 'update_file_cat' && $edit_id == $iid)
    {
      echo '
        <input type="hidden" name="edit_id" value="'. $edit_id .'" />
        <tr class="rex-table-row-activ">
          <td class="rex-icon"><img src="media/folder.gif" alt="'. htmlspecialchars($OOCat->getName()).'" title="'. htmlspecialchars($OOCat->getName()).'" /></td>
          <td class="rex-small">'. $iid .'</td>
          <td>
            <label class="rex-form-hidden-label" for="rex-form-field-name">'. $I18N->msg('pool_kat_name') .'</label>
            <input class="rex-form-text" type="text" id="rex-form-field-name" name="cat_name" value="'. htmlspecialchars($iname) .'" />
          </td>
          <td colspan="2">
            <input type="submit" class="rex-form-submit" value="'. $I18N->msg('pool_kat_update'). '"'. rex_accesskey($I18N->msg('pool_kat_update'), $REX['ACKEY']['SAVE']) .' />
          </td>
        </tr>
      ';
    }else
    {
      echo '<tr>
              <td class="rex-icon"><a href="'. $link . $iid .'"><img src="media/folder.gif" alt="'.htmlspecialchars($OOCat->getName()).'" title="'.htmlspecialchars($OOCat->getName()).'" /></a></td>
              <td class="rex-small">'. $iid .'</td>
              <td><a href="'. $link . $iid .'">'. htmlspecialchars($OOCat->getName()) .'</a></td>
              <td><a href="'. $link . $cat_id .'&amp;media_method=update_file_cat&amp;edit_id='. $iid .'">'. $I18N->msg('pool_kat_edit').'</a></td>
              <td><a href="'. $link . $cat_id .'&amp;media_method=delete_file_cat&amp;edit_id='. $iid .'" onclick="return confirm(\''. $I18N->msg('delete').' ?\')">'. $I18N->msg('pool_kat_delete') .'</a></td>
            </tr>';
    }
  }
  echo '
      </tbody>
    </table>';

  if ($media_method == 'add_cat' || $media_method == 'update_file_cat')
  {
    echo '
        </div>
      </fieldset>
    </form>
	</div>
    ';
  }
}




// *************************************** Subpage: ADD FILE

// ----- METHOD ADD FILE
if ($subpage == 'add_file' && $media_method == 'add_file'){

  // echo $_FILES['file_new']['name'];

  // function in function.rex_medienpool.inc.php
  if ($_FILES['file_new']['name'] != "" and $_FILES['file_new']['name'] != "none")
  {

    $FILEINFOS['title'] = $ftitle;

    if (!$PERMALL && !$REX_USER->hasPerm("media[$rex_file_category]")) $rex_file_category = 0;

    $return = rex_medienpool_saveMedia($_FILES['file_new'],$rex_file_category,$FILEINFOS,$REX_USER->getValue("login"));
    $msg = $return['msg'];
    $subpage = "";

    // ----- EXTENSION POINT
    if ($return['ok'] == 1)
      rex_register_extension_point('MEDIA_ADDED','',$return);

    if (isset($saveandexit) and $saveandexit != "" && $return['ok'] == 1)
    {
      $file_name = $return['filename'];
      $ffiletype = $return['type'];
      $title = $return['title'];

      if($opener_input_field == 'TINYIMG')
      {
        if (OOMedia::_isImage($file_name))
        {
          $js = "insertImage('$file_name','$title');";
        }
      }
      elseif($opener_input_field == 'TINY')
      {
          $js = "insertLink('".$file_name."');";
      }
      elseif($opener_input_field != '')
      {
        if (substr($opener_input_field,0,14)=="REX_MEDIALIST_")
        {
          $js = "selectMedialist('".$file_name."');";
        }
        else
        {
	        $js = "selectMedia('".$file_name."');";
        }
      }

      echo "<script language=javascript>\n";
      echo $js;
      echo "\nself.close();\n";
      echo "</script>";
      exit;
    }

  }else
  {
    // $msg = ;
    $msg = $I18N->msg('pool_file_not_found');
  }
}

// ----- METHOD ADD FORM
if ($subpage == "add_file")
{
  echo rex_medienpool_Uploadform($rex_file_category);
}










// *************************************** Subpage: Detail

if ($subpage=='detail' && rex_post('btn_delete', 'string'))
{

  $file_id = rex_request('file_id', 'int');
  $media = OOMedia::getMediaById($file_id);

  if ($media)
  {
    $file_name = $media->getFileName();
    if ($PERMALL || $REX_USER->hasPerm('media['.$media->getCategoryId().']'))
    {
      $articleUsesMedia = $media->isInUse();
      if($articleUsesMedia === false)
      {
        if($media->delete())
        {
          $msg = $I18N->msg('pool_file_deleted');
        }
        else
        {
          $msg = $I18N->msg('pool_file_delete_error_1', $file_name);
        }
        $subpage = "";
      }
      else
      {
        $msg = array();
        $msg[] = $I18N->msg('pool_file_delete_error_1', $file_name).' '.
                 $I18N->msg('pool_file_delete_error_2').'<br />';
        $msg[] = '<ul>';
        foreach($articleUsesMedia as $art_arr)
        {
          $aid = $art_arr['article_id'];
          $clang = $art_arr['clang'];
          $ooa = OOArticle::getArticleById($aid, $clang);
          $name = $ooa->getName();
          $msg[] ='<li><a href="../index.php?article_id='. $aid .'&amp;clang='. $clang .'" onclick="window.open(this.href); return false;">'. $name .'</a></li>';
        }
        $msg[] = '</ul>';
        $subpage = '';

      }
    }else
    {
      $msg = $I18N->msg('no_permission');
    }
  }else
  {
    $msg = $I18N->msg('pool_file_not_found');
    $subpage = "";
  }
}

if ($subpage=="detail" && rex_post('btn_update', 'string')){

  $gf = new rex_sql;
  $gf->setQuery("select * from ".$REX['TABLE_PREFIX']."file where file_id='$file_id'");
  if ($gf->getRows()==1)
  {
    if ($PERMALL || ($REX_USER->hasPerm('media['.$gf->getValue('category_id').']') && $REX_USER->hasPerm('media['. $rex_file_category .']')))
    {
    	
			$FILEINFOS = array();
			$FILEINFOS["rex_file_category"] = $rex_file_category;
  		$FILEINFOS["file_id"] = $file_id;
	  	$FILEINFOS["title"] = rex_request("ftitle","string");
	  	$FILEINFOS["filetype"] = $gf->getValue('filetype');
	  	$FILEINFOS["filename"] = $gf->getValue('filename');
    	
    	$return = rex_medienpool_updateMedia($_FILES['file_new'],$FILEINFOS,$REX_USER->getValue("login"));
	    $msg = $return['msg'];
	    $subpage = "";

      if($return["ok"] == 1)
      {
        // ----- EXTENSION POINT
 	       // rex_register_extension_point('MEDIA_UPDATED','',array('id' => $file_id, 'type' => $FILEINFOS["filetype"], 'filename' => $FILEINFOS["filename"] ));
 	       rex_register_extension_point('MEDIA_UPDATED','', $return);
      }
    }else
    {
      $msg = $I18N->msg('no_permission');
    }
  }else
  {
    $msg = $I18N->msg('pool_file_not_found');
    $subpage = "";
  }

}

if ($subpage == "detail")
{
  $gf = new rex_sql;
  $gf->setQuery("select * from ".$REX['TABLE_PREFIX']."file where file_id='$file_id'");
  if ($gf->getRows()==1)
  {
    $TPERM = false;
    if ($PERMALL || $REX_USER->hasPerm("media[".$gf->getValue("category_id")."]")) $TPERM = true;

    echo $cat_out;

    $ftitle = $gf->getValue('title');
    $fname = $gf->getValue('filename');
    $ffiletype = $gf->getValue('filetype');
    $ffile_size = $gf->getValue('filesize');
    $ffile_size = OOMedia::_getFormattedSize($ffile_size);
    $rex_file_category = $gf->getValue('category_id');

    $encoded_fname = urlencode($fname);
    $file_ext = substr(strrchr($fname, '.'),1);
    $icon_src = 'media/mime-default.gif';
    if (OOMedia::isDocType($file_ext)) $icon_src = 'media/mime-'.$file_ext.'.gif';
    {
      $thumbnail = '<img src="'. $icon_src .'" alt="'. htmlspecialchars($ftitle) .'" title="'. htmlspecialchars($ftitle) .'" />';
    }

    $ffiletype_ii = OOMedia::_isImage($fname);
    if ($ffiletype_ii)
    {
      $fwidth = $gf->getValue('width');
      $fheight = $gf->getValue('height');
      if($size = @getimagesize($REX['HTDOCS_PATH'].'/files/'.$fname))
      {
        $fwidth = $size[0];
        $fheight = $size[1];
      }

      if ($fwidth >199) $rfwidth = 200;
      else $rfwidth = $fwidth;
    }

    $add_image = '';
    $add_ext_info = '';
	  $style_width = '';
    if ($ffiletype_ii)
    {
      $add_ext_info = '
      <div class="rex-form-row">
        <p class="rex-form-read">
          <label for="fwidth">'. $I18N->msg('pool_img_width') .' / '.$I18N->msg('pool_img_height') .'</label>
          <span class="rex-form-read" id="fwidth">'. $fwidth .' px / '. $fheight .' px</span>
        </p>
      </div>';
      $imgn = '../files/'. $encoded_fname .'" width="'. $rfwidth;

      if (!file_exists($REX['INCLUDE_PATH'].'/../../files/'. $fname))
      {
        $imgn = 'media/mime-error.gif';
      }else if ($thumbs && $thumbsresize && $rfwidth>199)
      {
        $imgn = '../index.php?rex_resize=200a__'. $encoded_fname;
      }

      $add_image = '<div class="rex-medienpool-detail-image">
		  		<p class="rex-me1">
						<img src="'. $imgn .'" alt="'. htmlspecialchars($ftitle) .'" title="'. htmlspecialchars($ftitle) .'" />
					</p>
					</div>';
	   $style_width = ' style="width:64.9%; border-right: 1px solid #FFF;"';
    }

    if ($msg != '')
    {
      echo rex_warning($msg);
      $msg = '';
    }

    if (!isset($opener_link)) $opener_link = '';
    if($opener_input_field == 'TINYIMG')
    {
      if ($ffiletype_ii)
      {
        $opener_link .= '<a href="javascript:insertImage(\''. $encoded_fname .'\',\''.$gf->getValue('title').'\');">'.$I18N->msg('pool_image_get').'</a> | ';
      }

    }
    elseif($opener_input_field == 'TINY')
    {
      $opener_link .= '<a href="javascript:insertLink(\''.$encoded_fname.'\');">'.$I18N->msg('pool_link_get').'</a>';
    }
    elseif($opener_input_field != '')
    {
      $opener_link = '<a href="javascript:selectMedia(\''.$encoded_fname.'\');">'.$I18N->msg('pool_file_get').'</a>';
      if (substr($opener_input_field,0,14)=="REX_MEDIALIST_")
      {
        $opener_link = '<a href="javascript:selectMedialist(\''.$encoded_fname.'\');">'.$I18N->msg('pool_file_get').'</a>';
      }
    }

    if($opener_link != '')
    {
      $opener_link = ' | '. $opener_link;
    }

    if ($TPERM)
    {
      $cats_sel = new rex_select;
      $cats_sel->setStyle('class="rex-form-select"');
      $cats_sel->setSize(1);
      $cats_sel->setName('rex_file_category');
      $cats_sel->setId('rex_file_new_category');
      $cats_sel->addOption($I18N->msg('pool_kats_no'),'0');
      $mediacat_ids = array();
      $rootCat = 0;
      if ($rootCats = OOMediaCategory::getRootCategories())
      {
          foreach( $rootCats as $rootCat) {
              rex_medienpool_addMediacatOptionsWPerm( $cats_sel, $rootCat, $mediacat_ids);
          }
      }
      $cats_sel->setSelected($rex_file_category);

      echo '
        <div class="rex-form" id="rex-form-medienpool-detail">
	  			<form action="index.php" method="post" enctype="multipart/form-data">
          	<fieldset class="rex-form-col-1">
            	<legend>'. $I18N->msg('pool_file_edit') . $opener_link.'</legend>
            	
            	<div class="rex-form-wrapper">
              	<input type="hidden" name="page" value="medienpool" />
              	<input type="hidden" name="subpage" value="detail" />
              	<input type="hidden" name="media_method" value="edit_file" />
              	<input type="hidden" name="file_id" value="'.$file_id.'" />
                '. $arg_fields .'


            		<div class="rex-medienpool-detail-data"'.$style_width.'>
            		  <div class="rex-form-row">
                    <p class="rex-form-text">
                      <label for="ftitle">Titel</label>
                    	<input class="rex-form-text" type="text" size="20" id="ftitle" name="ftitle" value="'. htmlspecialchars($ftitle) .'" />
                    </p>
                  </div>
                  
            		  <div class="rex-form-row">
                    <p class="rex-form-select">
                      <label for="rex_file_new_category">'. $I18N->msg('pool_file_category') .'</label>
                      '. $cats_sel->get() .'
                    </p>
                  </div>';

  // ----- EXTENSION POINT
  echo rex_register_extension_point('MEDIA_FORM_EDIT', '', array ('file_id' => $file_id, 'media' => $gf));

  echo '
                      '. $add_ext_info .'
            		  <div class="rex-form-row">
                    <p class="rex-form-read">
                   		<label for="flink">'. $I18N->msg('pool_filename') .'</label>
                  		<a href="../files/'. $encoded_fname .'" id="flink">'. htmlspecialchars($fname) .'</a> [' . $ffile_size . ']
                   	</p>
                  </div>
                  
            		  <div class="rex-form-row">
                    <p class="rex-form-read">
                    	<label for="fupdate">'. $I18N->msg('pool_last_update') .'</label>
                     	<span id="fupdate">'. strftime($I18N->msg('datetimeformat'),$gf->getValue("updatedate")) .' ['. $gf->getValue("updateuser") .']</span>
                    </p>
                  </div>
                  
            		  <div class="rex-form-row">
                    <p class="rex-form-read">
                     	<label for="fcreate">'. $I18N->msg('pool_created') .'</label>
                   		<span id="fcreate">'. strftime($I18N->msg('datetimeformat'),$gf->getValue("createdate")).' ['.$gf->getValue("createuser") .']</span>
                   	</p>
                  </div>
                  
            		  <div class="rex-form-row">
                    <p class="rex-form-file">
                   		<label for="file_new">'. $I18N->msg('pool_file_exchange') .'</label>
                   		<input class="rex-form-file" type="file" id="file_new" name="file_new" size="20" />
                   	</p>
                  </div>
                  
            		  <div class="rex-form-row">
                    <p class="rex-form-submit">
                   		<input type="submit" class="rex-form-submit" value="'. $I18N->msg('pool_file_update') .'" name="btn_update"'. rex_accesskey($I18N->msg('pool_file_update'), $REX['ACKEY']['SAVE']) .' />
                   		<input type="submit" class="rex-form-submit rex-form-submit-2" value="'. $I18N->msg('pool_file_delete') .'" name="btn_delete"'. rex_accesskey($I18N->msg('pool_file_delete'), $REX['ACKEY']['DELETE']) .' onclick="return confirm(\''.$I18N->msg('delete').' ?\');" />
                  	</p>
                  </div>
                </div><!-- END rex-medienpool-detail-data //-->

            		'. $add_image .'
              </div>
           	</fieldset>
         	</form>
        </div>';
    }
    else
    {
      $catname = $I18N->msg('pool_kats_no');
      $Cat = OOMediaCategory::getCategoryById($rex_file_category);
      if ($Cat) $catname = $Cat->getName();

      if($REX_USER->hasPerm('advancedMode[]'))
      {
        $ftitle .= ' ['. $file_id .']';
        $catname .= ' ['. $rex_file_category .']';
      }

      echo '<h2 class="rex-hl2">'. $I18N->msg('pool_file_details') . $opener_link.'</h2>
            <div class="rex-form" id="rex-form-medienpool-detail">
              <div class="rex-form-wrapper">
                <div class="rex-medienpool-detail-data"'.$style_width.'>

                  <div class="rex-form-row">
                    <p class="rex-form-read">
                        <label for="ftitle">Titel</label>
                        <span class="rex-form-read" id="ftitle">'. htmlspecialchars($ftitle) .'</span>
                    </p>
                  </div>
                  <div class="rex-form-row">
                    <p class="rex-form-read">
                        <label for="rex_file_new_category">'. $I18N->msg('pool_file_category') .'</label>
                        <span class="rex-form-read" id="rex_file_new_category">'. htmlspecialchars($catname) .'</span>
                    </p>
                  </div>
                  <div class="rex-form-row">
                    <p class="rex-form-read">
                        <label for="flink">'. $I18N->msg('pool_filename') .'</label>
                        <a class="rex-form-read" href="../files/'. $encoded_fname .'" id="flink">'. $fname .'</a> [' . $ffile_size . ']
                    </p>
                  </div>
                  <div class="rex-form-row">
                    <p class="rex-form-read">
                        <label for="fupdate">'. $I18N->msg('pool_last_update') .'</label>
                        <span class="rex-form-read" id="fupdate">'. strftime($I18N->msg('datetimeformat'),$gf->getValue("updatedate")) .' ['. $gf->getValue("updateuser") .']</span>
                    </p>
                  </div>
                  <div class="rex-form-row">
                    <p class="rex-form-read">
                        <label for="fcreate">'. $I18N->msg('pool_last_update') .'</label>
                        <span class="rex-form-read" id="fcreate">'. strftime($I18N->msg('datetimeformat'),$gf->getValue("createdate")).' ['.$gf->getValue("createuser") .']</span>
                    </p>
                  </div>
                  
    	  			  </div><!-- END rex-medienpool-detail-data //-->
                '. $add_image .'
                
            	</div>
            </div>';
    }
  }
  else
  {
    $msg = $I18N->msg('pool_file_not_found');
    $subpage = "";
  }
}












// *************************************** SYNC FUNCTIONS


// ----- SYNC DB WITH FILES DIR
if($PERMALL && isset($subpage) and $subpage == 'sync')
{
  // ---- Dateien aus dem Ordner lesen
  $folder_files = array();
  $handle = opendir($REX['MEDIAFOLDER']);
  if($handle) {
    while(($file = readdir($handle)) !== false)
    {
      if(!is_file($REX['MEDIAFOLDER'] .'/'. $file)) continue;

      // Tempfiles nicht synchronisieren
      if(substr($file, 0, strlen($REX['TEMP_PREFIX'])) != $REX['TEMP_PREFIX'])
      {
        $folder_files[] = $file;
      }
    }
    closedir($handle);
  }

  // ---- Dateien aus der DB lesen
  $db = new rex_sql();
  $db->setQuery('SELECT filename FROM '. $REX['TABLE_PREFIX'].'file');
  $db_files = array();

  for($i=0;$i<$db->getRows();$i++)
  {
    $db_files[] = $db->getValue('filename');
    $db->next();
  }

  $diff_files = array_diff($folder_files, $db_files);
  $diff_count = count($diff_files);

  if(!empty($_POST['save']) && !empty($_POST['sync_files']))
  {
    if($diff_count > 0)
    {
      foreach($_POST['sync_files'] as $file)
      {
        // hier mit is_int, wg kompatibilität zu PHP < 4.2.0
        if(!is_int($key = array_search($file, $diff_files))) continue;

        if(rex_medienpool_syncFile($file,$rex_file_category,$ftitle,'',''))
        {
          unset($diff_files[$key]);
        }
      }
      // diff count neu berechnen, da (hoffentlich) diff files in die db geladen wurden
      $diff_count = count($diff_files);
    }
  }

  echo rex_medienpool_Syncform($rex_file_category);

  $title = $I18N->msg('pool_sync_affected_files');
  if(!empty($diff_count))
  {
    $title .= ' ('. $diff_count .')';
  }
  echo '<fieldset class="rex-form-col-1">';
  echo '<legend>'. $title .'</legend>';
  echo '<div class="rex-form-wrapper">';

  if($diff_count > 0)
  {
    echo '<ul>';
    foreach($diff_files as $file)
    {
      echo '<li>';
      if(is_writable($REX['MEDIAFOLDER'] .'/'. $file))
      {
        echo '
              <input class="rex-form-checkbox" type="checkbox" id="sync_file_'. $file .'" name="sync_files[]" value="'. $file .'" />
              <label for="sync_file_'. $file .'">'. $file .'</label>
             ';
      }
      else
      {
        echo $file .' - '.  $I18N->msg('pool_file_not_writable') . "\n";
      }
      echo '</li>';
    }

    echo '<li>
            <input class="rex-form-checkbox" type="checkbox" name="checkie" id="checkie" value="0" onchange="SetAllCheckBoxes(\'sync_files[]\',this)" />
            <label for="checkie">'. $I18N->msg('pool_select_all') .'</label>
          </li>';

    echo '</ul>';
  }
  else
  {
    echo '<p>
            <strong>'. $I18N->msg('pool_sync_no_diffs') .'</strong>
          </p>';
  }

  echo '</div>
        </fieldset>
  		</form>
    </div>';
}



// *************************************** EXTRA FUNCTIONS

if($PERMALL && $media_method == 'updatecat_selectedmedia')
{
	$selectedmedia = rex_post('selectedmedia','array');
	if($selectedmedia[0]>0){

    foreach($selectedmedia as $file_id){

      $db = new rex_sql;
      // $db->debugsql = true;
      $db->setTable($REX['TABLE_PREFIX'].'file');
      $db->setWhere('file_id='.$file_id);
      $db->setValue('category_id',$rex_file_category);
      $db->addGlobalUpdateFields();
      $db->update();

      $msg = $I18N->msg('pool_selectedmedia_moved');
    }
  }else{
    $msg = $I18N->msg('pool_selectedmedia_error');
  }
}

if($PERMALL && $media_method == 'delete_selectedmedia')
{
	$selectedmedia = rex_post("selectedmedia","array");
	if($selectedmedia[0]>0)
  {
    $msg = "";
    foreach($selectedmedia as $file_id)
    {

      $gf = new rex_sql;
      $gf->setQuery("select * from ".$REX['TABLE_PREFIX']."file where file_id='$file_id'");
      if ($gf->getRows()==1)
      {
        $file_name = $gf->getValue("filename");

        // check if file is in an article slice
        $file_search = '';

        for($c=1;$c<11;$c++){
          $file_search.= "OR file$c='$file_name' ";
          $file_search.= "OR value$c LIKE '%$file_name%' ";
        }

        $file_search = substr($file_search,2);
        $sql = "SELECT ".$REX['TABLE_PREFIX']."article.name,".$REX['TABLE_PREFIX']."article.id FROM ".$REX['TABLE_PREFIX']."article_slice LEFT JOIN ".$REX['TABLE_PREFIX']."article on ".$REX['TABLE_PREFIX']."article_slice.article_id=".$REX['TABLE_PREFIX']."article.id WHERE ".$file_search." AND ".$REX['TABLE_PREFIX']."article_slice.article_id=".$REX['TABLE_PREFIX']."article.id";
        $res1 = $db->getArray($sql);

        $sql = "SELECT ".$REX['TABLE_PREFIX']."article.name,".$REX['TABLE_PREFIX']."article.id FROM ".$REX['TABLE_PREFIX']."article where file='$file_name'";
        $res2 = $db->getArray($sql);

        if(count($res1)==0 and count($res2)==0){

          $sql = "DELETE FROM ".$REX['TABLE_PREFIX']."file WHERE file_id = '$file_id'";
          $db->setQuery($sql);
          // fehler unterdrücken, falls die Datei nicht mehr vorhanden ist
          @unlink($REX['MEDIAFOLDER']."/".$file_name);
          $msg .= "\"$file_name\" ".$I18N->msg('pool_file_deleted');
          $subpage = "";
        }else{
          $msg .= $I18N->msg('pool_file_delete_error_1',$file_name)." ";
          $msg .= $I18N->msg('pool_file_delete_error_2')."<br>";
          if(is_array($res1))
          {
            foreach($res1 as $var){
              $msg .= ' | <a href="../index.php?article_id='.$var['id'].'" target="_blank">'.$var['name'].'</a>';
            }
          }
          if(is_array($res2))
          {
            foreach($res2 as $var){
              $msg .= ' | <a href="../index.php?article_id='.$var['id'].'" target="_blank">'.$var['name'].'</a>';
            }
          }
          $msg .= " | ";
          $subpage = "";
        }
      }else
      {
        $msg .= $I18N->msg('pool_file_not_found');
        $subpage = "";
      }
      $msg .= "<br />";
    }
  }else{
    $msg = $I18N->msg('pool_selectedmedia_error');
  }
}



// *************************************** SUBPAGE: "" -> MEDIEN ANZEIGEN

if ($subpage == '')
{
  $cats_sel = new rex_select;
  $cats_sel->setSize(1);
  $cats_sel->setStyle('class="rex-form-select"');
  $cats_sel->setName("rex_file_category");
  $cats_sel->setId("rex_file_category");
  $cats_sel->addOption($I18N->msg('pool_kats_no'),"0");
  $mediacat_ids = array();
  $rootCat = 0;
  if ($rootCats = OOMediaCategory::getRootCategories())
  {
      foreach( $rootCats as $rootCat) {
          rex_medienpool_addMediacatOptionsWPerm( $cats_sel, $rootCat, $mediacat_ids);
      }
  }
  $cats_sel->setSelected($rex_file_category);

  echo $cat_out;

//                <tr>
//                  <th>'. $I18N->msg('pool_file_list') .'</th>
//                </tr>

  if(is_array($msg))
  {
    echo rex_warning_block(implode('', $msg));
  } else if($msg != '')
  {
    echo rex_warning($msg);
    $msg = "";
  }

  if(!empty($args['types']))
    echo rex_info($I18N->msg('pool_file_filter', $args['types']));

  //deletefilelist und cat change
  echo '<div class="rex-form" id="rex-form-medienpool-media">
  		 <form action="index.php" method="post" enctype="multipart/form-data">
          <fieldset class="rex-form-col-1">
            <legend class="rex-form-hidden-legend">'. $I18N->msg('pool_selectedmedia') .'</legend>
            
            <div class="rex-form-wrapper">
              <input type="hidden" name="page" value="medienpool" />
              <input type="hidden" id="media_method" name="media_method" value="" />
              '. $arg_fields .'

              <table class="rex-table" summary="'. htmlspecialchars($I18N->msg('pool_file_summary', $rex_file_category_name)) .'">
                <caption>'. $I18N->msg('pool_file_caption', $rex_file_category_name) .'</caption>
                <colgroup>
                  <col width="40" />
                  <col width="110" />
                  <col width="*" />
                  <col width="153" />
                </colgroup>
                <thead>
                  <tr>
                    <th class="rex-icon">-</th>
                    <th>'. $I18N->msg('pool_file_thumbnail') .'</th>
                    <th>'. $I18N->msg('pool_file_info') .' / '. $I18N->msg('pool_file_description') .'</th>
                    <th>'. $I18N->msg('pool_file_functions') .'</th>
                  </tr>
                </thead>';



  // ----- move and delete selected items
  if($PERMALL)
  {
    $add_input = '';
    $filecat = new rex_sql();
    $filecat->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."file_category ORDER BY name ASC LIMIT 1");
    if ($filecat->getRows() > 0)
    {
      $cats_sel->setId('rex_move_file_dest_category');
      $add_input = '
        <label for="rex_move_file_dest_category">'.$I18N->msg('pool_selectedmedia').'</label>
        '. $cats_sel->get() .'
        <input class="rex-form-submit" type="submit" value="'. $I18N->msg('pool_changecat_selectedmedia') .'" onclick="var needle=new getObj(\'media_method\');needle.obj.value=\'updatecat_selectedmedia\';" />';
    }
    $add_input .= '<input class="rex-form-submit" type="submit" value="'.$I18N->msg('pool_delete_selectedmedia').'"'. rex_accesskey($I18N->msg('pool_delete_selectedmedia'), $REX['ACKEY']['DELETE']) .' onclick="if(confirm(\''.$I18N->msg('delete').' ?\')){var needle=new getObj(\'media_method\');needle.obj.value=\'delete_selectedmedia\';}else{return false;}" />';

    echo '
    	<tfoot>
    	<tr>
    		<td class="rex-icon">
        	<label class="rex-form-hidden-label" for="checkie">'.$I18N->msg('pool_select_all').'</label>
        	<input class="rex-form-checkbox" type="checkbox" name="checkie" id="checkie" value="0" onclick="SetAllCheckBoxes(\'selectedmedia[]\',this)" />
    		</td>
    		<td colspan="3">
    			'.$add_input.'
    		</td>
    	</tr>
    	</tfoot>
    ';
  }



  $where = 'f.category_id='.$rex_file_category;
  if(isset($args['types']))
  {
    $types = array();
    foreach(explode(',',$args['types']) as $type)
    {
      $types[] = 'SUBSTRING(f.filename,LOCATE(".",f.filename)+1)="'. htmlspecialchars($type) .'"';
    }
    $where .= ' AND ('. implode(' OR ', $types) .')';
  }
  $qry = "SELECT * FROM ".$REX['TABLE_PREFIX']."file f WHERE ". $where ." ORDER BY f.updatedate desc";

  // ----- EXTENSION POINT
  $qry = rex_register_extension_point('MEDIA_LIST_QUERY', $qry,
    array(
      'category_id' => $rex_file_category
    )
  );
  $files = new rex_sql;
//   $files->debugsql = 1;
  $files->setQuery($qry);


  print '<tbody>';
  for ($i=0;$i<$files->getRows();$i++)
  {

    $file_id =   $files->getValue('file_id');
    $file_name = $files->getValue('filename');
    $file_oname = $files->getValue('originalname');
    $file_title = $files->getValue('title');
    $file_type = $files->getValue('filetype');
    $file_size = $files->getValue('filesize');
    $file_stamp = rex_formatter::format($files->getValue('updatedate'), "strftime", "datetime");
    $file_updateuser = $files->getValue('updateuser');

    $encoded_file_name = urlencode($file_name);

    // Eine titel Spalte schätzen
    $alt = '';
    foreach(array('title', 'med_description') as $col)
    {
      if($files->hasValue($col) && $files->getValue($col) != '')
      {
        $alt = htmlspecialchars($files->getValue($col));
        break;
      }
    }

    // Eine beschreibende Spalte schätzen
    $desc = '';
    foreach(array('med_description') as $col)
    {
      if($files->hasValue($col) && $files->getValue($col) != '')
      {
        $desc = htmlspecialchars($files->getValue($col));
        break;
      }
    }
    if($desc != '')
      $desc .= '<br />';

    // wenn datei fehlt
    if (!file_exists($REX['INCLUDE_PATH'].'/../../files/'. $file_name))
    {
      $thumbnail = '<img src="media/mime-error.gif" width="44" height="38" alt="file does not exist" />';
    }
    else
    {
      $file_ext = substr(strrchr($file_name,'.'),1);
      $icon_src = 'media/mime-default.gif';
      if (OOMedia::isDocType($file_ext))
      {
        $icon_src = 'media/mime-'. $file_ext .'.gif';
      }
      $thumbnail = '<img src="'. $icon_src .'" width="44" height="38" alt="'. $alt .'" title="'. $alt .'" />';

      if (OOMedia::_isImage($file_name) && $thumbs)
      {
        $thumbnail = '<img src="../files/'.$encoded_file_name.'" width="80" alt="'. $alt .'" title="'. $alt .'" />';
        if ($thumbsresize) $thumbnail = '<img src="../index.php?rex_resize=80a__'.$encoded_file_name.'" alt="'. $alt .'" title="'. $alt .'" />';
      }
    }

    // ----- get file size
    $size = $file_size;
    $file_size = OOMedia::_getFormattedSize($size);

    if ($file_title == '') $file_title = '['.$I18N->msg('pool_file_notitle').']';
    if($REX_USER->hasPerm('advancedMode[]')) $file_title .= ' ['. $file_id .']';

    // ----- opener
    $opener_link = '';
    if ($opener_input_field == 'TINYIMG')
    {
      if (OOMedia::_isImage($file_name))
      {
        $opener_link .= "<a href=\"javascript:insertImage('$file_name','".$files->getValue("title")."')\">".$I18N->msg('pool_image_get')."</a><br>";
      }

    } elseif ($opener_input_field == 'TINY'){
        $opener_link .= "<a href=\"javascript:insertLink('".$encoded_file_name."');\">".$I18N->msg('pool_link_get')."</a>";
    } elseif ($opener_input_field != '')
    {
      $opener_link = "<a href=\"javascript:selectMedia('".$encoded_file_name."');\">".$I18N->msg('pool_file_get')."</a>";
      if (substr($opener_input_field,0,14)=="REX_MEDIALIST_")
      {
        $opener_link = "<a href=\"javascript:selectMedialist('".$encoded_file_name."');\">".$I18N->msg('pool_file_get')."</a>";
      }
    }

    $ilink = 'index.php?page=medienpool&amp;subpage=detail&amp;file_id='.$file_id.'&amp;rex_file_category='.$rex_file_category. $arg_url;

    $add_td = '<td></td>';
    if ($PERMALL) $add_td = '<td class="rex-icon"><input class="rex-form-checkbox" type="checkbox" name="selectedmedia[]" value="'.$file_id.'" /></td>';

    echo '<tr>
            '. $add_td .'
            <td class="rex-thumbnail"><a href="'.$ilink.'">'.$thumbnail.'</a></td>
            <td>
                <p class="rex-tx4">
                  <a href="'.$ilink.'">'.htmlspecialchars($file_title).'</a>
                </p>
                <p class="rex-tx4">
                  '. $desc .'
                  <span class="rex-suffix">'.htmlspecialchars($file_name).' ['.$file_size.']</span>
                </p>
                <p class="rex-tx1">
                  '.$file_stamp .' | '. htmlspecialchars($file_updateuser).'
                </p>
            </td>
            <td>';

    echo rex_register_extension_point('MEDIA_LIST_FUNCTIONS',$opener_link,
    	array(
				"file_id" => $files->getValue('file_id'),
				"file_name" => $files->getValue('filename'),
				"file_oname" => $files->getValue('originalname'),
				"file_title" => $files->getValue('title'),
				"file_type" => $files->getValue('filetype'),
				"file_size" => $files->getValue('filesize'),
				"file_stamp" => $files->getValue('updatedate'),
				"file_updateuser" => $files->getValue('updateuser')
			)
		);

    echo '</td>
         </tr>';

    $files->next();
  } // endforeach

  // ----- no items found
  if ($files->getRows()==0)
  {
    echo '
      <tr>
        <td></td>
        <td colspan="3">'.$I18N->msg('pool_nomediafound').'</td>
      </tr>';
  }

  print '
      </tbody>
      </table>
      </div>
    </fieldset>
  </form>
  </div>';
}


?>