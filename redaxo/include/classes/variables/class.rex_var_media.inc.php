<?php

/**
 * REX_FILE[1], REX_MEDIA[1], REX_FILELIST[1], REX_MEDIALIST[1], REX_FILE_BUTTON[1], REX_MEDIA_BUTTON[1], REX_FILELIST_BUTTON[1], REX_MEDIALIST_BUTTON[1],
 * @package redaxo3
 * @version $Id$
 */

class rex_var_media extends rex_var
{
	function getBEOutput(&$sql,$content)
	{
		global $REX;
		for($i=0;$i<11;$i++)
		{
			$content = str_replace('REX_FILE_BUTTON['.$i.']',$this->getMediaButton($i,$sql->getValue($REX['TABLE_PREFIX'].'article_slice.clang')),$content);
			$content = str_replace('REX_MEDIA_BUTTON['.$i.']',$this->getMediaButton($i,$sql->getValue($REX['TABLE_PREFIX'].'article_slice.clang')),$content);

			$content = str_replace('REX_FILELIST_BUTTON['.$i.']',$this->getMedialistButton($i,$sql->getValue($REX['TABLE_PREFIX'].'article_slice.clang'),$sql->getValue($REX['TABLE_PREFIX'].'article_slice.filelist'.$i)),$content);
			$content = str_replace('REX_MEDIALIST_BUTTON['.$i.']',$this->getMedialistButton($i,$sql->getValue($REX['TABLE_PREFIX'].'article_slice.clang'),$sql->getValue($REX['TABLE_PREFIX'].'article_slice.filelist'.$i)),$content);
			
			$content = str_replace('REX_FILE['.$i.']',htmlspecialchars($sql->getValue($REX['TABLE_PREFIX'].'article_slice.file'.$i)),$content);
			$content = str_replace('REX_MEDIA['.$i.']',htmlspecialchars($sql->getValue($REX['TABLE_PREFIX'].'article_slice.file'.$i)),$content);

			$content = str_replace('REX_FILELIST['.$i.']',htmlspecialchars($sql->getValue($REX['TABLE_PREFIX'].'article_slice.filelist'.$i)),$content);
			$content = str_replace('REX_MEDIALIST['.$i.']',htmlspecialchars($sql->getValue($REX['TABLE_PREFIX'].'article_slice.filelist'.$i)),$content);
		}	
		return $content;
	}
	
	function getMediaButton($id,$clang)
	{
		global $REX;
		// ----------------------------- REX_MEDIA_BUTTON
		$media = "<table class=rexbutton><input type=hidden name=REX_MEDIA_DELETE_$id value=0 id=REX_MEDIA_DELETE_$id><tr>";
		$media.= "<td><input type=text size=30 name=REX_MEDIA_$id value='REX_FILE[$id]' class=inpgrey100 id=REX_MEDIA_$id readonly=readonly></td>";
		$media.= "<td class=inpicon><a href=javascript:openREXMedia($id,".$clang.");><img src=pics/file_open.gif width=16 height=16 title='medienpool' border=0></a></td>";
		$media.= "<td class=inpicon><a href=javascript:deleteREXMedia($id,".$clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
		$media.= "<td class=inpicon><a href=javascript:addREXMedia($id,".$clang.")><img src=pics/file_add.gif width=16 height=16 title='+' border=0></a></td>";
		$media.= "</tr></table>";
		$media = $this->stripPHP($media);
		return $media;
	}

	function getMedialistButton($id,$clang,$value)
	{
		global $REX;
		
		$media = "<table class=rexbutton><tr>";
		$media .= "<td valign=top><select name=REX_MEDIALIST_SELECT_$id id=REX_MEDIALIST_SELECT_$id size=8 class=inpgrey100>";
		$medialistarray = explode(',',$value);
		if (is_array($medialistarray))
		{
			for($j=0;$j<count($medialistarray);$j++)
			{
			  if (current($medialistarray)!="") $media .= "<option value='".current($medialistarray)."'>".current($medialistarray)."</option>\n";
			  next($medialistarray);
			}
		}
		$media .= "</select></td>";
		$media .= "<td class=inpicon>".
		"<a href=javascript:moveREXMedialist($id,'top');><img src=pics/file_top.gif width=16 height=16 vspace=2 title='^^' border=0></a>".
		"<br><a href=javascript:moveREXMedialist($id,'up');><img src=pics/file_up.gif width=16 height=16 vspace=2 title='^' border=0></a>".
		"<br><a href=javascript:moveREXMedialist($id,'down');><img src=pics/file_down.gif width=16 height=16 vspace=2 title='v' border=0></a>".
		"<br><a href=javascript:moveREXMedialist($id,'bottom');><img src=pics/file_bottom.gif width=16 height=16 vspace=2 title='vv' border=0></a></td>";
		$media .= "<td class=inpicon>".
		"<a href=javascript:openREXMedialist($id);><img src=pics/file_add.gif width=16 height=16 vspace=2 title='+' border=0></a>".
		"<br><a href=javascript:deleteREXMedialist($id);><img src=pics/file_del.gif width=16 height=16 vspace=2 title='-' border=0></a></td>";
		$media .= "</tr>";
		$media .= "<input type=hidden name=REX_MEDIALIST_$id value='REX_MEDIALIST[$id]' id=REX_MEDIALIST_$id >";
		$media .= "</table><br><br>";
		return $media;		
	}
	
	
	
}

?>