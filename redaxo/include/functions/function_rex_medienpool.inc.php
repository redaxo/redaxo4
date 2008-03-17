<?php

/**
 * Funktionensammlung für den Medienpool
 *
 * @package redaxo4
 * @version $Id$
 */

/**
 * Erstellt einen Filename der eindeutig ist für den Medienpool
 * @param $FILENAME Dateiname
 */
function rex_medienpool_filename($FILENAME, $doSubindexing = true)
{
  global $REX;

  // ----- neuer filename und extension holen
  $NFILENAME = strtolower($FILENAME);
  $NFILENAME = str_replace(array('ä','ö', 'ü', 'ß'),array('ae', 'oe', 'ue', 'ss'),$NFILENAME);
  $NFILENAME = preg_replace('/[^a-zA-Z0-9.\-\+]/','_',$NFILENAME);
  if (strrpos($NFILENAME,'.') != '')
  {
    $NFILE_NAME = substr($NFILENAME,0,strlen($NFILENAME)-(strlen($NFILENAME)-strrpos($NFILENAME,'.')));
    $NFILE_EXT  = substr($NFILENAME,strrpos($NFILENAME,'.'),strlen($NFILENAME)-strrpos($NFILENAME,'.'));
  }else
  {
    $NFILE_NAME = $NFILENAME;
    $NFILE_EXT  = '';
  }

  // ---- ext checken - alle scriptendungen rausfiltern
  if (in_array($NFILE_EXT,$REX['MEDIAPOOL']['BLOCKED_EXTENSIONS']))
  {
    $NFILE_NAME .= $NFILE_EXT;
    $NFILE_EXT = '.txt';
  }

  $NFILENAME = $NFILE_NAME.$NFILE_EXT;

  if($doSubindexing)
  {
    // ----- datei schon vorhanden -> namen aendern -> _1 ..
    if (file_exists($REX['MEDIAFOLDER'].'/'.$NFILENAME))
    {
      $cnt = 1;
      while(file_exists($REX['MEDIAFOLDER'].'/'.$NFILE_NAME.'_'.$cnt.$NFILE_EXT))
        $cnt++;

      $NFILENAME = $NFILE_NAME.'_'.$cnt.$NFILE_EXT;
    }
  }

  return $NFILENAME;
}

/**
 * Holt ein upgeloadetes File und legt es in den Medienpool
 * Dabei wird kontrolliert ob das File schon vorhanden ist und es
 * wird eventuell angepasst, weiterhin werden die Fileinformationen übergeben
 *
 * @param $FILE
 * @param $rex_file_category
 * @param $FILEINFOS
 * @param $userlogin
*/
function rex_medienpool_saveMedia($FILE, $rex_file_category, $FILEINFOS, $userlogin = null){

  global $REX,$I18N;

  $rex_file_category = (int) $rex_file_category;

  $gc = new rex_sql();
  $gc->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'file_category WHERE id='. $rex_file_category);
	if ($gc->getRows() != 1)
	{
  	$rex_file_category = 0;
	}

  $FILENAME = $FILE['name'];
  $FILESIZE = $FILE['size'];
  $FILETYPE = $FILE['type'];
  $NFILENAME = rex_medienpool_filename($FILENAME);
  $message = '';

  // ----- neuer filename
  $dstFile = $REX['MEDIAFOLDER'].'/'.$NFILENAME;

  // ----- dateiupload
  $upload = true;
  if(!@move_uploaded_file($FILE['tmp_name'],$dstFile))
  {
    $message .= $I18N->msg("pool_file_movefailed");
    $ok = 0;
    $upload = false;
  }

  if($upload)
  {
    chmod($dstFile, $REX['FILEPERM']);

    // get widht height
    $size = @getimagesize($dstFile);

    $FILESQL = new rex_sql;
    $FILESQL->setTable($REX['TABLE_PREFIX'].'file');
    $FILESQL->setValue('filetype',$FILETYPE);
    $FILESQL->setValue('title',$FILEINFOS['title']);
    $FILESQL->setValue('filename',$NFILENAME);
    $FILESQL->setValue('originalname',$FILENAME);
    $FILESQL->setValue('filesize',$FILESIZE);

    if($size)
    {
      $FILESQL->setValue('width',$size[0]);
      $FILESQL->setValue('height',$size[1]);
    }

    $FILESQL->setValue('category_id',$rex_file_category);
    // TODO Create + Update zugleich?
    $FILESQL->addGlobalCreateFields($userlogin);
    $FILESQL->addGlobalUpdateFields($userlogin);
    $FILESQL->insert();
    $ok = 1;

    $message .= $I18N->msg("pool_file_added");
  }

  $RETURN['title'] = $FILEINFOS['title'];
  $RETURN['type'] = $FILETYPE;
  $RETURN['msg'] = $message;
  $RETURN['ok'] = $ok;
  $RETURN['filename'] = $NFILENAME;
  $RETURN['old_filename'] = $FILENAME;

  if($size)
  {
    $RETURN['width'] = $size[0];
    $RETURN['height'] = $size[1];
  }

  return $RETURN;
}

?>