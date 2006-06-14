<?php

/** 
 *  
 * @package redaxo3
 * @version $Id$
 */ 

rex_title("Login","");

if (isset($FORM['loginmessage']) and $FORM['loginmessage'] != "")
{
  echo '<p class="rex-warning">'.$FORM['loginmessage'].'</p>'."\n";
}

if (!isset($REX_ULOGIN)) 
{ 
  $REX_ULOGIN = ''; 
}

echo '

<!-- *** OUTPUT OF LOGIN-FORM - START *** -->
<div class="rex-lgn-loginform">
<form action="index.php" method="post" id="loginformular">
  <fieldset>
    <legend>Login</legend>
    <input type="hidden" name="page" value="structure" />
    <p>
      <label for="REX_ULOGIN">'.$I18N->msg('login_name').':</label>
      <input type="text" value="'.$REX_ULOGIN.'" id="REX_ULOGIN" name="REX_ULOGIN" />
    </p>
    <p>
      <label for="REX_UPSW">'.$I18N->msg('password').':</label>
      <input type="password" name="REX_UPSW" id="REX_UPSW" />
    </p>
    <p>
      <input class="rex-fsubmit" type="submit" value="'.$I18N->msg('login').'" />
    </p>
  </fieldset>
</form>
</div>
<script type="text/javascript"> 
   <!-- 
   var needle = new getObj("REX_ULOGIN");
   needle.obj.focus();
   //--> 
</script>
<!-- *** OUTPUT OF LOGIN-FORM - END *** -->

';

?>