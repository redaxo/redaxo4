<?
/**
 * Returns true if $string starts with $start
 * 
 * @param $string String Searchstring
 * @param $start String Prefix to search for
 * @author Markus Staab <kills@t-online.de>
 */
function startsWith( $string, $start) {
    return strstr($sting, $start) == $string;
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

?>