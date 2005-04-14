/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('redaxo', 'en,de');


function TinyMCE_redaxo_getControlHTML(control_name) {
	switch (control_name) {
		case "pasteRichtext":
			return '<img id="{$editor_id}_pasteRichtext" src="{$pluginurl}/images/pasteRichtext.gif" title="{$lang_redaxo_pasteRichtext_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcepasteRichtext\');">';
	}
	switch (control_name) {
		case "insertEmail":
			return '<img id="{$editor_id}_insertEmail" src="{$pluginurl}/images/insertEmail.gif" title="{$lang_redaxo_insertEmail_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceinsertEmail\');">';
	}
	switch (control_name) {
		case "linkHack":
			return '<img id="{$editor_id}_linkHack" src="{$pluginurl}/images/link.gif" title="{$lang_redaxo_insertEmail_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.settings[\'insertlink_callback\']=false;tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceLink\');tinyMCE.settings[\'insertlink_callback\']=\'insertIntLink\';">';
	}

	return "";
}

function TinyMCE_redaxo_execCommand(editor_id, element, command, user_interface, value) {

	switch (command) {
		case "mcepasteRichtext":

			var template = new Array();

			template['file'] = '../../plugins/redaxo/pasteRichtext.htm'; // Relative to theme
			template['width'] = 600;
			template['height'] = 400;

			tinyMCE.openWindow(template, {editor_id : editor_id});

			return true;

	}
	switch (command) {
		case "mceinsertEmail":

			var template = new Array();

			template['file'] = '../../plugins/redaxo/insertEmail.htm'; // Relative to theme
			template['width'] = 600;
			template['height'] = 400;

			tinyMCE.openWindow(template, {editor_id : editor_id});

			return true;

	}

	return false;
}