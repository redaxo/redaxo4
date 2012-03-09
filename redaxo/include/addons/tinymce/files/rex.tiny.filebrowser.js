// Redaxo-Popups
// ------------------------------------------------------------
function TinyMCE_FileBrowser(field_name, url, type, win)
{
	//alert('Field_Name: ' + field_name + '\nURL: ' + url + '\nType: ' + type + '\nWin: ' + win); // debug/testing
	var cmsURL = window.location.toString();
	var urlbase = cmsURL.split('index.php');

	if (type == 'image' || type == 'media')
	{
		nameurl = url.replace('%FRONTEND_FILE%?tinymceimg=', '');
		if (nameurl != '')
		{
			nameurl = '&subpage=detail&file_name='+nameurl;
		}
		cmsURL = 'index.php?page=%MEDIAPOOL%&tinymce=true&opener_input_field='+field_name+'&clang=%CLANG%'+nameurl;
		popupTitle = 'Medienpool';
	}

	if (type == 'file')
	{
		idurl = url.replace('redaxo://', '');
		if (idurl != '')
		{
			idurl = '&category_id='+idurl;
		}
		cmsURL = 'index.php?page=linkmap&tinymce=true&opener_input_field='+field_name+'&clang=%CLANG%'+idurl;
		popupTitle = 'Linkmap';
	}

	cmsURL = urlbase[0] + cmsURL;

	tinyMCE.activeEditor.windowManager.open({
		file : cmsURL,
		title : popupTitle,
		width : 800,  // Your dimensions may differ - toy around with them!
		height : 600,
		resizable : 'yes',
		scrollbars : 'yes',
		inline : 'yes',  // This parameter only has an effect if you use the inlinepopups plugin!
		popup_css : false,  // Disable TinyMCE's default popup CSS
		close_previous : 'no'
	}, {
		window : win,
		typeid : type,
		input : field_name
	});

	return false;
}