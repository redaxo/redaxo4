<?php

/**
 * REX_LINK_BUTTON, REX_LINKLIST_BUTTON, REX_LINKLIST
 * @package redaxo3
 * @version $Id$
 */

class rex_var_link extends rex_var
{
	function getBEOutput(&$sql,$content)
	{	
		global $REX;
		return $this->getOutput($sql,$content);
	}
  
	function getBEInput(&$sql,$content)
	{	
		global $REX;
		
		$content = $this->getOutput($sql,$content);
		
		for($i=0;$i<11;$i++)
		{
			$content = str_replace('REX_LINK_BUTTON['.$i.']',$this->getLinkButton($i,$sql->getValue($REX['TABLE_PREFIX'].'article_slice.link'.$i),$sql->getValue($REX['TABLE_PREFIX'].'article_slice.clang')),$content);

			$content = str_replace('REX_LINKLIST_BUTTON['.$i.']',$this->getLinklistButton($i,$sql->getValue($REX['TABLE_PREFIX'].'article_slice.linklist'.$i),$sql->getValue($REX['TABLE_PREFIX'].'article_slice.clang')),$content);
		}
		
		return $content;
	}
	
    
    function getOutput(&$sql,$content)
    {
		global $REX;
		for($i=0;$i<11;$i++)
		{
			$content = str_replace('REX_LINKLIST['.$i.']',$sql->getValue($REX['TABLE_PREFIX'].'article_slice.linklist'.$i),$content);
			
			$content = str_replace('REX_LINK['.$i.']',rex_getUrl($sql->getValue($REX['TABLE_PREFIX'].'article_slice.link'.$i)),$content);
			$content = str_replace('REX_LINK_ID['.$i.']',$sql->getValue($REX['TABLE_PREFIX'].'article_slice.link'.$i),$content);
		}
		return $content;
    }
    
  
  
  
  	function getLinkButton($id,$article_id,$clang)
	{
		global $REX;
		$art = OOArticle::getArticleById($article_id);
		$link_name = '';
		if(OOArticle::isValid($art))
		{
			$link_name = $art->getName();
		
		}
		
		$media = "<table class=rexbutton><input type=hidden name=REX_LINK_DELETE_$id value=0 id=REX_LINK_DELETE_$id><input type=hidden name='LINK[$id]' value='REX_LINK_ID[$id]' id=LINK[$id]><tr>";
		$media.= "<td><input type=text size=30 name='LINK_NAME[$id]' value='$link_name' class=inpgrey100 id=LINK_NAME[$id] readonly=readonly></td>";
		$media.= "<td class=inpicon><a href=javascript:openLinkMap($id,".$clang.");><img src=pics/file_open.gif width=16 height=16 title='Linkmap' border=0></a></td>";
		$media.= "<td class=inpicon><a href=javascript:deleteREXLink($id,".$clang.");><img src=pics/file_del.gif width=16 height=16 title='-' border=0></a></td>";
		$media.= "</tr></table>";
		$media = $this->stripPHP($media);
		return $media;
	}
	
	function getLinklistButton($id,$article_id,$clang)
	{
		global $REX;
		// TODO: komplett
		$media = "<input type=text size=30 name=REX_LINKLIST_$id value='REX_LINKLIST[$id]' class=inpgrey id=REX_LINKLIST_$id readonly=readonly>";
		$media = $this->stripPHP($media);
		return "";
	}
	
	
}

?>