<?php

/**
 * REX_LINK_BUTTON,
 * REX_LINK,
 * REX_LINK_ID,
 * REX_LINKLIST_BUTTON,
 * REX_LINKLIST
 *
 * @package redaxo4
 * @version $Id: class.rex_var_link.inc.php,v 1.7 2008/04/13 14:37:47 kills Exp $
 */

class rex_var_link extends rex_var
{
  // --------------------------------- Actions

  function getACRequestValues($REX_ACTION)
  {
    $values     = rex_request('LINK', 'array');
    $listvalues = rex_request('LINKLIST', 'array');
    for ($i = 1; $i < 11; $i++)
    {
      $link     = isset($values[$i]) ? stripslashes($values[$i]) : '';
      $linklist = isset($listvalues[$i]) ? stripslashes($listvalues[$i]) : '';

      $REX_ACTION['LINK'][$i] = $link;
      $REX_ACTION['LINKLIST'][$i] = $linklist;
    }
    return $REX_ACTION;
  }

  function getACDatabaseValues($REX_ACTION, & $sql)
  {
    for ($i = 1; $i < 11; $i++)
    {
      $REX_ACTION['LINK'][$i] = $this->getValue($sql, 'link'. $i);
      $REX_ACTION['LINKLIST'][$i] = $this->getValue($sql, 'linklist'. $i);
    }

    return $REX_ACTION;
  }

  function setACValues(& $sql, $REX_ACTION, $escape = false)
  {
    global $REX;

    for ($i = 1; $i < 11; $i++)
    {
      $this->setValue($sql, 'link'. $i, $REX_ACTION['LINK'][$i], $escape);
      $this->setValue($sql, 'linklist'. $i, $REX_ACTION['LINKLIST'][$i], $escape);
    }
  }

  // --------------------------------- Output

  function getBEOutput(& $sql, $content)
  {
    return $this->getOutput($sql, $content);
  }

  function getBEInput(& $sql, $content)
  {
    $content = $this->getOutput($sql, $content);
    $content = $this->matchLinkButton($sql, $content);
    $content = $this->matchLinkListButton($sql, $content);

    return $content;
  }

  function getOutput(& $sql, $content)
  {
    $content = $this->matchLinkList($sql, $content);
    $content = $this->matchLink($sql, $content);
    $content = $this->matchLinkId($sql, $content);

    return $content;
  }

  /**
   * @see rex_var::handleDefaultParam
   */
  function handleDefaultParam($varname, $args, $name, $value)
  {
    switch($name)
    {
      case '1' :
      case 'category' :
        $args['category'] = (int) $value;
        break;
    }
    return parent::handleDefaultParam($varname, $args, $name, $value);
  }

  /**
   * Button für die Eingabe
   */
  function matchLinkButton(& $sql, $content)
  {
  	global $REX;

  	$def_category = '';
  	$article_id = rex_request('article_id', 'int');
  	if($article_id != 0)
  	{
  		$art = OOArticle::getArticleById($article_id);
  		$def_category = $art->getCategoryId();
  	}

    $var = 'REX_LINK_BUTTON';
    $matches = $this->getVarParams($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id, $args) = $match;

      if ($id < 11 && $id > 0)
      {
        if(isset($args['category']))
        {
          $category = $args['category'];
          unset($args['category']);
        }

      	// Wenn vom Programmierer keine Kategorie vorgegeben wurde,
      	// die Linkmap mit der aktuellen Kategorie öffnen
	      if($category == '') $category = $def_category;

        $replace = $this->getLinkButton($id, $this->getValue($sql, 'link' . $id), $category, $args);
        $replace = $this->handleGlobalWidgetParams($var, $args, $replace);
        $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
      }
    }

    return $content;
  }

  /**
   * Button für die Eingabe
   */
  function matchLinkListButton(& $sql, $content)
  {
    $var = 'REX_LINKLIST_BUTTON';
    $matches = $this->getVarParams($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id, $args) = $match;

      if ($id < 11 && $id > 0)
      {
        if(isset($args['category']))
        {
          $category = $args['category'];
          unset($args['category']);
        }

        $replace = $this->getLinklistButton($id, $this->getValue($sql, 'linklist' . $id), $category);
        $replace = $this->handleGlobalWidgetParams($var, $args, $replace);
        $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
      }
    }

    return $content;
  }

  /**
   * Wert für die Ausgabe
   */
  function matchLink(& $sql, $content)
  {
    $var = 'REX_LINK';
    $matches = $this->getVarParams($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id, $args) = $match;

      if ($id > 0 && $id < 11)
      {
      	$replace = '';
      	if ($this->getValue($sql, 'link' . $id) != "")
      		$replace = rex_getUrl($this->getValue($sql, 'link' . $id));

        $replace = $this->handleGlobalVarParams($var, $args, $replace);
        $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
      }
    }

    return $content;
  }

  /**
   * Wert für die Ausgabe
   */
  function matchLinkId(& $sql, $content)
  {
    $var = 'REX_LINK_ID';
    $matches = $this->getVarParams($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id, $args) = $match;

      if ($id > 0 && $id < 11)
      {
        $replace = $this->getValue($sql, 'link' . $id);
        $replace = $this->handleGlobalVarParams($var, $args, $replace);
        $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
      }
    }

    return $content;
  }

  /**
   * Wert für die Ausgabe
   */
  function matchLinkList(& $sql, $content)
  {
    $var = 'REX_LINKLIST';
    $matches = $this->getVarParams($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id, $args) = $match;

      if ($id > 0 && $id < 11)
      {
        $replace = $this->getValue($sql, 'linklist' . $id);
        $replace = $this->handleGlobalVarParams($var, $args, $replace);
        $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
      }
    }

    return $content;
  }

  /**
   * Gibt das Button Template zurück
   */
  function getLinkButton($id, $article_id, $category = '')
  {
    global $REX;

    $art_name = '';
    $clang = '';
    $art = OOArticle :: getArticleById($article_id);

    // Falls ein Artikel vorausgewählt ist, dessen Namen anzeigen und beim öffnen der Linkmap dessen Kategorie anzeigen
    if (OOArticle :: isValid($art))
    {
      $art_name = $art->getName();
			$category = $art->getCategoryId();
    }

    $open_params = '&clang=' . $REX['CUR_CLANG'];
    if ($category != '')
      $open_params .= '&category_id=' . $category;

    $media = '
	<div class="rex-wdgt">
		<div class="rex-wdgt-lnk">
      <p class="rex-wdgt-fld">
  			<input type="hidden" name="LINK[' . $id . ']" id="LINK_' . $id . '" value="'. $article_id .'" />
  			<input type="text" size="30" name="LINK_NAME[' . $id . ']" value="' . $art_name . '" id="LINK_' . $id . '_NAME" readonly="readonly" />
		  </p>
      <p class="rex-wdgt-icons">
       	<a href="#" onclick="openLinkMap(\'LINK_' . $id . '\', \'' . $open_params . '\');return false;"'. rex_tabindex() .'><img src="media/file_open.gif" width="16" height="16" alt="Open Linkmap" title="Open Linkmap" /></a>
 	  		<a href="#" onclick="deleteREXLink(' . $id . ');return false;"'. rex_tabindex() .'><img src="media/file_del.gif" width="16" height="16" title="Remove Selection" alt="Remove Selection" /></a>
 		  </p>
 		  <div class="rex-clearer"></div>
 		</div>
 	</div>';

    return $media;
  }

  /**
   * Gibt das ListButton Template zurück
   */
  function getLinklistButton($id, $value, $category = '')
  {
    global $REX;

    $open_params = '&clang=' . $REX['CUR_CLANG'];
    if ($category != '')
      $open_params .= '&category_id=' . $category;

    $options = '';
    $linklistarray = explode(',', $value);
    if (is_array($linklistarray))
    {
      foreach ($linklistarray as $link)
      {
        if ($link != '')
        {
		  $article = OOArticle::getArticleById($link);
          $options .= '<option value="' . $link . '">' . $article->getName() . '</option>';
        }
      }
    }

    $link = '
  <div class="rex-wdgt">
    <div class="rex-wdgt-mdlst">
      <input type="hidden" name="LINKLIST['. $id .']" id="REX_LINKLIST_'. $id .'" value="'. $value .'" />
      <p class="rex-wdgt-fld">
        <select name="LINKLIST_SELECT[' . $id . ']" id="REX_LINKLIST_SELECT_' . $id . '" size="8"'. rex_tabindex() .'>
          ' . $options . '
        </select>
      </p>
      <p class="rex-wdgt-icons">
        <a href="#" onclick="moveREXLinklist(' . $id . ',\'top\');return false;"'. rex_tabindex() .'><img src="media/file_top.gif" width="16" height="16" title="Move Selected Item Up To Top" alt="Move Selected Item Up To Top" /></a>
        <a href="#" onclick="openREXLinklist(' . $id . ', \'' . $open_params . '\');return false;"'. rex_tabindex() .'><img src="media/file_open.gif" width="16" height="16" title="Open Mediapool" alt="Open Mediapool" /></a>
        <br />
        <a href="#" onclick="moveREXLinklist(' . $id . ',\'up\');return false;"'. rex_tabindex() .'><img src="media/file_up.gif" width="16" height="16" title="Move Selected Item Upwards" alt="Move Selected Item Upwards" /></a>
   		  <a href="#" onclick="deleteREXLinklist(' . $id . ');return false;"'. rex_tabindex() .'><img src="media/file_del.gif" width="16" height="16" title="Remove Selection" alt="Remove Selection" /></a>
        <br />
        <a href="#" onclick="moveREXLinklist(' . $id . ',\'down\');return false;"'. rex_tabindex() .'><img src="media/file_down.gif" width="16" height="16" title="Move Selected Item Downwards" alt="Move Selected Item Downwards" /></a>
        <br />
        <a href="#" onclick="moveREXLinklist(' . $id . ',\'bottom\');return false;"'. rex_tabindex() .'><img src="media/file_bottom.gif" width="16" height="16" title="Move Selected Item Down To Bottom" alt="Move Selected Item Down To Bottom" /></a>
      </p>
      <div class="rex-clearer"></div>
    </div>
  </div>
    ';

    return $link;
  }
}
?>