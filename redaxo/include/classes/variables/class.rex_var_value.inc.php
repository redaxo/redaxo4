<?php

/**
 * REX_VALUE[1], REX_HTML_VALUE[1], REX_PHP, REX_PHP_VALUE[1], REX_HTML, REX_IS_VALUE
 * @package redaxo3
 * @version $Id$
 */

class rex_var_value extends rex_var
{
	function getBEOutput(&$sql,$content)
	{	
		global $REX;
		$content = str_replace('REX_PHP',$this->stripPHP($sql->getValue('php')),$this->getOutput($sql,$content,true));
		return $content;
	}
	
	function getFEOutput(&$sql,$content)
	{
		global $REX;
		$content = str_replace('REX_PHP',$sql->getValue('php'),$this->getOutput($sql,$content,true));
		return $content;
	}

	function getBEInput(&$sql,$content)
	{
		global $REX;
		$content = str_replace('REX_PHP',$sql->getValue('php'),$this->getOutput($sql,$content));
		return $content;
	}
	
	function getOutput(&$sql,$content,$nl2br = false)
	{
		global $REX;
		for($i=0;$i<21;$i++)
		{
			if ($nl2br) $content = str_replace('REX_VALUE['.$i.']',nl2br(htmlspecialchars($sql->getValue($REX['TABLE_PREFIX'].'article_slice.value'.$i))),$content);
			else $content = str_replace('REX_VALUE['.$i.']',htmlspecialchars($sql->getValue($REX['TABLE_PREFIX'].'article_slice.value'.$i)),$content);

			$content = str_replace('REX_HTML_VALUE['.$i.']',$this->stripPHP($sql->getValue($REX['TABLE_PREFIX'].'article_slice.value'.$i)),$content);
			if ($sql->getValue('value'.$i)!="")
			{
				$content = str_replace('REX_IS_VALUE['.$i.']',1,$content);
			}else
			{
				$content = str_replace('REX_IS_VALUE['.$i.']',0,$content);
			}
			$content = str_replace('REX_PHP_VALUE['.$i.']',$sql->getValue($REX['TABLE_PREFIX'].'article_slice.value'.$i),$content);
		}
		return $content;
	}	
}

?>