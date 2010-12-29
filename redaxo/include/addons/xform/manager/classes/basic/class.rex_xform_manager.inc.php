<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

if (!function_exists('rex_xform_manager_checkField'))
{
	function rex_xform_manager_checkField($l,$v,$p)
	{
		return rex_xform_manager::checkField($l,$v,$p);
	}
}


if (!class_exists('rex_xform_manager'))
{
class rex_xform_manager
{

	var $table = "";
	var $linkvars = array();
	var $type = "";

	function rex_xform_manager()
	{
		global $REX;



	}



	function setType($type = "")
	{
		// z.B. 'com' oder 'em';
		$this->type = $type;
	}

	function getType()
	{
		return $this->type;
	}


	// ----- Seitenausgabe

	function setLinkVars($linkvars)
	{
		$this->linkvars = $linkvars;
	}
	
	function getLinkVars()
	{
		return $this->linkvars;
	}
	

	function getDataPage()
	{
		// TODO	
		global $REX,$I18N;
		include $REX["INCLUDE_PATH"]."/addons/xform/manager/pages/data.inc.php";
	}


	function getFieldPage()
	{
		// TODO
		global $REX,$I18N;
		include $REX["INCLUDE_PATH"]."/addons/xform/manager/pages/field.inc.php";
	}









	// ----- Allgemeine Methoden

	function setFilterTable($table)
	{
		$this->filterTables[$table] = $table;
	}

	function getFilterTables()
	{
		if(isset($this->filterTables) && is_array($this->filterTables))
			return $this->filterTables;
		else
			return array();
	}

	function getTables()
	{
		global $REX;
		
		$where = '';
		foreach($this->getFilterTables() as $t)
		{
			if($where != "") 
				$where .= ' OR ';
			$where .= '(table_name = "'.$t.'")';
		}
		
		if($where != "")
			$where = ' where '.$where;
		
		$tb = rex_sql::factory();
		// $tb->debugsql = 1;
		$tb->setQuery('select * from rex_'.$this->getType().'_table '.$where.' order by prio,name');
		return $tb->getArray();
	}


	// ----- Felder

	function getTableFields($table, $type="")
	{
		global $REX;
		
		if($type == "") $type =  $this->getType();
		
		$tb = rex_sql::factory();
		$tb->setQuery('select * from rex_'.$type.'_field where table_name="'.$table.'" order by prio');
		return $tb->getArray();
	}
	
	function checkField($l,$v,$p)
	{
		global $REX;
		$q = 'select * from '.$p["type"].' where table_name="'.$p["table_name"].'" and '.$l.'="'.$v.'" LIMIT 1';
		$c = rex_sql::factory();
		// $c->debugsql = 1;
		$c->setQuery($q);
		if($c->getRows()>0)
		{
			// FALSE -> Warning = TRUE;
			return TRUE;
		}else
		{
			return FALSE;
		}
	}


	// ----- Installation

	function createBasicSet($mifix = "", $withdrop = FALSE)
	{
	
		if($mifix == "") { return FALSE; }
	

		$c = new rex_sql();

		if($withdrop)
			$c->setQuery('DROP TABLE IF EXISTS `rex_'.$mifix.'_table`;');

		$c->setQuery('CREATE TABLE IF NOT EXISTS `rex_'.$mifix.'_table` (
		  `id` int(11) NOT NULL auto_increment,
		  `status` tinyint(4) NOT NULL,
		  `table_name` varchar(255) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `description` text NOT NULL,
		  `list_amount` INT UNSIGNED NOT NULL DEFAULT \'50\',
		  `prio` varchar(255) NOT NULL,
		  `search` TINYINT NOT NULL,
		  `hidden` TINYINT NOT NULL,
		  `export` TINYINT NOT NULL,
		  `import` TINYINT NOT NULL,
		  PRIMARY KEY  (`id`)
		);');

		if($withdrop)
			$c->setQuery('DROP TABLE IF EXISTS `rex_'.$mifix.'_field`;');

		$c->setQuery('CREATE TABLE IF NOT EXISTS `rex_'.$mifix.'_field` (
		  `id` int(11) NOT NULL auto_increment, 
		  `table_name` varchar(255) NOT NULL,
		  `prio` varchar(255) NOT NULL,
		  `type_id` varchar(255) NOT NULL,
		  `type_name` varchar(255) NOT NULL,
		  `f1` text NOT NULL,
		  `f2` text NOT NULL,
		  `f3` text NOT NULL,
		  `f4` text NOT NULL,
		  `f5` text NOT NULL,
		  `f6` text NOT NULL,
		  `f7` text NOT NULL,
		  `f8` text NOT NULL,
		  `f9` text NOT NULL,
		  `list_hidden` TINYINT NOT NULL,
		  `search` TINYINT NOT NULL,
		  PRIMARY KEY  (`id`)
		);');

		if($withdrop)
			$c->setQuery('DROP TABLE IF EXISTS `rex_'.$mifix.'_relation`;');
		
		$c->setQuery('CREATE TABLE IF NOT EXISTS `rex_'.$mifix.'_relation` (
		  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		  `source_table` VARCHAR( 255 ) NOT NULL ,
		  `source_name` VARCHAR( 255 ) NOT NULL ,
		  `source_id` INT NOT NULL ,
		  `target_table` VARCHAR( 255 ) NOT NULL ,
		  `target_id` INT NOT NULL
		);');
	
		// *** TODO:Prüfen ob Tabellen vorhanden sind
		// Eventuell nur ALTER verwenden ob Tabellen auf den akgtuellen Stand zu bringen
	
		return TRUE;
	
	}

	function createTable($mifix = "", $data_table, $params = array(), $debug = FALSE)
	{

		// Tabelle erstellen wenn noch nicht vorhanden
		$c = new rex_sql;
		$c->debugsql = $debug;
		$c->setQuery('CREATE TABLE IF NOT EXISTS `'.$data_table.'` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY );');
		
		// Tabellenset in die Basics einbauen, wenn noch nicht vorhanden
		$c = new rex_sql;
		$c->debugsql = $debug;
		$c->setQuery('DELETE FROM rex_'.$mifix.'_table where table_name="'.$data_table.'"');
		$c->setTable('rex_'.$mifix.'_table');
		
		$params["table_name"] = $data_table;
		if(!isset($params["status"]))
			$params["status"] = 1;

		if(!isset($params["name"]))
			$params["name"] = 'Tabelle "'.$data_table.'"';

		if(!isset($params["prio"]))
			$params["prio"] = 100;

		if(!isset($params["search"]))
			$params["search"] = 0;

		if(!isset($params["hidden"]))
			$params["hidden"] = 0;

		if(!isset($params["export"]))
			$params["export"] = 0;
		
		foreach($params as $k => $v)
		{
			$c->setValue($k, $v);
		}
		
		$c->insert();

		return TRUE;		
	
	}

	function addDataFields($midfix, $data_table, $fields, $debug = FALSE)
	{
		
		// definition fields table
		foreach($fields as $field)
		{
		
			$table_name = $field['table_name']; // 'user'; 
			$type_id = $field['type_id']; // 'value'; 
			$type_name = $field['type_name']; // 'select'; 
			$f1 = $field['f1']; // 'status'; 

			if(!in_array($type_id,rex_xform::getTypes()))
				return FALSE;

			$gs = new rex_sql;
			$gs->debugsql = $debug;
			$gs->setQuery('delete from rex_'.$midfix.'_field where table_name="'.$table_name.'" and type_id="'.$type_id.'" and type_name="'.$type_name.'" and f1="'.$f1.'"');

			// fielddaten - datensatz anlegen
			$af = rex_sql::factory();
			$af->debugsql = $debug;
			$af->setTable('rex_'.$midfix.'_field');
			foreach($field as $k => $v)
			{
				$af->setValue($k, $v);
			}
			if(!$af->insert())
				return FALSE;

			// datentabelle - spalte hinzufügen
			if($type_id == "value" && $type_name != "" && $f1 != "")
			{
				if ($classname = rex_xform::includeClass('value',$type_name)){ }else { return FALSE; }
				$cl = new $classname;
				$definitions = $cl->getDefinitions();
				if(isset($definitions["dbtype"]) && $definitions["dbtype"]!="")
				{
					// Structur in spalte anpassen
					$af = rex_sql::factory();
					$af->debugsql = $debug;
					$af->setQuery('ALTER TABLE `'.$data_table.'` ADD `'.$f1.'` '.$definitions["dbtype"].' NOT NULL ;');
				}
			}

		}
	
		return TRUE;
	}
















	function generateAll()
	{
		global $REX;
	
		$types = rex_xform::getTypeArray();
		foreach($this->getTables() as $table)
		{
				
			// ********** Table schon vorhanden ?, wenn nein, dann anlegen
			$c = rex_sql::factory();
			// $c->debugsql = 1;
			$c->setQuery('CREATE TABLE IF NOT EXISTS `'.$table["table_name"].'` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY )');
				
			// Felder merken, erstellen und eventuell loeschen
			$c->setQuery('SHOW COLUMNS FROM `'.$table["table_name"].'`');
			$saved_columns = $c->getArray();
	
			foreach($this->getTableFields($table["table_name"]) as $field)
			{
				$type_name = $field["type_name"];
				$type_id = $field["type_id"];
	
				if($type_id == "value")
				{
					$type_label = $field["f1"];
					$dbtype = $types[$type_id][$type_name]['dbtype'];
	
					if($dbtype != "none" && $dbtype != "")
					{
						// Column schon vorhanden ?
						$add_column = TRUE;
						foreach($saved_columns as $uu => $vv)
						{
							if ($vv["Field"] == $type_label)
							{
								$add_column = FALSE;
								unset($saved_columns[$uu]);
								break;
							}
						}
	
						// Column erstellen
						if($add_column)
						{
							$c->setQuery('ALTER TABLE `'.$table["table_name"].'` ADD `'.$type_label.'` '.$dbtype);
						}
					}
						
				}
	
			}
	
			// Loeschen von Spalten ohne Zuweisung
			foreach($saved_columns as $uu => $vv)
			{
				if ($vv["Field"] != "id")
				{
					$c->setQuery('ALTER TABLE `'.$table["table_name"].'` DROP `'.$vv["Field"].'` ');
				}
			}
	
		}
	}



	function repairAll()
	{
		// Alle Tabellen durchgehen und anpassen
		// - relation
		// - field
		// - data
	
	}


}

}