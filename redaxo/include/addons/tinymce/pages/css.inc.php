<?php
/**
 * TinyMCE Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @author andreas[dot]eberhard[at]redaxo[dot]de Andreas Eberhard
 * @author <a href="http://rex.andreaseberhard.de">rex.andreaseberhad.de</a>
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

	$rxa_tinymce['get_func'] = rex_request('func', 'string');
	$rxa_tinymce['get_tinymcecss'] = rex_request('tinymcecss', 'string');

	// CSS speichern
	if ($rxa_tinymce['get_func'] == 'update')
	{
		@chmod(dirname(__FILE__) . '/config.inc.php', 0755);

		$filename = $rxa_tinymce['fe_path'] . '/content.css';
		$rxa_tinymce['get_tinymcecss'] = stripslashes($rxa_tinymce['get_tinymcecss']);
		if (file_put_contents($filename, $rxa_tinymce['get_tinymcecss']))
		{
			echo rex_info($I18N_A52->msg('msg_css_saved'));
		}
		else
		{
			echo rex_warning($I18N_A52->msg('msg_css_error'));
		}
	}

	// Tabelle bei REDAXO 3.2.x ausgeben
	if ($rxa_tinymce['rexversion'] == '32')
	{
		echo '<table border="0" cellpadding="5" cellspacing="1" width="770">';
		echo '<tr>';
		echo '<td class="grey">';
	}
?>

<div class="rex-addon-output">

	<h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_css_wysiwyg'); ?></h2>

	<div class="rex-area">
	<div class="rex-form">
	
		<form action="index.php" method="post">
		<fieldset>
		<div class="rex-form-wrapper">
		<input type="hidden" name="page" value="tinymce" />
		<input type="hidden" name="subpage" value="css" />
		<input type="hidden" name="func" value="update" />
<?php
	$filename = $rxa_tinymce['fe_path'] . '/content.css';
	if(is_readable($filename))
	{
		$csstext = htmlspecialchars(file_get_contents($filename));
	}
?>
  <div class="rex-form-row rex-form-element-v2">

<textarea name="tinymcecss" cols="80" rows="20" class="tinymce-code-big">
<?php echo htmlspecialchars($csstext); ?>
</textarea>
		</div>

  <div class="rex-form-row rex-form-element-v2">
			<p class="rex-form-submit">
				<input class="rex-form-submit rex-form-submit2" type="submit" value="<?php echo $I18N_A52->msg('button_save_css'); ?>" />
			</p>
		</div>

		</div>
      </fieldset>
		</form>

	</div> <!-- END rex-form -->
	</div> <!-- END rex-area -->

</div> <!-- END rex-addon-output -->

<?php
	// Tabelle bei REDAXO 3.2.x ausgeben
	if ($rxa_tinymce['rexversion'] == '32')
	{
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}
?>