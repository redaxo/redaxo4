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
		
		// echo '<pre>'; var_dump($this->elements);echo '</pre>'; 
		
    // 'index.php?page=mediapool'+ param +'&opener_input_field='+ mediaid
		$link = 'index.php?page='.rex_request("page").'&subpage='.$tabelle.'&popup=1';
		
		$wc = "";
    if (isset($warning["el_" . $this->getId()])) 
      $wc = $warning["el_" . $this->getId()];
		
    // counter, wenn dieses Feld šfter in einem Formular erscheint
    if(!isset($REX["xform_classes_be_relation"]))
      $REX["xform_classes_be_relation"] = 0;
    $REX["xform_classes_be_relation"]++;
    $i = $REX["xform_classes_be_relation"];
		
    $form_output[] = '
    
      <div class="xform-element formbe_relation formlabel-'.$this->label.'">
        <label class="text ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>
        <div class="rex-widget">
          <div class="rex-widget-media">
            <p class="rex-widget-field">
              <input type="text" class="text '.$wc.'" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" id="REX_RELATION_'.$i.'" readonly="readonly" value="'.htmlspecialchars(stripslashes($this->value)) . '" />
            </p>
            <p class="rex-widget-icons">
              <a onclick="newPoolWindow(\''.$link.'\');return false;" class="rex-icon-file-open" href="#"><img width="16" height="16" alt="Medium auswählen" title="Medium auswählen" src="media/file_open.gif"/></a>
              <a onclick="addREXMedia('.$i.');return false;" class="rex-icon-file-add" href="#"><img width="16" height="16" alt="Neues Medium hinzufügen" title="Neues Medium hinzufügen" src="media/file_add.gif"/></a>
              <a onclick="deleteREXMedia('.$i.');return false;" class="rex-icon-file-delete" href="#"><img width="16" height="16" alt="Ausgewähltes Medium löschen" title="Ausgewähltes Medium löschen" src="media/file_del.gif"/></a>
            </p>
          </div>
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
						'description' => 'hiemit kann man Verkn&uuml;pfungen zu anderen Tabellen setzen',
						'dbtype' => 'text'
			);
	}
	
	
}

?>