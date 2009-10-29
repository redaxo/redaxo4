<?php

/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */


rex_title($I18N->msg('dashboard'), '');









echo '

<div class="rex-form" id="rex-form-dashboard">
  			
			<div class="rex-area-col-2">

				<div class="rex-area-col-a">
					<h3 class="rex-hl2">'.$I18N->msg("mount_points").'</h3>
					<div class="rex-area-content">
					
						<p class="rex-tx1">'.$I18N->msg("delete_cache_description").'</p>
					
					';


$mps = $REX["USER"]->getMountpoints();
if(count($mps)>0)
	foreach($mps as $cid){
		if($cat = OOCategory::getCategoryById($cid))
		{
			echo '<a href="index.php?page=structure&category_id='.$cid.'&clang=0">'.$cat->getName().'</a><br />';
		}
	}

	

echo '
					
						</div>
				</div>
			
				<div class="rex-area-col-b">
					<h3 class="rex-hl2"> &nbsp;</h3>
					<div class="rex-area-content">
						<p class="rex-tx1"></p>
					</div> <!-- Ende rex-area-content //-->
				</div> <!-- Ende rex-area-col-b //-->

			</div> <!-- Ende rex-area-col-2 //-->
</div>
  ';