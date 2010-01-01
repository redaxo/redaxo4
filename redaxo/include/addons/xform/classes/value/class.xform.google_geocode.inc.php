<?php

class rex_xform_google_geocode extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	

		$label_lng = $this->elements[2];
		$label_lng_id = 0;

		$label_lat = $this->elements[3];
		$label_lat_value = 0;


		$google_key = "xxx";	
		if($this->elements[4] != "")
			$google_key = $this->elements[4];

		$address = explode(",",$this->elements[5]);
		
		$label_ids = array();

		foreach($this->obj as $o)
		{
			if($o->getLabel() == $label_lng)
				$label_lng_id = $o->getId();
			if($o->getLabel() == $label_lat)
				$label_lat_id = $o->getId();
			if(in_array($o->getLabel(),$address))
				$label_ids[] = $o->getId();
		}

		// rex_com::debug($this->obj);
	
		if ($this->value == "" && !$send)
			if (isset($this->elements[4])) 
				$this->value = $this->elements[4];

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) 
			$wc = $warning["el_" . $this->getId()];


		$vv = "";
		foreach($label_ids as $k => $v)
		{
			$vv .=  '
			address += jQuery("#el_'.$v.'")[0].value+", ";
			';
		}



		//global $REX; // Für resize.gif notwendig!
		if (file_exists('../files/addons/xform/resize.gif'))
		{
			$REX['HTDOCS_PATH'] = '../';
		}
		else{
			$REX['HTDOCS_PATH'] = './';
		}
		
		$form_output[] = '
	<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key='.$google_key.'" type="text/javascript"></script>		
	<script type="text/javascript">
	//<![CDATA[

	var map = null;
	var geocoder = null;
	var marker = null;
	
	var rex_zoom = 10; // Default Zoom bei vorhandenen Geo-Daten
	var rex_zoom_get = 15; // Default Zoom nach Ermittlung der Geo-Daten
	var rex_zoom_err = 5; // Default Zoom bei nicht vorhandenen Geo-Daten
	var rex_default_lat = 51; // Default-Position bei nicht vorhandenen Geo-Daten
	var rex_default_lng = 10; // Default-Position bei nicht vorhandenen Geo-Daten

	var resizable_map = true;
	var mapcontainer = null;
	var resizeButton = null;
	var resizable = false;
	var mouseX, mouseY, diffX, diffY;
	
	function ResizeControl(){};
	ResizeControl.prototype = new GControl();
	ResizeControl.prototype.initialize = function() 
	{
		resizeButton = document.createElement("div");
		resizeButton.style.width = "20px";
		resizeButton.style.height = "20px";
		resizeButton.style.backgroundImage = "url(\''.$REX['HTDOCS_PATH'].'files/addons/xform/resize.gif\')";
		resizeButton.style.cursor = "se-resize";

		resizeButton.onmousedown = function(){ resizable = true; map.hideControls(); resizeButton.style.visibility = "visible"; }
		document.onmouseup = function(){ resizable = false; map.checkResize(); map.showControls(); }

		mapcontainer = map.getContainer();
		mapcontainer.appendChild(resizeButton);

		var terms = mapcontainer.childNodes[2];
		terms.style.marginRight = "20px";

		jQuery("body").mousemove(function(e){ watchMouse(e); });

		return resizeButton;
	}
	ResizeControl.prototype.getDefaultPosition = function()
	{
		return new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(0,0));
	}

	function watchMouse(e) 
	{
		// Include possible scroll values
		var sx = window.scrollX || document.documentElement.scrollLeft || 0;
		var sy = window.scrollY || document.documentElement.scrollTop || 0;

		if(!e) e = window.event; // IEs event definition
		mouseX = e.clientX + sx;
		mouseY = e.clientY + sy;

		/* Direction of mouse movement
		*  deltaX: -1 for left, 1 for right
		*  deltaY: -1 for up, 1 for down
		*/
		var deltaX = mouseX - diffX;
		var deltaY = mouseY - diffY;
		// Store difference in global variables
		diffX = mouseX;
		diffY = mouseY;

		if (resizable) 
		{ 
			changeMapSize(deltaX, deltaY);
		}

		return false;
	}

	function changeMapSize(dx, dy) 
	{
		var width = parseInt(mapcontainer.style.width);
		var height =  parseInt(mapcontainer.style.height);
		if ((width + dx) < 300) { width = 300; dx = 0; }
		if ((height + dy) < 150) { height = 150; dy = 0; }
		mapcontainer.style.width = (width + dx) + "px";
		mapcontainer.style.height= (height + dy) + "px";
	}
	
	function createMarker'.$this->id.'(point) 
	{
		var marker = new GMarker(point, {draggable:true});
		
		GEvent.addListener(marker, "dragend", function(){
			point = marker.getPoint();
			jQuery("#el_'.$label_lat_id.'")[0].value = point.lat();
			jQuery("#el_'.$label_lng_id.'")[0].value = point.lng();
			map.panTo(point, true);
		});
		
		return marker;
	}	
	
	function deleteGGeo'.$this->id.'()
	{
		jQuery("#el_'.$label_lat_id.'")[0].value = "0";
		jQuery("#el_'.$label_lng_id.'")[0].value = "0";
	}

	function getGGeo'.$this->id.'(noalert)
	{
		var address = "";

		'.$vv.'

		if (geocoder) 
		{
			geocoder.getLatLng(address, function(point){
				if (!point) 
				{
					if (!noalert) { alert(address + " nicht gefunden!"); }
					return false
				} 
				else 
				{
					map.savePosition();
					jQuery("#el_'.$label_lat_id.'")[0].value = point.lat();
					jQuery("#el_'.$label_lng_id.'")[0].value = point.lng();
					if (map.getZoom() < rex_zoom_get) { map.setZoom(rex_zoom_get); }
					if (!marker) 
					{
						marker = createMarker'.$this->id.'(point);
						map.addOverlay(marker);
					}
					marker.setPoint(point);
					map.panTo(point, true);
					return true;
				}
			});
		}
		else
		{
			return false;
		}
	}	

	jQuery(function($){
		if (GBrowserIsCompatible()) 
		{
			geocoder = new GClientGeocoder();
			var create_marker = true;

			var rex_lat = jQuery("#el_'.$label_lat_id.'")[0].value;
			var rex_lng = jQuery("#el_'.$label_lng_id.'")[0].value;
			if ((rex_lat == "" || rex_lat == 0) && (rex_lng == "" || rex_lng == 0)) 
			{
				if (!getGGeo'.$this->id.'(true))
				{
					rex_zoom = rex_zoom_err;
					rex_lat = rex_default_lat;
					rex_lng = rex_default_lng;		
					create_marker = false;
				}				
			}

			var point = new GLatLng(rex_lat, rex_lng);
			map = new GMap2(document.getElementById("map_canvas"));
			map.setCenter(point, rex_zoom);
			map.setUIToDefault();

			if (resizable_map)
			{
				map.addControl(new ResizeControl());
			}
			
			if (create_marker) 
			{
				marker = createMarker'.$this->id.'(point);
				map.addOverlay(marker);
			}

			GEvent.addListener(map, "click", function(overlay, point){
				if (point) 
				{
					map.savePosition();
					jQuery("#el_'.$label_lat_id.'")[0].value = point.lat();
					jQuery("#el_'.$label_lng_id.'")[0].value = point.lng();
					if (!marker) 
					{
						marker = createMarker'.$this->id.'(point);
						map.addOverlay(marker);
					}
					marker.setPoint(point);
					map.panTo(point, true);
				}
			});
			
		}
	});	
	
	//]]>
	</script>';

		$output = '
			<div class="xform-element form_google_geocode formlabel-'.$this->label.'">
				<label class="text '.$wc.'" for="el_'.$this->id.'_lat" >'.$this->elements[6].'</label>
				<p class="form_google_geocode">';
		if ($vv != "")
			$output .= '<a href="javascript:void(0);" onclick="getGGeo'.$this->id.'(); return false">Geodaten holen</a> | ';	
		$output .= '<a href="javascript:void(0);" onclick="deleteGGeo'.$this->id.'(); return false">Geodaten nullen</a></p>
				<div class="form_google_geocode_map" id="map_canvas" style="width:400px; height:200px">Google Map</div>
			</div>';
			
		$form_output[] = $output;
			
		// $email_elements[$this->elements[1]] = stripslashes($this->value);
		// $sql_elements[$label_lat] = $label_lat_value;

	}
	
	function getDescription()
	{
		return "google_geocode -> Beispiel: google_geocode|gcode|pos_lng|pos_lat|googlemapkey|strasse,plz,ort|Google Map
		";
	}
	
  function getDefinitions()
  {
    return array(
            'type' => 'value',
            'name' => 'google_geocode',
            'values' => array(
              array( 'type' => 'label',   'name' => 'Label' ),
              array( 'type' => 'getlabel','name' => '"lng"-Label'),
              array( 'type' => 'getlabel','name' => '"lat"-Label'),
              array( 'type' => 'text',    'name' => 'GoogleMapKey'),
              array( 'type' => 'getlabels','name' => 'Labels Positionsfindung'),
              array( 'type' => 'text',     'name' => 'Bezeichnung'),
            ),
            'description' => 'GoogeMap Positionierung',
            'dbtype' => 'text'
      );
  
  }
	
}

?>