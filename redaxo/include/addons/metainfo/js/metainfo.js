var visibleNotice;

function checkConditionalFields(selectEl, activeIds)
{
  var needle = new getObj('rex_62_params_Feld_bearbeiten_erstellen_params');
  var params = needle.obj;
  var toggle = false;
  
  for(var i = 0; i < activeIds.length; i++)
  {
    if(selectEl.value == activeIds[i])
    {
      toggle = activeIds[i];
      break;
    }
  }
  
  if(toggle)
  {
    // show params field
    toggleElement(params.parentNode, '');
    
    if(visibleNotice)
    {
      toggleElement(visibleNotice, 'none');
    }
    
    var needle = new getObj('a62_field_params_notice_'+ toggle);
    if(needle.obj)
    {
      toggleElement(needle.obj, '');
      visibleNotice = needle.obj;
    }
  }
  else
  {
    // hide params field
    toggleElement(params.parentNode, 'none');

    if(visibleNotice)
    {
      toggleElement(visibleNotice, 'none');
    }
  }
}