<?

function title($head,$subline = '',$styleclass="grey", $width = '770px')
{
?>
	<br>
	
	<table width="<?php echo $width ?>" cellpadding="0" cellspacing="0">
    
        <tr style="height: 30px">
            <td class=<?php echo $styleclass ?>>&nbsp;&nbsp;<b class="head"><?php echo $head ?></b></td>
            <td rowspan="3" width="153px"><img src=pics/logo.gif width="153px" height="61px"></td>
        </tr>
        
        <tr style="height: 1px">
            <td></td>
        </tr>
        
        <tr style="height: 30px">
            <td class="<?php echo $styleclass ?>" >
                <b style='line-height:18px'>
                   <?php echo $subline. "\n" // \n aus Quellcode formatierungsgründen ?>
                </b>
            </td>
        </tr>
    
	</table>
    
    <br>
<?php
}
?>