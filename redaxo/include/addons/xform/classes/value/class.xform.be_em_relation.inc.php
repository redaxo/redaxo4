<?php

class rex_xform_be_em_relation extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		global $REX;


		// ***** CONFIG & CHECK

		$this->be_em = array();
		$this->be_em["target"] = array();
		$this->be_em["source"] = array();

		$this->be_em["label"] = $this->elements[2];	// HTML Bezeichnung

		$this->be_em["table"] = $this->elements[3]; // Zieltabelle
		$this->be_em["field"] = $this->elements[4]; // Zielfield welches angezeigt wird.

		$this->be_em["multiple"] = (int) $this->elements[5]; // single = 0 / multiple = 1
		if($this->be_em["multiple"] != 1)
		{
			$this->be_em["multiple"] = 0;
		}

		$this->be_em["eoption"] = (int) $this->elements[6]; // "Leer" Option
		if($this->be_em["eoption"] != 1)
		{
			$this->be_em["eoption"] = 0;
		}

		// Value angleichen -> immer Array mit IDs daraus machen
		if(!is_array($this->getValue()))
		{
			if(trim($this->getValue()) == "")
			{
				$this->setValue(array());
			}else
			{
				$this->setValue(explode(",",$this->getValue()));
			}
		}
		// Ab hier ist Value immer Array

		// Values prŸfen

		$sql = 'select id,'.$this->be_em["field"].' from rex_em_data_'.$this->be_em["table"];
		$value_names = array();
		if(count($this->getValue())>0)
		{
			$addsql = '';
			foreach($this->getValue() as $v)
			{
				if($addsql != "")
				{
					$addsql .= ' OR ';
				}
				$addsql .= ' id='.$v.'';
			}

			if($addsql != "")
			{
				$sql .= ' where '.$addsql;
			}
			$values = array();
			$vs = rex_sql::factory();
			$vs->setQuery($sql);
			foreach($vs->getArray() as $v){
				$value_names[$v["id"]] = $v[$this->be_em["field"]];
				$values[] = $v["id"];
			}
			$this->setValue($values);
		}



		$wc = "";
		if (isset($warning["el_" . $this->getId()]))
		{
			$wc = $warning["el_" . $this->getId()];
		}




		// ************ SELECT BOX

		$sss = rex_sql::factory();
		$sss->debugsql = $this->params["debug"];
		// $sss->debugsql = 1;
		$sss->setQuery('select * from rex_em_data_'.$this->be_em["table"].' order by '.$this->be_em["field"]);

		$SEL = new rex_select();
		$SEL->setName('FORM[' . $this->params["form_name"] . '][el_' . $this->id . '][]');
		$SEL->setId("el_" . $this->id);

		$SEL->setSize(1);

		// mit --- keine auswahl ---

		if($this->be_em["multiple"] == 1)
		{
			$SEL->setMultiple(TRUE);
			$SEL->setSize(5);
		}elseif($this->be_em["eoption"]==1)
		{
			$SEL->addOption("-", "");
		}
		foreach($sss->getArray() as $v)
		{
			$SEL->addOption( $v[$this->be_em["field"]],  $v["id"]);
		}

		// var_dump($this->getValue());
		$SEL->setSelected($this->getValue());

		$form_output[] = '
        <p class="formselect">
          <label class="select ' . $wc . '" for="el_' . $this->id . '" >' . $this->be_em["label"] . '</label>
          ' . $SEL->get() . '
        </p>';

		$email_elements[$this->getName()] = stripslashes(implode(",",$this->getValue()));
		$sql_elements[$this->getName()] = implode(",",$this->getValue());
















		return;

	}




	/*
	 * postAction wird nach dem Speichern ausgefŸhrt
	 * hier wird entsprechend der entities
	 */
	function postAction(&$email_elements, &$sql_elements)
	{

		$id = -1;
		if (isset($email_elements["ID"]) && $email_elements["ID"]>0)
		{
			$id = (int) $email_elements["ID"];
		}
		if ($id<1 && isset($this->params["main_id"]) && $this->params["main_id"]>0)
		{
			$id = (int) $this->params["main_id"];
		}


	}



	/*
	 * Allgemeine Beschreibung
	 */
	function getDescription()
	{
		// label,bezeichnung,tabelle,tabelle.feld,relationstype,style,no_db
		// return "be_em_relation -> Beispiel: ";
		return "";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'be_em_relation',
						'values' => array(
		array( 'type' => 'name',		'label' => 'Name' ),
		array( 'type' => 'text',		'label' => 'Bezeichnung'),
		array( 'type' => 'table',		'label' => 'Ziel Tabelle'),
		array( 'type' => 'table.field',	'label' => 'Ziel Tabellenfeld zur Anzeige'),
		array( 'type' => 'select',    'label' => 'Mehrfachauswahl', 'default' => '', 'definition' => 'single=0;multiple=1' ),
		array( 'type' => 'boolean',		'label' => 'Mit "Leer" Option' ),
		),
						'description' => 'Hiermit kann man Verkn&uuml;pfungen zu anderen Tabellen setzen',
						'dbtype' => 'text'
						);
	}


}

?>