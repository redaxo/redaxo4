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

	$func = rex_request('func', 'string');
	$tinymcecss = rex_request('tinymcecss', 'string');

	// CSS speichern
	if ($func == "update")
	{
		$file = dirname( __FILE__) . '/../tinymce/jscripts/content.css';
		$tinymcecss = stripslashes($tinymcecss);
		if (file_put_contents($file, $tinymcecss))
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
	<div class="rex-addon-content">
		<form action="index.php" method="post">
		<input type="hidden" name="page" value="tinymce" />
		<input type="hidden" name="subpage" value="css" />
		<input type="hidden" name="func" value="update" />
<?php
	$file = dirname( __FILE__) .'/../tinymce/jscripts/content.css';
	if(is_readable($file))
	{
		$csstext = htmlspecialchars(file_get_contents($file));
	}
?>
<textarea name="tinymcecss" style="width:99%;height:350px;">
<?php echo $csstext; ?>
</textarea>
		<br /><br />
		<input type="submit" value="<?php echo $I18N_A52->msg('button_save_css'); ?>" />
		</form>
		<br />
	</div>

</div>

<?php
	// Tabelle bei REDAXO 3.2.x ausgeben
	if ($rxa_tinymce['rexversion'] == '32')
	{
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}
?>