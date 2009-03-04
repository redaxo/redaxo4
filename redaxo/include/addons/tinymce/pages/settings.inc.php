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
	$mypage = 'tinymce';
	$func = rex_request('func', 'string');

	// CSS speichern
	if ($func == "update")
	{
		$active = rex_request('active', 'string');
		$lang = strtolower(rex_request('lang', 'string'));
		if ($lang == '')
			$lang = 'de';
		$pages = strtolower(rex_request('pages', 'string'));
		if ($pages == '')
			$pages = 'content';
		$includepages = explode(',', trim(str_replace(' ','',$pages)));
		if (!in_array('content', $includepages)) // Bei 'content' immer!
			$pages = 'content, '.$pages;
		$foreground = rex_request('foreground', 'string');
		$background = rex_request('background', 'string');
		$validxhtml = rex_request('validxhtml', 'string');
		$theme = rex_request('theme', 'string');
		$skin = rex_request('skin', 'string');
		$emoticons = rex_request('emoticons', 'string');
		$media = rex_request('media', 'string');
		$highlight = rex_request('highlight', 'string');
		$highlight = rex_request('highlight', 'string');

		$REX['ADDON'][$mypage]['active'] = $active;
		$REX['ADDON'][$mypage]['lang'] = $lang;
		$REX['ADDON'][$mypage]['pages'] = $pages;
		$REX['ADDON'][$mypage]['foreground'] = $foreground;
		$REX['ADDON'][$mypage]['background'] = $background;
		$REX['ADDON'][$mypage]['validxhtml'] = $validxhtml;
		$REX['ADDON'][$mypage]['theme'] = $theme;
		$REX['ADDON'][$mypage]['skin'] = $skin;
		$REX['ADDON'][$mypage]['emoticons'] = $emoticons;
		$REX['ADDON'][$mypage]['media'] = $media;
		$REX['ADDON'][$mypage]['highlight'] = $highlight;

		$content = '
$REX[\'ADDON\'][$mypage][\'active\'] = \'' . $active . '\';
$REX[\'ADDON\'][$mypage][\'lang\'] = \'' . $lang . '\';
$REX[\'ADDON\'][$mypage][\'pages\'] = \'' . $pages . '\';
$REX[\'ADDON\'][$mypage][\'foreground\'] = \'' . $foreground . '\';
$REX[\'ADDON\'][$mypage][\'background\'] = \'' . $background . '\';
$REX[\'ADDON\'][$mypage][\'validxhtml\'] = \'' . $validxhtml . '\';
$REX[\'ADDON\'][$mypage][\'theme\'] = \'' . $theme . '\';
$REX[\'ADDON\'][$mypage][\'skin\'] = \'' . $skin . '\';
$REX[\'ADDON\'][$mypage][\'emoticons\'] = \'' . $emoticons . '\';
$REX[\'ADDON\'][$mypage][\'media\'] = \'' . $media . '\';
$REX[\'ADDON\'][$mypage][\'highlight\'] = \'' . $highlight . '\';
		';

		$file = dirname( __FILE__) .'/../config.inc.php';
		rex_replace_dynamic_contents($file, $content);
		echo rex_info($I18N_A52->msg('msg_settings_saved'));
	}

	$tinymce_langs = str_replace('.js', '', implode(',', a52_readFolderFiles(dirname( __FILE__) . '/../tinymce/jscripts/tiny_mce/langs')));
	
	// Tabelle bei REDAXO 3.2.x ausgeben
	if ($rxa_tinymce['rexversion'] == '32')
	{
		echo '<table border="0" cellpadding="5" cellspacing="1" width="770">';
		echo '<tr>';
		echo '<td class="grey">';
	}
?>

<div class="rex-addon-output">

  <form action="index.php" method="post">
  <input type="hidden" name="page" value="tinymce" />
  <input type="hidden" name="subpage" value="settings" />
  <input type="hidden" name="func" value="update" />

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_active'); ?></h2>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="checkbox" id="tinymce_active" name="active" maxlength="2" <?php if ($REX['ADDON'][$mypage]['active'] == 'on') echo 'checked="checked"'; ?> />
    <label for="tinymce_active"><?php echo $I18N_A52->msg('tinymce_active'); ?></label>
  </div>
  <br />

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_language'); ?></h2>
  <div class="rex-addon-content">
    <input style="width:50px;" type="text" id="tinymce_lang" name="lang" maxlength="2" value="<?php echo $REX['ADDON'][$mypage]['lang']; ?>" />
    <label for="tinymce_lang"><?php echo $I18N_A52->msg('tinymce_langs', $tinymce_langs); ?></label>
  </div>
  <br />
  
  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_pages'); ?></h2>
  <div class="rex-addon-content">
    <?php echo $I18N_A52->msg('tinymce_pages'); ?><br /><br />
    <input style="width:99%;" type="text" id="tinymce_pages" name="pages" value="<?php echo $REX['ADDON'][$mypage]['pages']; ?>" />
  </div>
  <br />

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_foreground'); ?></h2>
  <div class="rex-addon-content">
    <?php echo $I18N_A52->msg('tinymce_foreground'); ?><br /><br />
    <input style="width:99%;" type="text" id="tinymce_foreground" name="foreground" value="<?php echo $REX['ADDON'][$mypage]['foreground']; ?>" />
  </div>
  <br />

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_background'); ?></h2>
  <div class="rex-addon-content">
    <?php echo $I18N_A52->msg('tinymce_background'); ?><br /><br />
    <input style="width:99%;" type="text" id="tinymce_background" name="background" value="<?php echo $REX['ADDON'][$mypage]['background']; ?>" />
  </div>
  <br />

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_validxhtml'); ?></h2>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="checkbox" id="tinymce_validxhtml" name="validxhtml" maxlength="2" <?php if ($REX['ADDON'][$mypage]['validxhtml'] == 'on') echo 'checked="checked"'; ?> />
    <label for="tinymce_validxhtml"><?php echo $I18N_A52->msg('tinymce_validxhtml'); ?></label>
  </div>
  <br />

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_theme'); ?></h2>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="radio" id="tinymce_theme_simple" name="theme" value="simple" <?php if ($REX['ADDON'][$mypage]['theme'] == 'simple') echo 'checked="checked"'; ?>/>
    <label for="tinymce_theme_simple"><strong><?php echo $I18N_A52->msg('theme_simple'); ?></strong><br /><img style="margin-left:60px;" src="./include/addons/tinymce/img/theme_simple.jpg" alt="" /></label>
  </div>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="radio" id="tinymce_theme_default" name="theme" value="default" <?php if ($REX['ADDON'][$mypage]['theme'] == 'default') echo 'checked="checked"'; ?> />
    <label for="tinymce_theme_default"><strong><?php echo $I18N_A52->msg('theme_default'); ?></strong><br /><img style="margin-left:60px;" src="./include/addons/tinymce/img/theme_default.jpg" alt="" /></label>
  </div>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="radio" id="tinymce_theme_advanced" name="theme" value="advanced" <?php if ($REX['ADDON'][$mypage]['theme'] == 'advanced') echo 'checked="checked"'; ?> />
    <label for="tinymce_theme_advanced"><strong><?php echo $I18N_A52->msg('theme_advanced'); ?></strong><br /><img style="margin-left:60px;" src="./include/addons/tinymce/img/theme_advanced.jpg" alt="" /></label>
  </div>
  <br />

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_skin'); ?></h2>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="radio" id="tinymce_skin_standard" name="skin" value="default" <?php if ($REX['ADDON'][$mypage]['skin'] == 'default') echo 'checked="checked"'; ?>/>
    <label for="tinymce_skin_standard"><strong><?php echo $I18N_A52->msg('skin_standard'); ?></strong><br /><img style="margin-left:60px;" src="./include/addons/tinymce/img/skin_default.jpg" alt="" /></label>
  </div>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="radio" id="tinymce_skin_o2k7" name="skin" value="o2k7" <?php if ($REX['ADDON'][$mypage]['skin'] == 'o2k7') echo 'checked="checked"'; ?>/>
    <label for="tinymce_skin_o2k7"><strong><?php echo $I18N_A52->msg('skin_o2k7'); ?></strong><br /><img style="margin-left:60px;" src="./include/addons/tinymce/img/skin_o2k7.jpg" alt="" /></label>
  </div>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="radio" id="tinymce_skin_o2k7_silver" name="skin" value="o2k7_silver" <?php if ($REX['ADDON'][$mypage]['skin'] == 'o2k7_silver') echo 'checked="checked"'; ?>/>
    <label for="tinymce_skin_o2k7_silver"><strong><?php echo $I18N_A52->msg('skin_o2k7_silver'); ?></strong><br /><img style="margin-left:60px;" src="./include/addons/tinymce/img/skin_o2k7_silver.jpg" alt="" /></label>
  </div>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="radio" id="tinymce_skin_o2k7_black" name="skin" value="o2k7_black" <?php if ($REX['ADDON'][$mypage]['skin'] == 'o2k7_black') echo 'checked="checked"'; ?>/>
    <label for="tinymce_skin_o2k7_black"><strong><?php echo $I18N_A52->msg('skin_o2k7_black'); ?></strong><br /><img style="margin-left:60px;" src="./include/addons/tinymce/img/skin_o2k7_black.jpg" alt="" /></label>
  </div>
  <br />

  <h2 class="rex-hl2"><?php echo $I18N_A52->msg('title_buttons'); ?></h2>
  <div class="rex-addon-content">
    <input style="margin:0 20px;" type="checkbox" id="tinymce_btn_emoticons" name="emoticons" maxlength="2" <?php if ($REX['ADDON'][$mypage]['emoticons'] == 'on') echo 'checked="checked"'; ?> />
    <label for="tinymce_btn_emoticons"><img style="margin-right:5px;" src="./include/addons/tinymce/img/emoticons.gif" alt="" width="20" height="20" /><?php echo $I18N_A52->msg('tinymce_btn_emoticons'); ?></label>
    <br /><br />
    <input style="margin:0 20px;" type="checkbox" id="tinymce_btn_media" name="media" maxlength="2" <?php if ($REX['ADDON'][$mypage]['media'] == 'on') echo 'checked="checked"'; ?> />
    <label for="tinymce_btn_media"><img style="margin-right:5px;" src="./include/addons/tinymce/img/media.gif" alt="" width="20" height="20" /><?php echo $I18N_A52->msg('tinymce_btn_media'); ?></label>
<!--
	 <br /><br />
    <input style="margin:0 20px;" type="checkbox" id="tinymce_btn_highlight" name="highlight" maxlength="2" <?php if ($REX['ADDON'][$mypage]['highlight'] == 'on') echo 'checked="checked"'; ?> />
    <label for="tinymce_btn_highlight"><img style="margin-right:5px;" src="./include/addons/tinymce/img/highlight.gif" alt="" width="20" height="20" /><?php echo $I18N_A52->msg('tinymce_btn_highlight'); ?></label>
-->
  </div>

  <br /><br />
  <input type="submit" class="rex-sbmt" name="sendit" value="<?php echo $I18N_A52->msg('button_save_settings'); ?>" />
  <br /><br />

  </form>
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