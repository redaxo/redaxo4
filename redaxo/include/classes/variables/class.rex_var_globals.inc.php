<?php

// REX_MODULE_ID

class rex_var_globals extends rex_var
{
	function getBEOutput(&$sql,$content)
	{	
		global $REX;
		$content = str_replace("REX_MODULE_ID",$sql->getValue($REX['TABLE_PREFIX']."article_slice.modultyp_id"),$content);
		$content = str_replace("REX_SLICE_ID",$sql->getValue($REX['TABLE_PREFIX']."article_slice.id"),$content);
		$content = str_replace("REX_CTYPE_ID",$sql->getValue($REX['TABLE_PREFIX']."article_slice.ctype"),$content);
		return $content;
	}
}

?>