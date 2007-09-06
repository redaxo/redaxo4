<?php
/**
 *
 * @package redaxo3
 * @version $Id$
 */

$OUT = TRUE;

$function = rex_request('function', 'string');

// ---------------------------- ACTIONSFUNKTIONEN FÜR MODULE
if (!empty($add_action))
{
  $action = new rex_sql();
  $action->setTable($REX['TABLE_PREFIX'].'module_action');
  $action->setValue('module_id', $modul_id);
  $action->setValue('action_id', $action_id);

  if($action->insert())
  {
    $message = $I18N->msg('action_taken');
    $goon = 'ja';
  }
  else
  {
    $message = $action->getErrro();
  }
}
elseif (isset($function_action) and $function_action == 'delete')
{
  $action = new rex_sql();
  $action->setTable($REX['TABLE_PREFIX'].'module_action');
  $action->setWhere('id='. $iaction_id . ' LIMIT 1');

  $message = $action->delete($I18N->msg('action_deleted_from_modul'));
}



// ---------------------------- FUNKTIONEN FÜR MODULE

if ($function == 'delete')
{
  $del = new rex_sql;
  $del->setQuery("SELECT DISTINCT ".$REX['TABLE_PREFIX']."article_slice.article_id, ".$REX['TABLE_PREFIX']."module.name FROM ".$REX['TABLE_PREFIX']."article_slice
      LEFT JOIN ".$REX['TABLE_PREFIX']."module ON ".$REX['TABLE_PREFIX']."article_slice.modultyp_id=".$REX['TABLE_PREFIX']."module.id
      WHERE ".$REX['TABLE_PREFIX']."article_slice.modultyp_id='$modul_id'");

  if ($del->getRows() >0)
  {
    $module = '';
    $modulname = htmlspecialchars($del->getValue($REX['TABLE_PREFIX']."module.name"));
    for ($i=0; $i<$del->getRows(); $i++)
    {
     $module .= '<li><a href="index.php?page=content&amp;article_id='.$del->getValue($REX['TABLE_PREFIX']."article_slice.article_id").'">'.$del->getValue($REX['TABLE_PREFIX']."article_slice.article_id").'</a></li>';
     $del->next();
    }

    if($module != '')
    {
      $module = '<ul>'. $module .'</ul>';
    }

    $message = $I18N->msg("module_cannot_be_deleted",$modulname).'<br /> '.$module;
  } else
  {
    $del->setQuery("DELETE FROM ".$REX['TABLE_PREFIX']."module WHERE id='$modul_id'");
    $del->setQuery("DELETE FROM ".$REX['TABLE_PREFIX']."module_action WHERE module_id='$modul_id'");

    $message = $I18N->msg("module_deleted");
  }
}

if ($function == 'add' or $function == 'edit')
{

  if (isset($save) and $save == 'ja')
  {
    $modultyp = new rex_sql;

    if ($function == 'add')
    {
      // $modultyp->setQuery("INSERT INTO ".$REX['TABLE_PREFIX']."modultyp (category_id, name, eingabe, ausgabe) VALUES ('$category_id', '$mname', '$eingabe', '$ausgabe')");

      $IMOD = new rex_sql;
      $IMOD->setTable($REX['TABLE_PREFIX'].'module');
      $IMOD->setValue('name',$mname);
      $IMOD->setValue('eingabe',$eingabe);
      $IMOD->setValue('ausgabe',$ausgabe);
      $IMOD->setValue('createdate',time());
      $IMOD->setValue('createuser',$REX_USER->getValue('login'));

      $message = $IMOD->insert($I18N->msg('module_added'));

    } else {
      $modultyp->setQuery('select * from '.$REX['TABLE_PREFIX'].'module where id='.$modul_id);
      if ($modultyp->getRows()==1)
      {
        $old_ausgabe = $modultyp->getValue('ausgabe');

        // $modultyp->setQuery("UPDATE ".$REX['TABLE_PREFIX']."modultyp SET name='$mname', eingabe='$eingabe', ausgabe='$ausgabe' WHERE id='$modul_id'");

        $UMOD = new rex_sql;
        $UMOD->setTable($REX['TABLE_PREFIX'].'module');
        $UMOD->setWhere('id='. $modul_id);
        $UMOD->setValue('name',$mname);
        $UMOD->setValue('eingabe',$eingabe);
        $UMOD->setValue('ausgabe',$ausgabe);
        $UMOD->setValue('updatedate',time());
        $UMOD->setValue('updateuser',$REX_USER->getValue('login'));

        $message = $UMOD->update($I18N->msg('module_updated').' | '.$I18N->msg('articel_updated'));
        $new_ausgabe = stripslashes($ausgabe);

		if ($old_ausgabe != $new_ausgabe)
		{
          // article updaten - nur wenn ausgabe sich veraendert hat
          $gc = new rex_sql;
          $gc->setQuery("SELECT DISTINCT(".$REX['TABLE_PREFIX']."article.id) FROM ".$REX['TABLE_PREFIX']."article
              LEFT JOIN ".$REX['TABLE_PREFIX']."article_slice ON ".$REX['TABLE_PREFIX']."article.id=".$REX['TABLE_PREFIX']."article_slice.article_id
              WHERE ".$REX['TABLE_PREFIX']."article_slice.modultyp_id='$modul_id'");
          for ($i=0; $i<$gc->getRows(); $i++)
          {
          	rex_deleteCacheArticle($gc->getValue($REX['TABLE_PREFIX']."article.id"));
            // rex_generateArticle($gc->getValue($REX['TABLE_PREFIX']."article.id"));
            $gc->next();
          }
        }
      }
    }

    if (isset($goon) and $goon != '')
    {
      $save = 'nein';
    } else
    {
      $function = '';
    }
  }



  if (!isset($save) or $save != 'ja')
  {
    if (!isset($modul_id)) $modul_id = '';
    if (!isset($mname)) $mname = '';
    if (!isset($eingabe)) $eingabe = '';
    if (!isset($ausgabe)) $ausgabe = '';

    if ($function == 'edit')
    {
      $legend = $I18N->msg('module_edit').' [ID='.$modul_id.']';

      $hole = new rex_sql;
      $hole->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'module WHERE id='.$modul_id);
      $category_id  = $hole->getValue('category_id');
      $mname    = $hole->getValue('name');
      $ausgabe  = $hole->getValue('ausgabe');
      $eingabe  = $hole->getValue('eingabe');
    }
    else
    {
      $legend = $I18N->msg('create_module');
    }

    $btn_update = '';
    if ($function != 'add') $btn_update = '<input type="submit" class="rex-sbmt" name="goon" value="'.$I18N->msg("save_module_and_continue").'"'. rex_accesskey($I18N->msg('save_module_and_continue'), $REX['ACKEY']['APPLY']) .' />';

    if (isset($message) and $message != '')
    {
      echo rex_warning($message);
    }

    echo '
		<div class="rex-mdl-editmode">
      <form action="index.php" method="post">
        <fieldset>
          <legend class="rex-lgnd" id="module">'. $legend .'</legend>
      	  <div class="rex-fldst-wrppr">
    			  <input type="hidden" name="page" value="module" />
    			  <input type="hidden" name="function" value="'.$function.'" />
    			  <input type="hidden" name="save" value="ja" />
    			  <input type="hidden" name="category_id" value="0" />
    			  <input type="hidden" name="modul_id" value="'.$modul_id.'" />
    			  <p>
      				<label for="mname">'.$I18N->msg("module_name").'</label>
      				<input type="text" size="10" id="mname" name="mname" value="'.htmlspecialchars($mname).'" />
    			  </p>
    			  <p>
      				<label for="eingabe">'.$I18N->msg("input").'</label>
      				<textarea class="rex-txtr-cd" cols="50" rows="6" name="eingabe" id="eingabe">'.htmlspecialchars($eingabe).'</textarea>
    			  </p>
    			  <p>
      				<label for="ausgabe">'.$I18N->msg("output").'</label>
      				<textarea class="rex-txtr-cd" cols="50" rows="6" name="ausgabe" id="ausgabe">'.htmlspecialchars($ausgabe).'</textarea>
    			  </p>
    			  <p>
      				<input class="rex-sbmt" type="submit" value="'.$I18N->msg("save_module_and_quit").'"'. rex_accesskey($I18N->msg('save_module_and_quit'), $REX['ACKEY']['SAVE']) .' />
      				'. $btn_update .'
    			  </p>
    		  </div>
        </fieldset>
    ';

    if ($function == 'edit')
    {
      // Im Edit Mode Aktionen bearbeiten

      $gaa = new rex_sql;
      $gaa->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."action ORDER BY name");

      if ($gaa->getRows()>0)
      {
        $gma = new rex_sql;
        $gma->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."module_action, ".$REX['TABLE_PREFIX']."action WHERE ".$REX['TABLE_PREFIX']."module_action.action_id=".$REX['TABLE_PREFIX']."action.id and ".$REX['TABLE_PREFIX']."module_action.module_id='$modul_id'");

        $actions = '';
        for ($i=0; $i<$gma->getRows(); $i++)
        {
          $iaction_id = $gma->getValue($REX['TABLE_PREFIX'].'module_action.id');
          $action_id = $gma->getValue($REX['TABLE_PREFIX'].'module_action.action_id');
          $action_edit_url = 'index.php?page=module&amp;subpage=actions&amp;action_id='.$action_id.'&amp;function=edit';
          $action_name = htmlspecialchars($gma->getValue('name'));

          $actions .= '<tr>
          	<td class="rex-icon"><a href="'. $action_edit_url .'"><img src="media/modul.gif" width="16" height="16" alt="' . $action_name . '" title="' . $action_name . '" /></a></td>
            <td class="rex-icon">' . $gma->getValue("id") . '</td>
          	<td><a href="'. $action_edit_url .'">'. $action_name .'</a></td>
          	<td><a href="index.php?page=module&amp;modul_id='.$modul_id.'&amp;function_action=delete&amp;function=edit&amp;iaction_id='.$iaction_id.'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg('action_delete').'</a></td>
          </tr>';

          $gma->next();
        }

        if($actions !='')
        {
          $actions = '
  					<table class="rex-table" summary="'.$I18N->msg('actions_added_summary').'">
  						<caption>'.$I18N->msg('actions_added_caption').'</caption>
    					<colgroup>
      				<col width="40" />
      				<col width="40" />
      				<col width="*" />
      				<col width="153" />
    					</colgroup>
    					<thead>
      					<tr>
        					<th class="rex-icon">&nbsp;</th>
        					<th class="rex-icon">ID</th>
        					<th>' . $I18N->msg('action_name') . '</th>
        					<th>' . $I18N->msg('action_functions') . '</th>
      					</tr>
    					</thead>
    				<tbody>
              '. $actions .'
            </tbody>
            </table>
          ';
        }

        $gaa_sel = new rex_select();
        $gaa_sel->setName('action_id');
        $gaa_sel->setId('action_id');
        $gaa_sel->setSize(1);
        $gaa_sel->setStyle('class="inp100"');

        for ($i=0; $i<$gaa->getRows(); $i++)
        {
          $gaa_sel->addOption(htmlspecialchars($gaa->getValue('name')),$gaa->getValue('id'));
          $gaa->next();
        }

        echo
        $actions .'
        <fieldset>
          <legend class="rex-lgnd" id="action">'.$I18N->msg('action_add').'</legend>
		      <div class="rex-fldst-wrppr">
					  <p>
							<label for="action_id">'.$I18N->msg('action').'</label>
							'.$gaa_sel->get().'
					  </p>
					  <p>
							<input class="rex-sbmt" type="submit" value="'.$I18N->msg('action_add').'" name="add_action" />
					  </p>
				  </div>
        </fieldset>';
      }
    }

    echo '
    </form></div>
    ';

    $OUT = false;
  }
}

if ($OUT)
{
  if (isset($message) and $message != '')
  {
    echo rex_warning($message);
  }

  // ausgabe modulliste !
  echo '
  <table class="rex-table" summary="'.$I18N->msg('module_summary').'">
  	<caption class="rex-hide">'.$I18N->msg('module_caption').'</caption>
    <colgroup>
      <col width="40" />
      <col width="40" />
      <col width="*" />
      <col width="153" />
    </colgroup>
    <thead>
      <tr>
        <th class="rex-icon"><a href="index.php?page=module&amp;function=add"'. rex_accesskey($I18N->msg('create_module'), $REX['ACKEY']['ADD']) .'"><img src="media/modul_plus.gif" alt="'.$I18N->msg("create_module").'" title="'.$I18N->msg("create_module").'" /></a></th>
        <th class="rex-icon">ID</th>
        <th>'.$I18N->msg('module_description').'</th>
        <th>'.$I18N->msg('module_functions').'</th>
      </tr>
    </thead>
    <tbody>
  ';


  $sql = new rex_sql;
  $sql->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."module ORDER BY name");

  for($i=0; $i<$sql->getRows(); $i++){

    echo '
      <tr>
        <td class="rex-icon"><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=edit"><img src="media/modul.gif" alt="'. $sql->getValue("name") .'" title="'. $sql->getValue("name") .'"/></a></td>
        <td class="rex-icon">'.$sql->getValue("id").'</td>
        <td><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=edit">'.htmlspecialchars($sql->getValue("name")).'</a></td>
        <td><a href="index.php?page=module&amp;modul_id='.$sql->getValue("id").'&amp;function=delete" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg("delete_module").'</a></td>
      </tr>'."\n";
    $sql->counter++;
  }

  echo '
    </tbody>
  </table>';
}

?>