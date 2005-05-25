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
	function OOMedia( $id = null)
	{
        if ( $id === null) {
            return;
        }
        
		$query = 'SELECT '.$this->_getTableName().'.*,'.OOMediaCategory :: _getTableName().'.name catname  FROM '.OOMedia :: _getTableJoin().' WHERE file_id = '.$id;
		$sql = new sql();
		//        $sql->debugsql = true;
		$result = $sql->get_array($query);
		if (count($result) == 0)
		{
			trigger_error('No OOMediaCategory found with id "'.$id.'"', E_USER_ERROR);
		}
		$result = $result[0];
		//        var_dump( $result);

		$this->_id = $result['file_id'];
		$this->_parent_id = $result['re_file_id'];
		$this->_cat_id = $result['category_id'];
		$this->_cat_name = $result['catname'];

		$this->_name = $result['filename'];
		$this->_orgname = $result['originalname'];
		$this->_type = $result['filetype'];
		$this->_size = $result['filesize'];

		$this->_width = $result['width'];
		$this->_height = $result['height'];

		$this->_title = $result['title'];
		$this->_description = $result['description'];
		$this->_copyright = $result['copyright'];

		$this->_updatedate = $result['updatedate'];
		$this->_updateuser = $result['updateuser'];

		$this->_createdate = $result['createdate'];
		$this->_createuser = $result['createuser'];
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
	function getMediaById($id)
	{
		return new OOMedia($id);
	}

	/**
	 * @access public
	 */
	function searchMediaByFileName($name)
	{
		$query = 'SELECT file_id FROM '.OOMedia :: _getTableName().' WHERE filename = "'.addslashes($name).'"';
		$sql = new sql();
		$result = $sql->get_array($query);

		$media = array ();
		foreach ($result as $line)
		{
			$media[] = OOMedia :: getMediaById($line['file_id']);
		}

		return $media;
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
    function hasParent() {
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
	function getUpdateUser()
	{
		return $this->_updateuser;
	}

	/**
	 * @access public
	 */
	function getUpdateDate($format = '')
	{
		global $I18N;
		if ($format !== null)
		{
			if ($format == '')
			{
				$format = $I18N->msg('dateformat');
			}
			return date($format, $this->_updatedate);
		}
		return $this->_updatedate;
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
	 */
	function getCreateDate($format = null)
	{
		if ($format !== null)
		{
			return date($format, $this->_createdate);
		}
		return $this->_createdate;
	}

	/**
	 * @access public
	 */
	function toImage($params = array ())
	{
		global $REX;

		$path = '';
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
		}
		else
		{
            $resize = false;
            
			// ResizeModus festlegen
			if (isset ($params['resize']) && $params['resize'])
			{
				unset ($params['resize']);
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
				// Bild 1:1 anzeigen
			}
			else
			{
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

		return '<img src="'.$path.$file.'" '.$additional.'/>';
	}

	/**
	 * @access public
	 */
	function toIcon($iconAttributes = array ())
	{
		static $icon_src;

		if (!isset ($icon_src))
		{
			$icon_src = "pics/mime_icons/";
		}

		$icon = $icon_src.'mime-'.$this->getExtension().'.gif';

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

		return '<img src="'.$icon.'"'.$attrs.' style="width: 44px; height: 38px">';
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
		static $imageExtensions;

		if (!isset ($imageExtensions))
		{
			$imageExtensions = array ('gif', 'jpeg', 'jpg', 'png', 'bmp');
		}
		return in_array($this->getExtension(), $imageExtensions);
	}

	/**
	 * @access public
	 */
	function toHTML()
	{
		global $REX;

		$file = $REX['HTDOCS_PATH'].'files/'.$this->getFileName();
		$filetype = $this->getExtension();

		switch ($filetype)
		{
			case 'jpg' :
			case 'jpeg' :
			case 'png' :
			case 'gif' :
			case 'bmp' :
				{
					return '<img src="'.$file.'" alt="'.htmlentities($this->getDescription()).'" width="'.$this->getWidth().'px" height="'.$this->getHeight().'px"/>';
				}
			case 'js' :
				{
					return '<script type="text/javascript" src="'.$file.'"></script>';
				}
			case 'css' :
				{
					return '<link href="'.$file.'" rel="stylesheet" type="text/css">';
				}
			default :
				return 'No html-equivalent available for type "'.$filetype.'"';
		}
	}

	/**
	 * @access public
	 */
	function toString()
	{
		return 'OOMedia, "'.$this->getId().'", "'.$this->getName().'"'."<br/>\n";
	}

	// new functions by vscope
	/**
	 * @access protected
	 */
	function getExtension()
	{
		return substr(strrchr($this->_name, "."), 1);
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
				trigger_error('File Icons Folder "'.$icons_folder.'" unavailable', E_USER_ERROR);
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
	 */
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
			}
		}

	}

	/**
	 * @access protected
	 */
	function _getSQLSetString()
	{
		$set = ' SET'
              .'  re_file_id = "'. sql::escape( $this->getParentId()) .'"'
              .', category_id = "'. sql::escape( $this->getCategoryId()) .'"'
              .', filetype = "'. sql::escape( $this->getType()).'"'
              .', filename = "'. sql::escape( $this->getFileName()).'"'
              .', originalname = "'. sql::escape( $this->getOrgFileName()).'"'
              .', filesize = "'. sql::escape( $this->getSize()).'"'
              .', width = "'. sql::escape( $this->getWidth()) .'"'
              .', height = "'. sql::escape( $this->getHeight()) .'"'
              .', title = "'. sql::escape( $this->getTitle()).'"'
              .', description = "'. sql::escape( $this->getDescription()).'"'
              .', copyright = "'. sql::escape( $this->getCopyright()).'"'
              .', updatedate = "'. sql::escape( $this->getUpdateDate()) .'"'
              .', createdate = "'. sql::escape( $this->getCreateDate()) .'"'
              .', updateuser = "'. sql::escape( $this->getUpdateUser()).'"'
              .', createuser = "'. sql::escape( $this->getCreateUser()).'"';

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

		$sql = new sql();
		//        $sql->debugsql = true;
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
		$qry .= ' WHERE file_id = "'. $this->getId() .'" LIMIT 1';

		//        $sql = new sql();
		$sql->debugsql = true;
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
			return $this->insert();
		}
	}

	/**
	 * @access protected
	 * @return Returns <code>true</code> on success or <code>false</code> on error
	 */
	function _delete()
	{
		$qry = 'DELETE FROM '.$this->_getTableName().' WHERE file_id = '.$this->getId().' LIMIT 1';
		$sql = new sql();
		//        $sql->debugsql = true;
		$sql->query($qry);
		return $sql->getError();
	}
}
?>