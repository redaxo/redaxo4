// -------------------------------------- FLASHDETECTION

// -------------------------------------- NS/MZ FLASH VORHANDEN ?

var ShockMode = 0;
var plugin = (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]) ? navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin : 0;
if (plugin&& parseInt(plugin.description.substring(plugin.description.indexOf(".")-1)) >= 5) ShockMode = 1;

if (navigator.userAgent.indexOf("Mac")>=0 && navigator.userAgent.indexOf("MSIE")>=0&& navigator.userAgent.indexOf("4.5")>=0)
{
	// -------------------------------------- MAC 4.5 FLASH = TRUE

	ShockMode = 1;

}else if (navigator.userAgent && navigator.userAgent.indexOf("MSIE")>=0 && navigator.userAgent.indexOf("Windows")>=0)
{
	// -------------------------------------- WIN
	
	document.write('\<SCRIPT LANGUAGE=VBScript\>');
	document.write('on error resume next \n');

	// flash version
	document.write('ShockMode = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.5")))\n');
	document.write('\</S\CRIPT\>');
}		

function Browser( requiredFlashVersion )
{ 
	this.isIE=false;
	this.isNS=false;
	this.isOpera=false;
	
	this.isWin=false;								
	this.isMac=false;
	this.isLinux=false;					
	
	this.version=0;	
	this.isFlashed=false;
	
	var maxFlashVersion=8;
	var agent=navigator.userAgent.toLowerCase();									
	
	this.isIE = agent.indexOf("msie") != -1 && agent.indexOf("opera") == -1; 
	this.isNS = (agent.indexOf("netscape") != -1 || navigator.appName == "Netscape") && agent.indexOf("opera") == -1; // in netscape4 "netscape" doesn't appear in navigator.userAgent
	if (agent.indexOf("mozilla") != -1 || agent.indexOf("safari") != -1) this.isNS = true;
	this.isOpera = agent.indexOf("opera") != -1;
	this.isKonqueror = agent.indexOf("konqueror") != -1;
	
	
	this.isWin = agent.indexOf("win") != -1;		
	this.isMac = agent.indexOf("mac") != -1;
	this.isLinux = agent.indexOf("linux") != -1;
						
	var minor = parseFloat(navigator.appVersion);
	
	if (this.isNS)
	{
		if (minor >= 5)
		{
			this.version=6;
		} else
		{
			this.version=minor;
		}
		
	}else if(this.isKonqueror)
	{
		this.version=minor;

	}else if(this.isIE)
	{
		if (agent.indexOf("msie 5") != -1 && minor == 4)
		{
			this.version = 5;
		} else
		{
			this.version = minor;
		}

	}else if (this.isOpera)
	{
		if (agent.indexOf("opera 2") != -1 || agent.indexOf("opera/2") != -1) 
		this.version=2;
		if (agent.indexOf("opera 3") != -1 || agent.indexOf("opera/3") != -1) 
		this.version=3;						
		if (agent.indexOf("opera 4") != -1 || agent.indexOf("opera/4") != -1) 
		this.version=4;						
		if (agent.indexOf("opera 5") != -1 || agent.indexOf("opera/5") != -1) 
		this.version=5;
		if (agent.indexOf("opera 6") != -1 || agent.indexOf("opera/6") != -1) 
		this.version=6;			    					
	}
	
	// -------------------------------------- WIN/IE FLASH VERSION CHECK
	
	if (this.isIE && this.isWin)
	{
		document.write('<SCR' + 'IPT LANGUAGE=VBScript\> \n');
		document.write('on error resume next \n');		
		for (i = requiredFlashVersion; i<maxFlashVersion+1; i++)
		{
			document.write('flash' + i + 'Installed = false \n');
			document.write('flash' + i + 'Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.' + i + '"))) \n');			
		}			
		document.write('</SCR' + 'IPT\> \n');		
	
		for (i = requiredFlashVersion; i<maxFlashVersion+1; i++)
		{
			if (eval("flash" + i + "Installed"))
			{
				this.isFlashed=true;
				break;
			}
		}
	
	// -------------------------------------- REST FLASH VERSION CHECK
	
	} else if (navigator.plugins && navigator.plugins["Shockwave Flash"])
	{
		
		var plugin = navigator.plugins["Shockwave Flash"];							
		this.isFlashed = (plugin.description.charAt(plugin.description.indexOf(".")-1) >= requiredFlashVersion);
		
	}
}

function flashVersion(version)
{
	var myBrowser = new Browser(version);
	if (myBrowser.isFlashed) return true;
	else return false;	
}