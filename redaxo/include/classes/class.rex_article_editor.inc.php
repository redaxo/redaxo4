<?php

/**
 * Erweiterung eines Artikels um slicemanagement.
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_article_editor extends rex_article
{
  var $MODULESELECT;

  function rex_article_editor($article_id = null, $clang = null)
  {
    parent::rex_article($article_id, $clang);
  }

  function outputSlice($artDataSql, $module_id, $I_ID,
    $RE_CONTS, $RE_CONTS_CTYPE, $RE_MODUL_IN, $RE_MODUL_OUT,
    $RE_MODUL_ID, $RE_MODUL_NAME, $RE_C)
  {
    global $REX, $I18N;

    if($this->mode=="edit")
    {
      $form_url = 'index.php';

      // ----- add select box einbauen
      if($this->function=="add" && $this->slice_id == $I_ID)
      {
        $slice_content = $this->addSlice($I_ID,$module_id);

      }else
      {

        // ----- BLOCKAUSWAHL - SELECT
        $this->MODULESELECT[$this->ctype]->setId("module_id". $I_ID);

        $slice_content = '
              <div class="rex-form rex-form-content-editmode">
              <form action="'. $form_url .'" method="get" id="slice'. $RE_CONTS[$I_ID] .'">
                <fieldset class="rex-form-col-1">
                  <legend><span>'. $I18N->msg("add_block") .'</span></legend>
                  <input type="hidden" name="article_id" value="'. $this->article_id .'" />
                  <input type="hidden" name="page" value="content" />
                  <input type="hidden" name="mode" value="'. $this->mode .'" />
                  <input type="hidden" name="slice_id" value="'. $I_ID .'" />
                  <input type="hidden" name="function" value="add" />
                  <input type="hidden" name="clang" value="'.$this->clang.'" />
                  <input type="hidden" name="ctype" value="'.$this->ctype.'" />
                  
                  <div class="rex-form-wrapper">
                    <div class="rex-form-row">
                      <p class="rex-form-col-a rex-form-select">
                        '. $this->MODULESELECT[$this->ctype]->get() .'
                        <noscript><input class="rex-form-submit" type="submit" name="btn_add" value="'. $I18N->msg("add_block") .'" /></noscript>
                      </p>
                    </div>
                  </div>
                </fieldset>
              </form>
              </div>';

      }

      // ----- EDIT/DELETE BLOCK - Wenn Rechte vorhanden
      if($REX['USER']->isAdmin() || $REX['USER']->hasPerm("module[".$RE_MODUL_ID[$I_ID]."]"))
      {
        $msg = '';

        if($this->slice_id == $RE_CONTS[$I_ID])
        {
          if($this->warning != '')
          {
            $msg .= rex_warning($this->warning);
          }
          if($this->info != '')
          {
            $msg .= rex_info($this->info);
          }
        }

        $sliceUrl = 'index.php?page=content&amp;article_id='. $this->article_id .'&amp;mode=edit&amp;slice_id='. $RE_CONTS[$I_ID] .'&amp;clang='. $this->clang .'&amp;ctype='. $this->ctype .'%s#slice'. $RE_CONTS[$I_ID];
        $listElements = array();
        $listElements[] = '<a href="'. sprintf($sliceUrl, '&amp;function=edit') .'" class="rex-tx3">'. $I18N->msg('edit') .' <span>'. $RE_MODUL_NAME[$I_ID] .'</span></a>';
        $listElements[] = '<a href="'. sprintf($sliceUrl, '&amp;function=delete&amp;save=1') .'" class="rex-tx2" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'. $I18N->msg('delete') .' <span>'. $RE_MODUL_NAME[$I_ID] .'</span></a>';
        if ($REX['USER']->hasPerm('moveSlice[]'))
        {
          $moveUp = $I18N->msg('move_slice_up');
          $moveDown = $I18N->msg('move_slice_down');
          // upd stamp übergeben, da sonst ein block nicht mehrfach hintereindander verschoben werden kann
          // (Links wären sonst gleich und der Browser lässt das klicken auf den gleichen Link nicht zu)
          $listElements[] = '<a href="'. sprintf($sliceUrl, '&amp;upd='. time() .'&amp;function=moveup') .'" title="'. $moveUp .'" class="rex-slice-move-up"><span>'. $RE_MODUL_NAME[$I_ID] .'</span></a>';
          $listElements[] = '<a href="'. sprintf($sliceUrl, '&amp;upd='. time() .'&amp;function=movedown') .'" title="'. $moveDown .'" class="rex-slice-move-down"><span>'. $RE_MODUL_NAME[$I_ID] .'</span></a>';
        }

        // ----- EXTENSION POINT
        $listElements = rex_register_extension_point(
          'ART_SLICE_MENU',
          $listElements,
          array(
            'article_id' => $this->article_id,
            'clang' => $this->clang,
            'ctype' => $RE_CONTS_CTYPE[$I_ID],
            'module_id' => $RE_MODUL_ID[$I_ID],
            'slice_id' => $RE_CONTS[$I_ID]
          )
        );

        $mne = $msg;


        if($this->function=="edit" && $this->slice_id == $RE_CONTS[$I_ID])
          $mne .= '<div class="rex-content-editmode-module-name rex-form-content-editmode-edit-slice">';
        else
          $mne .= '<div class="rex-content-editmode-module-name">';

        $mne .= '
                <h3 class="rex-hl4">'. htmlspecialchars($RE_MODUL_NAME[$I_ID]) .'</h3>
                <div class="rex-navi-slice">
                  <ul>
              ';

        $listElementFlag = true;
        foreach($listElements as $listElement)
        {
          $class = '';
          if ($listElementFlag)
          {
            $class = ' class="rex-navi-first"';
            $listElementFlag = false;
          }
          $mne  .= '<li'.$class.'>'. $listElement .'</li>';
        }

        $mne .= '</ul></div></div>';

        $slice_content .= $mne;
        if($this->function=="edit" && $this->slice_id == $RE_CONTS[$I_ID])
        {
          // **************** Aktueller Slice


          // ----- PRE VIEW ACTION [EDIT]
          $REX_ACTION = array ();

          // nach klick auf den übernehmen button,
          // die POST werte übernehmen
          if(rex_var::isEditEvent())
          {
            foreach ($REX['VARIABLES'] as $obj)
            $REX_ACTION = $obj->getACRequestValues($REX_ACTION);
          }
          // Sonst die Werte aus der DB holen
          // (1. Aufruf via Editieren Link)
          else
          {
            foreach ($REX['VARIABLES'] as $obj)
            $REX_ACTION = $obj->getACDatabaseValues($REX_ACTION, $artDataSql);
          }

          if ($this->function == 'edit') $modebit = '2'; // pre-action and edit
          elseif($this->function == 'delete') $modebit = '4'; // pre-action and delete
          else $modebit = '1'; // pre-action and add

          $ga = new rex_sql;
          if($this->debug)
            $ga->debugsql = 1;
          $ga->setQuery('SELECT preview FROM '.$REX['TABLE_PREFIX'].'module_action ma,'. $REX['TABLE_PREFIX']. 'action a WHERE preview != "" AND ma.action_id=a.id AND module_id='. $RE_MODUL_ID[$I_ID] .' AND ((a.previewmode & '. $modebit .') = '. $modebit .')');

          for ($t=0;$t<$ga->getRows();$t++)
          {
            $iaction = $ga->getValue('preview');

            // ****************** VARIABLEN ERSETZEN
            foreach($REX['VARIABLES'] as $obj)
            $iaction = $obj->getACOutput($REX_ACTION,$iaction);

            eval('?>'.$iaction);

            // ****************** SPEICHERN FALLS NOETIG
            foreach($REX['VARIABLES'] as $obj)
            $obj->setACValues($artDataSql, $REX_ACTION);

            $ga->next();
          }

          // ----- / PRE VIEW ACTION

          $slice_content .= $this->editSlice($artDataSql, $RE_CONTS[$I_ID],$RE_MODUL_IN[$I_ID],$RE_CONTS_CTYPE[$I_ID], $RE_MODUL_ID[$I_ID]);
        }
        else
        {
          // Modulinhalt ausgeben
          $slice_content .= '
                <!-- *** OUTPUT OF MODULE-OUTPUT - START *** -->
                <div class="rex-content-editmode-slice-output">
                  <div class="rex-content-editmode-slice-output-2">
                    '. $RE_MODUL_OUT[$I_ID] .'
                  </div>
                </div>
                <!-- *** OUTPUT OF MODULE-OUTPUT - END *** -->
                ';

          $slice_content = $this->replaceVars($artDataSql, $slice_content);
        }

      }else
      {
        // ----- hat keine rechte an diesem modul
        $mne = '
           <div class="rex-content-editmode-module-name">
            <h3 class="rex-hl4" id="slice'. $RE_CONTS[$I_ID] .'">'. $RE_MODUL_NAME[$I_ID] .'</h3>
            <div class="rex-navi-slice">
              <ul>
                <li>'. $I18N->msg('no_editing_rights') .' <span>'. $RE_MODUL_NAME[$I_ID] .'</span></li>
              </ul>
            </div>
          </div>';

        $slice_content .= $mne. $RE_MODUL_OUT[$I_ID];
        $slice_content = $this->replaceVars($artDataSql, $slice_content);
      }

    }else
    {

      // ----- wenn mode nicht edit
      $slice_content = parent::outputSlice(
        $artDataSql,
        $module_id,
        $I_ID,
        $RE_CONTS,
        $RE_CONTS_CTYPE,
        $RE_MODUL_IN,
        $RE_MODUL_OUT,
        $RE_MODUL_ID,
        $RE_MODUL_NAME,
        $RE_C
      );
    }
    
    return $slice_content;
  }


  function preArticle()
  {
    global $REX, $I18N;

    // ---------- moduleselect: nur module nehmen auf die der user rechte hat
    if($this->mode=='edit')
    {
      $MODULE = new rex_sql;
      $modules = $MODULE->getArray('select * from '.$REX['TABLE_PREFIX'].'module order by name');

      $template_ctypes = rex_getAttributes('ctype', $this->template_attributes, array ());
      // wenn keine ctyes definiert sind, gibt es immer den CTYPE=1
      if(count($template_ctypes) == 0)
      {
        $template_ctypes = array(1 => 'default');
      }

      $this->MODULESELECT = array();
      foreach($template_ctypes as $ct_id => $ct_name)
      {
        $this->MODULESELECT[$ct_id] = new rex_select;
        $this->MODULESELECT[$ct_id]->setName('module_id');
        $this->MODULESELECT[$ct_id]->setSize('1');
        $this->MODULESELECT[$ct_id]->setStyle('class="rex-form-select"');
        $this->MODULESELECT[$ct_id]->setAttribute('onchange', 'this.form.submit();');
        $this->MODULESELECT[$ct_id]->addOption('----------------------------  '.$I18N->msg('add_block'),'');
        foreach($modules as $m)
        {
          if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm('module['.$m['id'].']'))
          {
            if(rex_template::hasModule($this->template_attributes,$ct_id,$m['id']))
            {
              $this->MODULESELECT[$ct_id]->addOption(rex_translate($m['name'],NULL,FALSE),$m['id']);
            }
          }
        }
      }
    }
  }

  function postArticle($articleContent, $LCTSL_ID, $module_id)
  {
    global $REX, $I18N;

    // ----- add module im edit mode
    if ($this->mode == "edit")
    {
      $form_url = 'index.php';

      if($this->function=="add" && $this->slice_id == $LCTSL_ID)
      {
        $slice_content = $this->addSlice($LCTSL_ID,$module_id);
      }else
      {
        // ----- BLOCKAUSWAHL - SELECT
        $this->MODULESELECT[$this->ctype]->setId("module_id". $LCTSL_ID);

        // $slice_content = $add_select_box;
        $slice_content = '
            <div class="rex-form rex-form-content-editmode">
            <form action="'. $form_url .'" method="get">
              <fieldset class="rex-form-col-1">
                <legend><span>'. $I18N->msg("add_block") .'</span></legend>
                <input type="hidden" name="article_id" value="'. $this->article_id .'" />
                <input type="hidden" name="page" value="content" />
                <input type="hidden" name="mode" value="'. $this->mode .'" />
                <input type="hidden" name="slice_id" value="'. $LCTSL_ID .'" />
                <input type="hidden" name="function" value="add" />
                <input type="hidden" name="clang" value="'.$this->clang.'" />
                <input type="hidden" name="ctype" value="'.$this->ctype.'" />

                  
                  <div class="rex-form-wrapper">
                    <div class="rex-form-row">
                      <p class="rex-form-col-a rex-form-select">
                        '. $this->MODULESELECT[$this->ctype]->get() .'
                        <noscript><input class="rex-form-submit" type="submit" name="btn_add" value="'. $I18N->msg("add_block") .'" /></noscript>
                      </p>
                    </div>
                  </div>
              </fieldset>
            </form>
            </div>';
      }
      $articleContent .= $slice_content;
    }
    return $articleContent;
  }


  // ----- ADD Slice
  function addSlice($I_ID,$module_id)
  {
    global $REX,$I18N;

    $MOD = new rex_sql;
    $MOD->setQuery("SELECT * FROM ".$REX['TABLE_PREFIX']."module WHERE id=$module_id");
    if ($MOD->getRows() != 1)
    {
      $slice_content = rex_warning($I18N->msg('module_doesnt_exist'));
    }else
    {
      $slice_content = '
        <a name="addslice"></a>
        <div class="rex-form rex-form-content-editmode-add-slice">
        <form action="index.php#slice'. $I_ID .'" method="post" id="REX_FORM" enctype="multipart/form-data">
          <fieldset class="rex-form-col-1">
            <legend><span>'. $I18N->msg('add_block').'</span></legend>
            <input type="hidden" name="article_id" value="'. $this->article_id .'" />
            <input type="hidden" name="page" value="content" />
            <input type="hidden" name="mode" value="'. $this->mode .'" />
            <input type="hidden" name="slice_id" value="'. $I_ID .'" />
            <input type="hidden" name="function" value="add" />
            <input type="hidden" name="module_id" value="'. $module_id .'" />
            <input type="hidden" name="save" value="1" />
            <input type="hidden" name="clang" value="'. $this->clang .'" />
            <input type="hidden" name="ctype" value="'.$this->ctype .'" />
            
            <div class="rex-content-editmode-module-name">
              <h3 class="rex-hl4">
                '. $I18N->msg("module") .': <span>'. htmlspecialchars($MOD->getValue("name")) .'</span>
              </h3>
            </div>
              
            <div class="rex-form-wrapper">
              
              <div class="rex-form-row">
                <div class="rex-content-editmode-slice-input">
                <div class="rex-content-editmode-slice-input-2">
                  '. $MOD->getValue("eingabe") .'
                </div>
                </div>
              </div>
              
            </div>
          </fieldset>
          
          <fieldset class="rex-form-col-1">
             <div class="rex-form-wrapper">              
              <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-submit">
                  <input class="rex-form-submit" type="submit" name="btn_save" value="'. $I18N->msg('add_block') .'"'. rex_accesskey($I18N->msg('add_block'), $REX['ACKEY']['SAVE']) .' />
                </p>
              </div>
            </div>
          </fieldset>
        </form>
        </div>
        <script type="text/javascript">
           <!--
          jQuery(function($) {
            $(":input:visible:enabled:not([readonly]):first", $("form#REX_FORM")).focus();
          });
           //-->
        </script>';

      // Beim Add hier die Meldung ausgeben
      if($this->slice_id == 0)
      {
        if($this->warning != '')
        {
          echo rex_warning($this->warning);
        }
        if($this->info != '')
        {
          echo rex_info($this->info);
        }
      }

      $dummysql = new rex_sql();

      // Den Dummy mit allen Feldern aus rex_article_slice füllen
      $slice_fields = new rex_sql();
      $slice_fields->setQuery('SELECT * FROM '. $REX['TABLE_PREFIX'].'article_slice LIMIT 1');
      foreach($slice_fields->getFieldnames() as $fieldname)
      {
        switch($fieldname)
        {
          case 'clang'        : $def_value = $this->clang; break;
          case 'ctype'        : $def_value = $this->ctype; break;
          case 'modultyp_id'  : $def_value = $module_id; break;
          case 'article_id'   : $def_value = $this->article_id; break;
          case 'id'           : $def_value = 0; break;
          default             : $def_value = '';
        }
        $dummysql->setValue($REX['TABLE_PREFIX']. 'article_slice.'. $fieldname, $def_value);
      }

      $slice_content = $this->replaceVars($dummysql,$slice_content);
    }
    return $slice_content;
  }

  // ----- EDIT Slice
  function editSlice(&$sql, $RE_CONTS, $RE_MODUL_IN, $RE_CTYPE, $RE_MODUL_ID)
  {
    global $REX, $I18N;

    $slice_content = '
      <a name="editslice"></a>
      <div class="rex-form rex-form-content-editmode-edit-slice">
      <form enctype="multipart/form-data" action="index.php#slice'.$RE_CONTS.'" method="post" id="REX_FORM">
        <fieldset class="rex-form-col-1">
          <legend><span>'. $I18N->msg('edit_block') .'</span></legend>
          <input type="hidden" name="article_id" value="'.$this->article_id.'" />
          <input type="hidden" name="page" value="content" />
          <input type="hidden" name="mode" value="'.$this->mode.'" />
          <input type="hidden" name="slice_id" value="'.$RE_CONTS.'" />
          <input type="hidden" name="ctype" value="'.$RE_CTYPE.'" />
          <input type="hidden" name="module_id" value="'. $RE_MODUL_ID .'" />
          <input type="hidden" name="function" value="edit" />
          <input type="hidden" name="save" value="1" />
          <input type="hidden" name="update" value="0" />
          <input type="hidden" name="clang" value="'.$this->clang.'" />
            
          <div class="rex-form-wrapper">
            <div class="rex-form-row">
              <div class="rex-content-editmode-slice-input">
                <div class="rex-content-editmode-slice-input-2">
                '. $RE_MODUL_IN .'
                </div>
              </div>
            </div>
          </div>
        </fieldset>

        <fieldset class="rex-form-col-2">
          <div class="rex-form-wrapper">
            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-submit">
                <input class="rex-form-submit" type="submit" value="'.$I18N->msg('save_block').'" name="btn_save" '. rex_accesskey($I18N->msg('save_block'), $REX['ACKEY']['SAVE']) .' />
                <input class="rex-form-submit rex-form-submit-2" type="submit" value="'.$I18N->msg('update_block').'" name="btn_update" '. rex_accesskey($I18N->msg('update_block'), $REX['ACKEY']['APPLY']) .' />
              </p>
            </div>
          </div>
        </fieldset>
      </form>
      </div>
      <script type="text/javascript">
         <!--
        jQuery(function($) {
          $(":input:visible:enabled:not([readonly]):first", $("form#REX_FORM")).focus();
        });
         //-->
      </script>';

    $slice_content = $this->replaceVars($sql, $slice_content);
    return $slice_content;
  }
}