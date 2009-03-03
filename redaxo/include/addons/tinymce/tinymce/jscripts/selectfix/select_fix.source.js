/*
Select Fix for IE6 and IE5.x
Version 0.31
by Fabien Molinet : http://fabien-molinet.fr/
Released under BSD Licence
*/

/*
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
This is the source Javascript. It needs to be packed using : http://dean.edwards.name/packer/ before releasing.
 !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
*/

/* Declare SelectFix class as an anonymous static class */
var SelectFix = (function() {
	/*** Private static attributes ***/
	var oTimer = null;
	var bParseFloatingElementsFixed = false;
	var bIsZIndexRequired = false;
	var bCurrentStyleAvailable = null;
	var bIsW3BoxModelCompliant = null;
	var bIsCheckHtmlAlreadyDone = false;
	var arrInnerElementsFixer = new Array();

	/*** Public static methods ***/
	/* Repair an Element to be able to work on Internet Explorer */
	cSelectFix.repairFloatingElement = function(oElement, bForceResize) {
		/* Add the class and iframe only if they aren't already existing */
		/* If the current element is an Iframe there's no need to do something */
		if ((oElement.className.indexOf("select-free")==-1) && (oElement.tagName!="IFRAME")) {
			/* Change display (since the element may be hidden)
				to calculate the correct width & height of the children elements */
			var strCurrentDisplay = oElement.style.display;
			var bDisplayNone = (strCurrentDisplay == "none");
			if (bDisplayNone)
				oElement.style.display = "block";
			var oElementWidth = oElement.clientWidth;
			var oElementHeight = oElement.clientHeight;
			/* offsetWidth-clientWidth>0 when we have a border */
			var oElementBorderWidth = oElement.offsetWidth - oElementWidth;
			var oElementBorderHeight = oElement.offsetHeight - oElementHeight;
			/* Restore the display if he changed */
			if (bDisplayNone)
				oElement.style.display = strCurrentDisplay;
			
			if (bForceResize) {
				oElement.style.width = oElementWidth+'px';
				oElement.style.height = oElementWidth+'px';
			}			
				
			/* If there is a border we have to change the floating item size */
			if ((oElementBorderWidth>0) || (oElementBorderHeight>0)) {
				/* Add a div inside the current element  (for border fixing over select) */
				var oInnerDiv = document.createElement("div");
				copyCssBorderWithDirectionToNewElement(oElement,oInnerDiv,'border');
				oElement.style.border = '0';
				
				/* Set internal padding */
				var sBorderLeftWidth = getStyle(oInnerDiv,'border-left-width');
				var sBorderTopWidth = getStyle(oInnerDiv,'border-top-width');
				var sBorderRightWidth = getStyle(oInnerDiv,'border-right-width');
				var sBorderBottomWidth = getStyle(oInnerDiv,'border-bottom-width');
				oElement.style.paddingLeft = addSizes(sBorderLeftWidth,getStyle(oElement,'padding-left'))+'px';
				oElement.style.paddingTop = addSizes(sBorderTopWidth,getStyle(oElement,'padding-top'));+'px';
				oElement.style.paddingRight = addSizes(sBorderRightWidth,getStyle(oElement,'padding-right'))+'px';
				oElement.style.paddingBottom = addSizes(sBorderBottomWidth,getStyle(oElement,'padding-bottom'))+'px';

				/* Append the inner div (to fix border of the floating element) */
				if (!isW3BoxModelCompliant()) {
          /* The padding should already be set => we only add the border size */
				  var iNewWidth = oElementWidth+addSizes(sBorderLeftWidth,sBorderRightWidth);
				  var iNewHeight = oElementHeight+addSizes(sBorderTopWidth,sBorderBottomWidth);
				  oElement.style.width = iNewWidth+'px';
				  oElement.style.height = iNewHeight+'px';
					/* Since we're working with IE6 in quirk mode padding's doesn't respect W3c standards */
					appendFixer(oElement,oInnerDiv,iNewWidth,iNewHeight);
				} else {
				  /* Firefox or Internet Explorer in standard mode */
				  appendFixer(oElement,oInnerDiv,oElementWidth, oElementHeight);
				}
			}
			
			/* Add an Iframe inside the current element */
			var oIframe = document.createElement("iframe");
			var sProtocol = String(self.location).substring(0,5);
			if (sProtocol=="https") {
				/* This src is needed if we want the script to work with HTTPS websites */
				oIframe.src = "javascript:'<html></html>';";/* j avascript hack ? */
			} else {
				oIframe.src = "about:blank";
			}

			/* Append the IFrame fixer */
			appendFixer(oElement,oIframe,oElementWidth+oElementBorderWidth, oElementHeight+oElementBorderHeight);
			
			/* Add the CSS class fixer */
			oElement.className += " select-free";
		}
		
		/* If we need to check childrens too */
		if (bParseFloatingElementsFixed)
			recursiveLookChilds(oElement);
	};
	
	/* Repair all Elements each newTimeOut milliseconds to be able to work on Internet Explorer */
	/* If newTimeOut = 0 then the timeout is cancelled */
	cSelectFix.autoRepairFloatingElements = function(newTimeOut) {		
		if (!bIsCheckHtmlAlreadyDone) {
			setTimeout("SelectFix.autoRepairFloatingElements("+newTimeOut+")",newTimeOut);
			return;
		}
			
		/* We have to apply a new timer value */
		if (newTimeOut==0) {
			oTimer = null;
			return;
		}
		
		for (var i=0;i<arrInnerElementsFixer.length;i++) {
			var oElement = arrInnerElementsFixer[i].parentNode;
			if (isW3BoxModelCompliant()) {
				/* Get our border size (if any) */
				var iBorderWidth = arrInnerElementsFixer[i].offsetWidth - arrInnerElementsFixer[i].clientWidth;
				var iBorderHeight = arrInnerElementsFixer[i].offsetHeight - arrInnerElementsFixer[i].clientHeight;
				resizeFixerElement(arrInnerElementsFixer[i], oElement.offsetWidth-iBorderWidth, oElement.offsetHeight-iBorderHeight);
			} else {
				resizeFixerElement(arrInnerElementsFixer[i], oElement.offsetWidth, oElement.offsetHeight);
			}
		}
		checkHtmlElements();	
		oTimer = setTimeout("SelectFix.autoRepairFloatingElements("+newTimeOut+")",newTimeOut);
	};
	
	/* Should we parse floating elements that have been fixed by the current Javascript ? */
	/* They may contain child elements that need to be fixed too but... that costs more CPU  and there's little chance that it happens */
	cSelectFix.parseFloatingElementsFixed = function(bValue) {
		bParseFloatingElementsFixed = bValue;
	};
	
	/* Is the zIndex statement required for floating elements (default is false) ? */
	/* If you don't have to parse elements without zIndex call this method with a true value */
	cSelectFix.isZIndexRequired = function(bValue) {
		bIsZIndexRequired = bValue;
	};
	
	/*** Private static methods ***/
	/* Copy CSS class from the old element to the new element */
	function copyCssBorderWithDirectionToNewElement(oOldElem,oNewElem,strCssRule) {
		var arrCssRules = new Array('-top','-right','-bottom','-left');
		var arrCssRules2 = new Array('-width','-style','-color');
		for (var i=0;i<arrCssRules.length;i++) {
			for (var j=0;j<arrCssRules2.length;j++) {
				var currentCssRule = strCssRule+arrCssRules[i]+arrCssRules2[j];
				oNewElem.style[getCssRule(currentCssRule)] = getStyle(oOldElem,currentCssRule);
			}
		}
	}
	
  /* Detects Quirks Mode for box model... */
	function isW3BoxModelCompliant() {
    if (bIsW3BoxModelCompliant==null) {
      var oDiv = document.createElement('div');
      var iWidth = 100;
      var iPaddingLeft = 10;
      oDiv.style.width = iWidth+'px';
      oDiv.style.paddingLeft = iPaddingLeft+'px';

      document.body.appendChild(oDiv);
      bIsW3BoxModelCompliant = (oDiv.clientWidth==(iWidth+iPaddingLeft));
      document.body.removeChild(oDiv);
    }
    return bIsW3BoxModelCompliant;
  }

	/* Add a child element to the floating item which will fix some stuff */
	function appendFixer(oElement,oFixerElement,oElementWidth,oElementHeight) {
		oFixerElement.className = "innerFixer";
		oElement.appendChild(oFixerElement);
		arrInnerElementsFixer[arrInnerElementsFixer.length] = oFixerElement;
		resizeFixerElement(oFixerElement, oElementWidth, oElementHeight);	
	}
	
	/* Method to resize a fixer element (inner div or iframe) inside floating element fixed */
  function resizeFixerElement(oInnerElement, oElementWidth, oElementHeight) {
    if (oElementWidth>0)
      oInnerElement.style.width = oElementWidth +"px";
    if (oElementHeight>0)
      oInnerElement.style.height = oElementHeight +"px";
  }
  
  /* Add two sizes in pixels and return the new size in pixel (but without the unit!) */
  function addSizes(firstSz,secondSz) {
		return parseInt(firstSz)+parseInt(secondSz);
	}
	
	/* Repair all current Elements to be able to work on Internet Explorer */
	function checkHtmlElements() {
		var arrSelect = document.getElementsByTagName("select");
		/* If there's no SELECT then there's no need to fix elements */
		if (arrSelect.length==0) return;

		if (bCurrentStyleAvailable==null) {
			bCurrentStyleAvailable = (document.body.currentStyle!=null);
		}
		
		recursiveLookChilds(document.body);
		bIsCheckHtmlAlreadyDone = true;
	};
	
	function recursiveLookChilds(oElement) {
		/* Browse each childs of the current element */
		for(var i=0; i<oElement.childNodes.length;i++) {
			var oChild = oElement.childNodes[i];
			if (oChild.nodeType==1) {
				/* Check if the current element has the following properties : position=absolute */
				/* Check if the zIndex is defined (if we want to parse only floating elements with a zIndex) */
				if ((getStyle(oChild,"position")=="absolute") && ((!bIsZIndexRequired)||(getStyle(oChild,"z-index")!=""))) {
					/* Call public static method */
					cSelectFix.repairFloatingElement(oChild, false);
				} else {
					recursiveLookChilds(oChild);
				}
			}
		}
	};

  /* Molinet Fabien : I modified and splitted the function getStyle to work better under Internet Explorer */
  /* -------------------------------------------------------------------------------- */
	/* If the position or the z-index are modified throught oElm.style.position or oElm.style.zIndex it's automatically reported in the style attribute */
	/* function from : http://www.robertnyman.com/2006/04/24/get-the-rendered-style-of-an-element/ */
	function getCssRule(strCssRule) {
			return strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
					return p1.toUpperCase();
			});
	}
	
	function getStyle(oElm, strCssRule) {
		var strValue = "";
		if(document.defaultView && document.defaultView.getComputedStyle){
			strValue = document.defaultView.getComputedStyle(oElm, "").getPropertyValue(strCssRule);
		}
		else if(bCurrentStyleAvailable){
			strCssRule = getCssRule(strCssRule);
			if ((oElm.style[strCssRule] != null) && (oElm.style[strCssRule] != "")) {
				strValue = oElm.style[strCssRule];
			} else {
				strValue = oElm.currentStyle[strCssRule];
			}
		}
		return strValue;
	};

	/* Unobtrusive event listener : function from Scott Andrew */
	function addEvent(obj, evType, fn) {
		if (obj.addEventListener){ 
			obj.addEventListener(evType, fn, false); 
			return true; 
		} else if (obj.attachEvent){ 
			var r = obj.attachEvent("on"+evType, fn); 
			return r; 
		} else { 
			return false; 
		} 
	};
	
	/*** Private methods ***/
	/* Private constructor */
	function cSelectFix() {
		/* on window.onload check each Html Elements */
		addEvent(window, 'load', checkHtmlElements);
	};
	

	
	/* Invoke the constructor */
	cSelectFix();
	/* Return constructor to get current instance in SelectFix */
	return cSelectFix;
})();