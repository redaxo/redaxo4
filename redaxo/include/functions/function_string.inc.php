<?
/**
 * Returns true if $string starts with $start
 * 
 * @param $string String Searchstring
 * @param $start String Prefix to search for
 * @author Markus Staab <kills@t-online.de>
 */
function startsWith( $string, $start) {
    return strstr($string, $start) == $string;
}


/**
 * Returns true if $string ends with $end
 * 
 * @param $string String Searchstring
 * @param $start String Suffix to search for
 * @author Markus Staab <kills@t-online.de>
 */
function endsWith( $string, $end ) {
   return ( substr( $string, strlen( $string ) - strlen( $end ) ) == $end );
}

/**
 * Returns the truncated $string
 * 
 * @param $string String Searchstring
 * @param $start String Suffix to search for
 * @author Markus Staab <kills@t-online.de>
 */
function truncate($string, $length = 80, $etc = '...', $break_words = false)
{
    if ($length == 0)
        return '';

    if (strlen($string) > $length) {
        $length -= strlen($etc);
        if (!$break_words)
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
      
        return substr($string, 0, $length).$etc;
    } else
        return $string;
}
?>