<?php

unset($REX);

$REX['HTDOCS_PATH'] = "../";
$REX['GG'] = false;
$REX['REDAXO'] = true;

include "./include/master.inc.php";

// ----------------- CREATE LANG OBJ
$I18N = rex_create_lang( $REX['LANG']);
setlocale(LC_ALL,trim($I18N->msg("setlocale")));
header('Content-Type: text/html; charset='.$I18N->msg("htmlcharset"));

$LOGIN = FALSE;

include $REX['INCLUDE_PATH']."/layout/top.php";

rex_title("Login","");

if (isset($FORM['loginmessage']) and $FORM['loginmessage'] != "")
{
  echo '<table border="0" cellpadding="5" cellspacing="1" width="770">
  <tr><td align="center" class="warning" width="40"><img src="pics/warning.gif" width="16" height="16"></td>
  <td class="warning">'.$FORM['loginmessage'].'</td></tr>
  </table>'."\n";
}

if (!isset($REX_ULOGIN)) { $REX_ULOGIN = ''; }
echo '<br />
<table border="0" cellpadding="5" cellspacing="0" width="770" class=rex style="border:1px solid #dddddd;">
<form action="index.php" method="post" name="loginformular">
  <tr>
    <td class="dgrey">
      <table width="250" cellpadding="3" cellspacing="0" border="0">
        <tr>
          <td valign="middle"><input type="hidden" name="page" value="structure" class=inp100>'.$I18N->msg('login_name').':</td>
          <td valign="middle"><input type="text" size="15" value="'.$REX_ULOGIN.'" name="REX_ULOGIN" autocomplete="off"></td>
          <td valign="middle">&nbsp;</td>
        </tr>
        <tr>
          <td valign="middle">'.$I18N->msg('password').':</td>
          <td valign="middle"><input type="password" size="15" name="REX_UPSW"></td>
          <td valign="middle"><input type="submit" value="'.$I18N->msg('login').'"></td>
        </tr>
      </table>
    </td>
  </tr>
  </form>
</table>
'."\n";

echo '<script type="text/javascript"> 
   <!-- 
   document.loginformular.REX_ULOGIN.focus(); 
   //--> 
</script>
'."\n";

include $REX['INCLUDE_PATH']."/layout/bottom.php";

?>