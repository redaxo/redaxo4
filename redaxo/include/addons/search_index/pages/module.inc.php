<?php

/**
 * 
 * @package redaxo3
 * @version $Id$
 */

print '
    <table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
    <tr><th>Anleitung für Such<u>ausgabe</u> Modul</th></tr>
    <tr>
      <td colspan=2>
        <div style="border: solid 1px red; padding: 20px; margin: 20px; font-size:11px">';

$template = dirname(__FILE__).'/../templates/template.searchmodule.inc.php';
highlight_file($template);

print '</div>';
print '&nbsp;&nbsp; addon by <a href="http://www.vscope.at">vscope new media</a> version 0.1 beta - updated by jan@kristinus.de, markus@public4u.de';
print '</td></tr></table>';

?>