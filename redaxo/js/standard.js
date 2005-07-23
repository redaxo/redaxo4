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
// Vorraussetzung ist, das zuvor eine height per CSS gesetzt wurde!
function alter_box_height(boxid, pixelvalue)
{
    if ( typeof( boxid) == "object") {
       for (var i = 0; i < boxid.length; i++) {
          alter_box_height(boxid[i], pixelvalue);
       }
       return false;
    }
	var box = new getObj( boxid);
	var boxheight = parseInt(box.style.height);
	var newheight = boxheight + pixelvalue;
	if (newheight > 0)
	{
		box.style.height = newheight + "px";
	}
	return false;
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
        for( var i=0;i<=winObjCounter;i++)
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

var winObj = new Array();

var winObjCounter = -1;

// <body onload=self.focus(); onunload=closeAll();>


// -------------------------------------------------------------------------------------------------------------------

function newPoolWindow( link) 
{
    newWindow( 'rexmediapopup', link, 660,500,',status=yes,resizable=yes');
}

function openMediaPool(id)
{
    newPoolWindow('index.php?page=medienpool&opener_input_field=');
}

function openREXMedia(id)
{
	var mediaid = 'REX_MEDIA_'+id;
	var value = document.getElementById(mediaid).value;
	var param = '';
	
	if ( value != '') {
	   param = '&action=media_details&file_name='+ value;
	}

    newPoolWindow('index.php?page=medienpool'+ param +'&opener_input_field='+ mediaid);

}

function deleteREXMedia(id)
{
        var a = new getObj("REX_MEDIA_"+id);
        a.obj.value = "";
}

function addREXMedia(id,cat_id)
{
        newPoolWindow('	index.php?page=medienpool&action=media_upload&subpage=add_file&cat_id='+cat_id+'&opener_input_field=REX_MEDIA_'+id);
}

function openLinkMap(id)
{
        newWindow('linkmappopup','index.php?page=linkmap&opener_input_field='+id+'',660,500,',status=yes,resizable=yes');
}
function deleteREXLink(id)
{
        var a = new getObj("LINK["+id+"]");
        a.obj.value = "";
        a = new getObj("LINK_NAME["+id+"]");
        a.obj.value = "";
}

function openREXMedialist(id)
{
	var medialist = 'REX_MEDIALIST_'+id;
	var mediaselect = 'REX_MEDIALIST_SELECT_'+id;
	var source = document.getElementById(mediaselect);
	var sourcelength = source.options.length;
	var param= "";
	for (ii = 0; ii < sourcelength; ii++) {
		if (source.options[ii].selected) {
			param = '&action=media_details&file_name='+ source.options[ii].value;
		}
	}
	
    newPoolWindow('index.php?page=medienpool'+ param +'&opener_input_field='+ medialist);
}

function addREXMedialist(id,file_id)
{
	var mediaselect = 'REX_MEDIALIST_SELECT_'+id;
	var source = document.getElementById(mediaselect);
	var sourcelength = source.options.length;
	
	NewOption = new Option(file_id,file_id,true,true);
	source.options[sourcelength] = NewOption;
	writeREXMedialist(id);
}

function deleteREXMedialist(id)
{
	var mediaselect = 'REX_MEDIALIST_SELECT_'+id;
	var source = document.getElementById(mediaselect);
	var sourcelength = source.options.length;
	var position = "";
	for (ii = 0; ii < sourcelength; ii++) {
		if (source.options[ii].selected) {
			position = ii;
		}
	}
	source.options[position] = null;
	writeREXMedialist(id);
}

function moveREXMedialist(id,direction)
{
	// move top
	// move bottom
	// move up
	// move down	
	
	var mediaselect = 'REX_MEDIALIST_SELECT_'+id;
	var source = document.getElementById(mediaselect);
	var sourcelength = source.options.length;
	
	var elements = new Array();
	var was_selected = new Array();
	for (ii = 0; ii < sourcelength; ii++) {
		elements[ii] = new Array();
		elements[ii]['value'] = source.options[ii].value; 
		elements[ii]['title'] = source.options[ii].text; 
		was_selected[ii] = false;
	}
	
	var inserted = 0;
	var was_moved = new Array();
	was_moved[-1] = true;
	was_moved[sourcelength] = true;
	
	if (direction == 'top') {
		for (ii = 0; ii < sourcelength; ii++) {
			if (source.options[ii].selected) {
				elements = moveItem(elements, ii, inserted);
				was_selected[inserted] = true;
				inserted++;
			}
		}
	}
	
	if (direction == 'up') {
		for (ii = 0; ii < sourcelength; ii++) {
			was_moved[ii] = false;
			if (source.options[ii].selected) {
				to = ii-1;
				if (was_moved[to]) {
					to = ii;
				}
				elements = moveItem(elements, ii, to);
				was_selected[to] = true;
				was_moved[to] = true;
			}
		}
	}
	
	if (direction == 'down') {
		for (ii = sourcelength-1; ii >= 0; ii--) {
			was_moved[ii] = false;
			if (source.options[ii].selected) {
				to = ii+1;
				if (was_moved[to]) {
					to = ii;
				}
				elements = moveItem(elements, ii, to);
				was_selected[to] = true;
				was_moved[to] = true;
			}
		}
	}
	
	if (direction == 'bottom') {
		inserted = 0;
		for (ii = sourcelength-1; ii >= 0; ii--) {
			if (source.options[ii].selected) {
				to = sourcelength - inserted-1;
				if (to > sourcelength) {
					to = sourcelength;
				}
				elements = moveItem(elements, ii, to);
				was_selected[to] = true;
				inserted++;
			}
		}
	}
	
	for (ii = 0; ii < sourcelength; ii++) {
		source.options[ii] = new Option(elements[ii]['title'], elements[ii]['value']);
		source.options[ii].selected = was_selected[ii];
	}

	writeREXMedialist(id);

}

function writeREXMedialist(id)
{
	var medialist = 'REX_MEDIALIST_'+id;
	var mediaselect = 'REX_MEDIALIST_SELECT_'+id;
	
	var source = document.getElementById(mediaselect);
	var sourcelength = source.options.length;

	var target = document.getElementById(medialist);

	target.value = "";
	for (i=0; i < sourcelength; i++) {
		target.value += (source[i].value);
		if (sourcelength > (i+1))  target.value += ',';
	}

}

function moveItem(arr, from, to)
{
	if (from == to || to < 0)
	{
		return arr;
	}
	
	tmp = arr[from];
	if (from > to)
	{
		for (index = from; index > to; index--) {
			arr[index] = arr[index-1];
		}
	} else {
		for (index = from; index < to; index++) {
			arr[index] = arr[index+1];
		}
	}
	arr[to] = tmp;
	return arr;
}