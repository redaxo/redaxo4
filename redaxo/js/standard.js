// REDAXO JS 0.1

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

// -------------------------------------------------------------------------------------------------------------------

function changeImage(id,img)
{
        if(document.getElementById(id)) {
                document.getElementById(id).src = img;
        }

}

// -------------------------------------------------------------------------------------------------------------------

var pageloaded = false;

function init()
{
        pageloaded = true;
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
                extra = type
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

function openREXMedia(id)
{
        newWindow('rexmediapopup','index.php?page=medienpool&opener_input_field=REX_MEDIA_'+id,660,400,',status=yes,resizable=yes');
}

function deleteREXMedia(id)
{
        var a = new getObj("REX_MEDIA_"+id);
        a.obj.value = "delete file";
}

function addREXMedia(id)
{
        newWindow('rexmediapopup','index.php?page=medienpool&mode=add&opener_input_field=REX_MEDIA_'+id,660,400,',status=yes,resizable=yes');
}

function openREXMediaHTMLArea(area)
{
        newWindow('rexmediapopup','index.php?page=medienpool&func=add&HTMLArea='+area,660,400,',status=yes,resizable=yes');
}
function openLinkMapHTMLArea(area)
{
        newWindow('linkmappopup','index.php?page=linkmap&HTMLArea='+area,660,400,',status=yes,resizable=yes');
}
function openLinkMap(id)
{
        newWindow('linkmappopup','index.php?page=linkmap&opener_input_field=LINK['+id+']',660,400,',status=yes,resizable=yes');
}
function deleteREXLink(id)
{
        var a = new getObj("LINK["+id+"]");
        a.obj.value = "delete link";
}
