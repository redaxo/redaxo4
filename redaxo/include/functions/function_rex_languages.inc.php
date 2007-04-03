<?php

/** 
 * Dient zur Ausgabe des Sprachen-blocks  
 * @package redaxo3 
 * @version $Id$ 
 */
  
// rechte einbauen
// admin[]
// clang[xx], clang[0]
// $REX_USER->isValueOf("rights","csw[0]")

reset($REX['CLANG']);
$num_clang = count($REX['CLANG']);

if ($num_clang>1)
{
   echo '
<!-- *** OUTPUT OF CLANG-TOOLBAR - START *** -->
   <div id="rex-clang">
     <ul>
       <li>'.$I18N->msg("languages").' : </li>';
       
	$stop = false;
   $i = 1;
   foreach($REX['CLANG'] as $key => $val)
   {
    echo '<li>';
    
		if (!$REX_USER->hasPerm('admin[]') && !$REX_USER->hasPerm('clang[all]') && !$REX_USER->hasPerm('clang['. $key .']'))
		{
			echo '<span class="rex-strike">'. $val .'</span>';
         
			if ($clang == $key) $stop = true;
		}
    elseif ($key==$clang) 
    {
       echo $val;
    }
		else
    {
       echo '<a href="index.php?page='. $page .'&amp;clang='. $key . $sprachen_add .'&amp;ctype='. $ctype .'"'. rex_tabindex() .'>'. $val .'</a>';
    }
    if($i != $num_clang)
    {
       echo ' | ';
    }
         
    echo '</li>';
    $i++;
	}
   
	echo '
     </ul>
   </div>
<!-- *** OUTPUT OF CLANG-TOOLBAR - END *** -->
';
   
	if ($stop)
	{
		echo '
<!-- *** OUTPUT OF CLANG-VALIDATE - START *** -->
      <p class="rex-warning">You have no permission to this area</p>
<!-- *** OUTPUT OF CLANG-VALIDATE - END *** -->
';
		include $REX['INCLUDE_PATH']."/layout/bottom.php"; 
		exit;	
	}
}
else
{
	$clang = 0;	
}

?>