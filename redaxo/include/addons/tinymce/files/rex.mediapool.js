// Disable TinyMCE's default popup CSS
var allLinks = document.getElementsByTagName('link');
allLinks[allLinks.length-1].parentNode.removeChild(allLinks[allLinks.length-1]);

// get selected media
function TinyMCE_selectMedia(filename, alt)
{
	var win = tinyMCEPopup.getWindowArg('window');
	var type = tinyMCEPopup.getWindowArg('typeid');

	if (alt)
	{
		// Beschreibung
		if (win.document.getElementById('alt'))
		{
			win.document.getElementById('alt').value = alt;
		}
		// Titel
		if (win.document.getElementById('title'))
		{
			win.document.getElementById('title').value = alt;
		}
	}

	if (type == 'image')
	{
		win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = '%FRONTEND_FILE%?tinymceimg=' + filename;
		if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
		if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage('%FRONTEND_FILE%?tinymceimg=' + filename);
	}

	if (type == 'media')
	{
		win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = 'files/' + filename;	
	}

	if (type == 'media' && win.generatePreview)
	{
		win.generatePreview(filename);
	}

	tinyMCEPopup.close();
}
