<?

/*

----------------------------- todos

sprachen zugriff
englisch / deutsch / ...
clang

allgemeine zugriffe (array + addons)
  mediapool[]templates[] ...

optionen
  advancedMode[]expertMode[]

zugriff auf folgende categorien
  csw[2] write 
  csr[2] read

mulselect zugriff auf mediapool
  media[2]

mulselect module
- liste der module
  module[2]module[3]

*/

if ($user_id != "")
{
  $sql = new sql;
  $sql->setQuery("select * from rex_user where user_id='$user_id'");
  if ($sql->getRows()!=1) uset($user_id);
}



// Allgemeine Permissions setzen
$sel_all = new select;
$sel_all->multiple(1);
$sel_all->set_style("width:250px; height: 130px;");
$sel_all->set_size(10);
$sel_all->set_name("userperm_all[]");
$sel_all->set_id("userperm_all");
for($i=0;$i<count($REX['PERM']);$i++)
{
  if($i==0) reset($REX['PERM']);
  $sel_all->add_option(current($REX['PERM']),current($REX['PERM']));
  next($REX['PERM']);
}

$sel_ext = new select;
$sel_ext->multiple(1);
$sel_ext->set_style("width:250px; height: 130px;");
$sel_ext->set_size(10);
$sel_ext->set_name("userperm_ext[]");
$sel_ext->set_id("userperm_ext");
for($i=0;$i<count($REX['EXTPERM']);$i++)
{
  if($i==0) reset($REX['EXTPERM']);
  $sel_ext->add_option(current($REX['EXTPERM']),current($REX['EXTPERM']));
  next($REX['EXTPERM']);
}

// zugriff auf categorien
$sel_cat = new select;
$sel_cat->multiple(1);
$sel_cat->set_style("width:250px; height: 200px;");
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
  if(empty($cat))
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
$sel_media = new select;
$sel_media->multiple(1);
$sel_media->set_style("width:250px; height: 200px;");
$sel_media->set_size(20);
$sel_media->set_name("userperm_media[]");
$sel_media->set_id("userperm_media");
$mediacat_ids = array();

if ($rootCats = OOMediaCategory::getRootCategories())
{
    foreach( $rootCats as $rootCat) {
        add_mediacat_options( $sel_media, $rootCat, $mediacat_ids);
    }
}

function add_mediacat_options( &$select, &$mediacat, &$mediacat_ids, $groupName = '')
{
    if(empty($mediacat))
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
$sel_sprachen = new select;
$sel_sprachen->multiple(1);
$sel_sprachen->set_style("width:250px; height: 50px;");
$sel_sprachen->set_size(3);
$sel_sprachen->set_name("userperm_sprachen[]");
$sel_sprachen->set_id("userperm_sprachen");
$sqlsprachen = new sql;
$sqlsprachen->setQuery("select * from rex_clang order by id");
for ($i=0;$i<$sqlsprachen->getRows();$i++)
{
  $name = $sqlsprachen->getValue("name");
  // $c = substr_count($sql->getValue("path"),"|"); 
  $sel_sprachen->add_option($name,$sqlsprachen->getValue("id"));
  $sqlsprachen->next();
}

// eigene sprache
$sel_mylang = new select;
$sel_mylang->set_style("width:250px;");
$sel_mylang->set_size(1);
$sel_mylang->set_name("userperm_mylang");
$sel_mylang->set_id("userperm_mylang");
$sel_mylang->add_option("default","be_lang[default]");
$sel_mylang->add_option("de_de","be_lang[de_de]");
$sel_mylang->add_option("en_gb","be_lang[en_gb]");


// zugriff auf module
$sel_module = new select;
$sel_module->multiple(1);
$sel_module->set_style("width:250px; height: 150px;");
$sel_module->set_size(10);
$sel_module->set_name("userperm_module[]");
$sel_module->set_id("userperm_module");

$sqlmodule = new sql;
$sqlmodule->setQuery("select * from rex_modultyp order by name");

for ($i=0;$i<$sqlmodule->getRows();$i++)
{
  $name = $sqlmodule->getValue("name"); 
  $sel_module->add_option($name,$sqlmodule->getValue("id"));
  $sqlmodule->next();
}

// extrarechte - von den addons übergeben
$sel_extra = new select;
$sel_extra->multiple(1);
$sel_extra->set_style("width:250px; height: 150px;");
$sel_extra->set_size(10);
$sel_extra->set_name("userperm_extra[]");
$sel_extra->set_id("userperm_extra");

for($i=0;$i<count($REX['EXTRAPERM']);$i++)
{
  if($i==0) reset($REX['EXTRAPERM']);
  $sel_extra->add_option(current($REX['EXTRAPERM']),current($REX['EXTRAPERM']));
  next($REX['EXTRAPERM']);
}

// --------------------------------- Title

title($I18N->msg("title_user"),"");

// --------------------------------- FUNCTIONS

if ($FUNC_UPDATE != "")
{
  $updateuser = new sql;
  $updateuser->setTable("rex_user");
  $updateuser->where("user_id='$user_id'");
  $updateuser->setValue("name",$username);
  $updateuser->setValue("psw",$userpsw);
  $updateuser->setValue("description",$userdesc);

  $perm = "";
  if ($useradmin == 1) $perm .= "admin[]";
  if ($devadmin == 1) $perm .= "dev[]";
  if ($allcats == 1) $perm .= "csw[0]";
  if ($allmcats == 1) $perm .= "media[0]";

  // userperm_all
  for($i=0;$i<count($userperm_all);$i++)
  {
    $perm .= current($userperm_all);
    next($userperm_all);
  }
  // userperm_ext
  for($i=0;$i<count($userperm_ext);$i++)
  {
    $perm .= current($userperm_ext);
    next($userperm_ext);
  }
  // userperm_extra
  for($i=0;$i<count($userperm_extra);$i++)
  {
    $perm .= current($userperm_extra);
    next($userperm_extra);
  }
  
  // userperm_cat
  for($i=0;$i<count($userperm_cat);$i++)
  {
    $ccat = current($userperm_cat);
    $gp = new sql;
    $gp->setQuery("select * from rex_article where id='$ccat' and clang=0");
    if ($gp->getRows()==1)
    {
      foreach ( explode("|",$gp->getValue("path")) as $a)
      {
        if ($a!="")$userperm_cat_read[$a] = $a; 
      }
    }
    $perm .= "csw[$ccat]";
    next($userperm_cat);
  }
  
  for ($i=0;$i<count($userperm_cat_read);$i++)
  {
    $ccat = current($userperm_cat_read);
    $perm .= "csr[$ccat]";
    next($userperm_cat_read);
  }
  
  // userperm_media
  for($i=0;$i<count($userperm_media);$i++)
  {
    $perm .= "media[".current($userperm_media)."]";
    next($userperm_media);
  }
  
  // userperm_sprachen
  for($i=0;$i<count($userperm_sprachen);$i++)
  {
    $perm .= "clang[".current($userperm_sprachen)."]";
    next($userperm_sprachen);
  }
  
  // userperm mylang
  if ($userperm_mylang == "") $userperm_mylang = "be_lang[default]";
  $perm .= "$userperm_mylang";
  
  // userperm_module
  for($i=0;$i<count($userperm_module);$i++)
  {
    $perm .= "module[".current($userperm_module)."]";
    next($userperm_module);
  }
  $updateuser->setValue("rights",$perm);
  $updateuser->update();
  unset($user_id);
  unset($FUNC_UPDATE);
  $message = $I18N->msg("user_data_updated");

}elseif($FUNC_DELETE != "")
{
  if ($REX_USER->getValue("user_id")!=$user_id)
  {
    $deleteuser = new sql;
    $deleteuser->query("delete from rex_user where user_id='$user_id'");
    $message = $I18N->msg("user_deleted");
  }else
  {
    $message = $I18N->msg("user_notdeleteself");
  }

}elseif ($FUNC_ADD != "" && $save == "")
{
  // bei add default selected
  $sel_sprachen->set_selected("0");
}elseif($FUNC_ADD != "" && $save == 1)
{
  $adduser = new sql;
  $adduser->setQuery("select * from rex_user where login='$userlogin'");

  if ($adduser->getRows()==0 and $userlogin != "")
  {
    $adduser = new sql;
    $adduser->setTable("rex_user");
    $adduser->setValue("name",$username);
    $adduser->setValue("psw",$userpsw);
    $adduser->setValue("login",$userlogin);
    $adduser->setValue("description",$userdesc);
    
    $perm = "";
    if ($useradmin == 1) $perm .= "admin[]";
    if ($devadmin == 1) $perm .= "dev[]";
    if ($allcats == 1) $perm .= "csw[0]";
    if ($allmcats == 1) $perm .= "media[0]";
  
    // userperm_all
    for($i=0;$i<count($userperm_all);$i++)
    {
      $perm .= current($userperm_all);
      next($userperm_all);
    }
    // userperm_ext
    for($i=0;$i<count($userperm_ext);$i++)
    {
      $perm .= current($userperm_ext);
      next($userperm_ext);
    }
    // userperm_sprachen
    for($i=0;$i<count($userperm_sprachen);$i++)
    {
      $perm .= "clang[".current($userperm_sprachen)."]";
      next($userperm_sprachen);
    }
    // userperm mylang
    if ($userperm_mylang == "") $userperm_mylang = "be_lang[default]";
    $perm .= "$userperm_mylang";

    // userperm_extra
    for($i=0;$i<count($userperm_extra);$i++)
    {
      $perm .= current($userperm_extra);
      next($userperm_extra);
    }
    // userperm_cat
    for($i=0;$i<count($userperm_cat);$i++)
    {
      $perm .= "csw[".current($userperm_cat)."]";
      next($userperm_cat);
    }
    // userperm_media
    for($i=0;$i<count($userperm_media);$i++)
    {
      $perm .= "media[".current($userperm_media)."]";
      next($userperm_media);
    }
    // userperm_module
    for($i=0;$i<count($userperm_module);$i++)
    {
      $perm .= "module[".current($userperm_module)."]";
      next($userperm_module);
    }
    
    $adduser->setValue("rights",$perm);
    $adduser->insert();
    $user_id = 0;
    unset($FUNC_ADD);
    $message = $I18N->msg("user_added");
  }else
  {
    
    if ($useradmin == 1) $adminchecked = " checked";
    if ($devadmin == 1) $devchecked = " checked";
    if ($allcats == 1) $allcatschecked = " checked";
    if ($allmcats == 1) $allmcatschecked = " checked";
    
    
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
    for($i=0;$i<count($userperm_extra);$i++)
    {
      $sel_extra->set_selected(current($userperm_extra));
      next($userperm_extra);
    }
    // userperm_sprachen
    for($i=0;$i<count($userperm_sprachen);$i++)
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
    for($i=0;$i<count($userperm_media);$i++)
    {
      $sel_media->set_selected(current($userperm_media));
      next($userperm_media);
    }
    // userperm_module
    for($i=0;$i<count($userperm_module);$i++)
    {
      $sel_module->set_selected(current($userperm_module));
      next($userperm_module);
    }
    
    $message = $I18N->msg("user_login_exists");
  }
}


// ---------------------------------- ERR MSG

if ($message != "")
{
  echo "<table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1><tr class=warning><td class=icon><img src=pics/warning.gif width=16 height=16></td><td colspan=3 class=warning>$message</td></tr></table><br>";
}


// --------------------------------- FORMS

$SHOW = true;

if ($FUNC_ADD)
{
  $SHOW = false;

  echo "  <table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
    <form action=index.php method=post>
    <input type=hidden name=page value=user>
    <input type=hidden name=save value=1>
    <input type=hidden name=FUNC_ADD value=1>
    <tr><th colspan=4><b>".$I18N->msg("create_user")."</b></th></tr>
    
    <tr>
      <td width=100>".$I18N->msg("login_name")."</td>
      <td><input class=inp100 type=text size=20 name=userlogin value=\"".stripslashes(htmlspecialchars($userlogin))."\"></td>
      <td width=100>".$I18N->msg("password")."</td>
      <td ><input class=inp100 type=text size=20 name=userpsw value=\"".stripslashes(htmlspecialchars($userpsw))."\"></td>
    </tr>

    <tr>
      <td>".$I18N->msg("name")."</td>
      <td><input class=inp100 type=text size=20 name=username value=\"".stripslashes(htmlspecialchars($username))."\"></td>
      <td>".$I18N->msg("description")."</td>
      <td><input class=inp100 type=text size=20 name=userdesc value=\"".stripslashes(htmlspecialchars($userdesc))."\"></td>
    </tr>
    <tr>
      <td align=right><input type=checkbox id=useradmin name=useradmin value=1 $adminchecked></td>
            <td><label for=useradmin>".$I18N->msg("user_admin")."</label></td>
            <td align=right><input type=checkbox id=devadmin name=devadmin value=1 $devchecked></td>
            <td><label for=devadmin>".$I18N->msg("user_developer")."</label></td>
    </tr>
    <tr>
      <td>".$I18N->msg("user_lang_xs")."</td>
      <td>".$sel_sprachen->out()."<br>".$I18N->msg("ctrl")."</td>
      <td><!-- Meine Backendsprache -->&nbsp;</td>
      <td><!-- ".$sel_mylang->out()." -->&nbsp;</td>
    </tr>
    <tr>
      <td valign=top>".$I18N->msg("user_all")."</td>
      <td>
              ".$sel_all->out()."<br>".$I18N->msg("ctrl")."
            </td>
      <td valign=top>".$I18N->msg("user_options")."</td>
      <td>
              ".$sel_ext->out()."<br>".$I18N->msg("ctrl")."
            </td>
    </tr>
    <tr>
            <td align=right><input type=checkbox id=allcats name=allcats value=1 $allcatschecked></td>
            <td><label for=allcats>".$I18N->msg("all_categories")."</label></td>
            <td align=right><input type=checkbox id=allmcats name=allmcats value=1 $allmcatschecked></td>
            <td><label for=allmcats>".$I18N->msg("all_mediafolder")."</label></td>
    </tr>
    <tr>
      <td valign=top>".$I18N->msg("categories")."</td>
      <td>
              ".$sel_cat->out()."<br>".$I18N->msg("ctrl")."
            </td>
      <td valign=top>".$I18N->msg("mediafolder")."</td>
      <td>
              ".$sel_media->out()."<br>".$I18N->msg("ctrl")."
            </td>
    </tr>
    <tr>
      <td valign=top>".$I18N->msg("modules")."</td>
      <td>
              ".$sel_module->out()."<br>".$I18N->msg("ctrl")."
            </td>
      <td valign=top>".$I18N->msg("extras")."</td>
      <td>
              ".$sel_extra->out()."<br>".$I18N->msg("ctrl")."
            </td>
    </tr>
    
    <tr>
      <td>&nbsp;</td>
      <td colspan=3><input type=submit name=function value='".$I18N->msg("add_user")."'></td>
    </tr>
    </form>
    </table>";


}elseif($user_id != "")
{

  $sql = new sql;
  $sql->setQuery("select * from rex_user where user_id='$user_id'");

  if ($sql->getRows()==1)
  {

    // ----- EINLESEN DER PERMS
    if ($sql->isValueOf("rights","admin[]")) $adminchecked = "checked";
    else $adminchecked = "";

    if ($sql->isValueOf("rights","dev[]")) $devchecked = "checked";
    else $devchecked = "";

    if ($sql->isValueOf("rights","csw[0]")) $allcatschecked = "checked";
    else $allcatschecked = "";
    
    if ($sql->isValueOf("rights","media[0]")) $allmcatschecked = "checked";
    else $allmcatschecked = "";

    // Allgemeine Permissions setzen
    for($i=0;$i<count($REX['PERM']);$i++)
    {
      if($i==0) reset($REX['PERM']);
      if ($sql->isValueOf("rights",current($REX['PERM']))) $sel_all->set_selected(current($REX['PERM']));
      next($REX['PERM']);
    }
    
    // optionen
    for($i=0;$i<count($REX['EXTPERM']);$i++)
    {
      if($i==0) reset($REX['EXTPERM']);
      if ($sql->isValueOf("rights",current($REX['EXTPERM']))) $sel_ext->set_selected(current($REX['EXTPERM']));
      next($REX['EXTPERM']);
    }
    
    // optionen
    for($i=0;$i<count($REX['EXTRAPERM']);$i++)
    {
      if($i==0) reset($REX['EXTRAPERM']);
      if ($sql->isValueOf("rights",current($REX['EXTRAPERM']))) $sel_extra->set_selected(current($REX['EXTRAPERM']));
      next($REX['EXTRAPERM']);
    }
  
    foreach ( $cat_ids as $cat_id) 
        {
            $name = "csw[".$cat_id."]";
            if ($sql->isValueOf("rights",$name)) $sel_cat->set_selected($cat_id);
        }

        foreach ( $mediacat_ids as $cat_id) 
        {
      $name = "media[".$cat_id."]";
      if ($sql->isValueOf("rights",$name)) $sel_media->set_selected( $cat_id);
    }
    
    $sqlmodule->resetCounter();
    for ($i=0;$i<$sqlmodule->getRows();$i++)
    {
      $name = "module[".$sqlmodule->getValue("id")."]";
      if ($sql->isValueOf("rights",$name)) $sel_module->set_selected($sqlmodule->getValue("id"));
      $sqlmodule->next();
    }

    $sqlsprachen->resetCounter();
    for ($i=0;$i<$sqlsprachen->getRows();$i++)
    {
      $name = "clang[".$sqlsprachen->getValue("id")."]";
      if ($sql->isValueOf("rights",$name)) $sel_sprachen->set_selected($sqlsprachen->getValue("id"));
      $sqlsprachen->next();
    }
    
    if ($sql->isValueOf("rights","be_lang[de_de]")) $userperm_mylang = "be_lang[de_de]";
    else if ($sql->isValueOf("rights","be_lang[en_gb]")) $userperm_mylang = "be_lang[en_gb]";
    else $userperm_mylang = "be_lang[default]";
    $sel_mylang->set_selected($userperm_mylang);

    // ----- FORM UPDATE AUSGABE

    echo "
    <table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
    <form action=index.php method=post>
    <input type=hidden name=page value=user>
    <input type=hidden name=user_id value=$user_id>
    <tr><th colspan=4><b>".$I18N->msg("edit_user")."</b></th></tr>
    <tr>
      <td width=100>".$I18N->msg("login_name")."</td>
      <td width=250><b>".htmlspecialchars($sql->getValue("rex_user.login"))."</b></td>
      <td width=100>".$I18N->msg("password")."</td>
      <td><input class=inp100 type=text size=20 name=userpsw value=\"".htmlspecialchars($sql->getValue("rex_user.psw"))."\"></td>
    </tr>

    <tr>
      <td>".$I18N->msg("name")."</td>
      <td><input class=inp100 type=text size=20 name=username value=\"".htmlspecialchars($sql->getValue("rex_user.name"))."\"></td>
      <td>".$I18N->msg("description")."</td>
      <td><input class=inp100 type=text size=20 name=userdesc value=\"".htmlspecialchars($sql->getValue("rex_user.description"))."\"></td>
    </tr>
    <tr>
      <td align=right>";
      
    if ($REX_USER->getValue("login") == $sql->getValue("rex_user.login") && $adminchecked != "")
    {
      echo "<input type=hidden name=useradmin value=1><b>X</b>";
    }else
    {
      echo "<input type=checkbox id=useradmin name=useradmin value=1 $adminchecked>";
    }
      
    echo "</td>
      <td><label for=useradmin>".$I18N->msg("user_admin")."</label></td>
      <td align=right><input type=checkbox id=devadmin name=devadmin value=1 $devchecked></td>
      <td><label for=devadmin>".$I18N->msg("user_developer")."</label></td>
    </tr>
    <tr>
      <td>".$I18N->msg("user_lang_xs")."</td>
      <td>".$sel_sprachen->out()."<br>".$I18N->msg("ctrl")."</td>
      <td><!-- Meine Backendsprache -->&nbsp;</td>
      <td><!-- ".$sel_mylang->out()." -->&nbsp;</td>
    </tr>
    <tr>
            <td valign=top>".$I18N->msg("user_all")."</td>
            <td>
              ".$sel_all->out()."<br>".$I18N->msg("ctrl")."
           </td>
            <td valign=top>".$I18N->msg("user_options")."</td>
            <td>
              ".$sel_ext->out()."<br>".$I18N->msg("ctrl")."
            </td>
    </tr>
    <tr>
      <td align=right><input type=checkbox id=allcats name=allcats value=1 $allcatschecked></td>
      <td><label for=allcats>".$I18N->msg("all_categories")."</label></td>
      <td align=right><input type=checkbox id=allmcats name=allmcats value=1 $allmcatschecked></td>
      <td><label for=allmcats>".$I18N->msg("all_mediafolder")."</label></td>
    </tr>
    <tr>
            <td valign=top>".$I18N->msg("categories")."</td>
            <td>
              ".$sel_cat->out()."<br>".$I18N->msg("ctrl")."
           </td>
            <td valign=top>".$I18N->msg("mediafolder")."</td>
            <td>
              ".$sel_media->out()."<br>".$I18N->msg("ctrl")."
            </td>
    </tr>
    <tr>
            <td valign=top>".$I18N->msg("modules")."</td>
            <td>
              ".$sel_module->out()."<br>".$I18N->msg("ctrl")."
            </td>
            <td valign=top>".$I18N->msg("extras")."</td>
            <td>
              ".$sel_extra->out()."<br>".$I18N->msg("ctrl")."
          </td>
    </tr>

    <tr>
      <td>&nbsp;</td>
      <td><input type=submit name=FUNC_UPDATE value='".$I18N->msg("update")."'></td>
      <td colspan=2>";

    if ($REX_USER->getValue("user_id") != $user_id) echo "<input type=submit name=FUNC_DELETE value='".$I18N->msg("delete")."' onclick='return confirm(\"".$I18N->msg('delete')." ?\")'>";

    else echo "&nbsp;";
    echo "</td></tr>
    </form>
    </table>";

    $SHOW = false;
  }

}













// ---------------------------------- Userliste

if ($SHOW)
{

  echo "  <table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
    <tr>
      <th class=icon><a href=index.php?page=user&FUNC_ADD=1><img src=pics/user_plus.gif width=16 height=16 border=0 alt=\"".$I18N->msg("create_user")."\" title=\"".$I18N->msg("create_user")."\"></a></th>
      <th width=300>".$I18N->msg("name")."</th>
      <th>".$I18N->msg("login")."</th>
      <th>".$I18N->msg("last_login")."</th>

    </tr>";

  $sql = new sql;
  $sql->setQuery("select * from rex_user order by rex_user.name");

  for($i=0;$i<$sql->getRows();$i++)
  {
        $lasttrydate = $sql->getValue("rex_user.lasttrydate");
        $last_login = '-';
        
        if ( $lasttrydate != 0) {
            $last_login = strftime( $I18N->msg("datetimeformat"), $sql->getValue("rex_user.lasttrydate"));
        }
        
        $username = htmlspecialchars($sql->getValue("rex_user.name"));
        if ( $username == '') {
            $username = htmlspecialchars($sql->getValue("rex_user.login"));
        }
        
    echo "  <tr>
      <td class=icon><a href=index.php?page=user&user_id=".$sql->getValue("rex_user.user_id")."><img src=pics/user.gif width=16 height=16 border=0></a></td>
      <td><a href=index.php?page=user&user_id=".$sql->getValue("rex_user.user_id").">".$username."</a></td>
      <td>".$sql->getValue("rex_user.login")."</td>
      <td>".$last_login."</td>
      </tr>";
    $sql->counter++;
  }
  echo "</table>";

}


?>
