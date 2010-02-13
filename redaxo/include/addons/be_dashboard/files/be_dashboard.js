function componentInit(componentId)
{
  if(getCookie(componentId+"_state") == "minimized")
  {
    jQuery("#"+ componentId +" .rex-dashboard-component-content").hide();
  }
}
  
function componentRefresh(componentId)
{
	var component = jQuery("#"+ componentId);
	// inicate loading with animated image
	var link = jQuery("a.rex-i-refresh", component);
	
	// prepare url
  var url =window.location.href; 
  url = url.replace(/#/, ''); // strip anchor
  url = url.replace(/&refresh=[^&]*/, ''); // strip remaining refresh-parameter 
  url = url+'&refresh=' + componentId; // add refresh parameter so we get a up-to-date copy
  url = url+'&ajax-get=' + componentId; // get content in ajax form
//  url = url +'#'+ componentId; // add anchor to get back to the current component
	
	jQuery.ajax({
	  'url': url,
    beforeSend: function(request){
      // Handle the beforeSend event
      link.removeClass("rex-i-refresh").addClass("rex-i-refresh-ani");
    },
    complete: function(request, textStatus){
      // Handle the complete event
      link.removeClass("rex-i-refresh-ani").addClass("rex-i-refresh");
    },
    error: function(request, textStatus, errorThrown){
      // Handle the error event
      link.removeClass("rex-i-refresh-ani").addClass("rex-i-refresh");
    },
    success: function(data){
   	  // Handle succesfull request
      link.removeClass("rex-i-refresh-ani").addClass("rex-i-refresh");
      component.parent().html(data);
    }
	});
	
  // use replace, so browser will not save the redirect in the history
  // window.location.replace(url);
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
  var wasHidden = content.is(":hidden");
  
  if(!wasHidden)
  {
    var config = jQuery(".rex-dashboard-component-config", component);
    config.hide("slow");
  }
  content.slideToggle("slow");
 
  setCookie(componentId+"_state", (wasHidden ? "maximized":"minimized"), "never") ;
}

function setCookie (name, value, expires, path, domain, secure) {
  if(typeof expires != undefined && expires == "never")
  {
    // never expire means expires in 3000 days
    expires = new Date();
    expires.setTime(expires.getTime() + (1000 * 60 * 60 * 24 * 3000));
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
