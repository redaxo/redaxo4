<?php

// Nötige Konstanten
define('REX_LIST_OPT_SORT', 0);

/** 
 * Klasse zum erstellen von Listen
 * 
 * @package redaxo3 
 * @version $Id$ 
 */
 
/*
Beispiel:

$list = new rex_list('SELECT id,name FROM rex_article');
$list->setColumnFormat('id', 'date');
$list->setColumnLabel('name', 'Artikel-Name');
$list->setColumnSortable('name');
$list->addColumn('testhead','%id% - %name%',-1);
$list->addColumn('testhead2','testbody2');
$list->setCaption('thomas macht das geile css');
$list->show();

*/

class rex_list
{
	var $query;
	var $sql;
	var $debugsql;
	
	var $name;
	var $caption;	

	// --------- Column Attributes
	var $columnNames;
	var $columnLabels;
	var $columnFormates;
	var $columnOptions;
	
	// --------- Pagination Attributes
	var $rowsPerPage;
	
	/**
	 * Erstellt ein rex_list Objekt
	 * 
	 * @param $query SELECT Statement
	 * @param $rowsPerPage Anzahl der Elemente pro Zeile
	 * @param $listName Name der Liste
	 */
	function rex_list($query, $rowsPerPage = 10, $listName = null)
	{
		// --------- Validation
		if(!$listName) $listName = md5($query);
		
		// --------- List Attributes
		$this->query = $query;
		$this->sql =& new rex_sql();
		$this->sql->debugsql =& $this->debugsql;
		$this->debugsql = false;
		$this->name = $listName;
		
		// --------- Column Attributes
		$this->columnLabels = array();
		$this->columnFormates = array();
		$this->columnOptions = array();
		
		// --------- Pagination Attributes
		$this->rowsPerPage = $rowsPerPage;
		
		// --------- Load Data
		$this->sql->setQuery($this->prepareQuery($query));
		
		foreach($this->sql->getFieldnames() as $columnName)
			$this->columnNames[] = $columnName;
	}
	
	// ---------------------- setters/getters
	
	/**
	 * Gibt den Namen es Formulars zurück
	 * 
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}
	
	/**
	 * Setzt die Caption/den Titel der Tabelle
	 * Gibt den Namen es Formulars zurück
	 * 
	 * @param $caption Caption/Titel der Tabelle
	 */
	function setCaption($caption)
	{
		$this->caption = $caption;
	}
	
	/**
	 * Gibt die Caption/den Titel der Tabelle zurück
	 * 
	 * @return string
	 */
	function getCaption()
	{
		return $this->caption;
	}
	// ---------------------- Column setters/getters/etc
		
	/**
	 * Methode, um beliebige Spalten einzufügen
	 * 
	 * @param $columnHead Titel der Spalte
	 * @param $columnBody Text/Format der Spalte
	 * @param $columnIndex Stelle, an der die neue Spalte erscheinen soll
	 */
	function addColumn($columnHead, $columnBody, $columnIndex = null)
	{
		// Bei negativem columnIndex, das Element am Ende anfügen
		if($columnIndex < 0)
		{
			$columnIndex = count($this->columnNames);
		}
		
		$this->columnNames = array_insert($this->columnNames, $columnIndex, array($columnHead));
		$this->setColumnFormat($columnHead, $columnBody);
	}
	
	/**
	 * Gibt den Namen einer Spalte zurück
	 * 
	 * @param $columnIndex Nummer der Spalte
	 * @param $default Defaultrückgabewert, falls keine Spalte mit der angegebenen Nummer vorhanden ist
	 * 
	 * @return string|null
	 */
	function getColumnName($columnIndex, $default = null)
	{
		if(isset($this->columnNames[$columnIndex]))
			return $this->columnNames[$columnIndex];
			
		return $default;
	}
	
	/**
	 * Gibt alle Namen der Spalten als Array zurück
	 * 
	 * @return array
	 */
	function getColumnNames()
	{
		return $this->columnNames;
	}
	
	/**
	 * Setzt ein Label für eine Spalte
	 * 
	 * @param $columnName Name der Spalte
	 * @param $label Label für die Spalte
	 */
	function setColumnLabel($columnName, $label)
	{
		$this->columnLabels[$columnName] = $label;
	}

	/**
	 * Gibt das Label der Spalte zurück, falls gesetzt.
	 * 
	 * Falls nicht vorhanden und der Parameter $default auf null steht, 
	 * wird der Spaltenname zurückgegeben
	 * 
	 * @param $column Name der Spalte
	 * @param $default Defaultrückgabewert, falls kein Label gesetzt ist
	 * 
	 * @return string|null 
	 */
	function getColumLabel($column, $default = null)
	{
		if(isset($this->columnLabels[$column]))
			return $this->columnLabels[$column];
			
	  return $default === null ? $column : $default;
	}
	
	/**
	 * Setzt ein Format für die Spalte
	 * 
	 * @param $column Name der Spalte
	 * @param $format_type Formatierungstyp
	 * @param $format Zu verwendentes Format
	 */
	function setColumnFormat($column, $format_type, $format = '')
	{
		$this->columnFormates[$column] = array($format_type, $format);
	}
	
	/**
	 * Gibt das Format für eine Spalte zurück
	 * 
	 * @param $column Name der Spalte
	 * @param $default Defaultrückgabewert, falls keine Formatierung gesetzt ist
	 * 
	 * @return string|null
	 */
	function getColumnFormat($column, $default = null)
	{
		if(isset($this->columnFormates[$column]))
			return $this->columnFormates[$column];
			
		return $default;
	}

	/**
	 * Markiert eine Spalte als sortierbar
	 * 
	 * @param $column Name der Spalte
	 */	
	function setColumnSortable($column)
	{
		$this->setColumnOption($column, REX_LIST_OPT_SORT, true);
	}
	
	/**
	 * Setzt eine Option für eine Spalte
	 * (z.b. Sortable,..)
	 * 
	 * @param $column Name der Spalte
	 * @param $option Name/Id der Option
	 * @param $value Wert der Option
	 */
	function setColumnOption($column, $option, $value)
	{
		$this->columnOptions[$column][$option] = $value;
	}
	
	/**
	 * Gibt den Wert einer Option für eine Spalte zurück
	 * 
	 * @param $column Name der Spalte
	 * @param $option Name/Id der Option
	 * @param $default Defaultrückgabewert, falls die Option nicht gesetzt ist
	 * 
	 * @return mixed|null
	 */
	function getColumnOption($column, $option, $default = null)
	{
		if($this->hasColumnOption($column, $option))
		{
			return $this->columnOptions[$column][$option];
		}
		return $default;
	}

	/**
	 * Gibt zurück, ob für eine Spalte eine Option gesetzt wurde
	 * 
	 * @param $column Name der Spalte
	 * @param $option Name/Id der Option
	 * @param $default Defaultrückgabewert, falls die Option nicht gesetzt ist
	 * 
	 * @return boolean
	 */
	function hasColumnOption($column, $option)
	{
		return isset($this->columnOptions[$column][$option]);
	}

	/**
	 * Gibt eine Urls zurück
	 */	
	function getUrl($params = array())
	{
		global $page;
		
		if(!isset($params['items']))
		{
			$params['items'] = $this->getRowsPerPage();
		}
			
		if(!isset($params['sort']))
		{
			$params['sort'] = $this->getSortColumn();
			$params['sorttype'] = $this->getSortType();
		}
		
		$paramString = '';
		foreach($params as $name => $value)
		{
			$paramString .= '&'. $name .'='. $value;
		}
		return str_replace('&', '&amp;', '?page='. $page .'&list='. $this->getName() . $paramString);
	}
	
	// ---------------------- Pagination
	
	/**
	 * Prepariert das SQL Statement vorm anzeigen der Liste
	 * 
	 * @param $query SQL Statement
	 * 
	 * @return string
	 */
	function prepareQuery($query)
	{
		$rowsPerPage = $this->getRowsPerPage();
		$startRow = $this->getStartRow();
		
		$sortColumn = $this->getSortColumn();
		$sortType = $this->getSortType();
		
		if($sortColumn != '')
			$query .= ' ORDER BY '. $sortColumn .' '. $sortType;
					
		$query .= ' LIMIT '. $startRow .','. $rowsPerPage;
		
		return $query;
	}

	/**
	 * Gibt die Anzahl der Zeilen zurück, welche vom SQL Statement betroffen werden
	 * 
	 * @return int
	 */
	function getRows()
	{
		if(!$this->rows)
		{
			$sql = rex_sql::getInstance();
			$sql->debugsql = $this->debugsql;
			$sql->setQuery(preg_replace('/SELECT(.*)FROM/iU', 'SELECT count(*) as rowCount FROM	', $this->query, 1));
			$this->rows = $sql->getValue('rowCount'); 
		}
		
		return $this->rows;
	}
	
	/**
	 * Gibt die Anzahl der Zeilen pro Seite zurück
	 * 
	 * @return int
	 */
	function getRowsPerPage()
	{
		if(rex_request('list', 'string') == $this->getName())
		{
			$rowsPerPage = rex_request('items', 'int');
			if($rowsPerPage <= 0)
			{
				// Fallback auf Default-Wert
				$rowsPerPage = $this->rowsPerPage;
			}
			$this->rowsPerPage = $rowsPerPage;
		}
		
		return $this->rowsPerPage;
	}
	
	/**
	 * Gibt die Nummer der Zeile zurück, von der die Liste beginnen soll
	 * 
	 * @return int
	 */
	function getStartRow()
	{
		$start = 1;
				
		if(rex_request('list', 'string') == $this->getName())
		{
			$start = rex_request('start', 'int', 1);
			$rows = $this->getRows();
			
			if($start < 0 || $start > $rows)
			{
				$start = 1;
			}
		}
			
		return $start;
	}
	
	/**
	 * Gibt zurück, nach welcher Spalte sortiert werden soll
	 * 
	 * @return string
	 */
	function getSortColumn($default = null)
	{
		if(rex_request('list', 'string') == $this->getName())
		{
			return rex_request('sort','string', $default);
		}
		return $default;
	}
	
	/**
	 * Gibt zurück, in welcher Art und Weise sortiert werden soll (ASC/DESC)
	 * 
	 * @return string
	 */
	function getSortType($default = null)
	{
		if(rex_request('list', 'string') == $this->getName())
		{
			$sortType = rex_request('sorttype','string');
			
			if(in_array($sortType, array('asc', 'desc')))
			{
				return $sortType;
			}
		}
		return $default;
	}
	
	/**
	 * Gibt die Navigation der Liste zurück
	 * 
	 * @return string
	 */
	function getPagination()
	{
		$start = $this->getStartRow();
		$rows = $this->getRows();
		$rowsPerPage = $this->getRowsPerPage();
		$pages = ceil($rows / $rowsPerPage);
		
		$s = ''. "\n";
		$s .= '  <ul>'. "\n";
		for($i = 1; $i <= $pages; $i++)
		{
			$first = ($i - 1) * $rowsPerPage + 1;
			$last = $i * $rowsPerPage;
			
			if($last > $rows)
			  $last = $rows;
			  
			$pageLink = $first .'-'. $last;
			if($start < $first || $start > $last)
			{
				$pageLink = '<a href="'. $this->getUrl(array('start' => $first)) .'">'. $pageLink .'</a>';
			}
			
			$s .= '    <li>'. $pageLink .'</li>'. "\n";
		}
		$s .= '  </ul>'. "\n";
		
		return $s;
	}
	
	// ---------------------- Layout
	
	/**
	 * Gibt den Footer der Liste zurück
	 * 
	 * @return string
	 */
	function getFooter()
	{
		$s = '';
		
		$s .= '      <tr>'. "\n";
		$s .= '        <td colspan="'. count($this->getColumnNames()) .'"><input type="text" name="items" value="'. $this->getRowsPerPage() .'" maxlength="2" /><input type="submit" value="Anzeigen" /></td>'. "\n";
		$s .= '      </tr>'. "\n";
		
		return $s;
	}
	
	// ---------------------- Generate Output
	
	/**
	 * Ersetzt alle Variablen im Format %<Spaltenname>%.
	 * 
	 * @param $value Zu durchsuchender String
	 * @param $sql SQL Objekt, dass alle Werte enthält
	 * @param $columnNames Zu suchende Spaltennamen
	 * 
	 * @return string
	 */
	function replaceVariables($value, $sql, $columnNames)
	{
		if(is_array($columnNames))
		{
			foreach($columnNames as $columnName)
			{
				// Spalten, die mit addColumn eingefügt wurden
				if(is_array($columnName))
					continue;
					
				$value = str_replace('%'. $columnName .'%', $sql->getValue($columnName), $value);
			}
		}
		return $value;
	}

	/**
	 * Formatiert einen übergebenen String anhand der rexFormatter Klasse
	 * 
	 * @param $value Zu formatierender String
	 * @param $format Array mit den Formatierungsinformationen
	 * 
	 * @return string 
	 */
	function formatValue($value, $format)
	{
		if(!is_array($format))
			return $value;

	  return rex_formatter::format($value, $format[0], $format[1]);
	}
	
	/**
	 * Erstellt den Tabellen Quellcode
	 * 
	 * @return string
	 */
	function get()
	{
	  $columnFormates = array();
	  $columnNames = $this->getColumnNames();
	  $rowCount = $this->getRows();
	  $sortColumn = $this->getSortColumn();
	  $sortType = $this->getSortType();
	  $caption = $this->getCaption();	  
	  
		$s = '';
		$s .= $this->getPagination();
		$s .= '<form action="'. $this->getUrl() .'" method="post">'. "\n";
		$s .= '  <table>'. "\n";
		
		if($caption != '')
		{
			$s .= '    <caption>'. $caption .'</caption>'. "\n";
		}
		
		$s .= '    <thead>'. "\n";
		$s .= '      <tr>'. "\n";
		foreach($columnNames as $columnName)
		{
			// Spalten, die mit addColumn eingefügt wurden
			if(is_array($columnName))
			{
				$columnName = $columnName[0];
			}
			
			$columnHead = $this->getColumLabel($columnName);
			if($columnNames != $sortColumn && $this->hasColumnOption($columnName, REX_LIST_OPT_SORT))
			{
				$columnSortType = $sortType == 'desc' ? 'asc' : 'desc';
				$columnHead = '<a href="'. $this->getUrl(array('start' => $this->getStartRow(),'sort' => $columnName, 'sorttype' => $columnSortType)) .'">'. $columnHead .'</a>';
			}
			$s .= '        <th>'. $columnHead .'</th>'. "\n";
			
			// Formatierungen hier holen, da diese Schleife jede Spalte nur einmal durchläuft
			$columnFormates[$columnName] = $this->getColumnFormat($columnName);
		}
		$s .= '      </tr>'. "\n";
		$s .= '    </thead>'. "\n";
		
		$s .= '    <tfoot>'. "\n";
		$s .= $this->getFooter();
		$s .= '    </tfoot>'. "\n";
		
		$s .= '    <tbody>'. "\n";
		for($i = 0; $i < $this->sql->getRows(); $i++)
		{
			$s .= '      <tr>'. "\n";
			foreach($columnNames as $columnName)
			{
				// Spalten, die mit addColumn eingefügt wurden
				if(is_array($columnName))
				{
					// Nur hier sind Variablen erlaubt
					$columnName = $columnName[0];
					$s .= '        <td>'. $this->replaceVariables($this->formatValue($columnFormates[$columnName][0], $columnFormates[$columnName]), $this->sql, $columnNames) .'</td>'. "\n";
				}
				else
				{
					$s .= '        <td>'. $this->formatValue($this->sql->getValue($columnName), $columnFormates[$columnName]) .'</td>'. "\n";
				}
			}
			$s .= '      </tr>'. "\n";
			
			$this->sql->next();
		}
		$s .= '    </tbody>'. "\n";
		
		$s .= '  </table>'. "\n";
		$s .= '</form>'. "\n";
		
		return $s;
	}
	
	function show()
	{
		echo $this->get();
	}
}

?>