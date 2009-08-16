<?php

class rex_xform_google_geocode extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	

		$labels = explode(",",$this->elements[2]);

		$label_lng = $labels[0];
		$label_lng_id = 0;

		$label_lat = $labels[1];
		$label_lat_value = 0;


		$google_key = "ABQIAAAA9X7aYuoSxHOtyCq4UchU-RQyipGq1b1Vxx1ZHLOcBEyNcCPR-RQvmOEddgXQfl-Xds-NLuqPv8OH1Q";	
		if($this->elements[3] != "")
			$google_key = $this->elements[3];

		$address = explode(",",$this->elements[4]);
		
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
			if (isset($this->elements[3])) 
				$this->value = $this->elements[3];

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) 
			$wc = $warning["el_" . $this->getId()];


		$vv = "";
		foreach($label_ids as $k => $v)
		{
			$vv .=  '
			address += document.getElementById("el_'.$v.'").value+",";
			';
		}




		$form_output[] = '
	<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key='.$google_key.'" type="text/javascript"></script>		
	<script type="text/javascript">


	function deleteGGeo'.$this->id.'()
	{
		document.getElementById("el_'.$label_lat_id.'").value = "0";
		document.getElementById("el_'.$label_lng_id.'").value = "0";
	}

	function getGGeo'.$this->id.'()
	{

		var address = "";

		'.$vv.'
		
		if (geocoder) {
			geocoder.getLatLng(
				address,
				function(point) {
					if (!point) {
						alert(address + " nicht gefunden");
					} else {
					document.getElementById("el_'.$label_lat_id.'").value = point.lat();
					document.getElementById("el_'.$label_lng_id.'").value = point.lng();
					map.setCenter(point, 13);
					var marker = new GMarker(point);
					map.addOverlay(marker);
					marker.openInfoWindowHtml(address);
			}
			}
			);
		}

	}


	var map = null;
	var geocoder = null;
	
	jQuery(function($) {
	
		if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById("map_canvas"));
			map.setCenter(new GLatLng(50.06613112982356, 8.779449462890625), 9);
			geocoder = new GClientGeocoder();
			// map.addControl(new GSmallMapControl());
	        // map.addControl(new GMapTypeControl());
			map.setUIToDefault();

			GEvent.addListener(map,"click", function(overlay,latlng) {
				if (latlng) {

					document.getElementById("el_'.$label_lat_id.'").value = latlng.lat();
					document.getElementById("el_'.$label_lng_id.'").value = latlng.lng();
					
				}
			});

		}
	
	}
	)
	
	</script>';

		$output = '
			<div class="xform-element form_google_geocode formlabel-'.$this->label.'">
				<label class="text '.$wc.'" for="el_'.$this->id.'_lat" >'.$this->elements[5].'</label>
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
		return "google_geocode -> Beispiel: 
		google_geocode|gcode|pos_lng,pos_lat|ABQIAAAA9X7aYuoSxHOtyCq4UchU-RQyipGq1b1Vxx1ZHLOcBEyNcCPR-RQvmOEddgXQfl-Xds-NLuqPv8OH1Q|strasse,plz,ort|Google Map
		";
	}
}

?>