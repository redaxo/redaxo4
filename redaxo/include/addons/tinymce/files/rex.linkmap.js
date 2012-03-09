// Disable TinyMCE's default popup CSS
var allLinks = document.getElementsByTagName('link');
allLinks[allLinks.length-1].parentNode.removeChild(allLinks[allLinks.length-1]);

// get selected link
function TinyMCE_insertLink(link,name)
{
	var win = tinyMCEPopup.getWindowArg('window');
	win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = link;
	win.document.getElementById('title').value = name;
	tinyMCEPopup.close();
}
