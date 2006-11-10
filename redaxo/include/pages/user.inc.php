<?php
/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */ 

/*

----------------------------- todos

sprachen zugriff
englisch / deutsch / ...
clang

allgemeine zugriffe (array + addons)
  mediapool[]templates[] ...

optionen
  advancedMode[]

zugriff auf folgende categorien
  csw[2] write 
  csr[2] read

mulselect zugriff auf mediapool
  media[2]

mulselect module
- liste der module
  module[2]module[3]

*/

$user_id = rex_request('user_id', 'int');
if ($user_id != 0)
{
  $sql = new rex_sql;
  $sql->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'user WHERE user_id = '. $user_id .' LIMIT 2');
  if ($sql->getRows()!= 1) unset($user_id);
}

// Allgemeine Permissions setzen
$sel_all = new rex_select;
$sel_all->multiple(1);
$sel_all->set_style('class=rex-perm-fselect');
$sel_all->set_size(10);
$sel_all->set_name("userperm_all[]");
$sel_all->set_id("userperm_all");
$sel_all->add_array_options($REX['PERM'],false);


// Erweiterte Permissions setzen
$sel_ext = new rex_select;
$sel_ext->multiple(1);
$sel_ext->set_style('class=rex-perm-fselect');
$sel_ext->set_size(10);
$sel_ext->set_name("userperm_ext[]");
$sel_ext->set_id("userperm_ext");
$sel_ext->add_array_options($REX['EXTPERM'],false);

// zugriff auf categorien
$sel_cat = new rex_select;
$sel_cat->multiple(1);
$sel_cat->set_style('class=rex-perm-fselect');
$sel_cat->set_size(20);
$sel_cat->set_name("userperm_cat[]");
$sel_cat->set_id("userperm_cat");
$cat_ids = array();

if ($rootCats = OOCategory::getRootCategories())
{
  foreach( $rootCats as $rootCat) {
    add_cat_options( $sel_cat, $rootCat, $cat_ids);
  }
}

function add_cat_options( &$select, &$cat, &$cat_ids, $groupName = '')
{
  if (empty($cat))
  {
    return;
  }
  $cat_ids[] = $cat->getId();
  $select->add_option($cat->getName(),$cat->getId(), $cat->getId(),$cat->getParentId());
  $childs = $cat->getChildren();
  if (is_array($childs))
  {
    foreach ( $childs as $child) {
      add_cat_options( $select, $child, $cat_ids, $cat->getName());
    }
  }
}

// zugriff auf mediacategorien
$sel_media = new rex_select;
$sel_media->multiple(1);
$sel_media->set_style('class=rex-perm-fselect');
$sel_media->set_size(20);
$sel_media->set_name("userperm_media[]");
$sel_media->set_id("userperm_media");
$mediacat_ids = array();

if ($rootCats = OOMediaCategory::getRootCategories())
{
  foreach ( $rootCats as $rootCat) {
    add_mediacat_options( $sel_media, $rootCat, $mediacat_ids);
  }
}

function add_mediacat_options( &$select, &$mediacat, &$mediacat_ids, $groupName = '')
{
  if (empty($mediacat))
  {
      return;
  }
  $mediacat_ids[] = $mediacat->getId();
  $select->add_option($mediacat->getName(),$mediacat->getId(), $mediacat->getId(),$mediacat->getParentId());
  $childs = $mediacat->getChildren();
  if (is_array($childs))
  {
    foreach ( $childs as $child) {
      add_cat_options( $select, $child, $mediacat_ids, $mediacat->getName());
    }
  }
}

// zugriff auf sprachen
$sel_sprachen = new rex_select;
$sel_sprachen->multiple(1);
$sel_sprachen->set_style('class=rex-perm-fselect');
$sel_sprachen->set_size(3);
$sel_sprachen->set_name("userperm_sprachen[]");
$sel_sprachen->set_id("userperm_sprachen");
	
$sqlsprachen = new rex_sql;
$sqlsprachen->setQuery("select * from ".$REX['TABLE_PREFIX']."clang order by id");
for ($i=0;$i<$sqlsprachen->getRows();$i++)
{
  $name = $sqlsprachen->getValue("name");
  // $c = substr_count($sql->getValue("path"),"|"); 
  $sel_sprachen->add_option($name,$sqlsprachen->getValue("id"));
  $sqlsprachen->next();
}

// eigene sprache
$sel_mylang = new rex_select;
$sel_mylang->set_style('class=rex-perm-fselect');
$sel_mylang->set_size(1);
$sel_mylang->set_name("userperm_mylang");
$sel_mylang->set_id("userperm_mylang");
$sel_mylang->add_option("default","be_lang[default]");
$sel_mylang->add_option("de_de","be_lang[de_de]");
$sel_mylang->add_option("en_gb","be_lang[en_gb]");


// zugriff auf module
$sel_module = new rex_select;
$sel_module->multiple(1);
$sel_module->set_style('class=rex-perm-fselect');
$sel_module->set_size(10);
$sel_module->set_name("userperm_module[]");
$sel_module->set_id("userperm_module");

$sqlmodule = new rex_sql;
$sqlmodule->setQuery("select * from ".$REX['TABLE_PREFIX']."modultyp order by name");

for ($i=0;$i<$sqlmodule->getRows();$i++)
{
  $name = $sqlmodule->getValue("name"); 
  $sel_module->add_option($name,$sqlmodule->getValue("id"));
  $sqlmodule->next();
}

// extrarechte - von den addons übergeben
$sel_extra = new rex_select;
$sel_extra->multiple(1);
$sel_extra->set_style('class=rex-perm-fselect');
$sel_extra->set_size(10);
$sel_extra->set_name("userperm_extra[]");
$sel_extra->set_id("userperm_extra");

if (isset($REX['EXTRAPERM']))
  $sel_extra->add_array_options($REX['EXTRAPERM'], false);


// --------------------------------- Title

rex_title($I18N->msg("title_user"),"");

// --------------------------------- FUNCTIONS

if ((isset($FUNC_UPDATE) && $FUNC_UPDATE != '') || (isset($FUNC_APPLY) and $FUNC_APPLY != ''))
{
  $updateuser = new rex_sql;
  $updateuser->setTable($REX['TABLE_PREFIX']."user");
  $updateuser->setWhere("user_id='$user_id'");
  $updateuser->setValue("name",$username);
  $updateuser->setValue("updatedate",time());
  $updateuser->setValue("updateuser",$REX_USER->getValue("login"));
  if ($REX['PSWFUNC']!="" && $userpsw != $sql->getValue($REX['TABLE_PREFIX']."user.psw")) $userpsw = call_user_func($REX['PSWFUNC'],$userpsw);
  $updateuser->setValue("psw",$userpsw);
  $updateuser->setValue("description",$userdesc);
  if (isset($_REQUEST["logintriesreset"]) && $_REQUEST["logintriesreset"] == 1) $updateuser->setValue("login_tries","0");
  if (isset($userstatus) and $userstatus == 1) $updateuser->setValue("status",1);
  else $updateuser->setValue("status",0);
  
  $perm = "";
  if (isset($useradmin) and $useradmin == 1) $perm .= "#admin[]";
  if (isset($allcats) and $allcats == 1)     $perm .= "#csw[0]";
  if (isset($allmcats) and $allmcats == 1)   $perm .= "#media[0]";

  // userperm_all
  if (isset($userperm_all)) {
    for ($i=0;$i<count($userperm_all);$i++)
    {
      $perm .= "#".current($userperm_all);
      next($userperm_all);
    }
  }
  // userperm_ext
  if (isset($userperm_ext)) {
    for ($i=0;$i<count($userperm_ext);$i++)
    {
      $perm .= "#".current($userperm_ext);
      next($userperm_ext);
    }
  }
  // userperm_extra
  if (isset($userperm_extra)) {
    for ($i=0;$i<count($userperm_extra);$i++)
    {
      $perm .= "#".current($userperm_extra);
      next($userperm_extra);
    }
  }  
  
  // userperm_cat
  if (isset($userperm_cat)) {
    for ($i=0;$i<count($userperm_cat);$i++)
    {
      $ccat = current($userperm_cat);
      $gp = new rex_sql;
      $gp->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id='$ccat' and clang=0");
      if ($gp->getRows()==1)
      {
        foreach ( explode("|",$gp->getValue("path")) as $a)
        {
          if ($a!="")$userperm_cat_read[$a] = $a; 
        }
      }
      $perm .= "#"."csw[$ccat]";
      next($userperm_cat);
    }
  }
      
  if (isset($userperm_cat_read)) {
    for ($i=0;$i<count($userperm_cat_read);$i++)
    {
      $ccat = current($userperm_cat_read);
      $perm .= "#"."csr[$ccat]";
      next($userperm_cat_read);
    }
  }
  
  // userperm_media
  if (isset($userperm_media)) {
    for ($i=0;$i<count($userperm_media);$i++)
    {
      $perm .= "#"."media[".current($userperm_media)."]";
      next($userperm_media);
    }
  }
      
  // userperm_sprachen
  if (isset($userperm_sprachen)) {
    for ($i=0;$i<count($userperm_sprachen);$i++)
    {
      $perm .= "#"."clang[".current($userperm_sprachen)."]";
      next($userperm_sprachen);
    }
  }
  
  // userperm mylang
  if (!isset($userperm_mylang) or $userperm_mylang == "") $userperm_mylang = 'be_lang[default]';
  $perm .= "#"."$userperm_mylang";
  
  // userperm_module
  if (isset($userperm_module)) {
    for ($i=0;$i<count($userperm_module);$i++)
    {
      $perm .= "#"."module[".current($userperm_module)."]";
      next($userperm_module);
    }
  }
  
  $updateuser->setValue("rights",$perm."#");
  $updateuser->update();
  
  if(isset($FUNC_UPDATE) && $FUNC_UPDATE != '')
  {
    unset($user_id);
    unset($FUNC_UPDATE);
  }
  
  $message = $I18N->msg("user_data_updated");

} elseif (isset($FUNC_DELETE) and $FUNC_DELETE != '')
{
  // man kann sich selbst nicht löschen..
  if ($REX_USER->getValue("user_id")!=$user_id)
  {
    $deleteuser = new rex_sql;
    $deleteuser->query("DELETE FROM ".$REX['TABLE_PREFIX']."user WHERE user_id = '$user_id' LIMIT 1");
    $message = $I18N->msg("user_deleted");
    unset($user_id);
  }else
  {
    $message = $I18N->msg("user_notdeleteself");
  }

} elseif ((isset($FUNC_ADD) and $FUNC_ADD != '') and (isset($save) and $save == ''))
{
  // bei add default selected
  $sel_sprachen->set_selected("0");
} elseif ((isset($FUNC_ADD) and $FUNC_ADD != '') and (isset($save) and $save == 1))
{
  $adduser = new rex_sql;
  $adduser->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."user WHERE login = '$userlogin'");

  if ($adduser->getRows()==0 and $userlogin != "")
  {
    $adduser = new rex_sql;
    $adduser->setTable($REX['TABLE_PREFIX']."user");
    $adduser->setValue("name",$username);
    if ($REX['PSWFUNC']!="") $userpsw = call_user_func($REX['PSWFUNC'],$userpsw);
    $adduser->setValue("psw",$userpsw);
    $adduser->setValue("login",$userlogin);
    $adduser->setValue("description",$userdesc);
    $adduser->setValue("createdate",time());
    $adduser->setValue("createuser",$REX_USER->getValue("login"));
    if (isset($userstatus) and $userstatus == 1) $adduser->setValue("status",1);
    else $adduser->setValue("status",0);
        
    $perm = "";
    if (isset($useradmin) and $useradmin == 1) $perm .= "#"."admin[]";
    if (isset($allcats) and $allcats == 1)     $perm .= "#"."csw[0]";
    if (isset($allmcats) and $allmcats == 1)   $perm .= "#"."media[0]";
  
    // userperm_all
    if (isset($userperm_all)) {
      for($i=0;$i<count($userperm_all);$i++)
      {
        $perm .= "#".current($userperm_all);
        next($userperm_all);
      }
    }
    // userperm_ext
    if (isset($userperm_ext)) {
      for($i=0;$i<count($userperm_ext);$i++)
      {
        $perm .= "#".current($userperm_ext);
        next($userperm_ext);
      }
    }
    // userperm_sprachen
    if (isset($userperm_sprachen)) {
      for($i=0;$i<count($userperm_sprachen);$i++)
      {
        $perm .= "#"."clang[".current($userperm_sprachen)."]";
        next($userperm_sprachen);
      }
    }
    // userperm mylang
    if (!isset($userperm_mylang) or $userperm_mylang == '') $userperm_mylang = "be_lang[default]";
    $perm .= "#".$userperm_mylang;

    // userperm_extra
    if (isset($userperm_extra)) {
      for($i=0;$i<count($userperm_extra);$i++)
      {
        $perm .= "#".current($userperm_extra);
        next($userperm_extra);
      }
    }
    // userperm_cat
    if (isset($userperm_cat)) {
      for($i=0;$i<count($userperm_cat);$i++)
      {
        $perm .= "#"."csw[".current($userperm_cat)."]";
        next($userperm_cat);
      }
    }
    // userperm_media
    if (isset($userperm_media)) {
      for($i=0;$i<count($userperm_media);$i++)
      {
        $perm .= "#"."media[".current($userperm_media)."]";
        next($userperm_media);
      }
    }
    // userperm_module
    if (isset($userperm_module)) {
      for($i=0;$i<count($userperm_module);$i++)
      {
        $perm .= "#"."module[".current($userperm_module)."]";
        next($userperm_module);
      }
    }

    $adduser->setValue("rights",$perm."#");
    $adduser->insert();
    $user_id = 0;
    unset($FUNC_ADD);
    $message = $I18N->msg("user_added");
  } else
  {
    
    if ($useradmin == 1) $adminchecked = ' checked="checked"';
    if ($allcats == 1) $allcatschecked = ' checked="checked"';
    if ($allmcats == 1) $allmcatschecked = ' checked="checked"';
    
    
    // userperm_all
    for($i=0;$i<count($userperm_all);$i++)
    {
      $sel_all->set_selected(current($userperm_all));
      next($userperm_all);
    }
    // userperm_ext
    for($i=0;$i<count($userperm_ext);$i++)
    {
      $sel_ext->set_selected(current($userperm_ext));
      next($userperm_ext);
    }
    // userperm_extra
    for ($i=0;$i<count($userperm_extra);$i++)
    {
      $sel_extra->set_selected(current($userperm_extra));
      next($userperm_extra);
    }
    // userperm_sprachen
    for ($i=0;$i<count($userperm_sprachen);$i++)
    {
      $sel_sprachen->set_selected(current($userperm_sprachen));
      next($userperm_sprachen);
    }

    if ($userperm_mylang=="") $userperm_mylang = "be_lang[default]";
    $sel_mylang->set_selected($userperm_mylang);

    // userperm_cat
    for($i=0;$i<count($userperm_cat);$i++)
    {
      $sel_cat->set_selected(current($userperm_cat));
      next($userperm_cat);
    }
    // userperm_media
    for ($i=0;$i<count($userperm_media);$i++)
    {
      $sel_media->set_selected(current($userperm_media));
      next($userperm_media);
    }
    // userperm_module
    for ($i=0;$i<count($userperm_module);$i++)
    {
      $sel_module->set_selected(current($userperm_module));
      next($userperm_module);
    }
    
    $message = $I18N->msg("user_login_exists");
  }
}


// ---------------------------------- ERR MSG

if (!empty($message))
{
  echo '<p class="rex-warning">'.$message.'</p>';
}


// --------------------------------- FORMS

$SHOW = true;

if (isset($FUNC_ADD) && $FUNC_ADD || (isset($user_id) && $user_id != ""))
{
  $SHOW = false;

  if (!isset($userlogin)) { $userlogin = ''; }
  if (!isset($userpsw)) { $userpsw = ''; }
  if (!isset($username)) { $username = ''; }
  if (!isset($userdesc)) { $userdesc = ''; }
  if (!isset($adminchecked)) { $adminchecked = ''; }
  if (!isset($allcatschecked)) { $allcatschecked = ''; }
  if (!isset($allmcatschecked)) { $allmcatschecked = ''; }
  if (!isset($statuschecked)) { $statuschecked = ''; }
  
  $add_login_reset_chkbox = '';
  
  if($user_id != '')
  {
    // User Edit
    
    $form_label = $I18N->msg('edit_user');
    $add_hidden = '<input type="hidden" name="user_id" value="'.$user_id.'" />';
    $add_submit = '<div>
						<p class="rex-cnt-col2">
						<input type="submit" class="rex-sbmt" name="FUNC_UPDATE" value="'.$I18N->msg('user_save').'" />
						</p>
						<p class="rex-cnt-col2">
						<input type="submit" class="rex-sbmt" name="FUNC_APPLY" value="'.$I18N->msg('user_apply').'" />
						</p>
					</div>';
    $add_user_login = '<span id="userlogin">'. $sql->getValue($REX['TABLE_PREFIX']."user.login") .'</span>';
   
    $sql = new rex_login_sql;
    $sql->setQuery("select * from ".$REX['TABLE_PREFIX']."user where user_id='$user_id'");
    
    if ($sql->getRows()==1)
    {
      // ----- EINLESEN DER PERMS
      if ($sql->hasPerm("admin[]")) $adminchecked = "checked";
      else $adminchecked = "";
  
      if ($sql->hasPerm("csw[0]")) $allcatschecked = "checked";
      else $allcatschecked = "";
      
      if ($sql->hasPerm("media[0]")) $allmcatschecked = "checked";
      else $allmcatschecked = "";
      
      if ($sql->getValue($REX['TABLE_PREFIX'].'user.status') == 1) $statuschecked = "checked";
      else $statuschecked = "";
  
      // Allgemeine Permissions setzen
      for($i=0;$i<count($REX['PERM']);$i++)
      {
        if($i==0) reset($REX['PERM']);
        if ($sql->hasPerm(current($REX['PERM']))) $sel_all->set_selected(current($REX['PERM']));
        next($REX['PERM']);
      }
      
      // optionen
      for($i=0;$i<count($REX['EXTPERM']);$i++)
      {
        if($i==0) reset($REX['EXTPERM']);
        if ($sql->hasPerm(current($REX['EXTPERM']))) $sel_ext->set_selected(current($REX['EXTPERM']));
        next($REX['EXTPERM']);
      }
      
      // optionen
      if (isset($REX['EXTRAPERM'])) {
        for ($i=0; $i < count($REX['EXTRAPERM']); $i++)
        {
          if ($i == 0) reset($REX['EXTRAPERM']);
          if ($sql->hasPerm(current($REX['EXTRAPERM']))) $sel_extra->set_selected(current($REX['EXTRAPERM']));
          next($REX['EXTRAPERM']);
        }
      }
    
      foreach ( $cat_ids as $cat_id) 
      {
        $name = "csw[".$cat_id."]";
        if ($sql->hasPerm($name)) $sel_cat->set_selected($cat_id);
      }
  
      foreach ( $mediacat_ids as $cat_id) 
      {
        $name = "media[".$cat_id."]";
        if ($sql->hasPerm($name)) $sel_media->set_selected( $cat_id);
      }
      
      $sqlmodule->resetCounter();
      for ($i=0;$i<$sqlmodule->getRows();$i++)
      {
        $name = "module[".$sqlmodule->getValue("id")."]";
        if ($sql->hasPerm($name)) $sel_module->set_selected($sqlmodule->getValue("id"));
        $sqlmodule->next();
      }
  
      $sqlsprachen->resetCounter();
      for ($i=0;$i<$sqlsprachen->getRows();$i++)
      {
        $name = "clang[".$sqlsprachen->getValue("id")."]";
        if ($sql->hasPerm($name)) $sel_sprachen->set_selected($sqlsprachen->getValue("id"));
        $sqlsprachen->next();
      }
      
      if ($sql->hasPerm("be_lang[de_de]")) $userperm_mylang = "be_lang[de_de]";
      else if ($sql->hasPerm("be_lang[en_gb]")) $userperm_mylang = "be_lang[en_gb]";
      else $userperm_mylang = "be_lang[default]";
      $sel_mylang->set_selected($userperm_mylang);
      
      $userpsw = $sql->getValue($REX['TABLE_PREFIX']."user.psw");
      $username = $sql->getValue($REX['TABLE_PREFIX']."user.name");
      $userdesc = $sql->getValue($REX['TABLE_PREFIX']."user.description");
      
      // Der Benutzer kann sich selbst die Rechte nicht entziehen 
      if ($REX_USER->getValue('login') == $sql->getValue($REX['TABLE_PREFIX'].'user.login') && $adminchecked != '')
      {
        $add_admin_chkbox = '<input type="hidden" name="useradmin" value="1" /><input class="rex-chckbx" type="checkbox" id="useradmin" name="useradmin" value="1" '.$adminchecked.' disabled="disabled" />';
      }
      else
      {
        $add_admin_chkbox = '<input class="rex-chckbx" type="checkbox" id="useradmin" name="useradmin" value="1" '.$adminchecked.' />';
      }
      
      // Der Benutzer kann sich selbst den Status nicht entziehen 
      if ($REX_USER->getValue('login') == $sql->getValue($REX['TABLE_PREFIX'].'user.login') && $statuschecked != '')
      {
        $add_status_chkbox = '<input type="hidden" name="userstatus" value="1" /><input class="rex-chckbx" type="checkbox" id="userstatus" name="userstatus" value="1" '.$statuschecked.' disabled="disabled" />';
      }
      else
      {
        $add_status_chkbox = '<input class="rex-chckbx" type="checkbox" id="userstatus" name="userstatus" value="1" '.$statuschecked.' />';
      }
      
      
      
      // Account gesperrt?
      if ($REX['MAXLOGINS'] < $sql->getValue("login_tries"))
      {
        $add_login_reset_chkbox = '
        <p class="rex-warning">
          <input class="rex-chckbx" type="checkbox" name="logintriesreset" id="logintriesreset" value="1" />
          <label for="logintriesreset">'. $I18N->msg("user_reset_tries",$REX['MAXLOGINS']) .'</label>
        </p>';
      }
      
    }
  }
  else
  {
    // User Add
    $form_label = $I18N->msg('create_user');
    $add_hidden = '<input type="hidden" name="FUNC_ADD" value="1" />';
    $add_submit = '<input type="submit" class="rex-sbmt" name="function" value="'.$I18N->msg("add_user").'" />';
    $add_admin_chkbox = '<input class="rex-chckbx" type="checkbox" id="useradmin" name="useradmin" value="1" '.$adminchecked.' />';
    $add_status_chkbox = '<input class="rex-chckbx" type="checkbox" id="userstatus" name="userstatus" value="1" '.$statuschecked.' />';
    $add_user_login = '<input type="text" id="userlogin" name="userlogin" value="'.htmlspecialchars($userlogin).'" />';
  }
  
  echo '
  <div class="rex-usr-editmode">
  <form action="index.php" method="post">
    <fieldset>
      <legend class="rex-lgnd">'.$form_label.'</legend>

      <div class="rex-fldst-wrppr">
      <input type="hidden" name="page" value="user" />
      <input type="hidden" name="save" value="1" />
      '. $add_hidden .'

      '. $add_login_reset_chkbox .'


        <div>
          <p class="rex-cnt-col2">
            <label for="userlogin">'.$I18N->msg('login_name').'</label>
            '. $add_user_login .'  
          </p>
          <p class="rex-cnt-col2">
            <label for="userpsw">'.$I18N->msg('password').'</label>
            <input type="text" id="userpsw" name="userpsw" value="'.htmlspecialchars($userpsw).'" />
            '. ($REX['PSWFUNC']!='' ? '<span>'. $I18N->msg('psw_encrypted') .'</span>' : '') .'  
          </p>
		</div>
		
        <div>
          <p class="rex-cnt-col2">
            <label for="username">'.$I18N->msg('name').'</label>
            <input type="text" id="username" name="username" value="'.htmlspecialchars($username).'" />
          </p>
          <p class="rex-cnt-col2">
            <label for="userdesc">'.$I18N->msg('description').'</label>
            <input type="text" id="userdesc" name="userdesc" value="'.htmlspecialchars($userdesc).'" />
          </p>
		</div>
		
        <div>
          <p class="rex-cnt-col2">
            '. $add_admin_chkbox .'
            <label class="rex-lbl-rght" for="useradmin">'.$I18N->msg('user_admin').'</label>
          </p>
          <p class="rex-cnt-col2">
            '. $add_status_chkbox .'
            <label class="rex-lbl-rght" for="userstatus">'.$I18N->msg('user_status').'</label>
          </p>
		</div>
		
        <div>
          <p class="rex-cnt-col2">
            <label for="userperm_sprachen">'.$I18N->msg('user_lang_xs').'</label>
            '. $sel_sprachen->out() .'
            <span>'. $I18N->msg('ctrl') .'</span>
          </p>
          <!--
          <p class="rex-cnt-col2">
            <label for="userperm_mylang">Meine Backendsprache</label>
            '.$sel_mylang->out().'
          </p>
          -->
		</div>
		
        <div>
          <p class="rex-cnt-col2">
            <label for="userperm_all">'.$I18N->msg('user_all').'</label>
            '. $sel_all->out() .'
            <span>'. $I18N->msg('ctrl') .'</span>
          </p>
          <p class="rex-cnt-col2">
            <label for="userperm_ext">'.$I18N->msg('user_options').'</label>
            '. $sel_ext->out() .'
            <span>'. $I18N->msg('ctrl') .'</span>
          </p>
		</div>
		
        <div>
          <p class="rex-cnt-col2">
            <input class="rex-chckbx" type="checkbox" id="allcats" name="allcats" value="1" '.$allcatschecked.' />
            <label class="rex-lbl-rght" for="allcats">'.$I18N->msg('all_categories').'</label>
          </p>
          <p class="rex-cnt-col2">
            <input class="rex-chckbx" type="checkbox" id="allmcats" name="allmcats" value="1" '.$allmcatschecked.' />
            <label class="rex-lbl-rght" for="allmcats">'.$I18N->msg('all_mediafolder').'</label>
          </p>
		</div>
		
        <div>
          <p class="rex-cnt-col2">
            <label for="userperm_cat">'.$I18N->msg('categories').'</label>
            ' .$sel_cat->out() .'
            <span>'. $I18N->msg('ctrl') .'</span>
          </p>
          <p class="rex-cnt-col2">
            <label for="userperm_media">'.$I18N->msg('mediafolder').'</label>
            '. $sel_media->out() .'
            <span>'. $I18N->msg('ctrl') .'</span>
          </p>
		</div>
		
        <div>
          <p class="rex-cnt-col2">
            <label for="userperm_module">'.$I18N->msg('modules').'</label>
            '.$sel_module->out().'
            <span>'. $I18N->msg('ctrl') .'</span>
          </p>
          <p class="rex-cnt-col2">
            <label for="userperm_extra">'.$I18N->msg("extras").'</label>
            '. $sel_extra->out() .'
            <span>'. $I18N->msg('ctrl') .'</span>
          </p>
		</div>

      '. $add_submit .'
      </div>
    </fieldset>
  </form>
  </div>
';

}













// ---------------------------------- Userliste

if (isset($SHOW) and $SHOW)
{
  $add_col = ''; 
  $add_th = '';
  if ($REX_USER->hasPerm('advancedMode[]'))
  {
    $add_col = '<col width="5%" />'; 
    $add_th = '<th>ID</th>';
  }

  echo '
  <table class="rex-table" summary="'.$I18N->msg('user_summary').'">
    <caption class="rex-hide">'.$I18N->msg('user_caption').'</caption>
    <colgroup>
      <col width="5%" />
      '. $add_col .'
      <col width="*" />
      <col width="25%" />
      <col width="20%" />
      <col width="15%" />
    </colgroup>
    <thead>
      <tr>
        <th class="rex-icon"><a href="index.php?page=user&amp;FUNC_ADD=1"><img src="pics/user_plus.gif" width="16" height="16" alt="'.$I18N->msg('create_user').'" title="'.$I18N->msg('create_user').'" /></a></th>
        '. $add_th .'
        <th>'.$I18N->msg('name').'</th>
        <th>'.$I18N->msg('login').'</th>
        <th>'.$I18N->msg('last_login').'</th>
        <th>'.$I18N->msg('user_functions').'</th>
      </tr>
    </thead>
    <tbody>';

  $sql = new rex_sql;
  $sql->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'user ORDER BY name');

  for ($i=0; $i<$sql->getRows(); $i++)
  {
    $lasttrydate = $sql->getValue('lasttrydate');
    $last_login = '-';
    
    if ( $lasttrydate != 0) {
        $last_login = strftime( $I18N->msg('datetimeformat'), $sql->getValue('lasttrydate'));
    }
    
    $username = htmlspecialchars($sql->getValue('name'));
    if ( $username == '') {
        $username = htmlspecialchars($sql->getValue('login'));
    }

    $add_td = '';        
    if ($REX_USER->hasPerm('advancedMode[]'))
    {
      $add_td = '<td>'.$sql->getValue('user_id').'</td>';
    }
    
    $delete_func = $I18N->msg("user_delete");
    // man kann sich selbst nicht löschen..
    if ($REX_USER->getValue("user_id")!=$sql->getValue("user_id"))
    {
      $delete_func = '<a href="index.php?page=user&amp;user_id='.$sql->getValue("user_id").'&amp;FUNC_DELETE=1" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$delete_func.'</a>';
    }
    else
    {
      $delete_func = '<span class="rex-strike">'. $delete_func .'</span>';
    }
    
    echo '
      <tr>
        <td class="rex-icon"><a href="index.php?page=user&amp;user_id='.$sql->getValue("user_id").'"><img src="pics/user.gif" width="16" height="16" alt="'. $username .'" title="'. $username .'" /></a></td>
        '. $add_td .'
        <td><a href="index.php?page=user&amp;user_id='.$sql->getValue("user_id").'">'.$username.'</a></td>
        <td>'.$sql->getValue("login").'</td>
        <td>'.$last_login.'</td>
        <td>'. $delete_func .'</td>
      </tr>';
    $sql->counter++;
  }
  echo '
    </tbody>
  </table>';

}


?>