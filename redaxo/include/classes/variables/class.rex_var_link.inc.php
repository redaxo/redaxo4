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
      list ($param_str, $args) = $match;
      list ($id, $args) = $this->extractArg('id', $args, 0);
      
      if ($id < 11 && $id > 0)
      {
        // Wenn vom Programmierer keine Kategorie vorgegeben wurde,
        // die Linkmap mit der aktuellen Kategorie öffnen
      	list ($category, $args) = $this->extractArg('category', $args, $def_category);

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
      list ($param_str, $args) = $match;
      list ($id, $args) = $this->extractArg('id', $args, 0);
      
      if ($id < 11 && $id > 0)
      {
        list ($category, $args) = $this->extractArg('category', $args, 0);

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
      list ($param_str, $args) = $match;
      list ($id, $args) = $this->extractArg('id', $args, 0);
      
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
      list ($param_str, $args) = $match;
      list ($id, $args) = $this->extractArg('id', $args, 0);
      
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
      list ($param_str, $args) = $match;
      list ($id, $args) = $this->extractArg('id', $args, 0);
      
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
    global $REX, $I18N;

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
	<div class="rex-widget">
		<div class="rex-widget-link">
      <p class="rex-widget-field">
  			<input type="hidden" name="LINK[' . $id . ']" id="LINK_' . $id . '" value="'. $article_id .'" />
  			<input type="text" size="30" name="LINK_NAME[' . $id . ']" value="' . $art_name . '" id="LINK_' . $id . '_NAME" readonly="readonly" />
		  </p>
      <p class="rex-widget-icons">
       	<a href="#" class="rex-icon-file-open" onclick="openLinkMap(\'LINK_' . $id . '\', \'' . $open_params . '\');return false;"'. rex_tabindex() .'><img src="media/file_open.gif" width="16" height="16" alt="'. $I18N->msg('var_link_open') .'" title="'. $I18N->msg('var_link_open') .'" /></a>
 	  		<a href="#" class="rex-icon-file-delete" onclick="deleteREXLink(' . $id . ');return false;"'. rex_tabindex() .'><img src="media/file_del.gif" width="16" height="16" title="'. $I18N->msg('var_link_delete') .'" alt="'. $I18N->msg('var_link_delete') .'" /></a>
 		  </p>
 		</div>
 	</div>
 	<div class="rex-clearer"></div>';

    return $media;
  }

  /**
   * Gibt das ListButton Template zurück
   */
  function getLinklistButton($id, $value, $category = '')
  {
    global $REX, $I18N;

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
  <div class="rex-widget">
    <div class="rex-widget-linklist">
      <input type="hidden" name="LINKLIST['. $id .']" id="REX_LINKLIST_'. $id .'" value="'. $value .'" />
      <p class="rex-widget-field">
        <select name="LINKLIST_SELECT[' . $id . ']" id="REX_LINKLIST_SELECT_' . $id . '" size="8"'. rex_tabindex() .'>
          ' . $options . '
        </select>
      </p>
      <p class="rex-widget-icons">
        <a href="#" class="rex-icon-file-top" onclick="moveREXLinklist(' . $id . ',\'top\');return false;"'. rex_tabindex() .'><img src="media/file_top.gif" width="16" height="16" title="'. $I18N->msg('var_linklist_move_top') .'" alt="'. $I18N->msg('var_linklist_move_top') .'" /></a>
        <a href="#" class="rex-icon-file-open" onclick="openREXLinklist(' . $id . ', \'' . $open_params . '\');return false;"'. rex_tabindex() .'><img src="media/file_open.gif" width="16" height="16" title="'. $I18N->msg('var_link_open') .'" alt="'. $I18N->msg('var_link_open') .'" /></a><br />
        <a href="#" class="rex-icon-file-up" onclick="moveREXLinklist(' . $id . ',\'up\');return false;"'. rex_tabindex() .'><img src="media/file_up.gif" width="16" height="16" title="'. $I18N->msg('var_linklist_move_up') .'" alt="'. $I18N->msg('var_linklist_move_up') .'" /></a>
   		  <a href="#" class="rex-icon-file-delete" onclick="deleteREXLinklist(' . $id . ');return false;"'. rex_tabindex() .'><img src="media/file_del.gif" width="16" height="16" title="'. $I18N->msg('var_link_delete') .'" alt="'. $I18N->msg('var_link_delete') .'" /></a><br />
        <a href="#" class="rex-icon-file-down" onclick="moveREXLinklist(' . $id . ',\'down\');return false;"'. rex_tabindex() .'><img src="media/file_down.gif" width="16" height="16" title="'. $I18N->msg('var_linklist_move_down') .'" alt="'. $I18N->msg('var_linklist_move_down') .'" /></a><br />
        <a href="#" class="rex-icon-file-bottom" onclick="moveREXLinklist(' . $id . ',\'bottom\');return false;"'. rex_tabindex() .'><img src="media/file_bottom.gif" width="16" height="16" title="'. $I18N->msg('var_linklist_move_bottom') .'" alt="'. $I18N->msg('var_linklist_move_bottom') .'" /></a>
      </p>
    </div>
  </div>
 	<div class="rex-clearer"></div>
    ';

    return $link;
  }
}
?>