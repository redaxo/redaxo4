Bei Updates von tinyMCE -> Redaxo fix:

tinymce\jscripts\tiny_mce\plugins\advimage\jscripts\functions.js
zeile 349

ersetze
      html += " />";
mit
      html += " ismap=\"ismap\" />";
  

############################################


tinymce\jscripts\tiny_mce\plugins\advimage\image.htm

ersetze
        <li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{$lang_advimage_tab_advanced}</a></span></li>
mit
        <!-- redaxo 
        <li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{$lang_advimage_tab_advanced}</a></span></li>
        redaxo //-->
        
        
############################################

        
tinymce\jscripts\tiny_mce\plugins\advlink\link.htm
        
ersetze
        <li id="popup_tab"><span><a href="javascript:mcTabs.displayTab('popup_tab','popup_panel');" onmousedown="return false;">{$lang_advlink_popup_tab}</a></span></li>
        <li id="events_tab"><span><a href="javascript:mcTabs.displayTab('events_tab','events_panel');" onmousedown="return false;">{$lang_advlink_events_tab}</a></span></li>
        <li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{$lang_advlink_advanced_tab}</a></span></li>
mit
        <!-- redaxo
        <li id="popup_tab"><span><a href="javascript:mcTabs.displayTab('popup_tab','popup_panel');" onmousedown="return false;">{$lang_advlink_popup_tab}</a></span></li>
        <li id="events_tab"><span><a href="javascript:mcTabs.displayTab('events_tab','events_panel');" onmousedown="return false;">{$lang_advlink_events_tab}</a></span></li>
        <li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{$lang_advlink_advanced_tab}</a></span></li>
        redaxo //-->
