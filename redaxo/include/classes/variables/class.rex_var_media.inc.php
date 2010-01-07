<?php

/**
 * REX_FILE[1],
 * REX_FILELIST[1],
 * REX_FILE_BUTTON[1],
 * REX_FILELIST_BUTTON[1],
 * REX_MEDIA[1],
 * REX_MEDIALIST[1],
 * REX_MEDIA_BUTTON[1],
 * REX_MEDIALIST_BUTTON[1]
 *
 * Alle Variablen die mit REX_FILE beginnnen sind als deprecated anzusehen!
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_var_media extends rex_var
{
  // --------------------------------- Actions

  /*public*/ function getACRequestValues($REX_ACTION)
  {
    $values     = rex_request('MEDIA', 'array');
    $listvalues = rex_request('MEDIALIST', 'array');

    for ($i = 1; $i < 11; $i++)
    {
      $media     = isset($values[$i]) ? stripslashes($values[$i]) : '';
      $medialist = isset($listvalues[$i]) ? stripslashes($listvalues[$i]) : '';

      $REX_ACTION['MEDIA'][$i]     = $media;
      $REX_ACTION['MEDIALIST'][$i] = $medialist;
    }

    return $REX_ACTION;
  }

  /*public*/ function getACDatabaseValues($REX_ACTION, & $sql)
  {
    for ($i = 1; $i < 11; $i++)
    {
      $REX_ACTION['MEDIA'][$i]     = $this->getValue($sql, 'file'. $i);
      $REX_ACTION['MEDIALIST'][$i] = $this->getValue($sql, 'filelist'. $i);
    }

    return $REX_ACTION;
  }

  /*public*/ function setACValues(& $sql, $REX_ACTION, $escape = false)
  {
    global $REX;

    for ($i = 1; $i < 11; $i++)
    {
      $this->setValue($sql, 'file'. $i    , $REX_ACTION['MEDIA'][$i]    , $escape);
      $this->setValue($sql, 'filelist'. $i, $REX_ACTION['MEDIALIST'][$i], $escape);
    }
  }

  // --------------------------------- Output

  /*public*/ function getBEInput(& $sql, $content)
  {
    $content = $this->matchMediaButton($sql, $content);
    $content = $this->matchMediaListButton($sql, $content);
    $content = $this->getOutput($sql, $content);
    return $content;
  }

  /*public*/ function getBEOutput(& $sql, $content)
  {
    $content = $this->getOutput($sql, $content);
    return $content;
  }

  /**
   * Ersetzt die Value Platzhalter
   */
  /*private*/ function getOutput(& $sql, $content)
  {
    $content = $this->matchMedia($sql, $content);
    $content = $this->matchMediaList($sql, $content);
    return $content;
  }

  /*private*/ function handleDefaultParam($varname, $args, $name, $value)
  {
    switch($name)
    {
      case '1' :
      case 'category' :
        $args['category'] = (int) $value;
        break;
      case 'types' :
        $args[$name] = (string) $value;
        break;
      case 'preview' :
        $args[$name] = (boolean) $value;
        break;
      case 'mimetype' :
        $args[$name] = (string) $value;
        break;
    }
    return parent::handleDefaultParam($varname, $args, $name, $value);
  }

  /**
   * MediaButton für die Eingabe
   */
  /*private*/ function matchMediaButton(& $sql, $content)
  {
    $vars = array (
      'REX_FILE_BUTTON',
      'REX_MEDIA_BUTTON'
    );
    foreach ($vars as $var)
    {
      $matches = $this->getVarParams($content, $var);
      foreach ($matches as $match)
      {
        list ($param_str, $args) = $match;
        list ($id, $args) = $this->extractArg('id', $args, 0);
        
        if ($id < 11 && $id > 0)
        {
          list ($category, $args) = $this->extractArg('category', $args, '');
          
          $replace = $this->getMediaButton($id, $category, $args);
          $replace = $this->handleGlobalWidgetParams($var, $args, $replace);
          $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
        }
      }
    }

    return $content;
  }

  /**
   * MediaListButton für die Eingabe
   */
  /*private*/ function matchMediaListButton(& $sql, $content)
  {
    $vars = array (
      'REX_FILELIST_BUTTON',
      'REX_MEDIALIST_BUTTON'
    );
    foreach ($vars as $var)
    {
      $matches = $this->getVarParams($content, $var);
      foreach ($matches as $match)
      {
        list ($param_str, $args) = $match;
        list ($id, $args) = $this->extractArg('id', $args, 0);
        
        if ($id < 11 && $id > 0)
        {
        	$category = '';
          if(isset($args['category']))
          {
            $category = $args['category'];
            unset($args['category']);
          }

          $replace = $this->getMedialistButton($id, $this->getValue($sql, 'filelist' . $id), $category, $args);
          $replace = $this->handleGlobalWidgetParams($var, $args, $replace);
          $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
        }
      }
    }

    return $content;
  }

  /**
   * Wert für die Ausgabe
   */
  /*private*/ function matchMedia(& $sql, $content)
  {
    $vars = array (
      'REX_FILE',
      'REX_MEDIA'
    );
    foreach ($vars as $var)
    {
      $matches = $this->getVarParams($content, $var);
      foreach ($matches as $match)
      {
        list ($param_str, $args) = $match;
        list ($id, $args) = $this->extractArg('id', $args, 0);
        
        if ($id > 0 && $id < 11)
        {
          // Mimetype ausgeben
          if(isset($args['mimetype']))
          {
            $OOM = OOMedia::getMediaByName($this->getValue($sql, 'file' . $id));
            if($OOM)
            {
              $replace = $OOM->getType();
            }
          }
          // "normale" ausgabe
          else
          {
            $replace = $this->getValue($sql, 'file' . $id);
          }

          $replace = $this->handleGlobalVarParams($var, $args, $replace);
          $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
        }
      }
    }
    return $content;
  }

  /**
   * Wert für die Ausgabe
   */
  /*private*/ function matchMediaList(& $sql, $content)
  {
    $vars = array (
      'REX_FILELIST',
      'REX_MEDIALIST'
    );
    foreach ($vars as $var)
    {
      $matches = $this->getVarParams($content, $var);
      foreach ($matches as $match)
      {
        list ($param_str, $args) = $match;
        list ($id, $args) = $this->extractArg('id', $args, 0);
        
        if ($id > 0 && $id < 11)
        {
          $replace = $this->getValue($sql, 'filelist' . $id);
          $replace = $this->handleGlobalVarParams($var, $args, $replace);
          $content = str_replace($var . '[' . $param_str . ']', $replace, $content);
        }
      }
    }
    return $content;
  }

  /**
   * Gibt das Button Template zurück
   */
  /*public static*/ function getMediaButton($id, $category = '', $args = array())
  {
    global $I18N;

    $open_params = '';
    if ($category != '')
    {
      $open_params .= '&amp;rex_file_category=' . $category;
    }

    foreach($args as $aname => $avalue)
    {
      $open_params .= '&amp;args['. urlencode($aname) .']='. urlencode($avalue);
    }

    $wdgtClass = 'rex-widget-media';
    if(isset($args['preview']) && $args['preview'] && OOAddon::isAvailable('image_resize'))
    {
      $wdgtClass .= ' rex-widget-preview';
    }

    $media = '
    <div class="rex-widget">
      <div class="'. $wdgtClass .'">
        <p class="rex-widget-field">
          <input type="text" size="30" name="MEDIA[' . $id . ']" value="REX_MEDIA[' . $id . ']" id="REX_MEDIA_' . $id . '" readonly="readonly" />
        </p>
        <p class="rex-widget-icons">
          <a href="#" class="rex-icon-file-open" onclick="openREXMedia(' . $id . ',\'' . $open_params . '\');return false;"'. rex_tabindex() .'><img src="media/file_open.gif" width="16" height="16" title="'. $I18N->msg('var_media_open') .'" alt="'. $I18N->msg('var_media_open') .'" /></a>
          <a href="#" class="rex-icon-file-add" onclick="addREXMedia(' . $id . ');return false;"'. rex_tabindex() .'><img src="media/file_add.gif" width="16" height="16" title="'. $I18N->msg('var_media_new') .'" alt="'. $I18N->msg('var_media_new') .'" /></a>
          <a href="#" class="rex-icon-file-delete" onclick="deleteREXMedia(' . $id . ');return false;"'. rex_tabindex() .'><img src="media/file_del.gif" width="16" height="16" title="'. $I18N->msg('var_media_remove') .'" alt="'. $I18N->msg('var_media_remove') .'" /></a>
        </p>
        <div class="rex-media-preview"></div>
      </div>
    </div>
		<div class="rex-clearer"></div>
    ';

    return $media;
  }

  /**
   * Gibt das ListButton Template zurück
   */
  /*public static*/ function getMedialistButton($id, $value, $category = '', $args = array())
  {
    global $I18N;

    $open_params = '';
    if ($category != '')
    {
      $open_params .= '&amp;rex_file_category=' . $category;
    }

    foreach($args as $aname => $avalue)
    {
      $open_params .= '&amp;args['. $aname .']='. urlencode($avalue);
    }

    $wdgtClass = 'rex-widget-medialist';
    if(isset($args['preview']) && $args['preview'] && OOAddon::isAvailable('image_resize'))
    {
      $wdgtClass .= ' rex-widget-preview';
    }

    $options = '';
    $medialistarray = explode(',', $value);
    if (is_array($medialistarray))
    {
      foreach ($medialistarray as $file)
      {
        if ($file != '')
        {
          $options .= '<option value="' . $file . '">' . $file . '</option>';
        }
      }
    }

    $media = '
    <div class="rex-widget">
      <div class="'. $wdgtClass .'">
        <input type="hidden" name="MEDIALIST['. $id .']" id="REX_MEDIALIST_'. $id .'" value="'. $value .'" />
        <p class="rex-widget-field">
          <select name="MEDIALIST_SELECT[' . $id . ']" id="REX_MEDIALIST_SELECT_' . $id . '" size="8"'. rex_tabindex() .'>
            ' . $options . '
          </select>
        </p>
        <p class="rex-widget-icons">
          <a href="#" class="rex-icon-file-top" onclick="moveREXMedialist(' . $id . ',\'top\');return false;"'. rex_tabindex() .'><img src="media/file_top.gif" width="16" height="16" title="'. $I18N->msg('var_medialist_move_top') .'" alt="'. $I18N->msg('var_medialist_move_top') .'" /></a>
          <a href="#" class="rex-icon-file-open" onclick="openREXMedialist(' . $id . ',\'' . $open_params . '\');return false;"'. rex_tabindex() .'><img src="media/file_open.gif" width="16" height="16" title="'. $I18N->msg('var_media_open') .'" alt="'. $I18N->msg('var_media_open') .'" /></a><br />
          <a href="#" class="rex-icon-file-up" onclick="moveREXMedialist(' . $id . ',\'up\');return false;"'. rex_tabindex() .'><img src="media/file_up.gif" width="16" height="16" title="'. $I18N->msg('var_medialist_move_up') .'" alt="'. $I18N->msg('var_medialist_move_top') .'" /></a>
          <a href="#" class="rex-icon-file-add" onclick="addREXMedialist('. $id .');return false;"'. rex_tabindex() .'><img src="media/file_add.gif" width="16" height="16" title="'. $I18N->msg('var_media_new') .'" alt="'. $I18N->msg('var_media_new') .'" /></a><br />
          <a href="#" class="rex-icon-file-down" onclick="moveREXMedialist(' . $id . ',\'down\');return false;"'. rex_tabindex() .'><img src="media/file_down.gif" width="16" height="16" title="'. $I18N->msg('var_medialist_move_down') .'" alt="'. $I18N->msg('var_medialist_move_down') .'" /></a>
          <a href="#" class="rex-icon-file-delete" onclick="deleteREXMedialist(' . $id . ');return false;"'. rex_tabindex() .'><img src="media/file_del.gif" width="16" height="16" title="'. $I18N->msg('var_media_remove') .'" alt="'. $I18N->msg('var_media_remove') .'" /></a><br />
          <a href="#" class="rex-icon-file-bottom" onclick="moveREXMedialist(' . $id . ',\'bottom\');return false;"'. rex_tabindex() .'><img src="media/file_bottom.gif" width="16" height="16" title="'. $I18N->msg('var_medialist_move_bottom') .'" alt="'. $I18N->msg('var_medialist_move_bottom') .'" /></a>
        </p>
        <div class="rex-media-preview"></div>
      </div>
    </div>
	 	<div class="rex-clearer"></div>
    ';

    return $media;
  }

}