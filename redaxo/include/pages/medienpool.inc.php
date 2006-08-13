<?php
/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */ 

// TODOS
// - mediensuche
// - wysiwyg image pfade anschauen und kontrollieren
// - import checken
// - mehrere ebenen in kategorienedit  einbauen

// KOMMT NOCH
// - only types einbauen (only .gif/.pdf/.xxx ..)
// - direkt katjump von modulen aus
// - direktjump bei &action=media_details&file_name=xysd.jpg


// *************************************** WENN HTMLAREA ODER INPUT FELD.. SAVE

// ----- opener_input_field setzen
if(isset($_GET["opener_input_field"])) $_SESSION["media[opener_input_field]"] = $_GET["opener_input_field"];



// *************************************** PERMS
$PERMALL = false;
if ($REX_USER->hasPerm("admin[]") or $REX_USER->hasPerm("media[0]")) $PERMALL = true;





// *************************************** CONFIG

$doctypes = OOMedia::getDocTypes();
$imgtypes = OOMedia::getImageTypes();
$thumbs = true;
$thumbsresize = true;
$backend_mediafolder = str_replace('/redaxo/index.php','',$_SERVER['SCRIPT_NAME']). '/files/';
if (!OOAddon::isAvailable('image_resize')) $thumbsresize = false;





// *************************************** CAT ID IN SESSION SPEICHERN
if (isset($_REQUEST["rex_file_category"]))
{
  $rex_file_category = (int) $_REQUEST["rex_file_category"];
}
elseif (isset($_SESSION["rex_file_category"])) 
{
  $rex_file_category = $_SESSION['rex_file_category']; 
}
else
{
  $rex_file_category = 0;
}

$gc = new sql;
$gc->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'file_category WHERE id='. $rex_file_category);
if ($gc->getRows() != 1) 
{
  $rex_file_category = 0;
  $rex_file_category_name = $I18N->msg('pool_kats_no');
}
else
{
  $rex_file_category_name = $gc->getValue('name');
}

$_SESSION["rex_file_category"] = $rex_file_category;





// *************************************** HEADER

?>
<body id="rex-mpl">

<script type="text/javascript">
<!--

function selectMedia(filename)
{
  <?php 
  if ($opener_input_field!='')
  { 
    echo 'needle = new opener.getObj("'. $opener_input_field .'");
          needle.obj.value = filename;';
  } 
  ?>
  self.close();
}

function addMedialist(filename)
{
  <?php
    if (substr($opener_input_field,0,14) == "REX_MEDIALIST_")
    {
      $id = substr($opener_input_field,14,strlen($opener_input_field));
      echo "opener.addREXMedialist($id,filename);";
    }
  ?>
}

function insertLink(link){
  window.opener.tinyMCE.insertLink( "<?php echo $backend_mediafolder ?>" + link,"_self");
  self.close();
}

function insertImage(src, alt, width, height)
{
  var image = '<img src="<?php echo $backend_mediafolder ?>'+ src +'" alt="'+ alt +'" style="width: '+ width +'; height:'+ height +'" class="rex_image" ismap="ismap" />';
  insertHTML( image);
}

function insertHTML(html) {
  window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
  self.close();
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

// ----- old functions

function openImage(image){
  window.open('index.php?page=medienpool&popimage='+image,'popview','width=123,height=111');
}

function insertHTMLArea(html,filename){
  selection = window.opener.tinyMCE.getContent();
  if(selection!=''){
    html = '<a href=\"/files/'+filename+'\">'+selection+'<\/a>';
  }
  window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
  self.close();
}



//-->
</script>

<?php

$subline = array(
  array('', $I18N->msg('pool_file_list')),
  array('add_file', $I18N->msg('pool_file_insert')),
);

if($PERMALL)
{
  $subline[] = array('categories', $I18N->msg('pool_cat_list'));
  $subline[] = array('sync', $I18N->msg('pool_sync_files'));
}

rex_small_title($I18N->msg('pool_media'), $subline);


// *************************************** request vars

$subpage = rex_request('subpage', 'string');
$msg = rex_request('msg', 'string');
$media_method = rex_request('media_method', 'string');



// *************************************** Content Wrapper

echo '
<div id="rex-wrapper">
  <div id="rex-output">
';

// *************************************** MESSAGES
if ($msg != '')
{
  echo '<p class="rex-warning">'.$msg.'</p>
';
  $msg = '';
}





// *************************************** KATEGORIEN CHECK UND AUSWAHL

// ***** kategorie auswahl
$db = new sql();
$file_cat = $db->get_array('SELECT * FROM '.$REX['TABLE_PREFIX'].'file_category ORDER BY name ASC');

// ***** select bauen
$sel_media = new select;
$sel_media->set_id("rex_file_category");
$sel_media->set_name("rex_file_category");
$sel_media->set_size(1);
$sel_media->set_selected($rex_file_category);
$sel_media->set_selectextra('onchange="this.form.submit();"');

$sel_media->add_option($I18N->msg('pool_kats_no'),"0");
$mediacat_ids = array();
if ($rootCats = OOMediaCategory::getRootCategories())
{
    foreach( $rootCats as $rootCat) {
        rex_mpool_add_mediacat_options( $sel_media, $rootCat, $mediacat_ids);
    }
}

// ***** formular
$cat_out = '<div class="rex-mpl-catslct-frm">
              <form action="index.php" method="post">
                <fieldset>
                  <legend class="rex-lgnd"><span>'. $I18N->msg('pool_select_cat') .'</span></legend>
                  <input type="hidden" name="page" value="medienpool" />
                  <p>
                    <label for="rex_file_category">'. $I18N->msg('pool_kats') .'</label>
                    '. $sel_media->out() .'
                  </p>
                  <noscript>
                    <input type="submit" class="rex-sbmt" value="'. $I18N->msg('pool_search') .'" />
                  </noscript>
                </fieldset>
              </form>
            </div>
';






// *************************************** FUNCTIONS

function rex_mpool_save_media($FILE,$rex_file_category,$FILEINFOS){

  global $REX,$REX_USER;

  $FILENAME = $FILE['name'];
  $FILESIZE = $FILE['size'];
  $FILETYPE = $FILE['type'];
  $NFILENAME = "";
  $message = '';
  
  // ----- neuer filename und extension holen
  $NFILENAME = strtolower(preg_replace("/[^a-zA-Z0-9.\-\$\+]/","_",$FILENAME));
  if (strrpos($NFILENAME,".") != "")
  {
    $NFILE_NAME = substr($NFILENAME,0,strlen($NFILENAME)-(strlen($NFILENAME)-strrpos($NFILENAME,".")));
    $NFILE_EXT  = substr($NFILENAME,strrpos($NFILENAME,"."),strlen($NFILENAME)-strrpos($NFILENAME,"."));
  }else
  {
    $NFILE_NAME = $NFILENAME;
    $NFILE_EXT  = "";
  }

  // ---- ext checken
  $ERROR_EXT = array("php","php3","php4","php5","phtml","pl","asp","aspx","cfm");
  if (in_array($NFILE_EXT,$ERROR_EXT))
  {
    $NFILE_NAME .= $NFILE_EXT;
    $NFILE_EXT = ".txt";
  }

  $NFILENAME = $NFILE_NAME.$NFILE_EXT;

  // ----- datei schon vorhanden -> namen aendern -> _1 ..
  if (file_exists($REX['MEDIAFOLDER']."/$NFILENAME"))
  {
    for ($cf=1;$cf<1000;$cf++)
    {
      $NFILENAME = $NFILE_NAME."_$cf"."$NFILE_EXT";
      if (!file_exists($REX['MEDIAFOLDER']."/$NFILENAME")) break;
    }
  }

  // ----- dateiupload
  $upload = true;
  if(!@move_uploaded_file($FILE['tmp_name'],$REX['MEDIAFOLDER']."/$NFILENAME") )
  {
    if (!@copy($FILE['tmp_name'],$REX['MEDIAFOLDER']."/$NFILENAME"))
    {
      $message .= "move file $FILENAME failed | ";
      $ok = 0;
      $upload = false;
    }
  }

  if($upload)
  {

    chmod($REX['MEDIAFOLDER']."/$NFILENAME", $REX['FILEPERM']);

    // get widht height
    $size = @getimagesize($REX['MEDIAFOLDER']."/$NFILENAME");

    $FILESQL = new sql;
    // $FILESQL->debugsql=1;
    $FILESQL->setTable($REX['TABLE_PREFIX']."file");
    $FILESQL->setValue("filetype",$FILETYPE);
    $FILESQL->setValue("title",$FILEINFOS['title']);
    $FILESQL->setValue("description",$FILEINFOS['description']);
    $FILESQL->setValue("copyright",$FILEINFOS['copyright']);
    $FILESQL->setValue("filename",$NFILENAME);
    $FILESQL->setValue("originalname",$FILENAME);
    $FILESQL->setValue("filesize",$FILESIZE);
    $FILESQL->setValue("width",$size[0]);
    $FILESQL->setValue("height",$size[1]);
    $FILESQL->setValue("category_id",$rex_file_category);
    $FILESQL->setValue("createdate",time());
    $FILESQL->setValue("createuser",$REX_USER->getValue("login"));
    $FILESQL->setValue("updatedate",time());
    $FILESQL->setValue("updateuser",$REX_USER->getValue("login"));
    $FILESQL->insert();
    $ok = 1;
  }

  $RETURN['title'] = $FILEINFOS['title'];
  $RETURN['width'] = $size[0];
  $RETURN['height'] = $size[1];
  $RETURN['type'] = $FILETYPE;
  $RETURN['msg'] = $message;
  $RETURN['ok'] = $ok;
  $RETURN['filename'] = $NFILENAME;

  return $RETURN;
}

function rex_mpool_register_file($physical_filename,$org_filename,$filename,$category_id,$title,$description,$copyright,$filesize,$filetype)
{
  global $REX, $REX_USER;
  
  $abs_file = $REX['MEDIAFOLDER'].'/'. $physical_filename;
  
  if(!file_exists($abs_file))
  {
    return false;
  }
  
  if(empty($filesize))
  {
    $filesize = filesize($abs_file);
  }
  
  if(empty($filetype) && function_exists('mime_content_type'))
  {
    $filetype = mime_content_type($abs_file);
  }
  
  @chmod($abs_file, $REX['FILEPERM']);

  // get widht height
  $size = @getimagesize($abs_file);

  $FILESQL = new sql;
  // $FILESQL->debugsql=1;
  $FILESQL->setTable($REX['TABLE_PREFIX']."file");
  $FILESQL->setValue('filename',$filename);
  $FILESQL->setValue('originalname',$org_filename);
  $FILESQL->setValue('category_id',$category_id);
  $FILESQL->setValue('title',$title);
  $FILESQL->setValue('description',$description);
  $FILESQL->setValue('copyright',$copyright);
  $FILESQL->setValue('filesize',$filesize);
  $FILESQL->setValue('filetype',$filetype);
  $FILESQL->setValue('width',$size[0]);
  $FILESQL->setValue('height',$size[1]);
  
  $FILESQL->setValue('createdate',time());
  $FILESQL->setValue('createuser',$REX_USER->getValue('login'));
  $FILESQL->setValue('updatedate',time());
  $FILESQL->setValue('updateuser',$REX_USER->getValue('login'));
  
  $FILESQL->insert();
  
  return $FILESQL->getError() == '';
}

function rex_mpool_add_mediacat_options( &$select, &$mediacat, &$mediacat_ids, $groupName = '')
{
  if(empty($mediacat)) return;
  $mname = $mediacat->getName();
  $mediacat_ids[] = $mediacat->getId();
  $select->add_option($mname,$mediacat->getId(), $mediacat->getId(),$mediacat->getParentId());
  $childs = $mediacat->getChildren();
  if (is_array($childs))
  {
    foreach ( $childs as $child) {
      rex_mpool_add_mediacat_options( $select, $child, $mediacat_ids, $mname);
    }
  }
}

function rex_mpool_add_mediacat_options_wperm( &$select, &$mediacat, &$mediacat_ids, $groupName = '')
{
  global $PERMALL, $REX_USER;
    if(empty($mediacat)) return;
    $mname = $mediacat->getName();
  $mediacat_ids[] = $mediacat->getId();
  if ($PERMALL || $REX_USER->hasPerm("media[".$mediacat->getId()."]")) $select->add_option($mname,$mediacat->getId(), $mediacat->getId(),$mediacat->getParentId());
  $childs = $mediacat->getChildren();
  if (is_array($childs))
  {
    foreach ( $childs as $child) {
      rex_mpool_add_mediacat_options_wperm( $select, $child, $mediacat_ids, $mname);
    }
  }
}

function rex_mpool_media_form($form_title, $button_title, $rex_file_category, $file_chooser, $close_form)
{
  global $I18N, $subpage, $ftitle, $fdescription, $fcopyright;
  
  $s = '';
  
  $cats_sel = new select;
  $cats_sel->set_style('class="inp100"');
  $cats_sel->set_size(1);
  $cats_sel->set_name('rex_file_category');
  $cats_sel->set_id('rex_file_category');
  $cats_sel->add_option($I18N->msg('pool_kats_no'),"0");
  
  $mediacat_ids = array();
  $rootCat = 0;
  if ($rootCats = OOMediaCategory::getRootCategories())
  {
    foreach( $rootCats as $rootCat) {
      rex_mpool_add_mediacat_options_wperm( $cats_sel, $rootCat, $mediacat_ids);
    }
  }
  $cats_sel->set_selected($rex_file_category);

  if (isset($msg) and $msg != "")
  {
    $s .= '<p class="rex-warning">'. $msg .'</p>';
    $msg = "";
  }

  if (!isset($ftitle)) $ftitle = '';
  if (!isset($fdescription)) $fdescription = '';
  if (!isset($fcopyright)) $fcopyright = '';
  
  $add_file = '';
  if($file_chooser)
  {
    $add_file = '<p>
                   <label for="file_new">'.$I18N->msg('pool_file_file').'</label>
                   <input type="file" id="file_new" name="file_new" size="30" />
                 </p>';
  }
  
  $add_submit = '';
  if ($_SESSION['media[opener_input_field]'] != '') 
  {
    $add_submit = '<input type="submit" class="rex-sbmt" name="saveandexit" value="'.$I18N->msg('pool_file_upload_get').'" />';
  }
  
  $s .= '
  		<div class="rex-mpl-oth">
  		<form action="index.php" method="post" enctype="multipart/form-data">
           <fieldset>
             <legend class="rex-lgnd">'. $form_title .'</legend>
               <input type="hidden" name="page" value="medienpool" />
               <input type="hidden" name="media_method" value="add_file" />
               <input type="hidden" name="subpage" value="'. $subpage .'" />
               <p>
                 <label for="ftitle">'.$I18N->msg('pool_file_title').'</label>
                 <input type="text" size="20" id="ftitle" name="ftitle" value="'.htmlspecialchars(stripslashes($ftitle)).'" />
               </p>
               <p>
                 <label for="rex_file_category">'.$I18N->msg('pool_file_category').'</label>
                 '.$cats_sel->out().'
               </p>
               <p>
                 <label for="fdescription">'.$I18N->msg('pool_file_description').'</label>
                 <textarea cols="50" rows="6" id="fdescription" name="fdescription" >'.(stripslashes($fdescription)).'</textarea>
               </p>
               <p>
                 <label for="fcopyright">'.$I18N->msg('pool_file_copyright').'</label>
                 <input type="text" size="20" id="fcopyright" name="fcopyright" value="'.(stripslashes($fcopyright)).'" />
               </p>
               '. $add_file .'
               <p>
                 <input type="submit" class="rex-sbmt" name="save" value="'.$button_title.'" />
                 '. $add_submit .'
               </p>
           </fieldset>
        ';
  
  if($close_form)
  {
    $s .= '</form></div>'."\n";
  }
  
  return $s;
}

function rex_mpool_upload_form($rex_file_category)
{
  global $I18N;
  
  return rex_mpool_media_form($I18N->msg('pool_file_insert'), $I18N->msg('pool_file_upload'), $rex_file_category, true, true);
}

function rex_mpool_sync_form($rex_file_category)
{
  global $I18N;
  
  return rex_mpool_media_form($I18N->msg('pool_sync_title'), $I18N->msg('pool_sync_button'), $rex_file_category, false, false);
}







// *************************************** SUBPAGE: KATEGORIEN

if ($PERMALL && $subpage == "categories")
{

  $msg = "";
  if ($media_method == 'edit_file_cat')
  {
    $db = new sql;
    $db->setTable($REX['TABLE_PREFIX'].'file_category');
    $db->where('id=$edit_id');
    $db->setValue('name',$cat_name);
    $db->setValue('updatedate',time());
    $db->setValue('updateuser',$REX_USER->getValue('login'));
    $db->update();
    $msg = $I18N->msg('pool_kat_updated',$cat_name);

  } elseif ($media_method == 'delete_file_cat')
  {
    $gf = new sql;
    $gf->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'file WHERE category_id='.$edit_id);
    $gd = new sql;
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
    $db = new sql;
    $db->setTable($REX['TABLE_PREFIX'].'file_category');
    $db->setValue('name',$_REQUEST['catname']);
    $db->setValue('re_id',$_REQUEST['cat_id']);
    $db->setValue('path',$_REQUEST['catpath']);
    $db->setValue('createdate',time());
    $db->setValue('createuser',$REX_USER->getValue('login'));
    $db->setValue('updatedate',time());
    $db->setValue('updateuser',$REX_USER->getValue('login'));
    $db->insert();
    $msg = $I18N->msg('pool_kat_saved',$_REQUEST["catname"]);
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
    echo '<p class="rex-warning">'. $msg .'</p>';
  }
  
  if ($media_method == 'add_cat' || $media_method == 'update_file_cat')
  {
    $add_mode = $media_method == 'add_cat';
    $legend = $add_mode ? $I18N->msg('pool_kat_create_label') : $I18N->msg('pool_kat_edit');
    $method = $add_mode ? 'add_file_cat' : 'edit_file_cat';
     
    echo '
	  <div class="rex-mpl-cat">
      <form action="index.php" method="post">
        <fieldset>
          <legend class="rex-lgnd"><span>'. $legend .'</span></legend>
          <input type="hidden" name="page" value="medienpool" />
          <input type="hidden" name="subpage" value="categories" />
          <input type="hidden" name="media_method" value="'. $method .'" />
          <input type="hidden" name="cat_id" value="'. $cat_id .'" />
          <input type="hidden" name="catpath" value="'. $catpath .'" />
    ';
  }
  
  echo '<table class="rex-table" summary="'.$I18N->msg('pool_kat_summary').'">
          <caption class="rex-hide">'.$I18N->msg('pool_kat_caption').'</caption>
          <colgroup>
            <col width="5%" />
            <col width="5%" />
            <col width="*" />
            <col width="30%" />
          </colgroup>
          <thead>
            <tr>
              <th><a href="'. $link . $cat_id .'&amp;media_method=add_cat"><img src="pics/folder_plus.gif" alt="'. $I18N->msg('pool_kat_create') .'" title="'. $I18N->msg('pool_kat_create') .'" /></a></th>
              <th>'. $I18N->msg('pool_kat_id') .'</th>
              <th>'. $I18N->msg('pool_kat_name') .'</th>
              <th>'. $I18N->msg('pool_kat_function') .'</th>
            </tr>
          </thead>
          <tbody>';
      
  if ($media_method == 'add_cat')
  {
    echo '
      <tr class="rex-trow-actv">
        <td colspan="2"></td>
        <td>
          <span class="rex-hide"><label for="catname">'. $I18N->msg('pool_kat_name') .'</label></span>
          <input type="text" size="10" id="catname" name="catname" value="" />
        </td>
        <td>
          <input type="submit" class="rex-fsubmit" value="'. $I18N->msg('pool_kat_create'). '" />
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
        <tr class="rex-trow-actv">
          <td><img src="pics/folder.gif" alt="'.$OOCat->getName().'" title="'.$OOCat->getName().'" /></td>
          <td>'. $iid .'</td>
          <td>
            <span class="rex-hide"><label for="cat_name">'. $I18N->msg('pool_kat_name') .'</label></span>
            <input type="text" size="10" id="cat_name" name="cat_name" value="'. htmlspecialchars($iname) .'" />
          </td>
          <td>
            <input type="submit" class="rex-fsubmit" value="'. $I18N->msg('pool_kat_update'). '" />
          </td>
        </tr>
      ';
    }else
    {
      echo '<tr>
              <td><a href="'. $link . $iid .'"><img src="pics/folder.gif" alt="'.$OOCat->getName().'" title="'.$OOCat->getName().'" /></a></td>
              <td>'. $iid .'</td>
              <td><a href="'. $link . $iid .'">'.$OOCat->getName().'</a></td>
              <td>
                  <a href="'. $link . $cat_id .'&amp;media_method=update_file_cat&amp;edit_id='. $iid .'">'. $I18N->msg('pool_kat_edit').'</a> | 
                  <a href="'. $link . $cat_id .'&amp;media_method=delete_file_cat&amp;edit_id='. $iid .'" onclick="return confirm(\''. $I18N->msg('delete').' ?\')">'. $I18N->msg('pool_kat_delete') .'</a>
              </td>
            </tr>';
    }
  }
  echo '
      </tbody>
    </table>';

  if ($media_method == 'add_cat' || $media_method == 'update_file_cat')
  {
    echo '
      </fieldset>
    </form>
	</div>
    ';
  }
}




// *************************************** Subpage: ADD FILE

// ----- METHOD ADD FILE
if ($subpage == "add_file" && $media_method == 'add_file'){

  // echo $_FILES['file_new']['name'];

  // function in function.rex_medienpool.inc.php
  if ($_FILES['file_new']['name'] != "" and $_FILES['file_new']['name'] != "none")
  {

    $FILEINFOS['title'] = $ftitle;
    $FILEINFOS['description'] = $fdescription;
    $FILEINFOS['copyright'] = $fcopyright;

    if (!$PERMALL && !$REX_USER->hasPerm("media[$rex_file_category]")) $rex_file_category = 0;

    $return = rex_mpool_save_media($_FILES['file_new'],$rex_file_category,$FILEINFOS);
    $msg = $return['msg'];
    $subpage = "";

    // ----- EXTENSION POINT
    if ($return['ok'] == 1) 
    rex_register_extension_point('MEDIA_ADDED','',$return);

    if (isset($saveandexit) and $saveandexit != "" && $return['ok'] == 1)
    {
      $file_name = $return['filename'];
      $ffiletype = $return['type'];
      $width = $return['width'];
      $height = $return['height'];

      if($_SESSION["media[opener_input_field]"] == 'TINY')
      {
        if (OOMedia::_isImage($file_name))
        {
          $js = "insertImage('$file_name','". htmlspecialchars( $fdescription) ."','$width','$height');";
        }else
        {
          $js = "insertLink('".$file_name."');";
        }

      }elseif($_SESSION["media[opener_input_field]"] != '')
      {
        $js = "selectMedia('".$file_name."');";
        if (substr($_SESSION["media[opener_input_field]"],0,14)=="REX_MEDIALIST_")
        {
          $js = "addMedialist('".$file_name."');";
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
  echo rex_mpool_upload_form($rex_file_category);
}










// *************************************** Subpage: Detail

if ($subpage=="detail" && $media_method == 'delete_file')
{
  $gf = new sql;
  $gf->setQuery("select * from ".$REX['TABLE_PREFIX']."file where file_id='$file_id'");

  if ($gf->getRows()==1)
  {
    if ($PERMALL || $REX_USER->hasPerm("media[".$gf->getValue("category_id")."]"))
    {

      $file_name = $gf->getValue("filename");

      // check if file is in an article slice
      $file_search = '';

      for($c=1;$c<11;$c++){
        $file_search.= "OR file$c='$file_name' ";
        $file_search.= "OR value$c LIKE '%$file_name%' ";
      }

      $file_search = substr($file_search,2);

      // in rex_values ?
      $sql = "SELECT ".$REX['TABLE_PREFIX']."article.name,".$REX['TABLE_PREFIX']."article.id FROM ".$REX['TABLE_PREFIX']."article_slice LEFT JOIN ".$REX['TABLE_PREFIX']."article on ".$REX['TABLE_PREFIX']."article_slice.article_id=".$REX['TABLE_PREFIX']."article.id WHERE ".$file_search." AND ".$REX['TABLE_PREFIX']."article_slice.article_id=".$REX['TABLE_PREFIX']."article.id";
      // $db->setQuery($sql);
      $res1 = $db->get_array($sql);

      // in article metafile ?
      $sql = "SELECT ".$REX['TABLE_PREFIX']."article.name,".$REX['TABLE_PREFIX']."article.id FROM ".$REX['TABLE_PREFIX']."article where file='$file_name'";
      $res2= $db->get_array($sql);

      if(!is_array($res1) and !is_array($res2)){

        $sql = "DELETE FROM ".$REX['TABLE_PREFIX']."file WHERE file_id = '$file_id'";
        $db->query($sql);
        unlink($REX['MEDIAFOLDER']."/".$file_name);
        $msg = $I18N->msg('pool_file_deleted');
        $subpage = "";
      }else{

        $msg = $I18N->msg('pool_file_delete_error_1',"$file_name")." ";
        $msg.= $I18N->msg('pool_file_delete_error_2')."<br>";
        if(is_array($res1))
        {
          foreach($res1 as $var){
            $msg.=" | <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a>";
          }
        }
        if(is_array($res2))
        {
          foreach($res2 as $var){
            $msg.=" | <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a>";
          }
        }
        $msg .= " | ";
        $subpage = "";

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

if ($subpage=="detail" && $media_method == 'edit_file'){

  $gf = new sql;
  $gf->setQuery("select * from ".$REX['TABLE_PREFIX']."file where file_id='$file_id'");
  if ($gf->getRows()==1)
  {

    if ($PERMALL || ( $REX_USER->hasPerm("media[".$gf->getValue("category_id")."]") &&  $REX_USER->hasPerm("media[$rex_file_category]") ) )
    {

      $FILESQL = new sql;
      $FILESQL->setTable($REX['TABLE_PREFIX']."file");
      $FILESQL->where("file_id='$file_id'");
      $FILESQL->setValue("title",$ftitle);
      $FILESQL->setValue("description",$fdescription);
      $FILESQL->setValue("copyright",$fcopyright);
      $FILESQL->setValue("category_id",$rex_file_category);

      $msg = $I18N->msg('pool_file_infos_updated');
      $filename = $gf->getValue("filename");
      $filetype = $gf->getValue("filetype");

      if ($_FILES['file_new']['name'] != "" and $_FILES['file_new']['name'] != "none")
      {

        $ffilename = $_FILES['file_new']['tmp_name'];
        $ffiletype = $_FILES['file_new']['type'];
        $ffilesize = $_FILES['file_new']['size'];

        if ($ffiletype == $filetype || OOMedia::compareImageTypes($ffiletype,$filetype))
        {
          // unlink($REX['MEDIAFOLDER']."/".$filename);
          $upload = false;
          if (!move_uploaded_file($ffilename,$REX['MEDIAFOLDER']."/$filename"))
          {
            if (!@copy($ffilename,$REX['MEDIAFOLDER']."/$filename"))
            {
              $msg .= "<br>".$I18N->msg('pool_file_upload_error');
            }else
            {
              $FILESQL->setValue("filetype",$ffiletype);
              $FILESQL->setValue("originalname",$ffilename);
              $FILESQL->setValue("filesize",$ffilesize);
              $uploaded = true;
            }
          }else
          {
            $FILESQL->setValue("filetype",$ffiletype);
            $FILESQL->setValue("originalname",$ffilename);
            $FILESQL->setValue("filesize",$ffilesize);
            $uploaded = true;
          }

          if (isset($uploaded) and $uploaded)
          {
            $msg .= "<br>".$I18N->msg('pool_file_changed');;
            chmod($REX['MEDIAFOLDER']."/$filename", $REX['FILEPERM']);
            
            // ----- EXTENSION POINT
            rex_register_extension_point('MEDIA_UPDATED','',array("id" => $file_id, "type" => $ffiletype, "filename" => $filename ));
            
          }
        }else
        {
          $msg .= "<br>".$I18N->msg('pool_file_upload_errortype');
        }
      }
      $size = @getimagesize($REX['INCLUDE_PATH']."/../../files/$filename");

      $FILESQL->setValue("updatedate",time());
      $FILESQL->setValue("updateuser",$REX_USER->getValue("login"));
      $FILESQL->update();
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
  $gf = new sql;

  if (isset($file_name) and $file_name != "") $gf->setQuery("select * from ".$REX['TABLE_PREFIX']."file where filename='$file_name'");
  if ($gf->getRows()==1) $file_id = $gf->getValue("file_id");

  $gf->setQuery("select * from ".$REX['TABLE_PREFIX']."file where file_id='$file_id'");
  if ($gf->getRows()==1)
  {

    $TPERM = false;
    if ($PERMALL || $REX_USER->hasPerm("media[".$gf->getValue("category_id")."]")) $TPERM = true;

    echo $cat_out;

    $ftitle = $gf->getValue('title');
    $fdescription = $gf->getValue('description');
    $fcopyright = $gf->getValue('copyright');
    $fname = $gf->getValue('filename');
    $ffiletype = $gf->getValue('filetype');
    $rex_category_id = $gf->getValue('category_id');

    $file_ext = substr(strrchr($fname, '.'),1);
    $icon_src = 'pics/mime_icons/mime-default.gif';
    if (OOMedia::isDocType($file_ext)) $icon_src = 'pics/mime_icons/mime-'.$file_ext.'.gif';
    {
      $thumbnail = '<img src="'. $icon_src .'" alt="" title="" />';
    }

    $ffiletype_ii = OOMedia::_isImage($fname);
    if ($ffiletype_ii)
    {
      $size = @getimagesize($REX['INCLUDE_PATH'].'/../../files/'.$fname);
      $fwidth = $size[0];
      $fheight = $size[1];
      if ($fwidth >199) $rfwidth = 200;
      else $rfwidth = $fwidth;
    }

    $add_image = '';
	$style_width = '';
    if ($ffiletype_ii)
    {
      $imgn = '../files/'. $fname .'" width="'. $rfwidth;
      
      if (!file_exists($REX['INCLUDE_PATH'].'/../../files/'. $fname))
      {
        $imgn = 'pics/mime_icons/mime-error.gif';
      }
//      elseif ($thumbs && $thumbsresize && $ffiletype == 'image/gif' && $rfwidth>199 && !function_exists('imageGIF')) 
//      {
//        $imgn = '../files/'. $fname .'" width="200';
//      }
      else if ($thumbs && $thumbsresize && $rfwidth>199) 
      {
        $imgn = '../index.php?rex_resize=200w__'. $fname;
      }
            
      $add_image = '<div class="rex-mpl-dtl-img">
	  				<p>
						<img src="'. $imgn .'" alt="'. $fdescription .'" title="'. $fdescription .'" />
					</p>
					</div>';
	   $style_width = ' style="width:59.9%; border-right:1px solid #fff;"';	
    }
      
    if ($msg != '')
    {
      echo '<p class="rex-warning">'. $msg .'</p>';
      $msg = '';
    }

    if (!isset($opener_link)) $opener_link = '';
    if($_SESSION["media[opener_input_field]"] == 'TINY')
    {
      if (OOMedia::_isImage($fname))
      {
        $opener_link .= "<a href=javascript:insertImage('$fname','". htmlspecialchars( $fdescription) ."','".$gf->getValue("width")."','".$gf->getValue("height")."');>".$I18N->msg('pool_image_get')."</a> | ";
      }
      $opener_link .= "<a href=javascript:insertLink('".$fname."');>".$I18N->msg('pool_link_get')."</a>";
    }elseif($_SESSION["media[opener_input_field]"] != '')
    {
      $opener_link = "<a href=javascript:selectMedia('".$fname."');>".$I18N->msg('pool_file_get')."</a>";
      if (substr($_SESSION["media[opener_input_field]"],0,14)=="REX_MEDIALIST_")
      {
        $opener_link = "<a href=javascript:addMedialist('".$fname."');>".$I18N->msg('pool_file_get')."</a>";
      }
    }
    
    if($opener_link != '')
    {
      $opener_link = ' | '. $opener_link;
    }

    if ($TPERM)
    {
      $cats_sel = new select;
      $cats_sel->set_style('class="inp100"');
      $cats_sel->set_size(1);
      $cats_sel->set_name('rex_file_category');
      $cats_sel->set_id('rex_file_new_category');
      $cats_sel->add_option($I18N->msg('pool_kats_no'),'0');
      $mediacat_ids = array();
      $rootCat = 0;
      if ($rootCats = OOMediaCategory::getRootCategories())
      {
          foreach( $rootCats as $rootCat) {
              rex_mpool_add_mediacat_options_wperm( $cats_sel, $rootCat, $mediacat_ids);
          }
      }
      $cats_sel->set_selected($rex_file_category);
      
      // $I18N->msg('pool_file_detail')
      
      echo '<p class="rex-hdl">'. $I18N->msg('pool_file_details') . $opener_link.'</p>
	  		<div class="rex-mpl-dtl">
	  			<form action="index.php" method="post" enctype="multipart/form-data">
              	<fieldset>
                	<legend class="rex-lgnd"><span>'. $I18N->msg('pool_file_edit') .'</span></legend>
                	<input type="hidden" name="page" value="medienpool" />
                	<input type="hidden" name="subpage" value="detail" />
                	<input type="hidden" name="media_method" value="edit_file" />
                	<input type="hidden" name="file_id" value="'.$file_id.'" />
					
					<div class="rex-mpl-dtl-wrp">
            		<div class="rex-mpl-dtl-edt"'.$style_width.'>
                    	<p>
                    		<label for="ftitle">Titel</label>
                    		<input type="text" size="20" id="ftitle" name="ftitle" value="'. htmlspecialchars($ftitle) .'" />
                    	</p>
                    	<p>
                      		<label for="rex_file_new_category">'. $I18N->msg('pool_file_category') .'</label>
                      		'. $cats_sel->out() .'
                    	</p>
                    	<p>
                      		<label for="fdescription">'. $I18N->msg('pool_file_description') .'</label>
                      		<textarea cols="50" rows="6" name="fdescription" id="fdescription">'. htmlspecialchars($fdescription) .'</textarea>
                    	</p>
                    	<p>
                      		<label for="fcopyright">'. $I18N->msg('pool_file_copyright') .'</label>
                      		<input type="text" size="20" name="fcopyright" id="fcopyright" value="'. htmlspecialchars($fcopyright).'" />
                    	</p>
                    	<p>
                      		<label for="flink">'. $I18N->msg('pool_filename') .'</label>
                      		<a href="../files/'. $fname .'" id="flink">'. htmlspecialchars($fname) .'</a>
                    	</p>
                    	<p>
                     		<label for="fupdate">'. $I18N->msg('pool_last_update') .'</label>
                      		<span id="fupdate">'. strftime($I18N->msg('datetimeformat'),$gf->getValue("updatedate")) .' ['. $gf->getValue("updateuser") .']</span>
                    	</p>
                    	<p>
                      		<label for="fcreate">'. $I18N->msg('pool_last_update') .'</label>
                     		<span id="fcreate">'. strftime($I18N->msg('datetimeformat'),$gf->getValue("createdate")).' ['.$gf->getValue("createuser") .']</span>
                    	</p>
                    	<p>
                      		<label for="file_new">'. $I18N->msg('pool_file_exchange') .'</label>
                      		<input type="file" id="file_new" name="file_new" size="30" />
                    	</p>
                   	 	<p>
                      		<input type="submit" class="rex-sbmt" value="'. $I18N->msg('pool_file_update') .'" />
                      		<input type="submit" class="rex-sbmt" value="'. $I18N->msg('pool_file_delete') .'" onclick="if(confirm(\''.$I18N->msg('delete').' ?\')){var needle=new getObj(\'media_method\');needle.obj.value=\'delete_file\';}else{return false;}" />
                    	</p>
					</div>
            		
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

      echo '<p class="rex-hdl">'. $I18N->msg('pool_file_details') . $opener_link.'</p>
            <div class="rex-mpl-dtl">
				
				<div class="rex-mpl-dtl-wrp">
            	<div class="rex-mpl-dtl-edt"'.$style_width.'>
				                
                	<p>
                  		<label for="ftitle">Titel</label>
                  		<span id="ftitle">'. htmlspecialchars($ftitle) .'&nbsp;</span>
					</p>
                	<p>
                  		<label for="rex_file_new_category">'. $I18N->msg('pool_file_category') .'</label>
                  		<span id="rex_file_new_category">'. $catname .'&nbsp;</span>
                	</p>
                	<p>
                  		<label for="fdescription">'. $I18N->msg('pool_file_description') .'</label>
                  		<span id="fdescriptle">'. $fdescription .'&nbsp;</span>
                	</p>
                	<p>
                  		<label for="fcopyright">'. $I18N->msg('pool_file_copyright') .'</label>
                  		<span id="fcopyright">'. $fcopyright.'&nbsp;</span>
                	</p>
                	<p>
                  		<label for="flink">'. $I18N->msg('pool_filename') .'</label>
                  		<a href="../files/'. $fname .'" id="flink">'. $fname .'</a>
                	</p>
                	<p>
                  		<label for="fupdate">'. $I18N->msg('pool_last_update') .'</label>
                  		<span id="fupdate">'. strftime($I18N->msg('datetimeformat'),$gf->getValue("updatedate")) .' ['. $gf->getValue("updateuser") .']</span>
                	</p>
                	<p>
                  		<label for="fcreate">'. $I18N->msg('pool_last_update') .'</label>
                  		<span id="fcreate">'. strftime($I18N->msg('datetimeformat'),$gf->getValue("createdate")).' ['.$gf->getValue("createuser") .']</span>
                	</p>
				</div>
				
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
  $db = new sql();
  $db->setQuery('SELECT filename FROM '. $REX['TABLE_PREFIX'].'file');
  $db_files = array();
  
  for($i=0;$i<$db->getRows();$i++)
  {
    $db_files[] = $db->getValue('filename');
    $db->next();
  }
  
//  var_dump($folder_files);
//  var_dump($db_files);
  $diff_files = array_diff($folder_files, $db_files);
  $diff_count = count($diff_files);
//  $diff_files = array();

  if(!empty($_POST['save']) && !empty($_POST['sync_files']))
  {
    if($diff_count > 0)
    {
      foreach($_POST['sync_files'] as $file)
      {
        // hier mit is_int, wg kompatibilität zu PHP < 4.2.0
        if(!is_int($key = array_search($file, $diff_files))) continue;
        
        if(rex_mpool_register_file($file,$file,$file,$rex_file_category,$ftitle,$fdescription,$fcopyright,'',''))
        {
          unset($diff_files[$key]);
        }
      }
      // diff count neu berechnen, da (hoffentlich) diff files in die db geladen wurden 
      $diff_count = count($diff_files);
    }
  }

  echo rex_mpool_sync_form($rex_file_category);
  
  $title = $I18N->msg('pool_sync_affected_files');
  if(!empty($diff_count))
  {
    $title .= ' ('. $diff_count .')';
  }
  echo '<fieldset>';
  echo '<legend class="rex-lgnd">'. $title .'</legend>';
  
  if($diff_count > 0)
  {
    echo '<ul>';
    foreach($diff_files as $file)
    {
      echo '<li>';
      echo '<input class="rex-chckbx" type="checkbox" id="sync_file_'. $file .'" name="sync_files[]" value="'. $file .'" />';
      echo '<label class="rex-lbl-rght" for="sync_file_'. $file .'">'. $file .'</label>';
      echo '</li>';
    }
    
    echo '
		<li>
			<input class="rex-chckbx" type="checkbox" name="checkie" id="checkie" value="0" onClick="SetAllCheckBoxes(\'sync_files[]\',this)"/>
			<label for="checkie">'. $I18N->msg('pool_select_all') .'</label>
		</li>';
		
    echo '</ul>';
    
  }
  else
  {
    echo '<p>';
    echo '<strong>'. $I18N->msg('pool_sync_no_diffs') .'</strong>';
    echo '</p>';
  }
  
  echo '</fieldset>
  		</form>
    </div>';
}



// *************************************** EXTRA FUNCTIONS

if($PERMALL && $media_method == 'updatecat_selectedmedia')
{
  if(is_array($_POST["selectedmedia"])){

    foreach($_POST["selectedmedia"] as $file_id){

      $db = new sql;
      // $db->debugsql = true;
      $db->setTable($REX['TABLE_PREFIX'].'file');
      $db->where("file_id='$file_id'");
      $db->setValue('category_id',$rex_file_category);
      $db->setValue("updatedate",time());
      $db->setValue("updateuser",$REX_USER->getValue("login"));
      $db->update();

      $msg = $I18N->msg('pool_selectedmedia_moved');
    }
  }else{
    $msg = $I18N->msg('pool_selectedmedia_error');
  }
}

if($PERMALL && $media_method == 'delete_selectedmedia')
{

  if(is_array($_POST["selectedmedia"]))
  {
    $msg = "";
    foreach($_POST["selectedmedia"] as $file_id){

      //kopiet von Dateidetails delete_file

      $gf = new sql;
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
        // $db->setQuery($sql);
        $res1 = $db->get_array($sql);

        $sql = "SELECT ".$REX['TABLE_PREFIX']."article.name,".$REX['TABLE_PREFIX']."article.id FROM ".$REX['TABLE_PREFIX']."article where file='$file_name'";
        $res2 = $db->get_array($sql);

        if(!is_array($res1) and !is_array($res2)){

          $sql = "DELETE FROM ".$REX['TABLE_PREFIX']."file WHERE file_id = '$file_id'";
          $db->query($sql);
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
              $msg .=" | <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a>";
            }
          }
          if(is_array($res2))
          {
            foreach($res2 as $var){
              $msg .=" | <a href=../index.php?article_id=$var[id] target=_blank>$var[name]</a>";
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
      $msg .= "<br>";
    }
  }else{
    $msg = $I18N->msg('pool_selectedmedia_error');
  }
}



// *************************************** SUBPAGE: "" -> MEDIEN ANZEIGEN

if ($subpage == '')
{
  $cats_sel = new select;
  $cats_sel->set_style("width:150px;");
  $cats_sel->set_size(1);
  $cats_sel->set_name("rex_file_category");
  $cats_sel->set_id("rex_file_category");
  $cats_sel->add_option($I18N->msg('pool_kats_no'),"0");
  $mediacat_ids = array();
  $rootCat = 0;
  if ($rootCats = OOMediaCategory::getRootCategories())
  {
      foreach( $rootCats as $rootCat) {
          rex_mpool_add_mediacat_options_wperm( $cats_sel, $rootCat, $mediacat_ids);
      }
  }
  $cats_sel->set_selected($rex_file_category);

  echo $cat_out;
  
//                <tr>
//                  <th>'. $I18N->msg('pool_file_list') .'</th>
//                </tr>

  if (isset($msg) and $msg != '')
  {
    print '<p class="rex-warning">'. $msg .'</p>';
    $msg = "";
  }
  
  //deletefilelist und cat change
  print '<div class="rex-mpl-mdn">
  		 <form action="index.php" method="post" enctype="multipart/form-data">
          <fieldset>
            <legend class="rex-lgnd"><span>'. $I18N->msg('pool_selectedmedia') .'</span></legend>
            <input type="hidden" name="page" value="medienpool" />
            <input type="hidden" id="media_method" name="media_method" value="" />
  
            <table class="rex-table" summary="'. $I18N->msg('pool_file_summary', $rex_file_category_name) .'">
              <caption class="rex-hide">'. $I18N->msg('pool_file_caption', $rex_file_category_name) .'</caption>
              <colgroup>
                <col width="5%" />
                <col width="12%" />
                <col width="*" />
                <col width="23%" />
              </colgroup>
              <thead>
                <tr>
                  <th></th>
                  <th>'. $I18N->msg('pool_file_thumbnail') .'</th>
                  <th>'. $I18N->msg('pool_file_info') .' / '. $I18N->msg('pool_file_description') .'</th>
                  <th>'. $I18N->msg('pool_file_functions') .'</th>
                </tr>
              </thead>
              <tbody>';
              

  $files = new sql;
  // $files->debugsql = 1;
  $files->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."file WHERE category_id=".$rex_file_category." ORDER BY updatedate desc");

  for ($i=0;$i<$files->getRows();$i++)
  {

    $file_id =   $files->getValue('file_id');
    $file_name = $files->getValue('filename');
    $file_oname = $files->getValue('originalname');
    $file_title = $files->getValue('title');
    $file_description = $files->getValue('description');
    $file_copyright = $files->getValue('copyright');
    $file_type = $files->getValue('filetype');
    $file_size = $files->getValue('filesize');
    $file_stamp = date('d-M-Y | H:i',$files->getValue('updatedate')).'h';
    $file_updateuser = $files->getValue('updateuser');
    
    $alt = htmlspecialchars($file_description);

    // wenn datei fehlt
    if (!file_exists($REX['INCLUDE_PATH'].'/../../files/'. $file_name))
    {
      $thumbnail = '<img src=pics/mime_icons/mime-error.gif width="44" height="38" alt="file does not exist" />';
    }
    else
    {
      $file_ext = substr(strrchr($file_name,'.'),1);
      $icon_src = 'pics/mime_icons/mime-default.gif';
      if (OOMedia::isDocType($file_ext))
      {
        $icon_src = 'pics/mime_icons/mime-'. $file_ext .'.gif';
      }
      $thumbnail = '<img src="'. $icon_src .'" width="44" height="38" alt="'. $alt .'" />';
      
      if (OOMedia::_isImage($file_name) && $thumbs)
      {
        $thumbnail = '<img src="../files/'.$file_name.'" width="80" alt="'. $alt .'" />';
        if ($thumbsresize) $thumbnail = '<img src="../index.php?rex_resize=80w__'.$file_name.'" width="80" alt="'. $alt .'" />';
      }
    }

    // ----- get file size
    $size = $file_size;
    $file_size = OOMedia::_getFormattedSize($size);

    if ($file_title == '') $file_title = '['.$I18N->msg('pool_file_notitle').']';
    if ($file_description == '') $file_description = '['.$I18N->msg('pool_file_nodescription').']';

    // ----- opener
    $opener_link = '';
    if ($_SESSION['media[opener_input_field]'] == 'TINY')
    {
      if (OOMedia::_isImage($file_name))
      {
        $opener_link .= "<a href=\"javascript:insertImage('$file_name','". str_replace( " ", "&nbsp;", htmlspecialchars( $file_description)) ."','".$files->getValue("width")."','".$files->getValue("height")."');\">".$I18N->msg('pool_image_get')."</a><br>";
      }
      $opener_link .= "<a href=\"javascript:insertLink('".$file_name."');\">".$I18N->msg('pool_link_get')."</a>";

    } elseif ($_SESSION['media[opener_input_field]'] != '')
    {
      $opener_link = "<a href=\"javascript:selectMedia('".$file_name."');\">".$I18N->msg('pool_file_get')."</a>";
      if (substr($_SESSION["media[opener_input_field]"],0,14)=="REX_MEDIALIST_")
      {
        $opener_link = "<a href=\"javascript:addMedialist('".$file_name."');\">".$I18N->msg('pool_file_get')."</a>";
      }
    }

    $ilink = 'index.php?page=medienpool&amp;subpage=detail&amp;file_id='.$file_id.'&amp;rex_file_category='.$rex_file_category;
    
    $add_td = '<td></td>';
    if ($PERMALL) $add_td = '<td><input class="rex-chckbx" type="checkbox" name="selectedmedia[]" value="'.$file_id.'" /></td>';
    
    echo '<tr>
            '. $add_td .'
            <td><a href="'.$ilink.'">'.$thumbnail.'</a></td>
            <td>
                <span><a href="'.$ilink.'">'.$file_title.'</a></span>
                <span>'.$file_name.' ['.$file_size.']<br />'.nl2br(htmlspecialchars($file_description)).'</span>
                <span>'.$file_stamp .' | '. $file_updateuser.'</span>
            </td>
            <td>'.$opener_link.'</td>
         </tr>
';

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
  
  echo '</table>';
  
  // ----- move and delete selected items
  if($PERMALL)
  {
    $add_input = '';
    $filecat = new sql();
    $filecat->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."file_category ORDER BY name ASC LIMIT 1");
    if ($filecat->getRows() > 0)
    {
      $cats_sel->set_id('rex_move_file_dest_category');
      $add_input = '
        <label class="rex-hide" for="rex_move_file_dest_category">'.$I18N->msg('pool_selectedmedia').'</label>
        '. $cats_sel->out() .'
        <input class="rex-sbmt" type="submit" value="'. $I18N->msg('pool_changecat_selectedmedia') .'" onclick="var needle=new getObj(\'media_method\');needle.obj.value=\'updatecat_selectedmedia\';" />';
    }
    $add_input .= '<input class="rex-sbmt" type="submit" value="'.$I18N->msg('pool_delete_selectedmedia').'" onclick="if(confirm(\''.$I18N->msg('delete').' ?\')){var needle=new getObj(\'media_method\');needle.obj.value=\'delete_selectedmedia\';}else{return false;}" />';
    
    echo '
      <div class="rex-mpl-mdn-ftr">
	  	<p>
        <label class="rex-hide" for="checkie">'.$I18N->msg('pool_select_all').'</label>
        <input class="rex-chckbx" type="checkbox" name="checkie" id="checkie" value="0" onClick="SetAllCheckBoxes(\'selectedmedia[]\',this)" />
        '. $add_input .'
		</p>
      </div>';
  }
  
  print '
    </fieldset>
  </form>
  </div>';
}

echo '
  </div>
</div>'."\n\n";

?>