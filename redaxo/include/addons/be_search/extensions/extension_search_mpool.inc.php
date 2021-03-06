<?php

/**
 * Backend Search Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

function rex_a256_search_mpool($params)
{
    global $I18N, $REX;

    if (!$REX['USER']->hasPerm('be_search[mediapool]')) {
        return $params['subject'];
    }

    if (rex_request('subpage', 'string') != '') {
        return $params['subject'];
    }
    $media_name = rex_request('a256_media_name', 'string');

    $subject = $params['subject'];

    $search_form = '
        <p class="rex-form-col-a rex-form-text" id="a256-media-search">
            <label for="a256-media-name">' . $I18N->msg('be_search_mpool_media') . '</label>
            <input class="rex-form-text" type="text" name="a256_media_name" id="a256-media-name" value="' . htmlspecialchars(stripslashes($media_name)) . '" />
            <input class="rex-form-submit" type="submit" value="' . $I18N->msg('be_search_mpool_start') . '" />
        </p>
    ';

    $subject = str_replace('<div class="rex-form-row">', '<div class="rex-form-row">' . $search_form, $subject);
    $subject = str_replace('<fieldset class="rex-form-col-1">', '<fieldset class="rex-form-col-2">', $subject);
    $subject = str_replace('<p class="rex-form-select">', '<p class="rex-form-col-b rex-form-select">', $subject);

    return $subject;
}

function rex_a256_search_mpool_query($params)
{
    global $REX;

    if (!$REX['USER']->hasPerm('be_search[mediapool]')) {
        return $params['subject'];
    }

    $media_name = rex_request('a256_media_name', 'string');
    if ($media_name == '') {
        return $params['subject'];
    }

    $qry = $params['subject'];
    $category_id = (int) $params['category_id'];

    $qry = str_replace('f.category_id=' . $category_id, '1=1', $qry);
    $where = " (f.filename LIKE '%" . mysql_real_escape_string($media_name) . "%' OR f.title LIKE '%" . mysql_real_escape_string($media_name) . "%')";

    $searchmode = OOAddon::getProperty('be_search', 'searchmode', 'local');

    // global search - all
    if ($searchmode == 'global') {
        $qry = str_replace('WHERE ', 'WHERE ' . $where . ' AND ', $qry);
        return $qry;

    // local search - all categories and with no category
    } elseif ($category_id == 0) {
        $qry = str_replace('WHERE ', 'WHERE ' . $where . ' AND ', $qry);

        // local search - categorie and subcategories
    } else {
        $where .= ' AND f.category_id = c.id  ';
        $where .= " AND (c.path LIKE '%|" . $category_id . "|%' OR c.id=" . $category_id . ') ';
        $qry = str_replace('FROM ', 'FROM ' . $REX['TABLE_PREFIX'] . 'file_category c,', $qry);
        $qry = str_replace('WHERE ', 'WHERE ' . $where . ' AND ', $qry);

    }

    return $qry;
}
