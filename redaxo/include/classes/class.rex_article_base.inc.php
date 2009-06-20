<?php

/**
 * Klasse regelt den Zugriff auf Artikelinhalte.
 * Alle benötigten Daten werden von der DB bezogen.
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_article_base
{
  var $category_id;
  var $article_id;
  var $slice_id;
  var $mode;
  var $function;

  var $template_id;
  var $template_attributes;

  var $ctype;
  var $clang;
  var $getSlice;

  var $eval;

  var $article_revision;
  var $slice_revision;

  var $warning;
  var $info;
  var $debug;

  function rex_article_base($article_id = null, $clang = null)
  {
    global $REX;

    $this->article_id = 0;
    $this->template_id = 0;
    $this->ctype = -1; // zeigt alles an
    $this->slice_id = 0;

    $this->mode = "view";
    $this->eval = FALSE;

    $this->article_revision = 0;
    $this->slice_revision = 0;

    $this->debug = FALSE;

    $this->ARTICLE = new rex_sql;
    if($this->debug)
    $this->ARTICLE->debugsql = 1;

    if($clang !== null)
    $this->setCLang($clang);
    else
    $this->setClang($REX['CUR_CLANG']);

    // ----- EXTENSION POINT
    rex_register_extension_point('ART_INIT', "",
      array (
          'article' => &$this,
          'article_id' => $article_id,
          'clang' => $this->clang
      )
    );

    if ($article_id !== null)
    $this->setArticleId($article_id);
  }

  function setSliceRevision($sr)
  {
    $this->slice_revision = (int) $sr;
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

  function getArticleId()
  {
    return $this->article_id;
  }

  function getClang()
  {
    return $this->clang;
  }

  function setArticleId($article_id)
  {
    global $REX;

    $article_id = (int) $article_id;
    $this->article_id = $article_id;

    // ---------- select article
    $qry = "SELECT * FROM ".$REX['TABLE_PREFIX']."article WHERE ".$REX['TABLE_PREFIX']."article.id='$article_id' AND clang='".$this->clang."'";
    $this->ARTICLE->setQuery($qry);

    if ($this->ARTICLE->getRows() == 1)
    {
      $this->template_id = $this->getValue('template_id');
      $this->category_id = $this->getValue('category_id');
      return TRUE;
    }

    $this->article_id = 0;
    $this->template_id = 0;
    $this->category_id = 0;
    return FALSE;
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
    if ($value == 'category_id')
    {
      if ($this->getValue('startpage')!=1) $value = 're_id';
      else $value = 'id';
    }
    // über SQL muss article_id -> id heissen
    else if ($value == 'article_id')
    {
      $value = 'id';
    }

    return $value;
  }

  function _getValue($value)
  {
    global $REX;
    $value = $this->correctValue($value);

    return $this->ARTICLE->getValue($value);
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
    return $this->ARTICLE->hasValue($this->correctValue($value));
  }

  function outputSlice($artDataSql, $module_id, $I_ID,
    $RE_CONTS, $RE_CONTS_CTYPE, $RE_MODUL_IN, $RE_MODUL_OUT,
    $RE_MODUL_ID, $RE_MODUL_NAME, $RE_C)
  {
    if($this->getSlice){
      while(list($k, $v) = each($RE_CONTS))
      $I_ID = $k;
    }
    
    $slice_content .= $RE_MODUL_OUT[$I_ID];
    return $this->replaceVars($artDataSql, $slice_content);
  }


  function getArticle($curctype = -1)
  {
    global $REX,$I18N;

    $this->ctype = $curctype;
    $module_id = rex_request('module_id', 'int');

    $sliceLimit = '';
    if ($this->getSlice) {
      $sliceLimit = " AND ".$REX['TABLE_PREFIX']."article_slice.id = '" . $this->getSlice . "' ";
    }

    // ----- start: article caching
    ob_start();
    ob_implicit_flush(0);

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
            ".$REX['TABLE_PREFIX']."article.clang='".$this->clang."' AND 
            ".$REX['TABLE_PREFIX']."article_slice.revision='".$this->slice_revision."'
            ". $sliceLimit ."
            ORDER BY ".$REX['TABLE_PREFIX']."article_slice.re_article_slice_id";

      $artDataSql = new rex_sql;
      if($this->debug)
        $artDataSql->debugsql = 1;
      $artDataSql->setQuery($sql);

      $RE_CONTS = array();
      $RE_CONTS_CTYPE = array();
      $RE_MODUL_OUT = array();
      $RE_MODUL_IN = array();
      $RE_MODUL_ID = array();
      $RE_MODUL_NAME = array();
      $RE_C = array();

      // ---------- SLICE IDS/MODUL SETZEN - speichern der daten
      for ($i=0;$i<$artDataSql->getRows();$i++)
      {
        $RE_SLICE_ID = $artDataSql->getValue('re_article_slice_id');

        $RE_CONTS[$RE_SLICE_ID]       = $artDataSql->getValue($REX['TABLE_PREFIX'].'article_slice.id');
        $RE_CONTS_CTYPE[$RE_SLICE_ID] = $artDataSql->getValue($REX['TABLE_PREFIX'].'article_slice.ctype');
        $RE_MODUL_IN[$RE_SLICE_ID]    = $artDataSql->getValue($REX['TABLE_PREFIX'].'module.eingabe');
        $RE_MODUL_OUT[$RE_SLICE_ID]   = $artDataSql->getValue($REX['TABLE_PREFIX'].'module.ausgabe');
        $RE_MODUL_ID[$RE_SLICE_ID]    = $artDataSql->getValue($REX['TABLE_PREFIX'].'module.id');
        $RE_MODUL_NAME[$RE_SLICE_ID]  = $artDataSql->getValue($REX['TABLE_PREFIX'].'module.name');
        $RE_C[$RE_SLICE_ID]           = $i;
        $artDataSql->next();
      }

      // pre hook
      $this->preArticle();

      // ---------- SLICE IDS SORTIEREN UND AUSGEBEN
      $I_ID = 0;
      $PRE_ID = 0;
      $LCTSL_ID = 0;
      $artDataSql->reset();
      $articleContent = "";

      for ($i=0;$i<$artDataSql->getRows();$i++)
      {
        // ----- ctype unterscheidung
        if ($this->mode != "edit" && $i == 0)
        $articleContent = "<?php if (\$this->ctype == '".$RE_CONTS_CTYPE[$I_ID]."' || (\$this->ctype == '-1')) { ?>";

        // ------------- EINZELNER SLICE - AUSGABE
        $artDataSql->counter = $RE_C[$I_ID];

        $slice_content = $this->outputSlice(
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
          $articleContent .= $slice_content;

          // last content type slice id
          $LCTSL_ID = $RE_CONTS[$I_ID];
        }

        // ----- zwischenstand: ctype .. wenn ctype neu dann if
        if ($this->mode != "edit" && isset($RE_CONTS_CTYPE[$RE_CONTS[$I_ID]]) && $RE_CONTS_CTYPE[$I_ID] != $RE_CONTS_CTYPE[$RE_CONTS[$I_ID]] && $RE_CONTS_CTYPE[$RE_CONTS[$I_ID]] != "")
        {
          $articleContent .= "<?php } if(\$this->ctype == '".$RE_CONTS_CTYPE[$RE_CONTS[$I_ID]]."' || \$this->ctype == '-1'){ ?>";
        }

        // zum nachsten slice
        $I_ID = $RE_CONTS[$I_ID];
        $PRE_ID = $I_ID;

      }

      // ----- end: ctype unterscheidung
      if ($this->mode != "edit" && $i>0) $articleContent .= "<?php } ?>";


      // ----- post hook
      $articleContent = $this->postArticle($articleContent, $LCTSL_ID, $module_id);

      // -------------------------- schreibe content
      if ($this->eval === FALSE) echo $this->replaceLinks($articleContent);
      else eval("?>".$articleContent);

    }else
    {
      echo $I18N->msg('no_article_available');
    }

    // ----- end: article caching
    $CONTENT = ob_get_contents();
    ob_end_clean();

    return $CONTENT;
  }

  function preArticle()
  {
    // nichts tun
  }

  function postArticle($articleContent, $LCTSL_ID, $module_id)
  {
    // nichts tun
    return $articleContent;
  }

  // ----- Template inklusive Artikel zurückgeben
  function getArticleTemplate()
  {
    // global $REX hier wichtig, damit in den Artikeln die Variable vorhanden ist!
    global $REX;

    if ($this->template_id != 0 && $this->article_id != 0)
    {
      ob_start();
      ob_implicit_flush(0);

      $TEMPLATE = new rex_template();
      $TEMPLATE->setId($this->template_id);
      $tplContent = $this->replaceCommonVars($TEMPLATE->getTemplate());
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
      if(isset($REX['USER']))
      {
        $user_id = $REX['USER']->getValue('user_id');
        $user_login = $REX['USER']->getValue('login');
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