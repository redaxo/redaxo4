// layer: llesezeichen
// layer: lleseicon
// ersteller: jan.kristinus@pergopa.de
// datum: 07.09.2002
// bei weiterverwendung bitte kurze nachricht an mich.

// -------------------------------------------------------------------------------------------------------------------

var lesezeichen = new Array();
var Trenner = "^";
var ID = new Array();
var NAME = new Array();
var anzahllesezeichen = 20;

function addFavo(txt,id)
{
	if (pageloaded)
	{
		readFavo();
		var WRITEFA = true;
		for(var i=0;i<ID.length;i++)
		{
			if (ID[i]==id) WRITEFA = false;
		}
		if (ID.length<anzahllesezeichen && WRITEFA)
		{
			ID[(ID.length)] = id;
			NAME[(NAME.length)] = txt;
			writeFavo();
			readFavo();
			moveLeseicon();
			writeFavoToLayer();			
		}
	}	
}

function writeFavo()
{
	var MyCookie = document.cookie;
	var cookietxt = "lesezeichen=";
	for(var i=0;i<ID.length;i++)
	{
		cookietxt += Trenner+ID[i]+","+NAME[i];
	}
	var expire = new Date ();
	expire.setTime (expire.getTime() + (6 * 24 * 3600000)); //in 6 tagen expired
	expire = expire.toGMTString();
	cookietxt = cookietxt+"; path=/; expires="+expire;  	
  	document.cookie = cookietxt;
	readFavo();	
}

function writeFavoToLayer()
{
	var x = new getObj('llesezeichen');
	
	txt =  "<table cellpadding=0 cellspacing=0 border=1 bgcolor=#000000 width=200><tr><td><table width=100% border=0 cellpadding=0 cellspacing=0 bgcolor=#ffffff>";
	txt += "<tr><td bgcolor=#f2f1ed align=center><img src=/pics/leer.gif width=16 height=16 vspace=3 hspace=3></td><td class=lesezeichen colspan=2 valign=middle bgcolor=#f2f1ed><a href=javascript:hideFavo();>Fenster&nbsp;schliessen</a></td></tr>";
	
	readFavo();
		
	for(var i=0;i<ID.length;i++)
	{
		myid = ID[i];
		myname = NAME[i];
		txt += "<tr><td align=center><a href=javascript:delFavo("+i+");><img src=/pics/icons/-_lesezeichen.gif width=16 height=16 vspace=3 hspace=3 border=0></a></td><td class=lesezeichen colspan=2 valign=middle><a href=index.php?article_id="+myid+">"+myname+"</a></td></tr>";
	}
	
	if (ID.length==0) txt += "<tr><td align=center><img src=/pics/leer.gif width=16 height=16 vspace=3 hspace=3></td><td class=lesezeichen colspan=2 valign=middle>Keine Lesezeichen vorhanden</td></tr>";
	
	if (ID.length>1)  txt += "<tr><td bgcolor=#f2f1ed align=center><img src=/pics/icons/-_lesezeichen.gif width=16 height=16 vspace=3 hspace=3></td><td class=lesezeichen bgcolor=#f2f1ed colspan=2><a href=javascript:delFavo('all')>Alle&nbsp;Lesezeichen&nbsp;löschen&nbsp;</a></td></tr>";
	
	txt += "<tr><td bgcolor=#f2f1ed align=center><img src=/pics/icons/fragezeichen.gif width=16 height=16 vspace=3 hspace=3></td><td class=lesezeichen bgcolor=#f2f1ed colspan=2><a href=index.php?article_id=222>Der&nbsp;Lesezeichen-Service&nbsp;</a></td></tr>";
	txt += "</table></td></tr></table>";
	
	layerWrite('llesezeichen',txt);
}

function showFavo()
{	
	if (pageloaded)
	{
		writeFavoToLayer();
		visiFavo();
	}
}

function visiFavo()
{
	var x = new getObj('llesezeichen');
	x.style.visibility = visible;
}

function hideFavo()
{
	var x = new getObj('llesezeichen');
	x.style.visibility = hidden;
}

function delFavo(pos)
{
	if (pos == "all")
	{
		ID.length = 0;
		NAME.length = 0;
		writeFavo();
		hideFavo();
	}
	else
	{
		// strings extrahieren vorher - nachher und zusammenfuegen
		var idsbefore = ID.slice(0,pos);
		var idsafter  = ID.slice(pos+1,ID.length);
		var namesbefore = NAME.slice(0,pos);
		var namesafter  = NAME.slice(pos+1,ID.length);
		
		// alert(idsbefore.join(","));
		// alert(idsafter.join(","));
		ID.length = 0;
		NAME.length = 0;
		
		ID = idsbefore.concat(idsafter);
		NAME = namesbefore.concat(namesafter);		
		writeFavo()
		writeFavoToLayer();
	
	}
}

function readFavo()
{
	
	cookietxt = document.cookie.split(";");
	var lesestring = "";

	for (tA = 0; tA < cookietxt.length; tA++)
	{
		if (cookietxt[tA].indexOf('lesezeichen=') > -1) //lesezeichen gefunden
		{
			tPos = cookietxt[tA].indexOf("=")+1;
			var lesestring = cookietxt[tA].substring(tPos,cookietxt[tA].length); // "lesezeichen=" raus
		}

	}
	
	lesezeichen = lesestring.split("^");
	
	if (lesezeichen.length > 1)
	{
		var j = 0;
		for(var i=0;i<lesezeichen.length;i++)
		{
			block = lesezeichen[i].split(",");
			if (block.length > 1)
			{
				ID[j]  = block[0];
				NAME[j]= block[1];
				j++;
			}
		}
	}

	document.images["lesedigit"].src = "pics/digits/"+ID.length+".gif";
}


function setLesezeichenposition()
{
	var x = new getObj('llesezeichen');
	width = getDocumentProperty("clientWidth");
	x.style.top = 110;
	x.style.left = (width/2)+23;
	if (x.style.visibility != visible) x.style.visibility = hidden;
	
	timer2 = window.setTimeout("setLesezeichenposition()",1000);
}

var topposy = 0;
var topposx = 0;	
var fromposx= 0;
var fromposy= 0;
var newposy = 0;
var newposx = 0;
var step = 0;
var steps = 7;

function moveLeseicon()
{
	if (document.all && ID.length<anzahllesezeichen && pageloaded)
	{
		var width = getDocumentProperty("clientWidth");
		var x = new getObj('lleseicon');
		topposy = 110;
		topposx = (width/2)+23;	
		fromposx= mouseX;
		fromposy= mouseY;
		step = 0;
		setLeseiconposition();	
	}
}

function setLeseiconposition()
{
	var x = new getObj('lleseicon');
	x.style.visibility = visible;
	step++;
	if (step<steps)
	{
		newposy = parseInt(fromposy-((fromposy-topposy)*(step/steps)));
		newposx = parseInt(fromposx-((fromposx-topposx)*(step/steps)));
		x.style.top = newposy;
		x.style.left = newposx;
		timer3 = window.setTimeout("setLeseiconposition()",10);
	}else
	{
		x.style.visibility = hidden;
	}	
}




// -------------------------------------------------------------------------------------------------------------------