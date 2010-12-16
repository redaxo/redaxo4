<?php

// ********************************************* DATA ADD/EDIT/LIST

$func = rex_request("func","string","");
$data_id = rex_request("data_id","int","");
$rex_em_opener_field = rex_request("rex_em_opener_field","int",-1);
$rex_em_opener_fieldname = rex_request("rex_em_opener_fieldname","string","");
$rex_em_opener_info = rex_request("rex_em_opener_info","string","");
$rex_em_filter = rex_request("rex_em_filter","array");
$rex_em_set = rex_request("rex_em_set","array");
$rex_em_searchfields = rex_request("rex_em_searchfields","array");
$rex_em_searchtext = rex_request("rex_em_searchtext","string");
$rex_em_search = rex_request("rex_em_search","int","0");

// ********************************** DEFAULT - LISTE AUSGEBEN
$show_list = TRUE;

$link_vars = "";
foreach($this->getLinkVars() as $k => $v)
{
  $link_vars .= '&'.urlencode($k).'='.urlencode($v);
}

// ********************************** TABELLE HOLEN
unset($table);
$tables = $this->getTables();
if(!isset($table))
{
  if(count($tables) > 0)
  {
    $table = current($tables);
  }else
  {
    echo 'Keine Tabelle gefunden';
    exit;
  }
}

echo '<table cellpadding="5" class="rex-table"><tr><td><b>'.$table["table_name"].'</b>';
if($table["description"] != "") echo "<b>:</b> ".$table["description"];
/*
if($rex_em_opener_info != "")
{
  echo ' - '.$I18N->msg("openerinfo").': '.$rex_em_opener_info;
}
*/
echo '</td></tr></table><br />';
$table["fields"] = $this->getTableFields($table["table_name"]);


// ********************************** DB FELDER HOLEN
$fields = $table['fields'];
$field_names = array();
foreach($fields as $field){ if($field["type_id"] == "value") { $field_names[] = $field["f1"]; } }



// ********************************** DB DATA HOLEN

$data = array();
if($data_id != "")
{
  $gd = rex_sql::factory();
  $gd->setQuery('select * from '.$table["table_name"].' where id='.$data_id);
  if($gd->getRows()==1)
  {
    $datas = $gd->getArray();
    $data = current($datas);
  
  }else
  {
    $data_id = "";
  }
}

if($func == 'moveup' OR $func == 'movedown')
{
  if($func == 'moveup')
  {
    $sql = rex_sql::factory();
    $sql->setQuery('SELECT MAX('.$this->type.'_rgt) as '.$this->type.'_rgt FROM `'.$table['table_name'].'`');
    $maxrgt = $sql->getValue($this->type.'_rgt');
    $sql->flush();
    $sql->setQuery('
      SELECT id,'.$this->type.'_lft,'.$this->type.'_rgt
      FROM `'.$table['table_name'].'`
      WHERE '.$this->type.'_rgt = '.($data[$this->type.'_lft'] - 1).'
      AND '.$this->type.'_level = '.$data[$this->type.'_level'].'
    ');
    
    if($sql->getRows())
    {
      $qry1 = 'UPDATE `'.$table['table_name'].'` SET '.$this->type.'_lft = '.$this->type.'_lft + '.($data[$this->type.'_rgt']-$data[$this->type.'_lft']+1).' - '.$maxrgt.', '.$this->type.'_rgt = '.$this->type.'_rgt + '.($data[$this->type.'_rgt']-$data[$this->type.'_lft']+1).' - '.$maxrgt.' WHERE '.$this->type.'_lft >= '.$sql->getValue($this->type.'_lft').' AND '.$this->type.'_rgt <= '.$sql->getValue($this->type.'_rgt');
      $qry2 = 'UPDATE `'.$table['table_name'].'` SET '.$this->type.'_lft = '.$this->type.'_lft - '.($sql->getValue($this->type.'_rgt')-$sql->getValue($this->type.'_lft')+1).', '.$this->type.'_rgt = '.$this->type.'_rgt - '.($sql->getValue($this->type.'_rgt')-$sql->getValue($this->type.'_lft')+1).' WHERE '.$this->type.'_lft >= '.$data[$this->type.'_lft'].' AND '.$this->type.'_rgt <= '.$data[$this->type.'_rgt'].' AND '.$this->type.'_level > 0';
      $qry3 = 'UPDATE `'.$table['table_name'].'` SET '.$this->type.'_lft = '.$this->type.'_lft + '.$maxrgt.', '.$this->type.'_rgt = '.$this->type.'_rgt + '.$maxrgt.' WHERE '.$this->type.'_lft < 0';
      
      $sql->flush();
      $sql->setQuery($qry1);
      $sql->setQuery($qry2);
      $sql->setQuery($qry3);
    }
  }
  
  if($func == 'movedown')
  {
    $sql = rex_sql::factory();
    $sql->setQuery('SELECT ('.$this->type.'_rgt) as '.$this->type.'_rgt FROM `'.$table['table_name'].'`');
    $maxrgt = $sql->getValue($this->type.'_rgt');
    $sql->flush();
    $sql->setQuery('
      SELECT id,'.$this->type.'_lft,'.$this->type.'_rgt
      FROM `'.$table['table_name'].'`
      WHERE '.$this->type.'_lft = '.($data[$this->type.'_rgt'] + 1).'
      AND '.$this->type.'_level = '.$data[$this->type.'_level'].'
    ');
    
    if($sql->getRows())
    {
      $qry1 = 'UPDATE `'.$table['table_name'].'` SET '.$this->type.'_lft = '.$this->type.'_lft - '.($data[$this->type.'_rgt']-$data[$this->type.'_lft']+1).' - '.$maxrgt.', '.$this->type.'_rgt = '.$this->type.'_rgt - '.($data[$this->type.'_rgt']-$data[$this->type.'_lft']+1).' - '.$maxrgt.' WHERE '.$this->type.'_lft >= '.$sql->getValue($this->type.'_lft').' AND '.$this->type.'_rgt <= '.$sql->getValue($this->type.'_rgt');
      $qry2 = 'UPDATE `'.$table['table_name'].'` SET '.$this->type.'_lft = '.$this->type.'_lft + '.($sql->getValue($this->type.'_rgt')-$sql->getValue($this->type.'_lft')+1).', '.$this->type.'_rgt = '.$this->type.'_rgt + '.($sql->getValue($this->type.'_rgt')-$sql->getValue($this->type.'_lft')+1).' WHERE '.$this->type.'_lft >= '.$data[$this->type.'_lft'].' AND '.$this->type.'_rgt <= '.$data[$this->type.'_rgt'].' AND '.$this->type.'_level > 0';
      $qry3 = 'UPDATE `'.$table['table_name'].'` SET '.$this->type.'_lft = '.$this->type.'_lft + '.$maxrgt.', '.$this->type.'_rgt = '.$this->type.'_rgt + '.$maxrgt.' WHERE '.$this->type.'_lft < 0';
      
      $sql->flush();
      $sql->setQuery($qry1);
      $sql->setQuery($qry2);
      $sql->setQuery($qry3);
    }
  }
  
  header('Location: http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')).'/index.php?&page=editme&subpage='.$table['table_name']);
  exit;
}


// ********************************** Searchfields / Searchtext

foreach($rex_em_searchfields as $sf)
{
  $link_vars .= '&rex_em_searchfields[]='.urlencode($sf);
}
$link_vars .= '&rex_em_searchtext='.urlencode($rex_em_searchtext);
$link_vars .= '&rex_em_search='.urlencode($rex_em_search);




// ********************************** FILTER UND SETS PRFEN
$em_url_filter = "";
if(count($rex_em_filter)>0) {
  foreach($rex_em_filter as $k => $v) {
    if(in_array($k,$field_names)) { $em_url_filter .= '&amp;rex_em_filter['.$k.']='.urlencode($v); }
    else { unset($rex_em_filter[$k]); }
  }
};
$em_url_set = "";
if(count($rex_em_set)>0) {
  foreach($rex_em_set as $k => $v) {
    if(in_array($k,$field_names)) { $em_url_set .= '&amp;rex_em_set['.$k.']='.urlencode($v); }
    else { unset($rex_em_set[$k]); }
  }
};
$em_url = $em_url_filter.$em_url_set;

$em_rex_list = "";
$em_rex_list .= '&amp;list='.urlencode(rex_request('list','string'));
$em_rex_list .= '&amp;sort='.urlencode(rex_request('sort','string'));
$em_rex_list .= '&amp;sorttype='.urlencode(rex_request('sorttype','string'));
$em_rex_list .= '&amp;start='.urlencode(rex_request('start','string'));

// ---------- Opener Field .. dann wird rahmen weggeCSSt..
if($rex_em_opener_field > -1)
{
  echo '<link rel="stylesheet" type="text/css" href="../files/addons/xform/popup.css" media="screen, projection, print" />';
}



// ********************************************* Import
if($func == "import")
{
  include $REX["INCLUDE_PATH"].'/addons/xform/manager/pages/import.inc.php';
}



// ********************************************* LOESCHEN
if($func == "delete" && $data_id != "")
{
  $delete = TRUE;
  if(rex_register_extension_point('EM_DATA_DELETE', $delete, array("id"=>$data_id,"value"=>$data)))
  {
    $query = 'delete from '.$table["table_name"].' where id='.$data_id;
    if($table['hierarchic'])
      $query = 'delete from '.$table["table_name"].' where `'.$this->type.'_lft`>='.$data[$this->type.'_lft'].' AND  `'.$this->type.'_rgt`<='.$data[$this->type.'_rgt'];

    $delsql = new rex_sql;
    // $delsql->debugsql=1;
    $delsql->setQuery($query);
    echo rex_info($I18N->msg("datadeleted"));
    $func = "";

    rex_register_extension_point('EM_DATA_DELETED', "", array("id"=>$data_id,"value"=>$data));
  }
  
}


// ********************************************* FORMULAR
if($func == "add" || $func == "edit")
{

  $xform = new rex_xform;
  // $xform->setDebug(TRUE);

  foreach($this->getLinkVars() as $k => $v)
  {
    $xform->setHiddenField($k, $v);
  }

  $xform->setHiddenField("func",$func);

  // Speichern der Felder fr Popup und spezifische Zuweisungen
  $xform->setHiddenField("rex_em_opener_field",$rex_em_opener_field);
  $xform->setHiddenField("rex_em_opener_fieldname",$rex_em_opener_fieldname);
  if(count($rex_em_filter)>0) { foreach($rex_em_filter as $k => $v) { $xform->setHiddenField('rex_em_filter['.$k.']',$v); } };
  if(count($rex_em_set)>0) { foreach($rex_em_set as $k => $v) { $xform->setHiddenField('rex_em_set['.$k.']',$v); } };

  if(count($rex_em_searchfields)>0) { foreach($rex_em_searchfields as $k => $v) { $xform->setHiddenField('rex_em_searchfields['.$k.']',$v); } };
  $xform->setHiddenField("rex_em_search",$rex_em_search);
  $xform->setHiddenField("rex_em_searchtext",$rex_em_searchtext);

  // Speichern der Felder fr die rex_list
  $xform->setHiddenField('list',rex_request('list','string'));
  $xform->setHiddenField('sort',rex_request('sort','string'));
  $xform->setHiddenField('sorttype',rex_request('sorttype','string'));
  $xform->setHiddenField('start',rex_request('start','string'));
  $xform->setHiddenField($this->type.'_parent',rex_request($this->type.'_parent','int',1));

  foreach($fields as $field)
  {
    $type_name = $field["type_name"];
    $type_id = $field["type_id"];
    $values = array();
    for($i=1;$i<10;$i++){ $values[] = $field["f".$i]; }
    if($type_id == "value")
    {
      $xform->setValueField($field["type_name"],$values);
    }elseif($type_id == "validate")
    {
      $xform->setValidateField($field["type_name"],$values);
    }elseif($type_id == "action")
    {
      $xform->setActionField($field["type_name"],$values);
    }
  }
  
  /*if($table['hierarchic'] AND $func == 'add' AND $xform->objparams["send"])
  {
    $parent = rex_request($this->type.'_parent', 'int', 1);
    
    $sql = new rex_sql;
    $sql->setTable($table['table_name']);
    
    // select lft and rgt
    $sql->setWhere('id = '.$parent);
    
    if(!$sql->select('id,'.$this->type.'_rgt,'.$this->type.'_level'))
      return false; // error
    
    $id = $sql->getValue('id');
    $rgt = $sql->getValue($this->type.'_rgt');
    $level = $sql->getValue($this->type.'_level');
    
    // update rgt
    $sql->setQuery(
      sprintf('
        UPDATE `%s`
        SET '.$this->type.'_rgt = '.$this->type.'_rgt + 2
        WHERE '.$this->type.'_rgt >= %d',
        $table['table_name'],
        $rgt
      )
    );
    
    // update lft
    $sql->setQuery(
      sprintf('
        UPDATE `%s`
        SET '.$this->type.'_lft = '.$this->type.'_lft + 2
        WHERE '.$this->type.'_lft > %d',
        $table['table_name'],
        $rgt
      )
    );
    
    // new data
    $xform->setValueField($this->type.'_lft', $rgt);
    $xform->setValueField($this->type.'_rgt', $rgt + 1);
    $xform->setValueField($this->type.'_parent', $parent);
    $xform->setValueField($this->type.'_level', $level + 1);
  }*/

  // ***** START
  // Textblock gibt den formalarblock als text aus, um diesen in das xform modul einsetzen zu können.
  /*
  $text_block = '';
  foreach($fields as $field)
  { $values = array();
    for($i=1;$i<10;$i++){ $values[] = $field["f".$i]; } 
    if($field["type_id"] == "value")
    {
      $text_block .= "\n".'$xform->setValueField("'.$field["type_name"].'",array("'.implode('","',$values).'"));';
    }elseif($field["type_id"] == "validate")
    {
      $text_block .= "\n".'$xform->setValidateField("'.$field["type_name"].'",array("'.implode('","',$values).'"));';
    }elseif($field["type_id"] == "action")
    {
      $text_block .= "\n".'$xform->setActionField("'.$field["type_name"].'",array("'.implode('","',$values).'"));';
    }
    // $text_block .= "\n".$field["type_name"].'|'.implode("|",$values);
  }
  echo '<pre>'.$text_block.'</pre>';
  */
  // ***** ENDE

  $xform->setObjectparams("main_table",$table["table_name"]); // für db speicherungen und unique abfragen
  $xform->setObjectparams("submit_btn_label",$I18N->msg('submit'));
  
  $xform->setObjectparams("manager_type",$this->getType());
  
  if($func == "edit")
  {
    $xform->setHiddenField("data_id",$data_id);
    $xform->setActionField("db",array($table["table_name"],"id=$data_id"));
    $xform->setObjectparams("main_id",$data_id);
    $xform->setObjectparams("main_where","id=$data_id");
    $xform->setObjectparams("getdata",TRUE);

  }
  elseif($func == "add")
  {
    if($table['hierarchic'])
      $xform->setActionField("nestedset",array($table["table_name"]));
    
    $xform->setActionField("db",array($table["table_name"]));
  }

  $xform->setObjectparams("rex_em_set",$rex_em_set);

  $form = $xform->getForm();

  // Formular ausgeben wenn
  // - fehler
  // - edit und nur save
  // - add und nur save

  // formular nicht ausgeben wenn
  // - wenn edit und schliessen
  // - add und schliessen
  // - und nur wenn kein fehler

  if($xform->objparams["form_show"] || ($xform->objparams["form_showformafterupdate"] ))
  {

    if($xform->objparams["send"])
    {
      if($func == "edit")
      {
        if($form == "")
        {
          echo rex_info($I18N->msg("thankyouforupdate"));
          $xform = rex_register_extension_point('EM_DATA_UPDATED', $xform, array());
        }
      }elseif($func == "add")
      {
        if($form == "")
        {
          echo rex_info($I18N->msg("thankyouforentry"));
          $xform = rex_register_extension_point('EM_DATA_ADDED', $xform, array());
        }
      }
    }

    if($func == "edit")
    {
      echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("editdata").'</h3><div class="rex-area-content">';
    }else
    {
      echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("adddata").'</h3><div class="rex-area-content">';
    }
    echo $form;
    echo '</div></div>';
    
    echo rex_register_extension_point('EM_DATA_FORM', '', array("form" => $form, "func" => $func, "this" => $this));
    
    echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?'.$link_vars.'&rex_em_opener_field='.$rex_em_opener_field.'&rex_em_opener_fieldname='.htmlspecialchars($rex_em_opener_fieldname).$em_url.$em_rex_list.'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';
    $show_list = FALSE;
  }else
  {
    if($func == "edit")
    {
      echo rex_info($I18N->msg("thankyouforupdate"));
    }elseif($func == "add"){
      echo rex_info($I18N->msg("thankyouforentry"));
    }
  }

}







// ********************************************* LIST
if($show_list)
{
  echo '<table cellpadding="5" class="rex-table"><tr><td><a href="index.php?'.$link_vars.'&func=add&rex_em_opener_field='.$rex_em_opener_field.'&rex_em_opener_fieldname='.htmlspecialchars($rex_em_opener_fieldname).$em_url.$em_rex_list.'"><b>+ '.$I18N->msg("add").'</b></a></td>';
  
  
  if($table["export"] == 1 or $table["import"] == 1)
  {
    echo '<td style="text-align:right;">';
    if($table["export"] == 1)
      echo '&nbsp;&nbsp;&nbsp;<a href="index.php?'.$link_vars.'&func=export&rex_em_opener_field='.$rex_em_opener_field.'&rex_em_opener_fieldname='.htmlspecialchars($rex_em_opener_fieldname).$em_url.$em_rex_list.'"><b>o '.$I18N->msg('export').'</b></a>';
    if($table["import"] == 1)
      echo '&nbsp;&nbsp;&nbsp;<a href="index.php?'.$link_vars.'&func=import"><b>o '.$I18N->msg('import').'</b></a>';
      
    echo '</td>';
  }
  echo '</tr></table><br />';

  // ----- SUCHE
  if($table["search"]==1)
  {
    $list = rex_request("list","string","");
    $start = rex_request("start","string","");
    $sort = rex_request("sort","string","");
    $sorttype = rex_request("sorttype","string","");

    $addsql = "";

    $search_field_select = new rex_select();
    $search_field_select->setMultiple(1);
    $search_field_select->setSize(5);
    $search_field_select->setName("rex_em_searchfields[]");
    $search_field_select->setStyle("width:100%;");

    $field_names = array();
    foreach($fields as $field){ if($field["type_id"] == "value" && $field["search"] == 1) { $search_field_select->addOption($field["f2"].' ['.$field["f1"].']',$field["f1"]); } }
    foreach($rex_em_searchfields as $cs) { $search_field_select->setSelected($cs); }

    $suchform = '<table width=770 cellpadding=5 cellspacing=1 border=0 bgcolor=#ffffff class="rex-table">';
    $suchform .= '<form action="'.$_SERVER['PHP_SELF'].'" method="poost" >';
    
    foreach($this->getLinkVars() as $k => $v)
    {
      $suchform .= '<input type="hidden" name="'.$k.'" value="'.addslashes($v).'" />';
    }
    
    $suchform .= '<input type="hidden" name="rex_em_opener_field" value="'.$rex_em_opener_field.'" />';
    $suchform .= '<input type="hidden" name="rex_em_opener_fieldname" value="'.$rex_em_opener_fieldname.'" />';

    if(count($rex_em_filter)>0) { foreach($rex_em_filter as $k => $v) { $suchform .= '<input type="hidden" name="rex_em_filter['.$k.']" value="'.htmlspecialchars(stripslashes($v)).'" />'; } }
    if(count($rex_em_set)>0) { foreach($rex_em_set as $k => $v) { $suchform .= '<input type="hidden" name="rex_em_set['.$k.']" value="'.htmlspecialchars(stripslashes($v)).'" />'; } }
    if($rex_em_opener_field >-1) { $suchform .= '<input type="hidden" name="rex_em_opener_field" value="'.htmlspecialchars(stripslashes($rex_em_opener_field)).'" />'; };
    if($rex_em_opener_fieldname != "") { $suchform .= '<input type="hidden" name="rex_em_opener_fieldname" value="'.htmlspecialchars(stripslashes($rex_em_opener_fieldname)).'" />'; };
    if($rex_em_opener_info != "") { $suchform .= '<input type="hidden" name="rex_em_opener_info" value="'.htmlspecialchars(stripslashes($rex_em_opener_info)).'" />'; };

    if($list != "") { $suchform .= '<input type="hidden" name="list" value="'.htmlspecialchars(stripslashes($list)).'" />'; };
    if($start != "") { $suchform .= '<input type="hidden" name="start" value="'.htmlspecialchars(stripslashes($start)).'" />'; };
    if($sort != "") { $suchform .= '<input type="hidden" name="sort" value="'.htmlspecialchars(stripslashes($sort)).'" />'; };
    if($sorttype != "") { $suchform .= '<input type="hidden" name="sorttype" value="'.htmlspecialchars(stripslashes($sorttype)).'" />'; };

    $suchform .= '<input type="hidden" name="rex_em_search" value="1" />';
    $suchform .= '<tr>
      <th>'.$I18N->msg('searchtext').'</th>
      <th>'.$I18N->msg('searchfields').'</th>
      <th>&nbsp;</th>
      </tr>'; 
    $suchform .= '<tr>
      <td class="grey" valign="top"><input type="text" name="rex_em_searchtext" value="'.htmlspecialchars(stripslashes($rex_em_searchtext)).'" style="width:100%;" /></td>
      <td class="grey" valign="top">'.$search_field_select->get().'</td>
      <td class="grey" valign="top"><input type="submit" name="send" value="'.$I18N->msg('search').'"  class="inp100" /></td>
      </tr>';
    $suchform .= '</form>';
    $suchform .= '</table><br />';

    echo $suchform;


  }

  $where = false;
  $sqlwhere = '';

  // ---------- SQL AUFBAUEN
  $sql = "select * from ".$table["table_name"].' WHERE 1';
  
  if($table['hierarchic'])
    $sql .= ' AND `'.$this->type.'_level` > 0 ORDER BY `'.$this->type.'_lft`';
  
  if(count($rex_em_filter)>0)
  {
    $where = true;
    $sql_filter = '';
    foreach($rex_em_filter as $k => $v)
    {
      if($sql_filter != '')
      {
        $sql_filter .= ' AND ';
      }
      $sql_filter .= '`'.$k.'`="'.$v.'"';
    }
    $sqlwhere .= $sql_filter;
    // echo $sql;
  }

  if($rex_em_search == 1)
  {
    if(is_array($rex_em_searchfields) && count($rex_em_searchfields)>0 && $rex_em_searchtext != "")
    {
      if(!$where)
        
      $sqlwhere .= '(';
      foreach($rex_em_searchfields as $cs){
        $sqlwhere .= " `".$cs."` LIKE  '%".$rex_em_searchtext."%' OR ";
      }
      $sqlwhere = substr($sqlwhere, 0, strlen($sql)-3 );
      $sqlwhere .= ")";
      $where = true;
    }
  }
  
  if($where)
    $sql = str_replace('1', $sqlwhere, $sql);




  // ********************************************* Export
  // export is here because the query has been build here.
  if($func == "export")
  {
    include $REX["INCLUDE_PATH"].'/addons/xform/manager/pages/export.inc.php';
  }







  // ---------- LISTE AUSGEBEN
  if(!isset($table["list_amount"]) || $table["list_amount"]<1)
  $table["list_amount"] = 30;

  $list = rex_list::factory($sql,$table["list_amount"]);
  $list->setColumnFormat('id', 'Id');
  $list->setColumnLayout('id', array('<th>###VALUE###</th>', '<td style="padding-left:###'.$this->type.'_level###em">###VALUE###</td>'));

  $list->removeColumn($this->type.'_lft');
  $list->removeColumn($this->type.'_rgt');
  $list->removeColumn($this->type.'_parent');
  $list->removeColumn($this->type.'_level');

  foreach($this->getLinkVars() as $k => $v)
  {
    $list->addParam($k, $v);
  }
  $list->addParam("table_name", $table["table_name"]);


  if(count($rex_em_filter)>0) { foreach($rex_em_filter as $k => $v) { $list->addParam('rex_em_filter['.$k.']',$v); } }
  if(count($rex_em_set)>0) { foreach($rex_em_set as $k => $v) { $list->addParam('rex_em_set['.$k.']',$v); } }
  if($rex_em_opener_field >-1) { $list->addParam("rex_em_opener_field",$rex_em_opener_field); };
  if($rex_em_opener_fieldname != "") { $list->addParam("rex_em_opener_fieldname",$rex_em_opener_fieldname); };
  if($rex_em_opener_info != "") { $list->addParam("rex_em_opener_info",$rex_em_opener_info); };

  if($rex_em_search != "") { $list->addParam("rex_em_search",$rex_em_search); };
  if($rex_em_searchtext != "") { $list->addParam("rex_em_searchtext",$rex_em_searchtext); };
  if(count($rex_em_searchfields)>0) { foreach($rex_em_searchfields as $k => $v) { $list->addParam('rex_em_searchfields['.$k.']',$v); } }

  $list->setColumnParams("id", array("data_id"=>"###id###", "func"=>"edit" ));
  $list->setColumnSortable("id");
  
  foreach($fields as $field)
  {
    if($field["type_id"] == "value")
    {
      if($field["list_hidden"] == 1)
      {
        $list->removeColumn($field["f1"]);
      }else
      {
        $list->setColumnSortable($field["f1"]);
        $list->setColumnLayout($field["f1"], array('<th>###VALUE###</th>', '<td style="padding-left:###'.$this->type.'_level###em">###VALUE###</td>'));

      }
    }
  }

  $list->addColumn($I18N->msg('edit'),$I18N->msg('edit'));
  $list->setColumnParams($I18N->msg('edit'), array('data_id'=>'###id###','func'=>'edit','start'=>rex_request('start','string')));

  $list->addColumn($I18N->msg('delete'),'- '.$I18N->msg('delete'));
  $list->setColumnParams($I18N->msg('delete'), array('data_id'=>'###id###','func'=>'delete'));
  $list->setColumnLayout($I18N->msg('delete'), array('<th>###VALUE###</th>', '<td onclick="return confirm(\'Wirklich löschen?\');">###VALUE###</td>'));
  if($table['hierarchic'])
  {
    $list->setColumnLayout($I18N->msg('delete'), array('<th>###VALUE###</th>', '<td onclick="return confirm(\'Sollen dieser Datensatz und alle untergeordneten Datensätze wirklich gelöscht werden?\');">###VALUE###</td>'));
    
    #$list->setColumnLayout('name', array('<th>###VALUE###</th>', '<td class="level-###'.$this->type.'level###" style="padding-left:###'.$this->type.'level###em">###VALUE###</td>'));
    
    $list->addColumn('&#9650;','&#9650;');
    $list->setColumnParams('&#9650;', array('data_id'=>'###id###','func'=>'moveup'));
    
    $list->addColumn('&#9660;','&#9660;');
    $list->setColumnParams('&#9660;', array('data_id'=>'###id###','func'=>'movedown'));
    
    $list->addColumn($I18N->msg('add'),'+ '.$I18N->msg('add'));
    $list->setColumnParams($I18N->msg('add'), array('data_id'=>'###id###','func'=>'add',$this->type.'_parent'=>'###id###'));
  }
// if($rex_em_opener_field){ $list->addColumn('&uuml;bernehmen','<a href="javascript:em_setData('.$rex_em_opener_field.',###id###,\'###'.$rex_em_opener_fieldname.'###\')">&uuml;bernehmen</a>',-1,"asdasd"); }

  $list = rex_register_extension_point('EM_DATA_LIST', $list, array());

  echo $list->get();

}
