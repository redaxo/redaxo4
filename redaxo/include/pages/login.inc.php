<?php

/**
 *
 * @package redaxo4
 * @version $Id: login.inc.php,v 1.2 2008/03/19 13:02:44 kills Exp $
 */

rex_title('Login');

$js = '';
if (isset($FORM['loginmessage']) && $FORM['loginmessage'] != '')
{
  echo rex_warning($FORM['loginmessage'])."\n";
  $js = '
    var time_el = $("p.rex-message span strong");

    if(time_el.length == 1) {
      function disableLogin() {
        time_el.html((parseInt(time_el.html(), 10)-1) + "");

        if(parseInt(time_el.html(), 10) > 0) {
          setTimeout(disableLogin, 1000);
        } else {
          $("p.rex-message span").html("'. htmlspecialchars($I18N->msg('login_welcome')) .'");
          $("#loginformular input:not(:hidden)").attr("disabled", "");
          $("#REX_ULOGIN").focus();
        }
      };

      $("#loginformular input:not(:hidden)").attr("disabled", "disabled");
      setTimeout(disableLogin, 1000);
    }';
}

$REX_ULOGIN = rex_post('REX_ULOGIN', 'string');

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
      <input type="text" value="'.stripslashes(htmlspecialchars($REX_ULOGIN)).'" id="REX_ULOGIN" name="REX_ULOGIN"'. rex_tabindex() .' />
    </p>
    <p>
      <label for="REX_UPSW">'.$I18N->msg('password').':</label>
      <input type="password" name="REX_UPSW" id="REX_UPSW"'. rex_tabindex() .' />
	    <input class="rex-sbmt" type="submit" value="'.$I18N->msg('login').'"'. rex_tabindex() .' />
    </p>
  </fieldset>
</form>
</div>
<script type="text/javascript">
   <!--
  jQuery(function($) {
    $("#REX_ULOGIN").focus();
    $("#javascript").val("1");
    '. $js .'
  });
   //-->
</script>
<!-- *** OUTPUT OF LOGIN-FORM - END *** -->

';

?>