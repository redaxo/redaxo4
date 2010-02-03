function componentRefresh(component)
{
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