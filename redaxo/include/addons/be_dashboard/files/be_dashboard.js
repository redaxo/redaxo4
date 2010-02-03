function componentRefresh(component)
{
  var url = window.location.href; 
  url = url.replace(/#/, ''); // strip anchor
  url = url.replace(/&refresh=[^&]*/, ''); // strip 
  url = url+'&refresh=' + component.attr('id');
  url = url +'#'+ component.attr('id');
  
  // use replace, so browser will not save the redirect in the history
  window.location.replace(url);
}

function componentToggleSettings(component)
{
  var config = jQuery(".rex-dashboard-component-config", component);
  config.slideToggle("slow");
}

function componentToggleView(component)
{
  var content = jQuery(".rex-dashboard-component-content", component);
  
  if(!content.is(":hidden"))
  {
    var config = jQuery(".rex-dashboard-component-config", component);
    config.hide("slow");
  }
  content.slideToggle("slow");
}