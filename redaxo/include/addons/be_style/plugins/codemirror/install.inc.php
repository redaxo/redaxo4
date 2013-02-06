<?php

/**
 * be_style plugin: codemirror
 * 
 * Copyright (C) 2013 by Marijn Haverbeke <marijnh@gmail.com>
 * https://github.com/marijnh/CodeMirror
 *
 */

$error = '';

if ($error != '')
  $REX['ADDON']['installmsg']['codemirror'] = $error;
else
  $REX['ADDON']['install']['codemirror'] = true;