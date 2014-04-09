<?php
/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

function rex_a79_textile($code, $restricted = false, $doctype = 'xhtml')
{
    $textile = rex_a79_textile_instance($doctype);
    return $restricted == false
             ? $textile->TextileThis($code)
             : $textile->TextileRestricted($code);
}

function rex_a79_textile_instance($doctype = 'xhtml')
{
    static $instance = array();

    if (!isset($instance[$doctype])) {
        $instance[$doctype] = new rex_textile_parser($doctype);
    }

    return $instance[$doctype];
}

class rex_textile_parser extends Textile
{
    public function __construct($doctype = 'xhtml')
    {
        parent::__construct($doctype);
        $this->unrestricted_url_schemes[] = 'redaxo';
    }
}
