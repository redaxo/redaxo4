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
          $("#rex-form-login").focus();
        }
      };

      $("#loginformular input:not(:hidden)").attr("disabled", "disabled");
      setTimeout(disableLogin, 1000);
    }';
}

$REX_ULOGIN = rex_post('REX_ULOGIN', 'string');

echo '

<!-- *** OUTPUT OF LOGIN-FORM - START *** -->
<div class="rex-form rex-form-login">
<form action="index.php" method="post" id="loginformular">
  <fieldset class="rex-form-col-1">
    <legend>Login</legend>
    <input type="hidden" name="javascript" value="0" id="javascript" />
    
    <div class="rex-form-wrapper">
    
    	<div class="rex-form-row">
		    <p class="rex-form-col-a rex-form-text">
    			<label for="rex-form-login">'.$I18N->msg('login_name').':</label>
      		<input type="text" value="'.stripslashes(htmlspecialchars($REX_ULOGIN)).'" id="rex-form-login" name="REX_ULOGIN"'. rex_tabindex() .' />
    		</p>
    	</div>
    	<div class="rex-form-row">
		    <p class="rex-form-col-a rex-form-password">
      		<label for="REX_UPSW">'.$I18N->msg('password').':</label>
      		<input class="rex-form-password" type="password" name="REX_UPSW" id="REX_UPSW"'. rex_tabindex() .' />
	    		<input class="rex-form-submit" type="submit" value="'.$I18N->msg('login').'"'. rex_tabindex() .' />
	    	</p>
	    </div>
	  </div>
  </fieldset>
</form>
</div>
<script type="text/javascript">
   <!--
  jQuery(function($) {
    $("#rex-form-login").focus();
    $("#javascript").val("1");
    '. $js .'
  });
   //-->
</script>
<!-- *** OUTPUT OF LOGIN-FORM - END *** -->

';