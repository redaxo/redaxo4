<?php

/**
 * TinyMCE Addon
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

unset($_SESSION['tinymce']);

$table = $REX['TABLE_PREFIX'] . 'tinymce_profiles';
$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');
$css = rex_request('css', 'string');

// Update
if ($func == 'update')
{
  $sqlu = new rex_sql();
  $sqlu->debugsql=0;
  
  $query = 'SELECT configuration FROM '.$table.' WHERE id = 1 AND ptype = 1 ';
  $sql = new rex_sql;
  $sql->debugsql=0;
  $sql->setQuery($query);
  if ($sql->getRows() > 0)
  {
    $sqlu->setTable($table);
    $sqlu->setValue('id', '1');
    $sqlu->setValue('configuration', $css);
    $sqlu->setWhere('id = 1 AND ptype = 1');
    if($sqlu->update())
    {
      echo rex_info($I18N->msg('tinymce_css_saved'));
    }
    else
    {
      echo rex_warning($I18N->msg('tinymce_css_not_saved'));
    }
  }
  else
  {
    $sqlu->setTable($table);
    $sqlu->setValue('id', '1');
    $sqlu->setValue('name', 'css');
    $sqlu->setValue('ptype', '1');
    $sqlu->setValue('description', 'CSS fuer den TinyMCE');
    $sqlu->setValue('configuration', $css);
    if($sqlu->insert())
    {
      echo rex_info($I18N->msg('tinymce_css_saved'));
    }
    else
    {
      echo rex_warning($I18N->msg('tinymce_css_not_saved'));
    }
  }
}

// CSS aus Tabelle bereitstellen
  $query = 'SELECT configuration FROM '.$table.' WHERE id = 1 AND ptype = 1 ';
  $sql = new rex_sql;
  $sql->debugsql=0;
  $sql->setQuery($query);
  if ($sql->getRows() > 0)
  {
    $css = $sql->getValue('configuration');
  }
?>

<div class="rex-addon-output">
<div class="rex-form">
  <h2 class="rex-hl2"><?php echo $I18N->msg('tinymce_css_title'); ?></h2>

  <form action="index.php" method="post">
  <fieldset class="rex-form-col-1">

  <div class="rex-form-wrapper">
    <input type="hidden" name="page" value="<?php echo $page; ?>" />
    <input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
    <input type="hidden" name="func" value="update" />

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-text">
        <label for="css"><?php echo $I18N->msg('tinymce_css_text'); ?></label>
        <textarea class="rex-form-text" id="css" name="css" cols="50" rows="12" style="width:550px;height:300px;font-family:'Courier New';"><?php echo $css; ?></textarea>
      </p>
    </div>

    <div class="rex-form-row rex-form-element-v1">
      <p class="rex-form-submit">
        <input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('update'); ?>" />
      </p>
    </div>

  </div>

  </fieldset>
  </form>

</div>
</div>

<div class="rex-addon-output">

  <h2 class="rex-hl2"><?php echo $I18N->msg('tinymce_css_infotitle'); ?></h2>

  <div class="rex-addon-content">
    <p class="rex-tx1">
    <?php echo $I18N->msg('tinymce_css_info'); ?>
    </p>
  </div>

</div>
