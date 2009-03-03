<!--
function selectMedia(filename)
{
	var win = tinyMCEPopup.getWindowArg("window");
	var type = tinyMCEPopup.getWindowArg("typeid");
	win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = 'files/' + filename;
	if (type == 'image') {
		if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
		if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage('files/' + filename);
	}
	if (type == 'media' && win.generatePreview) {
		win.generatePreview(filename);
	}
	tinyMCEPopup.close();
}
//-->