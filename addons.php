<?php

function xmlValue($val)
{
  return utf8_encode(htmlspecialchars($val));
}

$addon = new rex_sql();
$addon->debugsql = true;
$addon->setQuery("
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
LIMIT 50");

echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
echo '<entries>'."\n";
for($i = 0; $i < $addon->getRows(); $i++)
{
  echo '<entry>'."\n";
  
  echo '<name><![CDATA['. xmlValue($addon->getValue("a.name")) .']]></name>'."\n";
  echo '<versionen><![CDATA['. xmlValue($addon->getValue("wv.name")) .']]></versionen>'."\n";
  echo '<kurzbeschreibung><![CDATA['. xmlValue($addon->getValue("a.shortdesc")) .']]></kurzbeschreibung>'."\n";
  echo '<beschreibung><![CDATA['. xmlValue($addon->getValue("a.description")) .']]></beschreibung>'. "\n";
  echo '<download><![CDATA['. xmlValue($addon->getValue("f.filename")) .']]></download>'."\n";
  echo '<hinweis><![CDATA['. xmlValue($addon->getValue("f.description")) .']]></hinweis>'."\n";
  echo '<ersteller><![CDATA['. xmlValue($addon->getValue("u.firstname") .' '. $addon->getValue("u.name")) .']]></ersteller>'."\n";
  echo '<updatedate><![CDATA['. xmlValue($addon->getValue("a.updatedate")) .']]></updatedate>'."\n";
  
  echo '</entry>'."\n";
  
  $addon->next();
}
echo '</entries>'."\n";

?>