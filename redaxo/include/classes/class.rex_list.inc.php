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
	var $debug;
	
	var $name;
	var $caption;
	var $params;

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
	function rex_list($query, $rowsPerPage = 10, $listName = null, $debug = false)
	{
		global $REX;
		// TODO remove flag
//		$debug = true;
		
		// --------- Validation
		if(!$listName) $listName = md5($query);
		
		// --------- List Attributes
		$this->query = $query;
		$this->sql =& new rex_sql();
		$this->debug = $debug;
		$this->sql->debugsql =& $this->debug;
		$this->name = $listName;
		$this->caption = '';
		$this->params = array();
		
		// --------- Column Attributes
		$this->columnLabels = array();
		$this->columnFormates = array();
		$this->columnParams = array();
		$this->columnOptions = array();
		
		// --------- Pagination Attributes
		$this->rowsPerPage = $rowsPerPage;
		
		// --------- Load Data
		$this->sql->setQuery($this->prepareQuery($query));
		
		foreach($this->sql->getFieldnames() as $columnName)
			$this->columnNames[] = $columnName;
			
		// --------- Load Env
	  if($REX['REDAXO'])
	  	$this->loadBackendConfig();
	  	
	  $this->init();
	}
	
	function init()
	{
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
	
	function getMessage()
	{
		return rex_request($this->getName().'_msg', 'string');
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
	
	function addParam($name, $value)
	{
		$this->params[$name] = $value;
	}
	
	function getParams()
	{
		return $this->params;
	}
	
	function loadBackendConfig()
	{
		global $page, $subpage;
		
		$this->addParam('page', $page);
		$this->addParam('subpage', $subpage);
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
	 * Verlinkt eine Spalte mit den übergebenen Parametern
	 * 
	 * @param $columnName Name der Spalte
	 * @param $params Array von Parametern
	 */
	function setColumnParams($columnName, $params = array())
	{
		if(!is_array($params))
		{
			trigger_error('rex_list: Erwarte 2. Parameter als Array!', E_USER_ERROR);
		}
		$this->columnParams[$columnName] = $params;
	}
	
	/**
	 * Gibt die Parameter für eine Spalte zurück
	 * 
	 * @param $columnName Name der Spalte
	 * 
	 * @return array
	 */
	function getColumnParams($columnName)
	{
		return $this->columnParams[$columnName];
	}
	
	/**
	 * Gibt zurück, ob Parameter für eine Spalte existieren
	 * 
	 * @param $columnName Name der Spalte
	 * 
	 * @return boolean
	 */
	function hasColumnParams($columnName)
	{
		return is_array($this->columnParams[$columnName]) && count($this->columnParams[$columnName]) > 0;
	}
	
	/**
	 * Gibt eine Url zurück
	 */	
	function getUrl($params = array())
	{
		$params = array_merge($this->getParams(), $params);
		
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
		return str_replace('&', '&amp;', 'index.php?list='. $this->getName() . $paramString);
	}
	
	function getParsedUrl($params = array())
	{
		return $this->replaceVariables($this->getUrl($params));
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
	 * Gibt die Anzahl der Zeilen zurück, welche vom ursprüngliche SQL Statement betroffen werden
	 * 
	 * @return int
	 */
	function getRows()
	{
		if(!$this->rows)
		{
			$sql = rex_sql::getInstance();
			$sql->debugsql = $this->debug;
			$sql->setQuery($this->query);
			$this->rows = $sql->getRows();
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
		$start = 0;
				
		if(rex_request('list', 'string') == $this->getName())
		{
			$start = rex_request('start', 'int', 0);
			$rows = $this->getRows();
			
			if($start < 0 || $start > $rows)
			{
				$start = 0;
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
		$s .= '<a href="'. $this->getUrl(array('start' => 0)) .'">first</a>'. "\n";
		$s .= '<a href="'. $this->getUrl(array('start' => $start - $rowsPerPage)) .'">previous</a>'. "\n";
		$s .= '<a href="'. $this->getUrl(array('func' => 'add')) .'">add</a>'. "\n";
		$s .= '<a href="'. $this->getUrl(array('start' => $start + $rowsPerPage)) .'">next</a>'. "\n";
		$s .= '<a href="'. $this->getUrl(array('start' => ($pages - 1)* $rowsPerPage)) .'">last</a>'. "\n";
		$s .= $this->getRows(). ' rows found ';
		
		if($pages > 1)
		{
			$s .= '  <ul>'. "\n";
			for($i = 1; $i <= $pages; $i++)
			{
				$first = ($i - 1) * $rowsPerPage;
				$last = $i * $rowsPerPage;
				
				if($last > $rows)
				  $last = $rows;
				  
				$pageLink = ($first + 1) .'-'. $last;
				if($start != $first)
				{
					$pageLink = '<a href="'. $this->getUrl(array('start' => $first)) .'">'. $pageLink .'</a>';
				}
				
				$s .= '    <li>'. $pageLink .'</li>'. "\n";
			}
			$s .= '  </ul>'. "\n";
		}
		
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
	
	function replaceVariable($value, $varname)
	{
		return str_replace('%'. $varname .'%', $this->sql->getValue($varname), $value);
	}
	
	/**
	 * Ersetzt alle Variablen im Format %<Spaltenname>%.
	 * 
	 * @param $value Zu durchsuchender String
	 * @param $columnNames Zu suchende Spaltennamen
	 * 
	 * @return string
	 */
	function replaceVariables($value)
	{
		if(strpos($value, '%') === false)
			return $value;
			
		$columnNames = $this->getColumnNames();
		
		if(is_array($columnNames))
		{
			foreach($columnNames as $columnName)
			{
				// Spalten, die mit addColumn eingefügt wurden
				if(is_array($columnName))
					continue;
					
				$value = $this->replaceVariable($value, $columnName);
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
		$s = '';
	  $columnFormates = array();
	  $columnNames = $this->getColumnNames();
	  $sortColumn = $this->getSortColumn();
	  $sortType = $this->getSortType();
	  $caption = $this->getCaption();
	  $message = $this->getMessage();
		
		if($message != '')
		{
			$s .= '<p class="rex-warning">'. $message .'</p>'. "\n";
		}
		
		$s .= '<p>'. "\n";
		$s .= $this->getPagination();
		$s .= '</p>'. "\n";
		$s .= '<form action="'. $this->getUrl() .'" method="post">'. "\n";
		$s .= '  <table class="rex-table">'. "\n";
		
		if($caption != '')
		{
			$s .= '    <caption class="rex-hide">'. $caption .'</caption>'. "\n";
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
		
		if($this->getRows() > 0)
		{
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
						$columnValue = $this->replaceVariables($this->formatValue($columnFormates[$columnName][0], $columnFormates[$columnName]));
					}
					else
					{
						$columnValue = $this->formatValue($this->sql->getValue($columnName), $columnFormates[$columnName]);
					}
					
					if($this->hasColumnParams($columnName))
					{
						$columnValue = '<a href="'. $this->getParsedUrl($this->getColumnParams($columnName)) .'">'. $columnValue .'</a>';
					}
					
					$s .= '        <td>'. $columnValue .'</td>'. "\n";
				}
				$s .= '      </tr>'. "\n";
				
				$this->sql->next();
			}
			$s .= '    </tbody>'. "\n";
		}
		else
		{
			$s .= '<tr><td>No Rows Found</td></tr>';
		}
		
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