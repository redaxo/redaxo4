<?php

/** 
 *  
 * @package redaxo3
 * @version $Id$
 */ 

rex_title("Login","");

if (isset($FORM['loginmessage']) and $FORM['loginmessage'] != "")
{
  echo '<p class="rex-warning"><span>'.$FORM['loginmessage'].'</span></p>'."\n";
}

if (!isset($REX_ULOGIN)) 
{ 
  $REX_ULOGIN = ''; 
}

echo '

<!-- *** OUTPUT OF LOGIN-FORM - START *** -->
<div class="rex-lgn-frm">
<form action="index.php" method="post" id="loginformular">
  <fieldset>
    <legend class="rex-lgnd">Login</legend>
    <input type="hidden" name="javascript" value="0" id="javascript" />
    <input type="hidden" name="page" value="structure" />
    <p>
      <label for="REX_ULOGIN">'.$I18N->msg('login_name').':</label>
      <input type="text" value="'.stripslashes(htmlspecialchars($REX_ULOGIN)).'" id="REX_ULOGIN" name="REX_ULOGIN" />
    </p>
    <p>
      <label for="REX_UPSW">'.$I18N->msg('password').':</label>
      <input type="password" name="REX_UPSW" id="REX_UPSW" />
	  <input class="rex-sbmt" type="submit" value="'.$I18N->msg('login').'" />
    </p>
  </fieldset>
</form>
</div>
<script type="text/javascript"> 
   <!-- 
   var needle = new getObj("REX_ULOGIN");
   needle.obj.focus();

   var needle = new getObj("javascript");
   needle.obj.value = 1;
   //--> 
</script>
<!-- *** OUTPUT OF LOGIN-FORM - END *** -->

';

?>