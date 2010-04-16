<?php

function xmlValue($val)
{
  return utf8_encode(htmlspecialchars($val));
}

$additionalWhere = '';

// if GET-Parameter given, show only details of the given addonid
$addonId = rex_get('addon_id', 'int');
if($addonId)
{
  $additionalWhere = ' AND f.addon_id='. $addonId;
}

$addon = new rex_sql();
// $addon->debugsql = true;
$addon->setQuery('
SELECT * 
FROM 
  rex_web_addons a,
  rex_web_addons_files f,
  rex_web_addons_files_versions v,
  rex_web_versions wv,
  rex_com_user u 
WHERE
  a.id = f.addon_id
  AND f.addon_id = v.addon_file_id
  AND v.version_id = wv.id
  AND a.createuser = u.id
  AND a.status=1
  '. $additionalWhere .'
ORDER BY
  f.addon_id,
  wv.version DESC,
  f.updatedate DESC
LIMIT 50');

echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
echo '<entries>'."\n";

$prevAddonId = -1;
for($i = 0; $i < $addon->getRows(); $i++)
{
  $addonId = $addon->getValue("a.id");
  
  if($addonId != $prevAddonId)
  {
    echo '<entry>'."\n";
    
    echo '<id><![CDATA['. xmlValue($addon->getValue("a.id")) .']]></id>'."\n";
    echo '<name><![CDATA['. xmlValue($addon->getValue("a.name")) .']]></name>'."\n";
    echo '<kurzbeschreibung><![CDATA['. xmlValue($addon->getValue("a.shortdesc")) .']]></kurzbeschreibung>'."\n";
    echo '<beschreibung><![CDATA['. xmlValue($addon->getValue("a.description")) .']]></beschreibung>'. "\n";
    echo '<ersteller><![CDATA['. xmlValue($addon->getValue("u.firstname") .' '. $addon->getValue("u.name")) .']]></ersteller>'."\n";
      echo '<files>'."\n";
  }

  
  echo '<file>'."\n";
    echo '<name><![CDATA['. xmlValue($addon->getValue("f.name")) .']]></name>'."\n";
    echo '<hinweis><![CDATA['. xmlValue($addon->getValue("f.description")) .']]></hinweis>'."\n";
    echo '<download><![CDATA[http://www.redaxo.de/files/'. xmlValue($addon->getValue("f.filename")) .']]></download>'."\n";
    echo '<versionen><![CDATA['. xmlValue($addon->getValue("wv.name")) .']]></versionen>'."\n";
    echo '<updatedate><![CDATA['. xmlValue($addon->getValue("f.updatedate")) .']]></updatedate>'."\n";
  echo '</file>'."\n";

  // perform lookahead
  if(($i+1) < $addon->getRows())
  {
    $addon->next();
    $nextAddonId = $addon->getValue("a.id");
  }
  else
  {
    // we already reached the end of the resultset
    $nextAddonId = -1;
  }
  
  
  if($addonId != $nextAddonId)
  {
      echo '</files>'."\n";
    echo '</entry>'."\n";
  }
  
  $prevAddonId = $addonId;
}
echo '</entries>'."\n";

?>