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


if (!isset($REX['FILEPERM'])) 
{
  $REX['FILEPERM'] = octdec(664); // oktaler wert
}
if (!isset($REX['DIRPERM'])) 
{
  $REX['DIRPERM'] = octdec(775); // oktaler wert
}


/**
 * für REDAXO 4.0.x
 */
if (!function_exists('rex_info'))
{
function rex_info($msg)
{
  //return '<p class="rex-warning" style="padding:7px; width:756px; background-color:#D2EFD9; color:#107C6C;">' . $msg . '</p>';
  return '<p class="rex-warning"><span>' . $msg . '</span></p> ';
}
} // End function_exists


/**
 * für REDAXO 4.0.x
 */
if (!function_exists('rex_get_file_contents'))
{
  function rex_get_file_contents($path)
  {
    return file_get_contents($path);
  }
} // end function_exists


/**
 * für REDAXO 4.0.x
 */
if (!function_exists('rex_put_file_contents'))
{
function rex_put_file_contents($path, $content)
{
global $REX;

  $writtenBytes = file_put_contents($path, $content);
  @chmod($path, $REX['FILEPERM']);

  return $writtenBytes;
}
} // end function_exists


/**
 * für REDAXO 4.0.x
 */
if (!function_exists('rex_replace_dynamic_contents'))
{
function rex_replace_dynamic_contents($path, $content)
{
  if($fcontent = rex_get_file_contents($path))
  {
    $content = "// --- DYN\n". trim($content) ."\n// --- /DYN";
    $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", $content, $fcontent);
    return rex_put_file_contents($path, $fcontent);
  }
  return false;
}
} // End function_exists


/**
 * für REDAXO 4.1.x
 */
if ($REX['REDAXO'] and !function_exists('rex_copyDir'))
{
	function rex_copyDir($srcdir, $dstdir, $startdir = "")
	{
	  global $REX;
	  
	  $debug = FALSE;
	  $state = TRUE;
	  
	  if(!is_dir($dstdir))
	  {
		$dir = '';
		foreach(explode(DIRECTORY_SEPARATOR, $dstdir) as $dirPart)
		{
		  $dir .= $dirPart . DIRECTORY_SEPARATOR;
		  if(strpos($startdir,$dir) !== 0 && !is_dir($dir))
		  {
			if($debug)
			  echo "Create dir '$dir'<br />\n";
			  
			mkdir($dir);
			chmod($dir, $REX['DIRPERM']);
		  }
		}
	  }
	  
	  if($curdir = opendir($srcdir))
	  {
		while($file = readdir($curdir))
		{
		  if($file != '.' && $file != '..' && $file != '.svn')
		  {
			$srcfile = $srcdir . DIRECTORY_SEPARATOR . $file;    
			$dstfile = $dstdir . DIRECTORY_SEPARATOR . $file;    
			if(is_file($srcfile))
			{
			  $isNewer = TRUE;
			  if(is_file($dstfile))
			  {
				$isNewer = (filemtime($srcfile) - filemtime($dstfile)) > 0;
			  }
				
			  if($isNewer)
			  {
				if($debug)
				  echo "Copying '$srcfile' to '$dstfile'...";
				if(copy($srcfile, $dstfile))
				{
				  touch($dstfile, filemtime($srcfile));
				  chmod($dstfile, $REX['FILEPERM']);
				  if($debug)
					echo "OK<br />\n";
				}
				else
				{
				  if($debug)
				   echo "Error: File '$srcfile' could not be copied!<br />\n";
				  return FALSE;
				}
			  }
			}
			else if(is_dir($srcfile))
			{
			  $state = rex_copyDir($srcfile, $dstfile, $startdir) && $state;
			}
		  }
		}
		closedir($curdir);
	  }
	  return $state;
	}
} // End function_exists


/**
 * String Highlight für ältere REDAXO-Versionen
 */ 
if (!function_exists('rex_highlight_string'))
{
function rex_highlight_string($string, $return = false)
{
  $s = '<p class="rex-code">'. highlight_string($string, true) .'</p>';
  if($return)
  {
    return $s;
  }
  echo $s; 
}
} // End function_exists


/**
 * Schreibberechtigung prüfen
 */
if (!function_exists('tinymce_is_writable'))
{
function tinymce_is_writable($path)
{
  if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
    return tinymce_is_writable($path.uniqid(mt_rand()).'.tmp');
  else if (is_dir($path))
    return tinymce_is_writable($path.'/'.uniqid(mt_rand()).'.tmp');
  // check tmp file for read/write capabilities
  $rm = file_exists($path);
  $f = @fopen($path, 'a');
  if ($f===false)
    return false;
  fclose($f);
  if (!$rm)
    unlink($path);
  return true;
}
} // End function_exists


/**
 * prüfen exclude Page/Subpage
 */
if (!function_exists('tinymce_exclude_page_subpage'))
{
function tinymce_exclude_page_subpage()
{
global $REX;

  if ($REX['ADDON']['tinymce']['excludecats'] <> '')
  {
    $exc = explode(',', $REX['ADDON']['tinymce']['excludecats']);
    foreach ($exc as $key => $val)
    {
      $exc[$key] = trim($val);
    }
    if (in_array(rex_request('page', 'string', ''), $exc))
      return true;
  }

  if ($REX['ADDON']['tinymce']['excludeids'] <> '')
  {
    $exc = explode(',', $REX['ADDON']['tinymce']['excludeids']);
    foreach ($exc as $key => $val)
    {
      $exc[$key] = trim($val);
    }
    if (in_array(rex_request('subpage', 'string', ''), $exc))
      return true;

    // evtl. vorhandener String in Url auch übergehen
    foreach ($exc as $key => $val)
    {
      if (strstr($_SERVER['REQUEST_URI'], $val))
      {
        return true;
      }
    }
  }

  return false;
}
} // End function_exists


/**
 * prüfen exclude Kategorie/Artikel-Id
 */
if (!function_exists('tinymce_exclude_cat_art'))
{
function tinymce_exclude_cat_art()
{
global $REX;

  if ($REX['ADDON']['tinymce']['excludecats'] <> '')
  {
    $artId = OOArticle::getArticleById($REX['ARTICLE_ID']);
    $exc = explode(',', $REX['ADDON']['tinymce']['excludecats']);
    foreach ($exc as $key => $val)
    {
      $exc[$key] = trim($val);
    }
    if (in_array($artId->getValue("category_id"), $exc))
      return true;
  }

  if ($REX['ADDON']['tinymce']['excludeids'] <> '')
  {
    $exc = explode(',', $REX['ADDON']['tinymce']['excludeids']);
    foreach ($exc as $key => $val)
    {
      $exc[$key] = trim($val);
    }
    if (in_array($REX['ARTICLE_ID'], $exc))
      return true;
  }

  return false;
}
} // End function_exists


/**
 * TinyMCE-Script im Head-Bereich einbinden
 */
if (!function_exists('tinymce_output_filter'))
{
function tinymce_output_filter($content)
{
  global $REX;

  // Wenn keine Textarea mit Klasse tinyMCEEditor vorhanden ist dann nichts machen
  if (strpos($content['subject'], 'tinyMCEEditor') === false) return $content['subject'];

  // Exclude für Backend und Frontend prüfen
  if ($REX['REDAXO'] and tinymce_exclude_page_subpage()) return $content['subject'];
  if (!$REX['REDAXO'] and tinymce_exclude_cat_art()) return $content['subject'];

  // TinyMCE einbinden
  if ($REX['REDAXO'])
  {
    $rp = 'redaxo/index.php';
  }
  else
  {
    $rp = $REX['FRONTEND_FILE'];
  }

  $search = '</head>';
  $replace  = "\n\n" . '  <!-- Addon TinyMCE -->';
  if ($REX['VERSION'] . $REX['SUBVERSION'] <= '40')
  {
    $replace .= "\n" .'<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>';
  }  
  $replace .= "\n" . '  <script src="' . $REX['HTDOCS_PATH'] . $rp . '?tinymceinit=true&amp;clang=' . $REX['CUR_CLANG'] . '" type="text/javascript"></script>' . "\n";
  $replace .= "\n" . '</head>' . "\n";

  return str_replace($search, $replace, $content['subject']);
}
} // End function_exists


/**
 * TinyMCE-Init-Script ausgeben
 */
if (!function_exists('tinymce_generate_script'))
{
function tinymce_generate_script()
{
global $REX;

  ob_end_clean();
  ob_end_clean();
  header("Content-type: application/javascript");

  echo '/**
 * Addon TinyMCE Version '.$REX['ADDON']['version']['tinymce'].'
 */

jQuery.noConflict();

// TinyMCE jquery interface
// ------------------------------------------------------------
';
  echo rex_get_file_contents($REX['HTDOCS_PATH'] . '/files/addons/tinymce/tiny_mce/jquery.tinymce.js');
  echo "\n\n";

  $scriptout = rex_get_file_contents($REX['HTDOCS_PATH'] . '/files/addons/tinymce/rex.tiny.filebrowser.js');
  echo tinymce_replace_vars($scriptout);
  echo "\n\n";

  echo '// Init TinyMCE-Profiles
// ------------------------------------------------------------
jQuery(document).ready(function($) {';

  $table = $REX['TABLE_PREFIX'] . 'tinymce_profiles';

  $query = 'SELECT id, name, description, configuration FROM ' . $table . ' WHERE ptype = 0 ORDER BY name ASC ';
  $sql = new rex_sql;
  $sql->debugsql = 0;
  $sql->setQuery($query);
  $defaulttiny = '';
  if ($sql->getRows() > 0)
  {
    for ($i = 0; $i < $sql->getRows(); $i ++)
    {
      $configout = trim($sql->getValue('configuration'));
      if ($configout[strlen($configout)-1] === ',') // remove last komma!
      {
        $configout[strlen($configout)-1] = ' ';
      }

		$configout = tinymce_replace_vars($configout);

      if ($sql->getValue('id') === '2') // default for class="tinyMCEEditor"
      {
        $defaulttiny = "\n\n\n// " . $sql->getValue('description');
        $defaulttiny .= "\n// ------------------------------------------------------------";
        $defaulttiny .= "\n" . '$(\'textarea.tinyMCEEditor\').tinymce({';
        $defaulttiny .= "\n" . $configout;
        $defaulttiny .= "\n});";
      }

      echo "\n\n\n// " . $sql->getValue('description');
      echo "\n// ------------------------------------------------------------";
      echo "\n" . '$(\'textarea.tinyMCEEditor-'.$sql->getValue('name').'\').tinymce({';
      echo "\n" . $configout;
      echo "\n});";
      $sql->next();
    }
  }
  else
  {
    $defaulttiny = 'alert("[Addon TinyMCE] - Error! No default Profile found!")';
  }
  echo $defaulttiny;
  echo '


}); // end document ready
';
  die;
}
} // End function_exists


/**
 * TinyMCE Script für Mediapool ausgeben
 */
if (!function_exists('tinymce_generate_mediascript'))
{
function tinymce_generate_mediascript()
{
global $REX;

  ob_end_clean();
  ob_end_clean();
  header("Content-type: application/javascript");

  echo '/**
 * Addon TinyMCE Version '.$REX['ADDON']['version']['tinymce'].'
 */

// TinyMCE Popup interface
// ------------------------------------------------------------
';
  echo rex_get_file_contents($REX['HTDOCS_PATH'] . '/files/addons/tinymce/tiny_mce/tiny_mce_popup.js');
  echo "\n\n";
  $scriptout = rex_get_file_contents($REX['HTDOCS_PATH'] . '/files/addons/tinymce/rex.mediapool.js');
  echo tinymce_replace_vars($scriptout);
  echo "\n\n";

  die;
}
} // End function_exists


/**
 * TinyMCE Script für Linkmap ausgeben
 */
if (!function_exists('tinymce_generate_linkscript'))
{
function tinymce_generate_linkscript()
{
global $REX;

  ob_end_clean();
  ob_end_clean();
  header("Content-type: application/javascript");

  echo '/**
 * Addon TinyMCE Version '.$REX['ADDON']['version']['tinymce'].'
 */

// TinyMCE Popup interface
// ------------------------------------------------------------
';
  echo rex_get_file_contents($REX['HTDOCS_PATH'] . '/files/addons/tinymce/tiny_mce/tiny_mce_popup.js');
  echo "\n\n";
  $scriptout = rex_get_file_contents($REX['HTDOCS_PATH'] . '/files/addons/tinymce/rex.linkmap.js');
  echo tinymce_replace_vars($scriptout);
  echo "\n\n";

  die;
}
} // End function_exists


/**
 * TinyMCE CSS ausgeben
 */
if (!function_exists('tinymce_generate_css'))
{
function tinymce_generate_css()
{
global $REX;

  ob_end_clean();
  ob_end_clean();

  $css = '';
  $table = $REX['TABLE_PREFIX'] . 'tinymce_profiles';

  $query = 'SELECT configuration FROM ' . $table . ' WHERE id = 1 AND ptype = 1 ';
  $sql = new rex_sql;
  $sql->debugsql=0;
  $sql->setQuery($query);
  if ($sql->getRows() > 0)
  {
    $css = $sql->getValue('configuration');
  }
  header("Content-type: text/css");
  echo $css;
  die;
}
} // End function_exists


/**
 * Bild ausgeben
 */
if (!function_exists('tinymce_generate_image'))
{
function tinymce_generate_image()
{
  global $REX;

  $tinymceimg = rex_request('tinymceimg', 'string', '');
  $file = $REX['MEDIAFOLDER'] . '/' . $tinymceimg;
  if (file_exists($file))
  {
    $lastModified = gmdate('r' );

    $file_extension = strtolower(substr(strrchr($tinymceimg, '.'), 1));
    switch ($file_extension)
    {
      case "gif": $ctype = "image/gif"; break;
      case "png": $ctype = "image/png"; break;
      case "jpeg": $ctype = "image/jpg"; break;
      case "jpg": $ctype = "image/jpg"; break;
    }

    header('Content-Type: ' . $ctype);
    header('Last-Modified: ' . $lastModified);
    //header('Content-Length: ' . $length);

    // caching clientseitig/proxyseitig erlauben
    header('Cache-Control: public');
    header("Pragma: public");
    header("Expires: 0");

    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastModified)
    {
      header('HTTP/1.1 304 Not Modified');
      exit();
    }

    readfile($file);
  }
  else
  {
    header('Cache-Control: false');
    header('Content-Type: image/');
    header('Content-Disposition: inline; filename=""');
    header('HTTP/1.0 404 Not Found');
    header("Status: 404 Not Found");
  }
  die;
}
} // End function_exists


/**
 * Output-Filter für Medienpool und Linkmap
 */
if (!function_exists('tinymce_opf_media_linkmap'))
{
function tinymce_opf_media_linkmap($params)
{
global $REX;
// Hinzufügen und übernehmen, Close Popup

  $content = $params['subject'];
  $page = rex_request('page', 'string');
  if ($page === 'medienpool')
  {
    $page = 'mediapool';
  }
  $oif = rex_request('opener_input_field', 'string', '');

  $search = $replace = array();

  // Medienpool anpassen
  if ($page === 'mediapool')
  {
    $search[0] = '</head>';
    $replace[0]  = "\n\n" . '  <!-- Addon TinyMCE -->';
    $replace[0] .= "\n" . '  <script src="' . $REX['HTDOCS_PATH'] . 'redaxo/index.php?tinymcemedia=true&amp;clang=' . $REX['CUR_CLANG'] . '&amp;opener_input_field=' . $oif . '" type="text/javascript"></script>' . "\n";
    $replace[0] .= "\n" . '</head>' . "\n";
    $search[1] = 'javascript:selectMedia(';
    $replace[1] = 'javascript:TinyMCE_selectMedia(';
    $search[2] = '<input type="hidden" name="page" value="' . $page . '" />';
    $replace[2] = $search[2] . "\n\n" . '<input type="hidden" name="tinymce" value="true" /> <!-- inserted by TinyMCE -->' . "\n";
    $search[3] = 'page=' . $page;
    $replace[3] = 'page=' . $page . '&amp;tinymce=true';	
    $search[4] = 'page=medienpool';
    $replace[4] = 'page=medienpool&amp;tinymce=true';	
    $search[5] = '<input type="hidden" name="page" value="medienpool" />';
    $replace[5] = $search[5] . "\n\n" . '<input type="hidden" name="tinymce" value="true" /> <!-- inserted by TinyMCE -->' . "\n";
  }

  // Linkmap anpassen
  if ($page === 'linkmap')
  {
    $search[0] = '</head>';
    $replace[0]  = "\n\n" . '  <!-- Addon TinyMCE -->';
    $replace[0] .= "\n" . '  <script src="' . $REX['HTDOCS_PATH'] . 'redaxo/index.php?tinymcelink=true&amp;clang=' . $REX['CUR_CLANG'] . '&amp;opener_input_field=' . $oif . '" type="text/javascript"></script>' . "\n";
    $replace[0] .= "\n" . '</head>' . "\n";
    $search[1] = 'javascript:insertLink(';
    $replace[1] = 'javascript:TinyMCE_insertLink(';
    $search[2] = '<input type="hidden" name="page" value="' . $page . '" />';
    $replace[2] = $search[2] . "\n\n" . '<input type="hidden" name="tinymce" value="true" /> <!-- inserted by TinyMCE -->' . "\n";
    $search[3] = 'page=' . $page;
    $replace[3] = 'page=' . $page . '&amp;tinymce=true';	
  }

  // Alles ersetzen
  return str_replace($search, $replace, $content);

}
} // End function_exists


/**
 * Extension-Point für Medienpool Button "Hinzufügen und übernehmen"
 */
if (!function_exists('tinymce_media_added'))
{
function tinymce_media_added($params)
{
global $REX;

  if (rex_request('saveandexit', 'string', '') <> '')
  {
    $scriptoutput = "\n\n" . '  <!-- Addon TinyMCE -->';
    $scriptoutput .= "\n" . '  <script src="' . $REX['HTDOCS_PATH'] . 'redaxo/index.php?tinymcemedia=true&amp;clang=' . $REX['CUR_CLANG'] . '" type="text/javascript"></script>' . "\n";
    $scriptoutput .= "\n\n";

    $scriptoutput .= "\n" . '<script type="text/javascript">';
    $scriptoutput .= "\n" . '//<![CDATA[';
    $scriptoutput .= "\n" . '    TinyMCE_selectMedia("'.$params['filename'].'", "'.$params['title'].'")';
    $scriptoutput .= "\n" . '//]]>';
    $scriptoutput .= "\n" . '</script>';
    echo $scriptoutput;
    die;
  }
}
} // End function_exists


/**
 * Variablen ersetzen
 */
if (!function_exists('tinymce_replace_vars'))
{
function tinymce_replace_vars($source)
{
global $REX;

  $clang = rex_request('clang', 'int', '0');
  $oif = rex_request('opener_input_field', 'string', '');

  $scriptout = str_replace('%HTDOCS_PATH%', $REX['HTDOCS_PATH'], $source);
  $scriptout = str_replace('%SERVER%', $REX['SERVER'], $scriptout);
  $scriptout = str_replace('%SERVERNAME%', $REX['SERVERNAME'], $scriptout);
  $scriptout = str_replace('%CLANG%', $REX['CUR_CLANG'], $scriptout);
  $scriptout = str_replace('%INCLUDE_PATH%', $REX['INCLUDE_PATH'], $scriptout);
  $scriptout = str_replace('%FRONTEND_PATH%', $REX['FRONTEND_PATH'], $scriptout);
  $scriptout = str_replace('%MEDIAFOLDER%', $REX['MEDIAFOLDER'], $scriptout);
  $scriptout = str_replace('%FRONTEND_FILE%', $REX['FRONTEND_FILE'], $scriptout);
  $scriptout = str_replace('%HTTP_HOST%', $_SERVER['HTTP_HOST'], $scriptout);
  $scriptout = str_replace('%OPENER_INPUT_FIELD%', $oif, $scriptout);
  if ($REX['VERSION'] . $REX['SUBVERSION'] < '42')
  {
    $scriptout = str_replace('%MEDIAPOOL%', 'medienpool', $scriptout);
  }
  else
  {
    $scriptout = str_replace('%MEDIAPOOL%', 'mediapool', $scriptout);
  }

  return $scriptout;
}
} // End function_exists
