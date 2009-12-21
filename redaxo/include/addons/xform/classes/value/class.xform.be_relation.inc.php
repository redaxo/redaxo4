<?php

class rex_xform_be_relation extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		global $REX;
		
		$bezeichnung = $this->elements[2];
		$tabelle = $this->elements[3];			// die tabelle mit der relation
		$tabelle_feld = $this->elements[4];	// tabellenfeld der zieltabelle welches angezeigt wird
		$relationstype = $this->elements[5];	// single=0;multiple=1
		$style = $this->elements[6];	// popup=0;selectbox=1
		
		// echo '<pre>'; var_dump($this->elements);echo '</pre>'; 
		
    // 'index.php?page=mediapool'+ param +'&opener_input_field='+ mediaid
		// $link = 'index.php?page='.rex_request("page").'&subpage='.$tabelle.'&popup=1';
		
		$wc = "";
    if (isset($warning["el_" . $this->getId()])) 
      $wc = $warning["el_" . $this->getId()];
		
    // TODO: MULTIPLE / SELECTBOX
    
    // SINGLE / POPUP
    if($relationstype == 0 && $style == 0)
    {
	    $form_output[] = '
	      <div class="xform-element formbe_relation formlabel-'.$this->label.'">
	        <label class="text ' . $wc . '" for="el_' . $this->id . '" >' . $bezeichnung . '</label>
	        <div class="rex-widget">
	          <div class="rex-widget-media">
	            <p class="rex-widget-field">
	              <input type="hidden" class="text" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" id="REX_RELATION_'.$this->id.'" value="'.htmlspecialchars(stripslashes($this->value)) . '" />
	              <input type="text" class="text '.$wc.'" id="REX_RELATION_TITLE_'.$this->id.'" readonly="readonly" value="" />
	            </p>
	            <p class="rex-widget-icons">
	              <a onclick="em_openRelation('.$this->id.',\''.$tabelle.'\',\''.$tabelle_feld.'\');return false;" class="rex-icon-file-open" href="#"><img width="16" height="16" alt="Medium auswählen" title="Medium auswählen" src="media/file_open.gif"/></a>
	              <a onclick="em_addRelation('.$this->id.',\''.$tabelle.'\',\''.$tabelle_feld.'\');return false;" class="rex-icon-file-add" href="#"><img width="16" height="16" alt="Neues Medium hinzufügen" title="Neues Medium hinzufügen" src="media/file_add.gif"/></a>
	              <a onclick="em_deleteRelation('.$this->id.',\''.$tabelle.'\',\''.$tabelle_feld.'\');return false;" class="rex-icon-file-delete" href="#"><img width="16" height="16" alt="Ausgewähltes Medium löschen" title="Ausgewähltes Medium löschen" src="media/file_del.gif"/></a>
	            </p>
	          </div>
	        </div>
	      </div>';		
    }elseif($relationstype == 0 && $style == 1)
    {
    
    	$sss = rex_sql::factory();
			$sss->debugsql = $this->params["debug"];
			$sss->setQuery('select * from rex_em_data_'.$tabelle.' order by '.$tabelle_feld);
			
			$SEL = new rex_select();
			$SEL->setName('FORM[' . $this->params["form_name"] . '][el_' . $this->id . ']');
			$SEL->setId("el_" . $this->id);
			$SEL->setSize(1);

			// mit --- keine auswahl ---
			// if ($this->elements[3] != 1)
			$SEL->addOption("-", "");

	    foreach($sss->getArray() as $k => $v)
			{
				$SEL->addOption( $v[$tabelle_feld], $k);
			}
    	
    	$SEL->setSelected($this->value);

			$form_output[] = '
				<p class="formselect">
					<label class="select ' . $wc . '" for="el_' . $this->id . '" >' . $bezeichnung . '</label>
					' . $SEL->get() . '
				</p>';
    
    }
		
		$email_elements[$this->label] = stripslashes($this->value);
		if (!isset($this->elements[7]) || $this->elements[7] != "no_db") 
			$sql_elements[$this->label] = $this->value;

	}
	
	function getDescription()
	{
		// label,bezeichnung,tabelle,tabelle.feld,relationstype,style,no_db
		// return "be_relation -> Beispiel: ";
		return "";
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