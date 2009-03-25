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
?>

<?php
//	$rxa_tinymce['name'] = 'tinymce';
	
	$rxa_tinymce['get_func'] = rex_request('func', 'string');

	// CSS speichern
	if ($rxa_tinymce['get_func'] == 'update')
	{
		$rxa_tinymce['get_active'] = rex_request('active', 'string');
		$rxa_tinymce['get_lang'] = strtolower(rex_request('lang', 'string'));
		if ($rxa_tinymce['get_lang'] == '')
			$rxa_tinymce['get_lang'] = 'de';
		$rxa_tinymce['get_pages'] = strtolower(rex_request('pages', 'string'));
		if ($rxa_tinymce['get_pages'] == '')
			$rxa_tinymce['get_pages'] = 'content';
		$includepages = explode(',', trim(str_replace(' ', '', $rxa_tinymce['get_pages'])));
		if (!in_array('content', $includepages)) // Bei 'content' immer!
			$rxa_tinymce['get_pages'] = 'content, ' . $rxa_tinymce['get_pages'];
		$rxa_tinymce['get_foreground'] = rex_request('foreground', 'string');
		$rxa_tinymce['get_background'] = rex_request('background', 'string');
		$rxa_tinymce['get_validxhtml'] = rex_request('validxhtml', 'string');
		$rxa_tinymce['get_theme'] = rex_request('theme', 'string');
		$rxa_tinymce['get_skin'] = rex_request('skin', 'string');
		$rxa_tinymce['get_emoticons'] = rex_request('emoticons', 'string');
		$rxa_tinymce['get_media'] = rex_request('media', 'string');
		$rxa_tinymce['get_highlight'] = rex_request('highlight', 'string');

		$REX['ADDON'][$rxa_tinymce['name']]['active'] = $rxa_tinymce['get_active'];
		$REX['ADDON'][$rxa_tinymce['name']]['lang'] = $rxa_tinymce['get_lang'];
		$REX['ADDON'][$rxa_tinymce['name']]['pages'] = $rxa_tinymce['get_pages'];
		$REX['ADDON'][$rxa_tinymce['name']]['foreground'] = $rxa_tinymce['get_foreground'];
		$REX['ADDON'][$rxa_tinymce['name']]['background'] = $rxa_tinymce['get_background'];
		$REX['ADDON'][$rxa_tinymce['name']]['validxhtml'] = $rxa_tinymce['get_validxhtml'];
		$REX['ADDON'][$rxa_tinymce['name']]['theme'] = $rxa_tinymce['get_theme'];
		$REX['ADDON'][$rxa_tinymce['name']]['skin'] = $rxa_tinymce['get_skin'];
		$REX['ADDON'][$rxa_tinymce['name']]['emoticons'] = $rxa_tinymce['get_emoticons'];
		$REX['ADDON'][$rxa_tinymce['name']]['media'] = $rxa_tinymce['get_media'];
		$REX['ADDON'][$rxa_tinymce['name']]['highlight'] = $rxa_tinymce['get_highlight'];

		$rxa_tinymce['config_content'] = '
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'active\'] = \'' . $rxa_tinymce['get_active'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'lang\'] = \'' . $rxa_tinymce['get_lang'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'pages\'] = \'' . $rxa_tinymce['get_pages'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'foreground\'] = \'' . $rxa_tinymce['get_foreground'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'background\'] = \'' . $rxa_tinymce['get_background'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'validxhtml\'] = \'' . $rxa_tinymce['get_validxhtml'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'theme\'] = \'' . $rxa_tinymce['get_theme'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'skin\'] = \'' . $rxa_tinymce['get_skin'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'emoticons\'] = \'' . $rxa_tinymce['get_emoticons'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'media\'] = \'' . $rxa_tinymce['get_media'] . '\';
$REX[\'ADDON\'][$rxa_tinymce[\'name\']][\'highlight\'] = \'' . $rxa_tinymce['get_highlight'] . '\';
		';

		//$filename = dirname( __FILE__) . '/../config.inc.php';
		$filename = $REX['INCLUDE_PATH'] . '/addons/' . $rxa_tinymce['name'] . '/config.inc.php';
		rex_replace_dynamic_contents($filename, $rxa_tinymce['config_content']);
		echo rex_info($I18N_A52->msg('msg_settings_saved'));
	}

	$rxa_tinymce['tinymce_langs'] = str_replace('.js', '', implode(',', a52_readFolderFiles($rxa_tinymce['fe_path'] . '/tiny_mce/langs')));
	
	// Tabelle bei REDAXO 3.2.x ausgeben
	if ($rxa_tinymce['rexversion'] == '32')
	{
		echo '<table border="0" cellpadding="5" cellspacing="1" width="770">';
		echo '<tr>';
		echo '<td class="grey">';
	}
?>

<div class="rex-addon-output">

<h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_config'); ?></h2>

	<div class="rex-area">
	<div class="rex-form">

	<form action="index.php" method="post">
		<fieldset>
      <div class="rex-form-wrapper">
			<input type="hidden" name="page" value="tinymce" />
			<input type="hidden" name="subpage" value="settings" />
			<input type="hidden" name="func" value="update" />

			<div class="rex-form-row-tiny-label-right">
				<h5><?php echo $I18N_A52->msg('title_active'); ?></h5>
				<p class="rex-form-text">
				<input type="checkbox" id="tinymce_active" name="active" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['active'] == 'on') echo 'checked="checked"'; ?> />
				<label for="tinymce_active"><?php echo $I18N_A52->msg('tinymce_active'); ?></label>
				</p>
			</div>

			<div class="rex-form-row-tiny-label-right">
				<h5><?php echo $I18N_A52->msg('title_language'); ?></h5>
				<p class="rex-form-text">
				<input type="text" id="tinymce_lang" name="lang" maxlength="2" value="<?php echo $REX['ADDON'][$rxa_tinymce['name']]['lang']; ?>" />
				<label for="tinymce_lang"><?php echo $I18N_A52->msg('tinymce_langs', $rxa_tinymce['tinymce_langs']); ?></label>
				</p>
			</div>
			
			<div class="rex-form-row-tiny-label-top">
				<h5><?php echo $I18N_A52->msg('title_pages'); ?></h5>
				<p class="rex-form-text">
				<label for="tinymce_pages"><?php echo $I18N_A52->msg('tinymce_pages'); ?></label><br />
				<input type="text" id="tinymce_pages" name="pages" value="<?php echo $REX['ADDON'][$rxa_tinymce['name']]['pages']; ?>" />
				</p>
			</div>

			<div class="rex-form-row-tiny-label-top">
				<h5><?php echo $I18N_A52->msg('title_foreground'); ?></h5>
				<p class="rex-form-text">
				<label for="tinymce_foreground"><?php echo $I18N_A52->msg('tinymce_foreground'); ?></label><br />
				<input type="text" id="tinymce_foreground" name="foreground" value="<?php echo $REX['ADDON'][$rxa_tinymce['name']]['foreground']; ?>" />
				</p>
			</div>

			<div class="rex-form-row-tiny-label-top">
				<h5><?php echo $I18N_A52->msg('title_background'); ?></h5>
				<p class="rex-form-text">
				<label for="tinymce_background"><?php echo $I18N_A52->msg('tinymce_background'); ?></label><br />
				<input type="text" id="tinymce_background" name="background" value="<?php echo $REX['ADDON'][$rxa_tinymce['name']]['background']; ?>" />
				</p>
			</div>

			<div class="rex-form-row-tiny-label-right">
				<h5><?php echo $I18N_A52->msg('title_validxhtml'); ?></h5>
				<p class="rex-form-text">
				<input type="checkbox" id="tinymce_validxhtml" name="validxhtml" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['validxhtml'] == 'on') echo 'checked="checked"'; ?> />
				<label for="tinymce_validxhtml"><?php echo $I18N_A52->msg('tinymce_validxhtml'); ?></label>
				</p>
			</div>

			<div class="rex-form-row-tiny-label-right">
				<h5><?php echo $I18N_A52->msg('title_theme'); ?></h5>
				<p class="rex-form-text">
				<input type="radio" id="tinymce_theme_simple" name="theme" value="simple" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['theme'] == 'simple') echo 'checked="checked"'; ?>/>
				<label for="tinymce_theme_simple"><strong><?php echo $I18N_A52->msg('theme_simple'); ?></strong><br /><img class="theme" src="./include/addons/tinymce/img/theme_simple.jpg" alt="" /></label>
				<br /><br />
				<input type="radio" id="tinymce_theme_default" name="theme" value="default" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['theme'] == 'default') echo 'checked="checked"'; ?> />
				<label for="tinymce_theme_default"><strong><?php echo $I18N_A52->msg('theme_default'); ?></strong><br /><img class="theme" src="./include/addons/tinymce/img/theme_default.jpg" alt="" /></label>
				<br /><br />
				<input type="radio" id="tinymce_theme_advanced" name="theme" value="advanced" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['theme'] == 'advanced') echo 'checked="checked"'; ?> />
				<label for="tinymce_theme_advanced"><strong><?php echo $I18N_A52->msg('theme_advanced'); ?></strong><br /><img class="theme" src="./include/addons/tinymce/img/theme_advanced.jpg" alt="" /></label>
				</p>
			</div>

			<div class="rex-form-row-tiny-label-right">
				<h5><?php echo $I18N_A52->msg('title_skin'); ?></h5>
				<p class="rex-form-text">
				<input type="radio" id="tinymce_skin_standard" name="skin" value="default" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['skin'] == 'default') echo 'checked="checked"'; ?>/>
				<label for="tinymce_skin_standard"><strong><?php echo $I18N_A52->msg('skin_standard'); ?></strong><br /><img class="skin" src="./include/addons/tinymce/img/skin_default.jpg" alt="" /></label>
				<br /><br />
				<input type="radio" id="tinymce_skin_o2k7" name="skin" value="o2k7" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['skin'] == 'o2k7') echo 'checked="checked"'; ?>/>
				<label for="tinymce_skin_o2k7"><strong><?php echo $I18N_A52->msg('skin_o2k7'); ?></strong><br /><img class="skin" src="./include/addons/tinymce/img/skin_o2k7.jpg" alt="" /></label>
				<br /><br />
				<input type="radio" id="tinymce_skin_o2k7_silver" name="skin" value="o2k7_silver" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['skin'] == 'o2k7_silver') echo 'checked="checked"'; ?>/>
				<label for="tinymce_skin_o2k7_silver"><strong><?php echo $I18N_A52->msg('skin_o2k7_silver'); ?></strong><br /><img class="skin" src="./include/addons/tinymce/img/skin_o2k7_silver.jpg" alt="" /></label>
				<br /><br />
				<input type="radio" id="tinymce_skin_o2k7_black" name="skin" value="o2k7_black" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['skin'] == 'o2k7_black') echo 'checked="checked"'; ?>/>
				<label for="tinymce_skin_o2k7_black"><strong><?php echo $I18N_A52->msg('skin_o2k7_black'); ?></strong><br /><img class="skin" src="./include/addons/tinymce/img/skin_o2k7_black.jpg" alt="" /></label>
				</p>
			</div>

			<div class="rex-form-row-tiny-label-right">
				<h5><?php echo $I18N_A52->msg('title_buttons'); ?></h5>
				<p class="rex-form-text">
				<input type="checkbox" id="tinymce_btn_emoticons" name="emoticons" maxlength="2" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['emoticons'] == 'on') echo 'checked="checked"'; ?> />
				<label for="tinymce_btn_emoticons"><img class="icon" src="./include/addons/tinymce/img/emoticons.gif" alt="" width="20" height="20" /><?php echo $I18N_A52->msg('tinymce_btn_emoticons'); ?></label>
				<br /><br />
				<input type="checkbox" id="tinymce_btn_media" name="media" maxlength="2" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['media'] == 'on') echo 'checked="checked"'; ?> />
				<label for="tinymce_btn_media"><img class="icon" src="./include/addons/tinymce/img/media.gif" alt="" width="20" height="20" /><?php echo $I18N_A52->msg('tinymce_btn_media'); ?></label>
				<br /><br />
				<input type="checkbox" id="tinymce_btn_highlight" name="highlight" maxlength="2" <?php if ($REX['ADDON'][$rxa_tinymce['name']]['highlight'] == 'on') echo 'checked="checked"'; ?> />
				<label for="tinymce_btn_highlight"><img class="icon" src="./include/addons/tinymce/img/syntaxhighlighter.gif" alt="" width="20" height="20" /><?php echo $I18N_A52->msg('tinymce_btn_highlight'); ?></label>
				</p>
			</div>

			<div class="rex-form-row">
				<p class="rex-form-submit">
					<input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N_A52->msg('button_save_settings'); ?>" />
				</p>
			</div>

		</div> <!-- END rex-form-wrapper -->
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