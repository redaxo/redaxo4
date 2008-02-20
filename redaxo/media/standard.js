/* 
 REDAXO JavaScript library
 @package redaxo4 
 @version $Id: standard.js,v 1.4 2007/12/12 16:24:43 kills Exp $
 */ 

// -------------------------------------------------------------------------------------------------------------------

function getObj(name)
{
        if (document.getElementById)
        {
                this.obj = document.getElementById(name);
                if(this.obj)
                  this.style = this.obj.style;
        }
        else if (document.all)
        {
                this.obj = document.all[name];
                if(this.obj)
                  this.style = this.obj.style;
        }
        else if (document.layers)
        {
                this.obj = document.layers[name];
                if(this.obj)
                  this.style = this.obj;
        }
}

function getObjArray(name)
{
  return document.getElementsByName(name);
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

// -------------------------------------------------------------------------------------------------------------------

function newPoolWindow(link) 
{
    newWindow( 'rexmediapopup', link, 660,500,',status=yes,resizable=yes');
}

function newLinkMapWindow(link) 
{
    newWindow( 'linkmappopup', link, 660,500,',status=yes,resizable=yes');
}

function openMediaDetails(id, file_id, file_category_id)
{
  if ( typeof(id) == 'undefined')
  {
    id = '';  
  }
  newPoolWindow('index.php?page=medienpool&subpage=detail&opener_input_field='+ id + '&file_id=' + file_id + '&file_category_id=' + file_category_id);
}

function openMediaPool(id)
{
  if ( typeof(id) == 'undefined')
  {
    id = '';  
  }
  newPoolWindow('index.php?page=medienpool&opener_input_field='+ id);
}

function openREXMedia(id,param)
{
  var mediaid = 'REX_MEDIA_'+id;
  var value = document.getElementById(mediaid).value;
  
  if ( typeof(param) == 'undefined')
  {
    param = '';  
  }
  
  if ( value != '') {
     param = param + '&action=media_details&file_name='+ value;
  }

  newPoolWindow('index.php?page=medienpool'+ param +'&opener_input_field='+ mediaid);
}

function deleteREXMedia(id)
{
    var a = new getObj("REX_MEDIA_"+id);
    a.obj.value = "";
}

function addREXMedia(id,params)
{
  if (typeof(params) == 'undefined')
  {
    params = '';  
  }
  
  newPoolWindow('index.php?page=medienpool&action=media_upload&subpage=add_file&opener_input_field=REX_MEDIA_'+id+params);
}

function openLinkMap(id, param)
{
  if ( typeof(id) == 'undefined')
  {
    id = '';  
  }
  if ( typeof(param) == 'undefined')
  {
    param = '';  
  }
  newLinkMapWindow('index.php?page=linkmap&opener_input_field='+id+param);
}

function setValue(id,value)
{
  var field = new getObj(id);
  field.obj.value = value;
}

function deleteREXLink(id)
{
        var link;
        link = new getObj("LINK_"+id);
        link.obj.value = "";
        link = new getObj("LINK_"+id+"_NAME");
        link.obj.value = "";
}

function openREXMedialist(id)
{
  var medialist = 'REX_MEDIALIST_'+id;
  var mediaselect = 'REX_MEDIALIST_SELECT_'+id;
  var needle = new getObj(mediaselect);
  
  var source = needle.obj;
  var sourcelength = source.options.length;
  var param= "";
  for (ii = 0; ii < sourcelength; ii++) {
    if (source.options[ii].selected) {
      param = '&action=media_details&file_name='+ source.options[ii].value;
      break;
    }
  }
  
  newPoolWindow('index.php?page=medienpool'+ param +'&opener_input_field='+ medialist);
}

function addREXMedialist(id,params)
{
  if (typeof(params) == 'undefined')
  {
    params = '';  
  }
  
  newPoolWindow('index.php?page=medienpool&action=media_upload&subpage=add_file&opener_input_field=REX_MEDIALIST_'+id+params);
}

function deleteREXMedialist(id)
{
  var medialist = 'REX_MEDIALIST_SELECT_'+id;
  var needle = new getObj(medialist);
  
  var source = needle.obj;
  var sourcelength = source.options.length;
  var position = null;
  for (ii = 0; ii < sourcelength; ii++) {
    if (source.options[ii].selected) {
      position = ii;
      break;
    }
  }
  
  if(position != null)
  {
    source.options[position] = null;
    sourcelength--;
  
    // Wenn das erste gelöscht wurde
    if(position == 0)
    {
      // Und es gibt noch weitere,
      // -> selektiere das "neue" erste
      if(sourcelength > 0)
        source.options[0].selected = "selected";
    }
    else
    {
      // -> selektiere das neue an der stelle >position<
      if(sourcelength > position)
        source.options[position].selected= "selected";
      else
        source.options[position-1].selected= "selected";
    }
    
    writeREXMedialist(id);
  }
  
}

function moveREXMedialist(id,direction)
{
  // move top
  // move bottom
  // move up
  // move down  
  
  var medialist = 'REX_MEDIALIST_SELECT_'+id;
  var needle = new getObj(medialist);
  var source = needle.obj;
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

// Checkbox mit der ID <id> anhaken
function checkInput(id)
{
  if(id)
  {
    var result = new getObj(id);
    var input = result.obj;
    if(input != null)
    {
      input.checked = 'checked'; 
    }
  }
}

// Inputfield (Checkbox/Radio) mit der ID <id> Haken entfernen
function uncheckInput(id)
{
  if(id)
  {
    var result = new getObj(id);
    var input = result.obj;
    if(input != null)
    {
      input.checked = ''; 
    }
  }
}

// Wenn der 2. Parameter angegeben wird, wird die style.display Eigenschaft auf den entsprechenden wert gesetzt,
// Sonst wird der wert getoggled
function toggleElement(id,display)
{
   var needle;
   
   if(typeof(id) != 'object')
   {
     needle = new getObj(id);
   }
   else
   {
     needle = id;
   }
   
   if (typeof(display) == 'undefined')
   {
     display = needle.style.display == '' ? 'none' : '';
   }
   
   needle.style.display = display;
   return display;
}

function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp('(^|\\s)'+searchClass+'(\\s|$)');
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}

// written by Dean Edwards, 2005
// with input from Tino Zijdel - crisp@xs4all.nl
// http://dean.edwards.name/weblog/2005/10/add-event/
function addEvent(element, type, handler)
{
  if (element.addEventListener)
    element.addEventListener(type, handler, false);
  else
  {
    if (!handler.$$guid) handler.$$guid = addEvent.guid++;
    if (!element.events) element.events = {};
    var handlers = element.events[type];
    if (!handlers)
    {
      handlers = element.events[type] = {};
      if (element['on' + type]) handlers[0] = element['on' + type];
      element['on' + type] = handleEvent;
    }
  
    handlers[handler.$$guid] = handler;
  }
}
addEvent.guid = 1;

function removeEvent(element, type, handler)
{
  if (element.removeEventListener)
    element.removeEventListener(type, handler, false);
  else if (element.events && element.events[type] && handler.$$guid)
    delete element.events[type][handler.$$guid];
}

function handleEvent(event)
{
  event = event || fixEvent(window.event);
  var returnValue = true;
  var handlers = this.events[event.type];

  for (var i in handlers)
  {
    if (!Object.prototype[i])
    {
      this.$$handler = handlers[i];
      if (this.$$handler(event) === false) returnValue = false;
    }
  }

  if (this.$$handler) this.$$handler = null;

  return returnValue;
}

function fixEvent(event)
{
  event.preventDefault = fixEvent.preventDefault;
  event.stopPropagation = fixEvent.stopPropagation;
  return event;
}
fixEvent.preventDefault = function()
{
  this.returnValue = false;
}
fixEvent.stopPropagation = function()
{
  this.cancelBubble = true;
}

// This little snippet fixes the problem that the onload attribute on the body-element will overwrite
// previous attached events on the window object for the onload event
// see http://therealcrisp.xs4all.nl/upload/addEvent_dean.html
if (!window.addEventListener)
{
  document.onreadystatechange = function()
  {
    if (window.onload && window.onload != handleEvent)
    {
      addEvent(window, 'load', window.onload);
      window.onload = handleEvent;
    }
  }
}

// --------------- /AddEvent

// Beim Drücken eines Access-Keys weiterleiten
addEvent(document, 'keypress', function(e)
{
  var activeElement = document.activeElement || e.explicitOriginalTarget;
  
  if(!activeElement)
    return;
    
  var sTagName = activeElement.tagName.toLowerCase();
  if(sTagName == 'input' || sTagName == 'textarea' || sTagName == 'select' || sTagName == 'option')
    return;
    
  var key = String.fromCharCode(e.keyCode || e.which);
  
  var buttons = document.getElementsByTagName('input');
  for (var i = 0; i < buttons.length; i++)
  {
    if (buttons[i].getAttribute('accesskey') == key)
    {
      buttons[i].click();
      stopEvent(e);
      return;
    }
  }
  
  var anchors = document.getElementsByTagName('a');
  for (var i = 0; i < anchors.length; i++)
  {
    if (anchors[i].getAttribute('accesskey') == key)
    {
      // href des links als location setzen 
      if(anchors[i].onclick && anchors[i].onclick != "")
        anchors[i].onclick();
        
      // onclick event auslösen, falls vorhanden
      if(anchors[i].href && anchors[i].href != "" && anchors[i].href != "#")
        document.location = anchors[i].href;
        
      stopEvent(e);
      return;
    }
  }
});


function stopEvent(e)
{
  if (e.stopPropagation) e.stopPropagation();
  else e.cancelBubble = true;
  
  return e.preventDefault();
}