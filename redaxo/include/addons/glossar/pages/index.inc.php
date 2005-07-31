<?php
$msg = '';

include $REX[INCLUDE_PATH]."/layout_redaxo/top.php";

// titel ausgeben
title($I18N_GLOSSAR->msg('glossar'), '');
$short_id = empty ($_REQUEST['short_id']) ? '' : $_REQUEST['short_id'];

// Aktionen ausführen 
if (!empty ($_POST['function']))
{
    $msg = doAction($short_id, $_POST['form_short'], $_POST['form_desc'], $_POST['form_lang'], $_POST['form_case'], htmlentities($_POST['function']));
}

// Formular anzeigen
?>

<form action="index.php" method="post">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="short_id" value="<?php echo $short_id ?>">
    <table cellpadding="5" cellspacing="1" width="770px">
        <colgroup>
            <col width="25px"/>
            <col width="50px"/>
            <col width="80px"/>
            <col width="*"/>
            <col width="130px"/>
            <col width="210px"/>
        </colgroup>
        <tr>
            <th><a href="index.php?page=<?php echo $page ?>&function=add">+</a></th>
            <th align="left"><?php echo $I18N_GLOSSAR->msg('shortcut'); ?></th>
            <th align="left"><?php echo $I18N_GLOSSAR->msg('language'); ?></th>
            <th align="left"><?php echo $I18N_GLOSSAR->msg('description'); ?></th>
            <th align="left" colspan="2"><?php echo $I18N_GLOSSAR->msg('casesensitivity'); ?></th>
        </tr>
<?

// eventuelle Fehlermeldung ausgeben
if ($msg != ''):
?>
        <tr>
            <td class="warning"><img src="pics/warning.gif" width="16" height="16"></td>
            <td class="warning" colspan="5"><?php echo $msg ?></td>
        </tr>
<?php
endif;

// Einträge auslesen
$sql = new sql;
$sql->setQuery("select * from rex__glossar order by shortcut");

// 1. Zeile Add-Formular einblenden, wenn ADD aufgerufen
if ($function == "add")
{
    printFormFields();
}

for ($i = 0; $i < $sql->getRows(); $i ++)
{
    $id = $sql->getValue("short_id");
    $shortcut = htmlentities($sql->getValue("shortcut"));
    $language = $sql->getValue("language");
    $description = htmlentities($sql->getValue("description"));
    $casesense = $sql->getValue("casesense");

    if ($short_id != $id):
?>
    <tr>
        <td class="dgrey"></td>
        <td class="dgrey"><a href="index.php?page=<?php echo $page ?>&short_id=<?php echo $id ?>"><?php echo $shortcut ?></a></td>
        <td class="dgrey"><?php echo getLanguage( $language) ?></td>
        <td class="dgrey"><?php echo $description ?></td>
        <td class="dgrey" colspan="2"><?php echo getCase( $casesense) ?></td>
    </tr>
<?php
    // Den aktuell ausgewählten Eintrag im Formular anzeigen
    else:
        printFormFields($shortcut, $description, $language, $casesense);
    endif;
    $sql->counter++;
}
?>
    </table>
</form>
<?php


include $REX[INCLUDE_PATH]."/layout_redaxo/bottom.php";

// Funktion zur anzeige des Eingabe-Formulars
function printFormFields($shortcut = '', $description = '', $language = '', $casesense = '')
{
    global $I18N, $I18N_GLOSSAR;

    $oLangSelect = new select();
    if ($language !== '')
    {
        $oLangSelect->set_selected($language);
    }
    $oLangSelect->set_name('form_lang');
    $oLangSelect->set_style('width: 100%');
    $oLangSelect->set_size(1);
    $oLangSelect->add_option($I18N_GLOSSAR->msg('lang_de'), $I18N_GLOSSAR->msg('lang_de_id'));
    $oLangSelect->add_option($I18N_GLOSSAR->msg('lang_en'), $I18N_GLOSSAR->msg('lang_en_id'));
    $oLangSelect->add_option($I18N_GLOSSAR->msg('lang_fr'), $I18N_GLOSSAR->msg('lang_fr_id'));

    $oCaseSelect = new select();
    if ($casesense !== '')
    {
        $oCaseSelect->set_selected($casesense);
    }
    $oCaseSelect->set_size(1);
    $oCaseSelect->set_name('form_case');
    $oCaseSelect->set_style('width: 100%');
    $oCaseSelect->add_option($I18N_GLOSSAR->msg('casesense'), 1);
    $oCaseSelect->add_option($I18N_GLOSSAR->msg('caseless'), 0);

    $mode = $shortcut !== '' || $description !== '' || $language !== '' || $casesense !== '' ? 'update' : 'add';
?>
    <tr>
        <td class="dgrey"></td>
        <td class="dgrey"><input style="width:100%" type="text" size="20" maxlength="255" name="form_short" value="<?php echo htmlentities($shortcut) ?>"></td>
        <td class="dgrey"><?php echo $oLangSelect->out() ?></td>
        <td class="dgrey"><input style="width:100%" type="text" size="20" maxlength="255" name="form_desc" value="<?php echo htmlentities($description) ?>"></td>
        <td class="dgrey"><?php echo $oCaseSelect->out() ?></td>
        <td class="dgrey">
            <input type="submit" name="function" value="<?php echo $I18N->msg( $mode) ?>">
            <input type="submit" name="function" value="<?php echo $I18N->msg( 'delete') ?>">
        </td>
    </tr>
<?php


}

// Ausführen von aktionen
function doAction($short_id, $shortcut, $description, $language, $casesense, $mode = 'update')
{
    global $I18N, $I18N_GLOSSAR;

    // Validierung der Modi
    if ($mode != trim($I18N->msg('add')) && $mode != trim($I18N->msg('update')) && $mode != trim($I18N->msg('delete')))
    {
        return $I18N_GLOSSAR->msg('invalid_action').'"$mode"';
    }

    $sql = new sql();
    $sql->setTable('rex__glossar');

    // Delete Query zusammenbauen
    if ($mode == trim($I18N->msg('delete')))
    {
        $sql->where("short_id='$short_id'");
        $sql->delete();
        return $I18N_GLOSSAR->msg('deleted');
    }

    // Validierung der eingaben (Alle Felder müssen gefüllt sein)
    if ($shortcut === '' || $description === '' || $language === '' || $casesense === '')
    {
        return $I18N_GLOSSAR->msg('invalid_input');
    }

    $sql->setValue('shortcut', $shortcut);
    $sql->setValue('description', $description);
    $sql->setValue('language', $language);
    $sql->setValue('casesense', $casesense);

    // Update Query zusammenbauen
    if ($mode == trim($I18N->msg('update')))
    {
        $sql->where("short_id='$short_id'");
        $sql->update();
        return $I18N_GLOSSAR->msg('updated');
    }
    else
    // Create Query zusammenbauen
    {
        $sql->insert();
        return $I18N_GLOSSAR->msg('inserted');
    }
}

// Gibt anhand der CaseId den entsprechenden Schlüssel aus den Sprachfiles für Groß/Kleinschreibung zurück
function getCase($caseId)
{
    global $I18N_GLOSSAR;

    if ($caseId == 0)
    {
        $casesense = $I18N_GLOSSAR->msg('caseless');
    }
    else
    {
        $casesense = $I18N_GLOSSAR->msg('casesense');
    }

    return $casesense;
}

// Gibt anhand der SprachId den entsprechenden Schlüssel aus den Sprachfiles zurück
function getLanguage($languageId)
{
    global $I18N_GLOSSAR;

    if ($languageId == 0)
    {
        $language = $I18N_GLOSSAR->msg('lang_de');
    }
    elseif ($languageId == 1)
    {
        $language = $I18N_GLOSSAR->msg('lang_en');
    }
    else
    {
        $language = $I18N_GLOSSAR->msg('lang_fr');
    }

    return $language;
}
?>