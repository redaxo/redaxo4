<?php

rex_dir::copy(
    __DIR__ . '/backup',
    rex_path::addonData('import_export', 'backups')
);

$REX['ADDON']['update']['import_export'] = 1;
