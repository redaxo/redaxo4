<?php

class rex_xform_be_relation extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		global $REX;

		$label = $this->elements[1];
		$bezeichnung = $this->elements[2];
		$tabelle = $this->elements[3];
		$tabelle_feld = $this->elements[4];
		$relationstype = $this->elements[5];	// single=0;multiple=1
		$style = $this->elements[6];	// popup=0;selectbox=1
		
		echo '<pre>'; var_dump($this->elements);echo '</pre>'; 
		
		
		$link = 'index.php?page='.rex_request("page").'&subpage='.$tabelle.'&popup=1';
		
		// 'index.php?page=mediapool'+ param +'&opener_input_field='+ mediaid
		
		
		$form_output[] = '

		<br />value: '.$this->value.'
		<br />bezeichnung: '.$bezeichnung.'
<div class="rex-widget">
 <div class="rex-widget-media">
  <p class="rex-widget-field">
   <input type="text" size="30" name="MEDIA[1]" value="" id="REX_MEDIA_1" readonly="readonly" />
  </p>
  <p class="rex-widget-icons">
   <a href="#" class="rex-icon-file-open" onclick="newPoolWindow(\''.$link.'\');return false;" tabindex="21"><img src="media/file_open.gif" width="16" height="16" title="Medium auswŠhlen" alt="Medium auswŠhlen" /></a>
   <a href="#" class="rex-icon-file-add" onclick="addREXMedia(1);return false;" tabindex="22"><img src="media/file_add.gif" width="16" height="16" title="Neues Medium hinzufŸgen" alt="Neues Medium hinzufŸgen" /></a>
   <a href="#" class="rex-icon-file-delete" onclick="deleteREXMedia(1);return false;" tabindex="23"><img src="media/file_del.gif" width="16" height="16" title="AusgewŠhltes Medium lšschen" alt="AusgewŠhltes Medium lšschen" /></a>
  </p>
  <div class="rex-media-preview"></div>
 </div>
</div>';
		
		
		
		
		
		
	}
	
	function getDescription()
	{
		// label,bezeichnung,tabelle,tabelle.feld,relationstype,style
		return "be_relation -> Beispiel: ";
	}
	
	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'be_relation',
						'values' => array(
             	array( 'type' => 'label',   'name' => 'Label' ),
              array( 'type' => 'text',    'name' => 'Bezeichnung'),
              array( 'type' => 'table', 		'name' => 'Tabelle'),
              array( 'type' => 'table.field', 		'name' => 'Tabellenfeld zur Anzeige'),
              array( 'type' => 'select', 	'name' => 'Relationtype',	'default' => '', 'definition' => 'single=0;multiple=1' ),
              array( 'type' => 'select', 	'name' => 'Relationstyle',	'default' => '', 'definition' => 'popup=0;selectbox=1' ),
            ),
						'description' => 'Mediafeld, welches eine Datei aus dem Medienpool holt',
						'dbtype' => 'text'
			);
	}
	
	
}

?>