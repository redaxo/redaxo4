<?php

/**
 * Artikel Objekt.
 * Zuständig für die Verarbeitung eines Artikel
 *
 * @package redaxo4
 * @version $Id: class.rex_article.inc.php,v 1.12 2008/02/26 10:52:57 kills Exp $
 */

class rex_article
{
  var $slice_id;
  var $article_id;
  var $mode;
  var $article_content;
  var $function;
  var $eval;
  var $category_id;  
  var $CONT;
  var $template_id;
  var $ViewSliceId;
  var $setanker;
  var $save;
  var $ctype;
  var $clang;
  var $getSlice;
  var $viasql; // Content über Datenbank holen

  var $warning;
  var $info;
	
  // ----- Konstruktor
  function rex_article($article_id = null, $clang = null)
  {
  	global $REX;

    $this->article_id = 0;
    $this->template_id = 0;
    $this->ctype = -1; // zeigt alles an
    $this->slice_id = 0;
    $this->mode = "view";
    $this->article_content = "";
    $this->eval = FALSE;
    $this->setanker = true;
    $this->viasql = false;


    // AUSNAHME: modul auswählen problem
    // action=index.php#1212 problem
    if (strpos($_SERVER["HTTP_USER_AGENT"],"Mac") and strpos($_SERVER["HTTP_USER_AGENT"],"MSIE") ) $this->setanker = FALSE;

    if($clang !== null)
      $this->setCLang($clang);
    else
      $this->setClang($REX['CUR_CLANG']);

    if ($article_id !== null)
      $this->setArticleId($article_id);
  }

	function getContentAsQuery($viasql = TRUE)
	{
		if ($viasql !== TRUE) $viasql = FALSE;
		$this->viasql = $viasql;
	}


  // ----- Slice Id setzen für Editiermodus
  function setSliceId($value)
  {
    $this->slice_id = $value;
  }

  function setClang($value)
  {
    global $REX;
    if (!isset($REX['CLANG'][$value]) || $REX['CLANG'][$value] == "") $value = $REX['CUR_CLANG'];
    $this->clang = $value;
  }

  function setArticleId($article_id)
  {
    global $REX;

    $article_id = (int) $article_id;
    $this->article_id = (int) $article_id;

    if (!$REX['GG'] || $this->viasql)
    {
      // ---------- select article
      $qry = "SELECT * FROM ".$REX['TABLE_PREFIX']."article WHERE ".$REX['TABLE_PREFIX']."article.id='$article_id' AND clang='".$this->clang."'";
      $this->ARTICLE = new rex_sql;
      // $this->ARTICLE->debugsql = 1;

      $this->ARTICLE->setQuery($qry);

      if ($this->ARTICLE->getRows() == 1)
      {
        $this->template_id = $this->ARTICLE->getValue($REX['TABLE_PREFIX']."article.template_id");
        $this->category_id = $this->getValue("category_id");
        return TRUE;
      }else
      {
        $this->article_id = 0;
        $this->template_id = 0;
        $this->category_id = 0;
        return FALSE;
      }
    }else
    {
    	$FX = file_exists($REX['INCLUDE_PATH']."/generated/articles/".$article_id.".".$this->clang.".content");
      if ($FX && @include $REX['INCLUDE_PATH']."/generated/articles/".$article_id.".".$this->clang.".article")
      {
        $this->category_id = $REX['ART'][$article_id]['re_id'][$this->clang];
        $this->template_id = $REX['ART'][$article_id]['template_id'][$this->clang];
        return TRUE;
      }else
      {
				$this->ARTICLE = new rex_sql;
	      $this->ARTICLE->setQuery("select * from ".$REX['TABLE_PREFIX']."article where ".$REX['TABLE_PREFIX']."article.id='$article_id' and clang='".$this->clang."'");
	   	  if ($this->ARTICLE->getRows() == 1)
     	  {
     	  	include_once ($REX["INCLUDE_PATH"]."/functions/function_rex_generate.inc.php");
     	  	rex_generateArticle($article_id);
     	  	if (@include $REX['INCLUDE_PATH']."/generated/articles/".$article_id.".".$this->clang.".article")
	     		{
  	    		$this->category_id = $REX['ART'][$article_id]['re_id'][$this->clang];
        		$this->template_id = $REX['ART'][$article_id]['template_id'][$this->clang];
        		return TRUE;
        	}else
        	{
        		return FALSE;
        	}
     	  }else
     	  {
     	  	return FALSE;
     	  }
      }
    }
  }

  function setTemplateId($template_id)
  {
    $this->template_id = $template_id;
  }

  function getTemplateId()
  {
    return $this->template_id;
  }

  function setMode($mode)
  {
    $this->mode = $mode;
  }

  function setFunction($function)
  {
    $this->function = $function;
  }

  function setEval($value)
  {
    if ($value) $this->eval = TRUE;
    else $this->eval = FALSE;
  }

  function correctValue($value)
  {
    global $REX;

    if ($value == 'category_id')
    {
      if ($this->getValue('startpage')!=1) $value = 're_id';
      else if($REX['GG'] && !$this->viasql) $value = 'article_id';
      else $value = 'id';
    }
    // Nicht generated, oder über SQL muss article_id -> id heissen
    else if ((!$REX['GG'] || $this->viasql) && $value == 'article_id')
    {
      $value = 'id';
    }

    return $value;
  }

  function _getValue($value)
  {
    global $REX;
    $value = $this->correctValue($value);

    if ($REX['GG'] && !$this->viasql) return $REX['ART'][$this->article_id][$value][$this->clang];
    else return $this->ARTICLE->getValue($value);
  }

  function getValue($value)
  {
    // damit alte rex_article felder wie teaser, online_from etc
    // noch funktionieren
    // gleicher BC code nochmals in OOREDAXO::getValue
    foreach(array('', 'art_', 'cat_') as $prefix)
    {
      $val = $prefix . $value;
      if($this->hasValue($val))
      {
        return $this->_getValue($val);
      }
    }
    return '['. $value .' not found]';
  }

  function hasValue($value)
  {
    global $REX;
    $value = $this->correctValue($value);

    if ($REX['GG'] && !$this->viasql) return isset($REX['ART'][$this->article_id][$value][$this->clang]);
    else return $this->ARTICLE->hasValue($value);
  }

  function getArticle($curctype = -1)
  {
    global $REX_USER,$REX,$I18N;

    $this->ctype = $curctype;
    $module_id = rex_request('module_id', 'int');

    $sliceLimit = '';
    if ($this->getSlice) {
      //$REX['GG'] = 0;
      $sliceLimit = " AND ".$REX['TABLE_PREFIX']."article_slice.id = '" . $this->getSlice . "' ";
    }

    // ----- start: article caching
    ob_start();
    ob_implicit_flush(0);

    if ($REX['GG'] && !$this->viasql && !$this->getSlice)
    {
      if ($this->article_id != 0)
      {
        $article_content_file = $REX['INCLUDE_PATH'].'/generated/articles/'.$this->article_id.'.'.$this->clang.'.content';
        if($cont = rex_get_file_contents($article_content_file))
        {
          eval($cont);
        }
      }
    }else
    {
      if ($this->article_id != 0)
      {
        // ---------- alle teile/slices eines artikels auswaehlen
        $sql = "SELECT ".$REX['TABLE_PREFIX']."module.id, ".$REX['TABLE_PREFIX']."module.name, ".$REX['TABLE_PREFIX']."module.ausgabe, ".$REX['TABLE_PREFIX']."module.eingabe, ".$REX['TABLE_PREFIX']."article_slice.*, ".$REX['TABLE_PREFIX']."article.re_id
          FROM
            ".$REX['TABLE_PREFIX']."article_slice
          LEFT JOIN ".$REX['TABLE_PREFIX']."module ON ".$REX['TABLE_PREFIX']."article_slice.modultyp_id=".$REX['TABLE_PREFIX']."module.id
          LEFT JOIN ".$REX['TABLE_PREFIX']."article ON ".$REX['TABLE_PREFIX']."article_slice.article_id=".$REX['TABLE_PREFIX']."article.id
          WHERE
            ".$REX['TABLE_PREFIX']."article_slice.article_id='".$this->article_id."' AND
            ".$REX['TABLE_PREFIX']."article_slice.clang='".$this->clang."' AND
            ".$REX['TABLE_PREFIX']."article.clang='".$this->clang."'
            ". $sliceLimit ."
            ORDER BY ".$REX['TABLE_PREFIX']."article_slice.re_article_slice_id";

        $this->CONT = new rex_sql;
        $this->CONT->debugsql = 0;
        $this->CONT->setQuery($sql);

        $RE_CONTS = array();
        $RE_CONTS_CTYPE = array();
        $RE_MODUL_OUT = array();
        $RE_MODUL_IN = array();
        $RE_MODUL_ID = array();
        $RE_MODUL_NAME = array();
        $RE_C = array();

        // ---------- SLICE IDS/MODUL SETZEN - speichern der daten
        for ($i=0;$i<$this->CONT->getRows();$i++)
        {
          $RE_SLICE_ID = $this->CONT->getValue('re_article_slice_id');
          
          $RE_CONTS[$RE_SLICE_ID]       = $this->CONT->getValue($REX['TABLE_PREFIX'].'article_slice.id');
          $RE_CONTS_CTYPE[$RE_SLICE_ID] = $this->CONT->getValue($REX['TABLE_PREFIX'].'article_slice.ctype');
          $RE_MODUL_IN[$RE_SLICE_ID]    = $this->CONT->getValue($REX['TABLE_PREFIX'].'module.eingabe');
          $RE_MODUL_OUT[$RE_SLICE_ID]   = $this->CONT->getValue($REX['TABLE_PREFIX'].'module.ausgabe');
          $RE_MODUL_ID[$RE_SLICE_ID]    = $this->CONT->getValue($REX['TABLE_PREFIX'].'module.id');
          $RE_MODUL_NAME[$RE_SLICE_ID]  = $this->CONT->getValue($REX['TABLE_PREFIX'].'module.name');
          $RE_C[$RE_SLICE_ID]           = $i;
          $this->CONT->next();
        }

        // ---------- moduleselect: nur module nehmen auf die der user rechte hat
        if($this->mode=='edit')
        {
          $MODULE = new rex_sql;
          $MODULE->setQuery('select * from '.$REX['TABLE_PREFIX'].'module order by name');

          $MODULESELECT = new rex_select;
          $MODULESELECT->setName('module_id');
          $MODULESELECT->setSize('1');
          $MODULESELECT->setStyle('class="rex-form-select"');
          $MODULESELECT->setAttribute('onchange', 'this.form.submit();');
          $MODULESELECT->addOption('----------------------------  '.$I18N->msg('add_block'),'');

          for ($i=0;$i<$MODULE->getRows();$i++)
          {
            if ($REX_USER->hasPerm('module['.$MODULE->getValue('id').']') || $REX_USER->hasPerm('admin[]')) $MODULESELECT->addOption(rex_translate($MODULE->getValue('name'),NULL,FALSE),$MODULE->getValue('id'));
            $MODULE->next();
          }
        }

        // ---------- SLICE IDS SORTIEREN UND AUSGEBEN
        $I_ID = 0;
        $PRE_ID = 0;
				$LCTSL_ID = 0;
        $this->CONT->reset();
        $this->article_content = "";

        for ($i=0;$i<$this->CONT->getRows();$i++)
        {
          // ----- ctype unterscheidung
          if ($this->mode != "edit" && $i == 0)
            $this->article_content = "<?php if (\$this->ctype == '".$RE_CONTS_CTYPE[$I_ID]."' || (\$this->ctype == '-1')) { ?>";

          // ------------- EINZELNER SLICE - AUSGABE
          $this->CONT->counter = $RE_C[$I_ID];
          $slice_content = "";
          $SLICE_SHOW = TRUE;

          if($this->mode=="edit")
          {
            $form_url = 'index.php';
            if ($this->setanker) $form_url .= '#addslice';

            $this->ViewSliceId = $RE_CONTS[$I_ID];

            // ----- add select box einbauen
            if($this->function=="add" && $this->slice_id == $I_ID)
            {
              $slice_content = $this->addSlice($I_ID,$module_id);

            }else
            {

              // ----- BLOCKAUSWAHL - SELECT
              $MODULESELECT->setId("module_id". $I_ID);

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
    		                '. $MODULESELECT->get() .'
        		            <noscript><input class="rex-form-submit" type="submit" name="btn_add" value="'. $I18N->msg("add_block") .'" /></noscript>
            		      </p>
            		    </div>
            		  </div>
                </fieldset>
              </form>
              </div>';

            }

            // ----- EDIT/DELETE BLOCK - Wenn Rechte vorhanden
            if($REX_USER->hasPerm("module[".$RE_MODUL_ID[$I_ID]."]") || $REX_USER->hasPerm("admin[]"))
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
              $listElements[] = '<a href="'. sprintf($sliceUrl, '&amp;function=edit') .'" class="rex-tx2">'. $I18N->msg('edit') .' <span>'. $RE_MODUL_NAME[$I_ID] .'</span></a>';
              $listElements[] = '<a href="'. sprintf($sliceUrl, '&amp;function=delete&amp;save=1') .'" class="rex-tx3" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'. $I18N->msg('delete') .' <span>'. $RE_MODUL_NAME[$I_ID] .'</span></a>';
              if ($REX_USER->hasPerm('moveSlice[]'))
              {
                $moveUp = $I18N->msg('move_slice_up');
                $moveDown = $I18N->msg('move_slice_down');
                // upd stamp übergeben, da sonst ein block nicht mehrfach hintereindander verschoben werden kann
                // (Links wären sonst gleich und der Browser lässt das klicken auf den gleichen Link nicht zu)
                $listElements[] = '<a href="'. sprintf($sliceUrl, '&amp;upd='. time() .'&amp;function=moveup') .'" title="'. $moveUp .'"><img src="media/file_up.gif" width="16" height="16" alt="'. $moveUp .'" /> <span>'. $RE_MODUL_NAME[$I_ID] .'</span></a>';
                $listElements[] = '<a href="'. sprintf($sliceUrl, '&amp;upd='. time() .'&amp;function=movedown') .'" title="'. $moveDown .'"><img src="media/file_down.gif" width="16" height="16" alt="'. $moveDown .'" /> <span>'. $RE_MODUL_NAME[$I_ID] .'</span></a>';
              }

              // ----- EXTENSION POINT
              $listElements = rex_register_extension_point('ART_SLICE_MENU', $listElements,
                array(
                  'article_id' => $this->article_id,
                  'clang' => $this->clang,
                  'ctype' => $RE_CONTS_CTYPE[$I_ID],
                  'module_id' => $RE_MODUL_ID[$I_ID],
                  'slice_id' => $RE_CONTS[$I_ID]
                )
              );

              $mne = $msg .'
		       		<div class="rex-content-editmode-module-name">
                <h3 class="rex-hl4">'. htmlspecialchars($RE_MODUL_NAME[$I_ID]) .'</h3>
                <div class="rex-navi-slice">
                	<ul>
              ';

              foreach($listElements as $listElement)
              {
                $mne  .= '<li>'. $listElement .'</li>';
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
                    $REX_ACTION = $obj->getACDatabaseValues($REX_ACTION, $this->CONT);
                }

                // TODO: PreviewActions gibts nur im EditMode...?
                if ($this->function == 'edit') $modebit = '2'; // pre-action and edit
                elseif($this->function == 'delete') $modebit = '4'; // pre-action and delete
                else $modebit = '1'; // pre-action and add

                $ga = new rex_sql;
                $ga->debugsql = 0;
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
                    $obj->setACValues($this->CONT, $REX_ACTION);

                  $ga->next();
                }

                // ----- / PRE VIEW ACTION

                $slice_content .= $this->editSlice($RE_CONTS[$I_ID],$RE_MODUL_IN[$I_ID],$RE_CONTS_CTYPE[$I_ID], $RE_MODUL_ID[$I_ID]);
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

                $slice_content = $this->replaceVars($this->CONT, $slice_content);
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
              $slice_content = $this->replaceVars($this->CONT, $slice_content);
            }

          }else
          {

            // ----- wenn mode nicht edit
            if($this->getSlice){
                while(list($k, $v) = each($RE_CONTS))
                  $I_ID = $k;
            }

            $slice_content .= $RE_MODUL_OUT[$I_ID];
            $slice_content = $this->replaceVars($this->CONT, $slice_content);
          }
          // --------------- ENDE EINZELNER SLICE

					// --------------- EP: SLICE_SHOW
  				$slice_content = rex_register_extension_point(
  					'SLICE_SHOW',
  					$slice_content,
  						array(
  							'article_id' => $this->article_id,
  							'clang' => $this->clang,
  							'ctype' => $RE_CONTS_CTYPE[$I_ID],
  							'module_id' => $RE_MODUL_ID[$I_ID],
  							'slice_id' => $RE_CONTS[$I_ID],
  							'function' => $this->function,
  							'function_slice_id' => $this->slice_id
  						)
  				);

          // ---------- slice in ausgabe speichern wenn ctype richtig
          if ($this->ctype == -1 or $this->ctype == $RE_CONTS_CTYPE[$I_ID])
          {
            $this->article_content .= $slice_content;

            // last content type slice id
            $LCTSL_ID = $RE_CONTS[$I_ID];
          }

          // ----- zwischenstand: ctype .. wenn ctype neu dann if
          if ($this->mode != "edit" && isset($RE_CONTS_CTYPE[$RE_CONTS[$I_ID]]) && $RE_CONTS_CTYPE[$I_ID] != $RE_CONTS_CTYPE[$RE_CONTS[$I_ID]] && $RE_CONTS_CTYPE[$RE_CONTS[$I_ID]] != "")
          {
            $this->article_content .= "<?php } if(\$this->ctype == '".$RE_CONTS_CTYPE[$RE_CONTS[$I_ID]]."' || \$this->ctype == '-1'){ ?>";
          }

          // zum nachsten slice
          $I_ID = $RE_CONTS[$I_ID];
          $PRE_ID = $I_ID;

        }

        // ----- end: ctype unterscheidung
        if ($this->mode != "edit" && $i>0) $this->article_content .= "<?php } ?>";

        // ----- add module im edit mode
        if ($this->mode == "edit")
        {
          $form_url = 'index.php';
          if ($this->setanker) $form_url .= '#addslice';

          if($this->function=="add" && $this->slice_id == $LCTSL_ID)
          {
            $slice_content = $this->addSlice($LCTSL_ID,$module_id);
          }else
          {
            // ----- BLOCKAUSWAHL - SELECT
            $MODULESELECT->setId("module_id". $LCTSL_ID);

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
		                  	'. $MODULESELECT->get() .'
		                  	<noscript><input class="rex-form-submit" type="submit" name="btn_add" value="'. $I18N->msg("add_block") .'" /></noscript>
                			</p>
                		</div>
                	</div>
              </fieldset>
            </form>
            </div>';
          }
          $this->article_content .= $slice_content;
        }

        // -------------------------- schreibe content
        if ($this->mode == "generate" || $this->viasql) echo $this->replaceLinks($this->article_content);
        else eval("?>".$this->article_content);

      }else
      {
        echo $I18N->msg('no_article_available');
      }
    }

    // ----- end: article caching
    $CONTENT = ob_get_contents();
    ob_end_clean();

    return $CONTENT;
  }

  // ----- Template inklusive Artikel zurückgeben
  function getArticleTemplate()
  {
    // global $REX hier wichtig, damit in den Artikeln die Variable vorhanden ist!
    global $REX;

    if ($this->getTemplateId() != 0 && $this->article_id != 0)
    {
      ob_start();
      ob_implicit_flush(0);

    	$TEMPLATE = new rex_template();
    	$TEMPLATE->setId($this->getTemplateId());
      $tplContent = $TEMPLATE->getTemplate();
      $tplContent = $this->replaceCommonVars($tplContent);
			eval("?>".$tplContent);

      $CONTENT = ob_get_contents();
      ob_end_clean();
    }
    else
    {
      $CONTENT = "no template";
    }

    return $CONTENT;
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
  function editSlice($RE_CONTS, $RE_MODUL_IN, $RE_CTYPE, $RE_MODUL_ID)
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
		          </p>
		          <p class="rex-form-col-b rex-form-submit">
		            <input class="rex-form-submit" type="submit" value="'.$I18N->msg('update_block').'" name="btn_update" '. rex_accesskey($I18N->msg('update_block'), $REX['ACKEY']['APPLY']) .' />
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

    $slice_content = $this->replaceVars($this->CONT, $slice_content);
    return $slice_content;
  }

  // ----- Modulvariablen werden ersetzt
  function replaceVars(&$sql, $content)
  {
    $content = $this->replaceObjectVars($sql,$content);
    $content = $this->replaceCommonVars($content);
    return $content;
  }

  // ----- REX_VAR Ersetzungen
  function replaceObjectVars(&$sql,$content)
  {
    global $REX;

    $tmp = '';
    $sliceId = $sql->getValue($REX['TABLE_PREFIX'].'article_slice.id');

  	foreach($REX['VARIABLES'] as $var)
  	{
  		if ($this->mode == 'edit')
  		{
  			if (($this->function == 'add' && $sliceId == '0') ||
      			($this->function == 'edit' && $sliceId == $this->slice_id))
  			{
  		  	if (isset($REX['ACTION']['SAVE']) && $REX['ACTION']['SAVE'] === false)
  		  	{
            // Wenn der aktuelle Slice nicht gespeichert werden soll
            // (via Action wurde das Nicht-Speichern-Flag gesetzt)
            // Dann die Werte manuell aus dem Post übernehmen
            // und anschließend die Werte wieder zurücksetzen,
            // damit die nächsten Slices wieder die Werte aus der DB verwenden
  		  		$var->setACValues($sql,$REX['ACTION']);
            $tmp = $var->getBEInput($sql,$content);
            $sql->flushValues();
  		  	}
          else
          {
            // Slice normal parsen
    		  	$tmp = $var->getBEInput($sql,$content);
          }
  		  }else
  		  {
  		  	$tmp = $var->getBEOutput($sql,$content);
  		  }
  		}else
      {
  			$tmp = $var->getFEOutput($sql,$content);
  		}

      // Rückgabewert nur auswerten wenn auch einer vorhanden ist
      // damit $content nicht verfälscht wird
      // null ist default Rückgabewert, falls kein RETURN in einer Funktion ist
      if($tmp !== null)
      {
        $content = $tmp;
      }
  	}

	  return $content;
  }

  // ---- Artikelweite globale variablen werden ersetzt
  function replaceCommonVars($content)
  {
  	global $REX;

    static $user_id = null;
		static $user_login = null;

    // UserId gibts nur im Backend
    if($user_id === null)
    {
      global $REX_USER;
      if($REX_USER)
      {
        $user_id = $REX_USER->getValue('user_id');
        $user_login = $REX_USER->getValue('login');
      }else
      {
        $user_id = '';
        $user_login = '';
      }
    }

    static $search = array(
       'REX_ARTICLE_ID',
       'REX_CATEGORY_ID',
       'REX_CLANG_ID',
       'REX_TEMPLATE_ID',
       'REX_USER_ID',
       'REX_USER_LOGIN'
    );

    $replace = array(
      $this->article_id,
      $this->category_id,
      $this->clang,
      $this->getTemplateId(),
      $user_id,
      $user_login
    );

    return str_replace($search, $replace,$content);
  }

  function replaceLinks($content)
  {
    // Hier beachten, dass man auch ein Zeichen nach dem jeweiligen Link mitmatched,
    // damit beim ersetzen von z.b. redaxo://11 nicht auch innerhalb von redaxo://112
    // ersetzt wird
    // siehe dazu: http://forum.redaxo.de/ftopic7563.html

    // -- preg match redaxo://[ARTICLEID]-[CLANG] --
    preg_match_all('@redaxo://([0-9]*)\-([0-9]*)(.){1}/?@im',$content,$matches,PREG_SET_ORDER);
    foreach($matches as $match)
    {
      if(empty($match)) continue;

      $url = rex_getURL($match[1], $match[2]);
      $content = str_replace($match[0],$url.$match[3],$content);
    }

    // -- preg match redaxo://[ARTICLEID] --
    preg_match_all('@redaxo://([0-9]*)(.){1}/?@im',$content,$matches,PREG_SET_ORDER);
    foreach($matches as $match)
    {
      if(empty($match)) continue;

      $url = rex_getURL($match[1], $this->clang);
      $content = str_replace($match[0],$url.$match[2],$content);
    }

    return $content;
  }
}