<?

/**
 * @example
 * 
 * $subpages = array(
 *  array( '', 'Index'),
 *  array( 'lang', 'Sprachen'),
 * );
 * title( 'Headline', $subpages)
 */
function title($head,$subline = '',$styleclass="grey", $width = '770px')
{
    $subtitle = $subline;
    if( is_array( $subline)) 
    {
        $subtitle = '&nbsp;&nbsp;&nbsp;';
        $numPages = count( $subline);
        $i = 0;
        
        foreach ( $subline as $subpage)
        {
            if ( !is_array( $subpage)) {
                continue;
            }
            
            $link = $subpage[0];
            $label = $subpage[1];
            $active = (empty( $_REQUEST['subpage']) && $link == '') || (!empty( $_REQUEST['subpage'])&& $_REQUEST['subpage'] == $link);

            // Auf der Aktiven seite den Link nicht anzeigen            
            if( $active) 
            {
                $format = '%s';
                $subtitle .= sprintf( $format, $label); 
            }
            else if ( $link == '')
            {
                $format = '<a href="?page='. $_REQUEST['page'] .'">%s</a>';
                $subtitle .= sprintf( $format, $label); 
            }
            else
            {
                $format = '<a href="?page='. $_REQUEST['page'] .'&subpage=%s">%s</a>';
                $subtitle .= sprintf( $format, $link, $label); 
            }
            
            if ( $i != ($numPages - 1))
            {
                $subtitle .= ' | ';
            }
            
            $i++; 
        }
    }
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
                   <?php echo $subtitle. "\n" // \n aus Quellcode formatierungsgründen ?>
                </b>
            </td>
        </tr>
    
	</table>
    
    <br>
<?php
}
?>