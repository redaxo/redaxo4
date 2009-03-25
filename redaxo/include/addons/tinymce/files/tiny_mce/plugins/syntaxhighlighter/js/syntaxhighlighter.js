/**
 * @author Andreas Eberhard
 * http://andreaseberhard.de - http://projekte.andreaseberhard.de/tinysyntax
 */

var syntaxhighlighterDialog = {

	init : function(ed) {

		function isSyntaxHighlighter(n) {
			if (n.className.indexOf('brush:') >= 0) {
				return true;
			}
			return false;
		};

		tinyMCEPopup.resizeToInnerSize();

		var formObj = document.forms[0];
		var inst = tinyMCEPopup.editor;
		var elm = inst.selection.getNode();
		var action = 'insert';
		var wcode = '';

		if (elm != null && elm.nodeName == 'PRE' && isSyntaxHighlighter(elm))
			action = 'update';

		formObj.insert.value = tinyMCEPopup.getLang(action, 'Insert', true);

		if (action == 'update') {
			inst.focus();
			inst.selection.select(elm);
			wcode = inst.selection.getContent({format : 'text'});
		}

		setFormValue('code', wcode );

	},

	update : function() {
		var inst = tinyMCEPopup.editor;
		var elm, elementArray, i;
		var f = document.forms[0];

		var elm = inst.selection.getNode();
		elm = inst.dom.getParent(elm, "A");

		tinyMCEPopup.execCommand("mceBeginUndoLevel");
		
		alert(getFormValue('code'));
		
		h = '<pre class="brush:php;">';
		//h += getFormValue('code');
		h += f.code.value;
		h += '</pre>';
		
		inst.execCommand("mceInsertContent", false, h);
		
		
/*
		if (elm != null && elm.nodeName == 'A') {
			setAttrib(elm, 'href', 'mailto:' + getFormValue('email'));
			setAttrib(elm, 'id', getFormValue('id'));
			setAttrib(elm, 'class', getFormValue('class'));
			setAttrib(elm, 'rel', getFormValue('rel'));
		} else {
			tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo : 1});
			elementArray = tinymce.grep(inst.dom.select("a"), function(n) {return inst.dom.getAttrib(n, 'href') == '#mce_temp_url#';});
			for (i=0; i<elementArray.length; i++) {
				elm = elementArray[i];
				setAttrib(elm, 'href', 'mailto:' + getFormValue('email'));
				setAttrib(elm, 'id', getFormValue('id'));
				setAttrib(elm, 'class', getFormValue('class'));
				setAttrib(elm, 'rel', getFormValue('rel'));
			}
		}

		// Don't move caret if selection was image
		if (elm.childNodes.length != 1 || elm.firstChild.nodeName != 'IMG') {
			inst.focus();
			inst.selection.select(elm);
			inst.selection.collapse(0);
			tinyMCEPopup.storeSelection();
		}
*/
		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
	}
};

function setFormValue(name, value) {
	document.forms[0].elements[name].value = value;
}
function getFormValue(name) {
	return document.forms[0].elements[name].value;
}

function setAttrib(elm, attrib, value) {
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib.toLowerCase()];
	var dom = tinyMCEPopup.editor.dom;

	if (typeof(value) == "undefined" || value == null) {
		value = "";

		if (valueElm)
			value = valueElm.value;
	}

	// Clean up the style
	if (attrib == 'style')
		value = dom.serializeStyle(dom.parseStyle(value));

	dom.setAttrib(elm, attrib, value);
}

tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(syntaxhighlighterDialog.init, syntaxhighlighterDialog);
