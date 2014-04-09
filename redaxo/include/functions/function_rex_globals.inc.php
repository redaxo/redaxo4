<?php
/**
 * Getter Funktionen zum Handling von Superglobalen Variablen
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/**
 * Gibt die Superglobale variable $varname des Array $_GET zurück und castet dessen Wert ggf.
 *
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_get($varname, $vartype = '', $default = '')
{
    return _rex_array_key_cast($_GET, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_POST zurück und castet dessen Wert ggf.
 *
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_post($varname, $vartype = '', $default = '')
{
    return _rex_array_key_cast($_POST, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_REQUEST zurück und castet dessen Wert ggf.
 *
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_request($varname, $vartype = '', $default = '')
{
    return _rex_array_key_cast($_REQUEST, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_SERVER zurück und castet dessen Wert ggf.
 *
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_server($varname, $vartype = '', $default = '')
{
    return _rex_array_key_cast($_SERVER, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_SESSION zurück und castet dessen Wert ggf.
 *
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_session($varname, $vartype = '', $default = '')
{
    global $REX;

    if (isset($_SESSION[$varname][$REX['INSTNAME']])) {
        return _rex_cast_var($_SESSION[$varname][$REX['INSTNAME']], $vartype, $default, 'found');
    }

    if ($default === '') {
        return _rex_cast_var($default, $vartype, $default, 'default');
    }
    return $default;
}

/**
 * Setzt den Wert einer Session Variable.
 *
 * Variablen werden Instanzabhängig gespeichert.
 */
function rex_set_session($varname, $value)
{
    global $REX;

    $_SESSION[$varname][$REX['INSTNAME']] = $value;
}

/**
 * L鰏cht den Wert einer Session Variable.
 *
 * Variablen werden Instanzabhängig gelöscht.
 */
function rex_unset_session($varname)
{
    global $REX;

    unset($_SESSION[$varname][$REX['INSTNAME']]);
}

/**
 * Gibt die Superglobale variable $varname des Array $_COOKIE zurück und castet dessen Wert ggf.
 *
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_cookie($varname, $vartype = '', $default = '')
{
    return _rex_array_key_cast($_COOKIE, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_FILES zurück und castet dessen Wert ggf.
 *
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_files($varname, $vartype = '', $default = '')
{
    return _rex_array_key_cast($_FILES, $varname, $vartype, $default);
}

/**
 * Gibt die Superglobale variable $varname des Array $_ENV zurück und castet dessen Wert ggf.
 *
 * Falls die Variable nicht vorhanden ist, wird $default zurückgegeben
 */
function rex_env($varname, $vartype = '', $default = '')
{
    return _rex_array_key_cast($_ENV, $varname, $vartype, $default);
}

/**
 * Durchsucht das Array $haystack nach dem Schlüssel $needle.
 *
 * Falls ein Wert gefunden wurde wird dieser nach
 * $vartype gecastet und anschließend zurückgegeben.
 *
 * Falls die Suche erfolglos endet, wird $default zurückgegeben
 *
 * @access private
 */
function _rex_array_key_cast($haystack, $needle, $vartype, $default = '')
{
    if (!is_array($haystack)) {
        trigger_error('Array expected for $haystack in _rex_array_key_cast()!', E_USER_ERROR);
        exit();
    }

    if (!is_scalar($needle)) {
        trigger_error('Scalar expected for $needle in _rex_array_key_cast()!', E_USER_ERROR);
        exit();
    }

    if (array_key_exists($needle, $haystack)) {
        return _rex_cast_var($haystack[$needle], $vartype, $default, 'found');
    }

    if ($default === '') {
        return _rex_cast_var($default, $vartype, $default, 'default');
    }
    return $default;
}

/**
 * Castet die Variable $var zum Typ $vartype
 *
 * Mögliche PHP-Typen sind:
 *  - bool (auch boolean)
 *  - int (auch integer)
 *  - double
 *  - string
 *  - float
 *  - real
 *  - object
 *  - array
 *  - '' (nicht casten)
 *
 * Mögliche REDAXO-Typen sind:
 *  - rex-article-id
 *  - rex-category-id
 *  - rex-clang-id
 *  - rex-template-id
 *  - rex-ctype-id
 *  - rex-slice-id
 *  - rex-module-id
 *  - rex-action-id
 *  - rex-media-id
 *  - rex-mediacategory-id
 *  - rex-user-id
 *
 * @access private
 */
function _rex_cast_var($var, $vartype, $default = null, $mode = 'default')
{
    global $REX;

    if (is_string($vartype)) {
        $casted = true;
        switch ($vartype) {
            // ---------------- REDAXO types
            case 'rex-article-id':
                $var = (int) $var;
                if ($mode == 'found') {
                    if (!OOArticle::isValid(OOArticle::getArticleById($var))) {
                        $var = (int) $default;
                    }
                }
                break;
            case 'rex-category-id':
                $var = (int) $var;
                if ($mode == 'found') {
                    if (!OOCategory::isValid(OOCategory::getCategoryById($var))) {
                        $var = (int) $default;
                    }
                }
                break;
            case 'rex-clang-id':
                $var = (int) $var;
                if ($mode == 'found') {
                    if (empty($REX['CLANG'][$var])) {
                        $var = (int) $default;
                    }
                }
                break;
            case 'rex-template-id':
            case 'rex-ctype-id':
            case 'rex-slice-id':
            case 'rex-module-id':
            case 'rex-action-id':
            case 'rex-media-id':
            case 'rex-mediacategory-id':
            case 'rex-user-id':
                // erstmal keine weitere validierung
                $var = (int) $var;
                break;

            // ---------------- PHP types
            case 'bool'   :
            case 'boolean':
                $var = (boolean) $var;
                break;
            case 'int'    :
            case 'integer':
                $var = (int)     $var;
                break;
            case 'double' :
                $var = (double)  $var;
                break;
            case 'float'  :
            case 'real'   :
                $var = (float)   $var;
                break;
            case 'string' :
                $var = (string)  $var;
                break;
            case 'object' :
                $var = (object)  $var;
                break;
            case 'array'  :
                if ($var === '') {
                    $var = array();
                } else {
                    $var = (array) $var;
                }
                break;

            // kein Cast, nichts tun
            case ''       : break;

            default:
                // check for array with generic type
                if (strpos($vartype, 'array[') === 0) {
                    if (empty($var)) {
                        $var = array();
                    } else {
                        $var = (array) $var;
                    }

                    // check if every element in the array is from the generic type
                    $matches = array();
                    if (preg_match('@array\[([^\]]*)\]@', $vartype, $matches)) {
                        foreach ($var as $key => $value) {
                            try {
                                $var[$key] = _rex_cast_var($value, $matches[1]);
                            } catch (InvalidArgumentException $e) {
                                // Evtl Typo im vartype, mit urspr. typ als fehler melden
                                throw new InvalidArgumentException('Unexpected vartype "' . $vartype . '" in _rex_cast_var()!');
                            }
                        }
                    } else {
                        throw new InvalidArgumentException('Unexpected vartype "' . $vartype . '" in _rex_cast_var()!');
                    }
                } else {
                    $casted = false;
                }
        }
        if ($casted) {
            return $var;
        }
    }

    if (is_callable($vartype)) {
        $var = call_user_func($vartype, $var);
    } elseif (is_array($vartype)) {
        $var = _rex_cast_var($var, 'array');
        $newVar = array();
        foreach ($vartype as $cast) {
            if (!is_array($cast) || !isset($cast[0])) {
                throw new InvalidArgumentException('Unexpected vartype in _rex_cast_var()!');
            }
            $key = $cast[0];
            $innerVartype = isset($cast[1]) ? $cast[1] : '';
            if (array_key_exists($key, $var)) {
                $newVar[$key] = _rex_cast_var($var[$key], $innerVartype);
            } elseif (!isset($cast[2])) {
                $newVar[$key] = _rex_cast_var('', $innerVartype);
            } else {
                $newVar[$key] = $cast[2];
            }
        }
        $var = $newVar;
    } elseif (is_string($vartype)) {
        throw new InvalidArgumentException('Unexpected vartype "' . $vartype . '" in _rex_cast_var()!');
    } else {
        throw new InvalidArgumentException('Unexpected vartype in _rex_cast_var()!');
    }

    return $var;
}

/**
 * Ermittelt die HTTP-Methode mit der das aktuelle Request aufgerufen wurde.
 *
 * @return String Die ermittelte HTTP-Methode in lowercase (head,get,post,put,delete)
 */
function rex_request_method()
{
    return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
}
