<?php

//------------------------------> Shortcutformular

if ($func == 'add' || $func == 'edit')
{
    $form = new rexform;
    
    $form->setWidth(770);
    $form->setLabelWidth(160);
    $form->setTablename( TBL_EXCEL_EXPORT_TBL);
    $form->setSubmitValue( "Übernehmen");
    
    if($func == "add"){
        $form->setFormtype("add", 'lang_id = language');
        $form->setFormheader("<input type=hidden name=page value=".$page."><input type=hidden name=func value=".$func." />");
        $form->setShowFormAlways(false);
    }else{          
        $form->setFormtype("edit", 'lang_id = language and short_id = '. $sid, $I18N_GLOSSAR->msg( 'invalid_shortcut'));
        $form->setFormheader("<input type=hidden name=page value=".$page."><input type=hidden name=func value=".$func." /><input type=hidden name=sid value=".$sid.">");
        $form->setShowFormAlways(true);             
    }
    
    $form->setValue("subline", "Tabelleninformationen" ,"left",0);

    $form->setValue("singleselect", "Name", "", 1,
            "SHOW TABLES", "Tables_in_redaxo2_7", "Tables_in_redaxo2_7");
            
    $form->setValue("text","Bezeichnung" ,"tbl_label",1);
    $form->setValue("text","Primärschlüssel" ,"tbl_pk",1);
    
    
    echo $form->showForm();
    
    echo "<br><br><a href=index.php?page=".$page."&subpage=". $subpage ."><b>&laquo; Zurück zur Übersicht</b></a><br>";

}

//------------------------------> Shortcut löschen

if ($func == 'delete')
{
    $query = 'DELETE FROM '.TBL_GLOSSAR.' WHERE short_id='.$sid;
    $delsql = new sql;
    //    $delsql->debugsql=1;
    $delsql->setQuery($query);
    $func = '';
}

//------------------------------> Shortcutliste

if ($func == '')
{

    $sql = 'SELECT * FROM '.TBL_EXCEL_EXPORT_TBL.' ORDER BY tbl_name';
//        var_dump( $sql);

    $list = new rexlist;
    $list->setQuery($sql);

    // Spalten setzen
    $list->setValue("Bezeichnung", "tbl_label");
    $list->setLink("index.php?page=".$page."&subpage=". $subpage ."&func=edit&tid=", "tbl_label");
//    $list->setValue($I18N_GLOSSAR->msg('language'), "langname");
//    $list->setValue($I18N_GLOSSAR->msg('description'), "description");
//    $list->setValue($I18N_GLOSSAR->msg('casesensitivity'), "casesensitivity");

    $list->addColumn($I18N->msg('delete'), "index.php?page=".$page."&func=delete&sid=", "short_id", " onclick=\"return confirm('".$I18N_GLOSSAR->msg("confirm_delete")."');\"");

    echo $list->showall($next);

    echo "<br><br><a href=index.php?page=". $page ."&subpage=". $subpage ."&func=add><b>Tabelle hinzufügen</b></a><br>";

}
?>