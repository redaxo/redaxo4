// ersteller: jan.kristinus@pergopa.de
// datum: 07.09.2002
// bei weiterverwendung bitte kurze nachricht an mich.

// -------------------------------------------------------------------------------------------------------------------

var OSname = 'WIN';
if (navigator.appVersion.indexOf("Win") > 0) OSname = "WIN";
if (navigator.appVersion.indexOf("Mac") > 0) OSname = "MAC";

var visible = "show";
var hidden  = "hide";

function getObj(name)
{
	if (document.layers)
	{
		visible = "show";
		hidden  = "hide";
	}else
	{
		visible = "visible";
		hidden  = "hidden";
	}
	
	if (document.getElementById)
	{
		this.obj = document.getElementById(name);
		this.style = document.getElementById(name).style;
	}
	else if (document.all)
	{
		this.obj = document.all[name];
		this.style = document.all[name].style;
	}
	else if (document.layers)
	{
		this.obj = document.layers[name];
		this.style = document.layers[name];
	}
}

function getDocumentProperty(property)
{
	
	if (property == "clientWidth"){
		if (document.layers) return ((window.innerWidth)-16);
		if (document.all) return (document.body.clientWidth);
		if (document.getElementById) return ((window.innerWidth)-16);
	}
	if (property == "clientHeight"){
		if (document.layers) return (window.innerHeight);
		if (document.all) return (document.body.clientHeight);
		if (document.getElementById) return (window.innerHeight);
	}
	if (property == "scrollTop"){
		if (document.layers) return (window.pageYOffset);
		if (document.all) return (document.body.scrollTop);
		if (document.getElementById) return (window.pageYOffset);
	}
	return (-1);
}

function layerWrite(lay,txt)
{
	var layerRef = new getObj(lay);
	
	if (!layerRef) alert("Objektfehler:"+lay);
	
	if (document.layers)
	{
		layerRef.obj.document.open();
		layerRef.obj.document.write(txt);
		layerRef.obj.document.close();
	}else if (document.all || document.getElementById)
	{
		if (OSname == "MAC" && document.all) txt += "<br>"; // ie mac layer write last tag problem
		layerRef.obj.innerHTML=txt;
	}
}

// -------------------------------------------------------------------------------------------------------------------

function resize()
{
	if (document.layers) self.location.href = self.location.href;
}

// -------------------------------------------------------------------------------------------------------------------

function changeImage(obj,srcs)
{
	if(document.getElementById(obj))
	{
		document.getElementById(obj).src = srcs;
	}
}

// -------------------------------------------------------------------------------------------------------------------

var pageloaded = false;
var anzahllesezeichen = 0;

function init()
{
	pageloaded = true;
	if (anzahllesezeichen>0) setLesezeichenposition();
	mouse();	
}

var mouseX = 0;
var mouseY = 0;

function mouse()
{
	// document.onmousemove = mousePosition;
	if (document.layers){ document.captureEvents(Event.MOUSEMOVE); }
	document.onmousemove = mousePosition;
}

function mousePosition(e)
{
	if (document.layers)
	{
		mouseX = e.pageX;
		mouseY = e.pageY;
	}else if (document.all)
	{
		mouseX = event.x+document.body.scrollLeft-1;
		mouseY = event.y+document.body.scrollTop-3;
	}else if (document.getElementById)
	{
		mouseX = e.pageX;
		mouseY = e.pageY;	
	}
	
	// status = "x: "+mouseX+" y: "+mouseY;
	status = "";
	return true
}

// -------------------------------------------------------------------------------------------------------------------

function makeWinObj(name,url,posx,posy,width,height,extra)
{
	if (extra == 'toolbar') extra = 'scrollbars=yes,toolbar=yes';
	else if (extra == 'empty') extra = 'scrollbars=no,toolbar=no';
	else extra = 'scrollbars=yes,toolbar=no' + extra;
		
	this.name=name;
	this.url=url;
	this.obj=window.open(url,name,'width='+width+',height='+height+', ' + extra);

	// alert("x: "+posx+" | posy: "+posy);

	this.obj.moveTo(posx,posy);
	this.obj.focus();

	return this;
}

function closeAll()
{
	for(i=0;i<=winObjCounter;i++)
	{
		if(winObj[i]) winObj[i].obj.close();
	} 
}

function newWindow(name,link,width,height,type)
{
	if (width==0) width=550;
	if (height==0) height=400;
	
	if (type == 'scrollbars')
	{
		extra = 'toolbar';
	}else if (type == 'empty')
	{
		extra = 'empty';
	}else
	{
		extra = '';	
	}
	
	if (type=="nav")
	{
		posx = parseInt(screen.width/2)-390;
		posy = parseInt(screen.height/2) - 24 - 290;
		width= 320;
		height=580;
	}else if (type=="content")
	{
		posx = parseInt(screen.width/2) - 390 + 330;
		posy = parseInt(screen.height/2) - 24 - 290;
		width= 470;
		height=580;
	}else
	{
		posx = parseInt((screen.width-width)/2);
		posy = parseInt((screen.height-height)/2) - 24;
	}
	
	
	
	winObjCounter++;
	winObj[winObjCounter]=new makeWinObj(name,link,posx,posy,width,height,extra);
}

winObj=new Array();

var winObjCounter = -1;

// <body onload=self.focus(); onunload=closeAll();>

// -------------------------------------------------------------------------------------------------------------------

function fdirekteinstieg()
{
	var sel  = document.direkteinstieg.linkto.selectedIndex;
	var link = document.direkteinstieg.linkto[sel].value;
	if (link != 0 && link != "") document.location.href = link;
		
}

// -------------------------------------------------------------------------------------------------------------------

var timer;

function showInfo(txt)
{
	if (txt != "" && pageloaded)
	{
		if (timer) clearTimeout(timer); 
		if (document.layers){ bgcolor = "#eeeeee"; }else{ bgcolor = "#ffffff"; }
		txt = "<table cellpadding=0 cellspacing=0 border=0 bgcolor=#000000><tr><td colspan=3><img src=/pics/black.gif width=1 height=1></td></tr><tr><td><img src=/pics/black.gif width=1 height=1></td><td bgcolor=#ffffff><table bgcolor=#ffffff cellpadding=3 cellspacing=0 border=0><tr><td align=center class=info>&nbsp;"+txt+"&nbsp;</td></tr></table></td><td><img src=/pics/black.gif width=1 height=1></td></tr><tr><td colspan=3><img src=/pics/black.gif width=1 height=1></td></tr></table>";
		
		layerWrite('info',txt);
		var x = new getObj('info');
		x.style.top = mouseY+10;
		x.style.left = mouseX+10;
		x.style.visibility = visible;
		timer = window.setTimeout("hideInfo()",1000);
	} 
}

function hideInfo()
{
    	var x = new getObj('info');
    	x.style.visibility = hidden;
}

// -------------------------------------------------------------------------------------------------------------------

function setTRColor(objID,objColor,objColor2,checkbox)
{
	tr = document.getElementById(objID);
	
	if (objColor == '' || typeof(tr.style) == 'undefined') return false;

	if (typeof(document.getElementsByTagName) != 'undefined') var theCells = tr.getElementsByTagName('td');
	else if (typeof(tr.cells) != 'undefined') var theCells = tr.cells;
	else return false;

	var rowCellsCnt  = theCells.length;
	for (var c = 0; c < rowCellsCnt; c++)
	{
		if (checkbox) theCells[c].style.backgroundColor = objColor2;
		else theCells[c].style.backgroundColor = objColor;
	}
	return true;
}