<?php
class OOMedia
{
   // id
   var $_id;
   // parent (FOR FUTURE USE!) 
   var $_parent_id;
   // categoryid
   var $_cat_id;

   // categoryname
   var $_cat_name;
   // oomediacategory
   var $_cat;

   // filename
   var $_name;
   // originalname
   var $_orgname;
   // filetype
   var $_type;
   // filesize
   var $_size;

   // filewidth
   var $_width;
   // fileheight
   var $_height;

   // filetitle
   var $_title;
   // filedescription
   var $_description;
   // copyright
   var $_copyright;

   // updatedate
   var $_updatedate;
   // createdate
   var $_createdate;

   // updateuser
   var $_updateuser;
   // createuser
   var $_createuser;

   // resizeextensions
   var $_resizeextensions = array ('jpeg', 'jpg', 'gif', 'png');

   /**
    * @access protected
    */
   function OOMedia($id = null)
   {
      $this->getMediaById($id);
   }

   /**
    * @access protected
    */
   function _getTableName()
   {
      global $REX;
      return $REX['TABLE_PREFIX'].'file';
   }

   /**
    * @access protected
    */
   function _getTableJoin()
   {
      $mediatable = OOMedia :: _getTableName();
      $cattable = OOMediaCategory :: _getTableName();
      return $mediatable.' LEFT JOIN '.$cattable.' ON '.$mediatable.'.category_id = '.$cattable.'.id';
   }

   /**
    * @access public
    */
   function & getMediaById($id)
   {
      $id = (int) $id;
      if (!is_numeric($id))
      {
         return null;
      }

      $query = 'SELECT '.OOMedia :: _getTableName().'.*, '.OOMediaCategory :: _getTableName().'.name catname  FROM '.OOMedia :: _getTableJoin().' WHERE file_id = '.$id;
      $sql = new sql();
      //        $sql->debugsql = true;
      $result = $sql->get_array($query);
      if (count($result) == 0)
      {
         //trigger_error('No OOMediaCategory found with id "'.$id.'"', E_USER_NOTICE);
         return null;
      }

      $result = $result[0];
      //        var_dump( $result);

      $media = new OOMedia();
      $media->_id = $result['file_id'];
      $media->_parent_id = $result['re_file_id'];
      $media->_cat_id = $result['category_id'];
      $media->_cat_name = $result['catname'];

      $media->_name = $result['filename'];
      $media->_orgname = $result['originalname'];
      $media->_type = $result['filetype'];
      $media->_size = $result['filesize'];

      $media->_width = $result['width'];
      $media->_height = $result['height'];

      $media->_title = $result['title'];
      $media->_description = $result['description'];
      $media->_copyright = $result['copyright'];

      $media->_updatedate = $result['updatedate'];
      $media->_updateuser = $result['updateuser'];

      $media->_createdate = $result['createdate'];
      $media->_createuser = $result['createuser'];

      return $media;
   }

   /**
    * @access public
    */
   function & getMediaByName($filename)
   {
      $query = 'SELECT file_id FROM '.OOMedia :: _getTableName().' WHERE filename = "'.sql :: escape($filename).'"';
      $sql = new sql();
      //$sql->debugsql = true;
      $result = $sql->get_array($query);

      if (count($result) == 0)
      {
         return null;
      }

      return OOMedia::getMediaById($result[0]['file_id']);
   }

   /**
    * @access public
    * @see #getMediaByExtension
    */
   function & searchMediaByExtension($extension)
   {
      return OOMedia :: getMediaByExtension($extension);
   }

   /**
    * @access public
    * 
    * @example OOMedia::getMediaByExtension('css');
    * @example OOMedia::getMediaByExtension('gif');
    */
   function & getMediaByExtension($extension)
   {
      $query = 'SELECT file_id FROM '.OOMedia :: _getTableName().' WHERE SUBSTRING(filename,LOCATE( ".",filename)+1) = "'.sql :: escape($extension).'"';
      $sql = new sql();
      //              $sql->debugsql = true;
      $result = $sql->get_array($query);

      $media = array ();

      if (is_array($result))
      {
         foreach ($result as $row)
         {
            $media[] = & OOMedia::getMediaById($row['file_id']);
         }
      }

      return $media;
   }

   /**
    * @access public
    * @see #getMediaByFileName
    */
   function & searchMediaByFileName($name)
   {
      return OOMedia :: getMediaByFileName($name);
   }

   /**
    * @access public
    */
   function & getMediaByFileName($name)
   {
      $query = 'SELECT file_id FROM '.OOMedia :: _getTableName().' WHERE filename = "'.sql :: escape($name).'"';
      $sql = new sql();
      $result = $sql->get_array($query);

      if (is_array($result))
      {
         foreach ($result as $line)
         {
            return OOMedia :: getMediaById($line['file_id']);
         }
      }

      return null;
   }

   /**
    * @access public
    */
   function getId()
   {
      return $this->_id;
   }

   /**
    * @access public
    */
   function getCategory()
   {
      if ($this->_cat === null)
      {
         $this->_cat = & OOMediaCategory :: getCategoryById($this->getCategoryId());
      }
      return $this->_cat;
   }

   /**
    * @access public
    */
   function getCategoryName()
   {
      return $this->_cat_name;
   }

   /**
    * @access public
    */
   function getCategoryId()
   {
      return $this->_cat_id;
   }

   /**
    * @access public
    */
   function getParentId()
   {
      return $this->_parent_id;
   }

   /**
    * @access public
    */
   function hasParent()
   {
      return $this->getParentId() != 0;
   }

   /**
    * @access public
    */
   function getTitle()
   {
      return $this->_title;
   }

   /**
    * @access public
    */
   function getDescription()
   {
      return $this->_description;
   }

   /**
    * @access public
    */
   function getCopyright()
   {
      return $this->_copyright;
   }

   /**
    * @access public
    */
   function getFileName()
   {
      return $this->_name;
   }

   /**
    * @access public
    */
   function getOrgFileName()
   {
      return $this->_orgname;
   }

   /**
    * @access public
    */
   function getPath()
   {
      global $REX;
      return $REX['HTDOCS_PATH'].'files';
   }

   /**
    * @access public
    */
   function getFullPath()
   {
      return $this->getPath().'/'.$this->getFileName();
   }

   /**
    * @access public
    */
   function getWidth()
   {
      return $this->_width;
   }

   /**
    * @access public
    */
   function getHeight()
   {
      return $this->_height;
   }

   /**
    * @access public
    */
   function getType()
   {
      return $this->_type;
   }

   /**
    * @access public
    */
   function getSize()
   {
      return $this->_size;
   }

   /**
    * @access public
    */
   function getFormattedSize()
   {
      return $this->_getFormattedSize($this->getSize());
   }

   /**
    * @access protected
    */
   function _getFormattedSize($size)
   {

      // Setup some common file size measurements.
      $kb = 1024; // Kilobyte
      $mb = 1024 * $kb; // Megabyte
      $gb = 1024 * $mb; // Gigabyte
      $tb = 1024 * $gb; // Terabyte
      // Get the file size in bytes.

      // If it's less than a kb we just return the size, otherwise we keep going until
      // the size is in the appropriate measurement range.
      if ($size < $kb)
      {
         return $size." Bytes";
      }
      else
         if ($size < $mb)
         {
            return round($size / $kb, 2)." KBytes";
         }
         else
            if ($size < $gb)
            {
               return round($size / $mb, 2)." MBytes";
            }
            else
               if ($size < $tb)
               {
                  return round($size / $gb, 2)." GBytes";
               }
               else
               {
                  return round($size / $tb, 2)." TBytes";
               }
   }

   /**
    * Formats a datestamp with the given format.
    * 
    * If format is <code>null</code> the datestamp is returned.
    *  
    * If format is <code>''</code> the datestamp is formated 
    * with the default <code>dateformat</code> (lang-files). 
    * 
    * @access public
    * @static
    */
   function _getDate($date, $format = null)
   {
      if ($format !== null)
      {
         if ($format == '')
         {
            // TODO Im Frontend gibts kein I18N
            // global $I18N;
            //$format = $I18N->msg('dateformat');
            $format = '%a %d. %B %Y';
         }
         return strftime($format, $date);
      }
      return $date;
   }

   /**
    * @access public
    */
   function getUpdateUser()
   {
      return $this->_updateuser;
   }

   /**
    * @access public
     * @see #_getDate
    */
   function getUpdateDate($format = null)
   {
      return $this->_getDate($this->_updatedate, $format);
   }

   /**
    * @access public
    */
   function getCreateUser()
   {
      return $this->_createuser;
   }

   /**
    * @access public
     * @see #_getDate
    */
   function getCreateDate($format = null)
   {
      return $this->_getDate($this->_createdate, $format);
   }

   /**
    * @access public
    */
   function toImage($params = array ())
   {
      global $REX;

      $path = $REX['HTDOCS_PATH'];
      if (isset ($params['path']))
      {
         $path = $params['path'];
         unset ($params['path']);
      }

      // Ist das Media ein Bild?        
      if (!$this->isImage())
      {
         $path = 'pics/';
         $file = 'file_dummy.gif';

         // Verwenden einer statischen variable, damit getimagesize nur einmal aufgerufen
         // werden muss, da es sehr lange dauert
         static $dummyFileSize;

         if (empty ($dummyFileSize))
         {
            $dummyFileSize = getimagesize($path.$file);
         }
         $params['width'] = $dummyFileSize[0];
         $params['height'] = $dummyFileSize[1];
      }
      else
      {
         $resize = false;

         // ResizeModus festlegen
         if (isset ($params['resize']) && $params['resize'])
         {
            unset ($params['resize']);
            // Resize Addon installiert?
            if (isset ($REX['ADDON']['status']['image_resize']) && $REX['ADDON']['status']['image_resize'] == 1)
            {
               $resize = true;
               if (isset ($params['width']))
               {
                  $resizeMode = 'w';
                  $resizeParam = $params['width'];
                  unset ($params['width']);
               }
               elseif (isset ($params['height']))
               {
                  $resizeMode = 'h';
                  $resizeParam = $params['height'];
                  unset ($params['height']);
               }
               else
               {
                  $resizeMode = 'a';
                  $resizeParam = 0;
               }

               // Evtl. Größeneinheiten entfernen
               $resizeParam = str_replace(array ('px', 'pt', '%', 'em'), '', $resizeParam);
            }
         }

         // Bild resizen?
         if ($resize)
         {
            $file = 'index.php?rex_resize='.$resizeParam.$resizeMode.'__'.$this->getFileName();
         }
         else
         {
            // Bild 1:1 anzeigen
            $path .= 'files/';
            $file = $this->getFileName();
         }
      }

      // Evtl. Zusatzatrribute anfügen 
      $additional = '';
      foreach ($params as $name => $value)
      {
         $additional .= $name.'="'.$value.'" ';
      }

      return '<img src="'.$path.$file.'" '.$additional.' />';
   }

   /**
    * @access public
    */
   function toLink($attributes = '')
   {
      return sprintf('<a href="%s" title="%s"%s>%s</a>', $this->getFullPath(), $this->getDescription(), $attributes, $this->getFileName());
   }
   /**
    * @access public
    */
   function toIcon($iconAttributes = array (), $iconPath = '')
   {
      static $icon_src;

      if (!isset ($icon_src))
      {
         $icon_src = "pics/mime_icons/";
      }

      $icon = $iconPath . $icon_src.'mime-'.$this->getExtension().'.gif';

      // Dateityp für den kein Icon vorhanden ist
      if (!file_exists($icon))
      {
         $icon = $icon_src.'mime-txt.gif';
      }

      $attrs = '';
      foreach ($iconAttributes as $attrName => $attrValue)
      {
         $attrs .= ' '.$attrName.'="'.$attrValue.'"';
      }

      return '<img src="'.$icon.'"'.$attrs.' style="width: 44px; height: 38px" />';
   }

   /**
    * @access public
    * @static
    */
   function isValid($media)
   {
      return is_object($media) && is_a($media, 'oomedia');
   }

   /**
    * @access public
    */
   function isImage()
   {
      return $this->_isImage($this->getFileName());
   }

   /**
    * @access public
    * @static
    */
   function _isImage($filename)
   {
      static $imageExtensions;

      if (!isset ($imageExtensions))
      {
         $imageExtensions = array ('gif', 'jpeg', 'jpg', 'png', 'bmp');
      }

      return in_array(OOMedia :: _getExtension($filename), $imageExtensions);
   }

   /**
    * @access public
    */
   function isInUse()
   {
      $sql = new sql();
      //        $sql->debugsql = true;
      $query_file = '';
      $query_filelist = '';
      for ($i = 1; $i < 11; $i ++)
      {
         if ($i > 1)
            $query_file .= ' or ';
         if ($i > 1)
            $query_filelist .= ' or ';
         $query_file .= ' file'.$i.'="'.$this->getFileName().'"';
         $query_filelist .= ' file'.$i.' like "%|'.$this->getFileName().'|%"';
      }
      $query_file = '('.$query_file.')';
      $query_filelist = '('.$query_filelist.')';
      $query = 'select id from rex_article_slice where '.$query_file.' or '.$query_filelist.' LIMIT 1';

      $sql->setQuery($query);
      return $sql->getRows() > 0;
   }

   /**
    * @access public
    */
   function toHTML($attributes = '')
   {
      global $REX;

      $file = $this->getFullPath();
      $filetype = $this->getExtension();

      switch ($filetype)
      {
         case 'jpg' :
         case 'jpeg' :
         case 'png' :
         case 'gif' :
         case 'bmp' :
            {
               $desc = htmlspecialchars($this->getDescription());
               return sprintf('<img src="%s" alt="%s" title="%s" style="width: %spx; height: %spx"%s/>', $file, $desc, $desc, $this->getWidth(), $this->getHeight(), $attributes);
            }
         case 'js' :
            {
               return sprintf('<script type="text/javascript" src="%s"%s></script>', $file, $attributes);
            }
         case 'css' :
            {
               return sprintf('<link href="%s" rel="stylesheet" type="text/css"%s>', $file, $attributes);
            }
         default :
            {
               return 'No html-equivalent available for type "'.$filetype.'"';
            }
      }
   }

   /* Not in use
   function _getJsLink( $javascript, $label, $additional = '') {
       return sprintf( '<a href="javascript:%s"%s>%s</a>', $javascript, $additional, $label);
   }
   
   function toInsertLink( &$poolParmas)
   {
       global $I18N;
       
       $insertLabel = $I18N->msg('pool_media_insert');
       $linkLabel = $I18N->msg('pool_media_link');
       $link = '';
       
       if ( $poolParmas->isMediaButtonMode()) 
       {
           $link = $this->_getJSLink( 'selectMedia(\''. $this->getFileName() .'\');', $insertLabel);
       } 
       else 
       {
           if ( $this->isImage()) 
           {
              $javascript = sprintf( 'insertImage(\'%s\', \'%s\', \'%s\', \'%s\');',
                            $this->getFileName(),
                            $this->getDescription(),
                            $this->getWidth(),
                            $this->getHeight());
              $link .= $this->_getJSLink( $javascript, $insertLabel) .'<br/><br/>';
           }
           
           $javascript = sprintf( 'insertLink( \'%s\')', $this->getFileName());
           $link .= $this->_getJSLink( $javascript, $linkLabel);
       }
       
       return $link;
   }
   */

   /**
    * @access public
    */
   function toString()
   {
      return 'OOMedia, "'.$this->getId().'", "'.$this->getName().'"'."<br/>\n";
   }

   // new functions by vscope
   /**
     * @access public
    */
   function getExtension()
   {
      return $this->_getExtension($this->_name);
   }

   /**
    * @access public
    * @static
    */
   function _getExtension($filename)
   {
      return substr(strrchr($filename, "."), 1);
   }

   /**
    * @access public
    */
   function getIcon()
   {
      global $REX;

      $default_file_icon = "file";
      $icons_folder = $REX['HTDOCS_PATH'].'redaxo/pics/pool_file_icons/';

      // get File icons from dir redaxo/pics/pool_file_icons/
      if (!$REX[MEDIA][ICONS])
      {
         if ($handle = opendir($icons_folder))
         {
            while (false !== ($file = readdir($handle)))
            {
               if ($file != "." && $file != "..")
               {
                  $REX[MEDIA][ICONS][] = str_replace(".gif", "", $file);
               }
            }
            closedir($handle);
         }
         else
         {
            trigger_error('File Icons Folder "'.$icons_folder.'" unavailable!', E_USER_ERROR);
            return false;
         }
      }

      // get File extension
      $extension = $this->getExtension();

      // get right Icon for Extension
      if ($key = array_search($extension, $REX[MEDIA][ICONS]))
      {
         $icon = $icons_folder.$REX[MEDIA][ICONS][$key].".gif";
      }
      else
      {
         $icon = $icons_folder.$default_file_icon.".gif";
      }

      return $icon;
   }

   /**
    * @access public
    * TODO
    */
   /*
   function resizeImage($width = '', $height = '', $quality = 90)
   {
      if (!$key = array_search($this->getExtension(), $this->_resizeextensions))
      {
         return false;
      }
      else
      {
         $resizeType = $this->_resizeextensions[$key];
         $imagePath = $REX['HTDOCS_PATH'].'/files'.$this->_name;
         switch ($resizeType)
         {
            case 'jpg' :
            case 'jpeg' :
               $im = imagecreatefromjpeg($imagePath);
               break;
         }
      }
   
   }*/

   /**
    * @access protected
    */
   function _getSQLSetString()
   {
      $set = ' SET'.'  re_file_id = "'.sql :: escape($this->getParentId()).'"'.', category_id = "'.sql :: escape($this->getCategoryId()).'"'.', filetype = "'.sql :: escape($this->getType()).'"'.', filename = "'.sql :: escape($this->getFileName()).'"'.', originalname = "'.sql :: escape($this->getOrgFileName()).'"'.', filesize = "'.sql :: escape($this->getSize()).'"'.', width = "'.sql :: escape($this->getWidth()).'"'.', height = "'.sql :: escape($this->getHeight()).'"'.', title = "'.sql :: escape($this->getTitle()).'"'.', description = "'.sql :: escape($this->getDescription()).'"'.', copyright = "'.sql :: escape($this->getCopyright()).'"'.', updatedate = "'.sql :: escape($this->getUpdateDate(null)).'"'.', createdate = "'.sql :: escape($this->getCreateDate(null)).'"'.', updateuser = "'.sql :: escape($this->getUpdateUser()).'"'.', createuser = "'.sql :: escape($this->getCreateUser()).'"';

      return $set;
   }

   /**
    * @access protected
    * @return Returns <code>true</code> on success or <code>false</code> on error
    */
   function _insert()
   {
      $qry = 'INSERT INTO '.$this->_getTableName();
      $qry .= $this->_getSQLSetString();

      //        echo $qry;
      //        return;

      $sql = new sql();
      //		$sql->debugsql = true;
      $sql->query($qry);

      return $sql->getError();
   }

   /**
    * @access protected
    * @return Returns <code>true</code> on success or <code>false</code> on error
    */
   function _update()
   {
      $qry = 'UPDATE '.$this->_getTableName();
      $qry .= $this->_getSQLSetString();
      $qry .= ' WHERE file_id = "'.$this->getId().'" LIMIT 1';

      //        echo $qry;
      //        return;

      $sql = new sql();
      //		$sql->debugsql = true;
      $sql->query($qry);

      return $sql->getError();
   }

   /**
    * @access protected
    * @return Returns <code>true</code> on success or <code>false</code> on error
    */
   function _save()
   {
      if ($this->getId() !== null)
      {
         return $this->_update();
      }
      else
      {
         return $this->_insert();
      }
   }

   /**
    * @access protected
    * @return Returns <code>true</code> on success or <code>false</code> on error
    */
   function _delete()
   {
      global $REX;

      $qry = 'DELETE FROM '.$this->_getTableName().' WHERE file_id = '.$this->getId().' LIMIT 1';
      $sql = new sql();
      //        $sql->debugsql = true;
      $sql->query($qry);

      ### todo - loeschen des files
      unlink($REX[INCLUDE_PATH]."/../../files/".$this->getFileName());

      return $sql->getError();
   }
}
?>