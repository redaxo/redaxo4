<?php

/**
 * MetaForm Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$error = '';

$result = rex_install_dump($REX['INCLUDE_PATH'] . '/addons/metainfo/_install.sql');
if ($result !== true) {
    $error = $result;
} else {
    require_once $REX['INCLUDE_PATH'] . '/addons/metainfo/classes/class.rex_table_manager.inc.php';

    $tablePrefixes = array('article' => array('art_', 'cat_'), 'file' => array('med_'));
    $columns = array('article' => array(), 'file' => array());
    foreach ($tablePrefixes as $table => $prefixes) {
        foreach (rex_sql::showColumns($REX['TABLE_PREFIX'] . $table) as $column) {
            $column = $column['name'];
            $prefix = substr($column, 0, 4);
            if (in_array(substr($column, 0, 4), $prefixes)) {
                $columns[$table][$column] = true;
            }
        }
    }

    $sql = rex_sql::factory();
    $sql->setQuery('SELECT p.name, p.default, t.dbtype, t.dblength FROM ' . $REX['TABLE_PREFIX'] . '62_params p, ' . $REX['TABLE_PREFIX'] . '62_type t WHERE p.type = t.id');
    $rows = $sql->getRows();
    $managers = array(
        'article' => new rex_a62_tableManager($REX['TABLE_PREFIX'] . 'article'),
        'file' => new rex_a62_tableManager($REX['TABLE_PREFIX'] . 'file')
    );
    for ($i = 0; $i < $sql->getRows(); $i++) {
        $column = $sql->getValue('name');
        if (substr($column, 0, 4) == 'med_') {
            $table = 'file';
        } else {
            $table = 'article';
        }

        if (isset($columns[$table][$column])) {
            $managers[$table]->editColumn($column, $column, $sql->getValue('dbtype'), $sql->getValue('dblength'), $sql->getValue('default'));
        } else {
            $managers[$table]->addColumn($column, $sql->getValue('dbtype'), $sql->getValue('dblength'), $sql->getValue('default'));
        }

        unset($columns[$table][$column]);
        $sql->next();
    }
}

if ($error != '') {
    $REX['ADDON']['installmsg']['metainfo'] = $error;
} else {
    $REX['ADDON']['install']['metainfo'] = true;
}
