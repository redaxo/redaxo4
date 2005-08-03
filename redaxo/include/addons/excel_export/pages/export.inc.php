<?php

/*
    excel_export Addon by <a href="mailto:staab@public-4u.de">Markus Staab</a>
    <a href="http://www.public-4u.de">www.public-4u.de</a>
    20.06.2005
    Version RC1
*/


// Posts auswerten
if ( !empty( $_REQUEST['ExportTable']) && is_array( $_REQUEST['ExportTable']))
{
    $argTables = array_keys( $_REQUEST['ExportTable']);
    $table = $argTables[0];
    
    $argModes = array_keys( $_REQUEST['ExportTable'][$table]);
    $mode = $argModes[0];
    
    doExport( $table, $mode);
}

?>


<style type="text/css">
  input.button {
     width: 115px;
  }
</style>
  
<form action="index.php" method="post">
  <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
  <table class="rex" border="0" cellpadding="5" cellspacing="1" style="width: 770px">
    <colgroup>
       <col width="*"/>
       <col width="50px"/>
       <col width="50px"/>
       <col width="120px"/>
       <col width="250px"/>
    </colgroup>
    <tr>
      <th>Tabelle</th>
      <th>Anzahl</th>
      <th>Neue</th>
      <th>Letzter Export</th>
      <th>Aktion</th>
    </tr>  
    <?php 
        foreach ( $XLS_TABLES as $table => $label ):
           $oTable = new ExportTable( $table); 
    ?>
    <tr>
      <td><?php echo $label ?></td>
      <td>
        <?php
           echo $numAll = $oTable->numAll();
        ?>
      </td>
      <td>
        <?php
           echo $numNew = $oTable->numNew();
        ?>
      </td>
      <td>
        <?php
           echo $oTable->lastExport();
        ?>
      </td>
      <td>
      <?php 
        foreach ( $XLS_MODES as $mode => $mode_label ):
           // Optisch ausgrauen der Buttons für Mozilla, da dieser nur die Funktion deaktiviert
           $disabled = $mode == 'all' && $numAll == 0 
                       || $mode == 'new' && $numNew == 0
                       ? ' disabled="disabled" style="color: gray"' : '';
      ?>
        <input type="submit" class="button" name="ExportTable[<?php echo $table ?>][<?php echo $mode ?>]" value="<?php echo $mode_label ?>"<?php echo $disabled ?>/>
      <?php
        endforeach;
      ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</form>