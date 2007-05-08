<?php

/**
 * REX_LINK_BUTTON,
 * REX_LINKLIST_BUTTON,
 * REX_LINKLIST
 * 
 * @package redaxo3
 * @version $Id$
 */

class rex_var_link extends rex_var
{
  // --------------------------------- Actions

  function getACRequestValues($REX_ACTION)
  {
    $values = rex_request('LINK', 'array');
    for ($i = 1; $i < 11; $i++)
    {
      if (!isset ($values[$i]))
        $values[$i] = '';

      $REX_ACTION['LINK'][$i] = stripslashes($values[$i]);
    }
    return $REX_ACTION;
  }

  function setACValues(& $sql, $REX_ACTION, $escape = false)
  {
    global $REX;
    for ($i = 1; $i < 11; $i++)
    {
      if ($escape)
        $this->setValue($sql, 'link'. $i, addslashes($REX_ACTION['LINK'][$i]));
      else
        $this->setValue($sql, 'link'. $i, $REX_ACTION['LINK'][$i]);
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

  function getInputParams($content, $varname)
  {
    $matches = array ();
    $id = '';
    $category = '';

    $match = $this->matchVar($content, $varname);
    foreach ($match as $param_str)
    {
      $params = $this->splitString($param_str);

      foreach ($params as $name => $value)
      {
        switch ($name)
        {
          case '0' :
          case 'id' :
            $id = (int) $value;
            break;

          case '1' :
          case 'category' :
            $category = (int) $value;
            break;
        }
      }

      $matches[] = array (
        $param_str,
        $id,
        $category
      );
    }

    return $matches;
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
    $matches = $this->getInputParams($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id, $category) = $match;
      
      if ($id < 11 && $id > 0)
      {
      	// Wenn vom Programmierer keine Kategorie vorgegeben wurde,
      	// die Linkmap mit der aktuellen Kategorie öffnen
	      if($category == '') $category = $def_category;
	      
        $replace = $this->getLinkButton($id, $this->getValue($sql, 'link' . $id), $category);
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
    $matches = $this->getInputParams($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id, $category) = $match;

      if ($id < 11 && $id > 0)
      {
        $replace = $this->getLinklistButton($id, $this->getValue($sql, 'linklist' . $id), $category);
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
    $matches = $this->getOutputParam($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id) = $match;

      if ($id > 0 && $id < 11)
      {
        $replace = rex_getUrl($this->getValue($sql, 'link' . $id));
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
    $matches = $this->getOutputParam($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id) = $match;

      if ($id > 0 && $id < 11)
      {
        $replace = $this->getValue($sql, 'link' . $id);
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
    $matches = $this->getOutputParam($content, $var);
    foreach ($matches as $match)
    {
      list ($param_str, $id) = $match;

      if ($id > 0 && $id < 11)
      {
        $replace = $this->getValue($sql, 'linklist' . $id);
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
			<p>
    			<input type="hidden" name="LINK[' . $id . ']" id="LINK_' . $id . '" value="REX_LINK_ID[' . $id . ']" />
    			<input type="text" size="30" name="LINK_NAME[' . $id . ']" value="' . $art_name . '" id="LINK_' . $id . '_NAME" readonly="readonly" />
    			<a href="#" onclick="openLinkMap(\'LINK_' . $id . '\', \'' . $open_params . '\');return false;"'. rex_tabindex() .'><img src="pics/file_open.gif" width="16" height="16" alt="Open Linkmap" title="Open Linkmap" /></a>
   				<a href="#" onclick="deleteREXLink(' . $id . ');return false;"'. rex_tabindex() .'><img src="pics/file_del.gif" width="16" height="16" title="Remove Selection" alt="Remove Selection" /></a>
   			</p>
   		</div>
   	</div>';

    return $media;
  }

  /**
   * Gibt das ListButton Template zurück
   * TODO: komplett überarbeiten
   */
  function getLinklistButton($id, $article_id, $category = '')
  {
    return "";
  }
}
?>