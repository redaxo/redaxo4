function componentInit(componentId)
{
  if(getCookie(componentId) == "false")
  {
    jQuery("#"+ componentId +" .rex-dashboard-component-content").hide();
  }
}
  
function componentRefresh(componentId)
{
  var url = window.location.href; 
  url = url.replace(/#/, ''); // strip anchor
  url = url.replace(/&refresh=[^&]*/, ''); // strip remaining refresh-parameter 
  url = url+'&refresh=' + componentId; // add current refresh parameter
  url = url +'#'+ componentId; // add anchor to get back to the current component
  
  // use replace, so browser will not save the redirect in the history
  window.location.replace(url);
}

function componentToggleSettings(componentId)
{
  var component = jQuery("#"+componentId);
  var config = jQuery(".rex-dashboard-component-config", component);
  config.slideToggle("slow");
}

function componentToggleView(componentId)
{
  var component = jQuery("#"+componentId);
  var content = jQuery(".rex-dashboard-component-content", component);
  var isHidden = content.is(":hidden");
  
  if(!isHidden)
  {
    var config = jQuery(".rex-dashboard-component-config", component);
    config.hide("slow");
  }
  content.slideToggle("slow");
 
  setCookie(componentId, (isHidden ? "true":"false"), "never") ;
}

function setCookie (name, value, expires, path, domain, secure) {
  if(typeof expires != undefined && expires == "never")
  {
    // never expire means expires in 365 days
    expires = new Date();
    expires.setTime(expires.getTime() + (1000 * 60 * 60 * 24 * 365));
    expires = expires.toGMTString();
  }
  
  document.cookie = name + "=" + escape(value) +
    ((expires) ? "; expires=" + expires : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
}

function getCookie(cookieName) {
 var theCookie=""+document.cookie;
 var ind=theCookie.indexOf(cookieName);
 if (ind==-1 || cookieName=="") return "";
  
 var ind1=theCookie.indexOf(';',ind);
 if (ind1==-1) ind1=theCookie.length;
  
 return unescape(theCookie.substring(ind+cookieName.length+1,ind1));
}
