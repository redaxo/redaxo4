<?php

rex_dir::copy(
    __DIR__ . '/backup',
    rex_path::addonData('import_export', 'backups')
);

$REX['ADDON']['install']['import_export'] = 1;
// ERRMSG IN CASE: $REX[ADDON][installmsg]["import_export"] = "Leider konnte nichts installiert werden da.";
