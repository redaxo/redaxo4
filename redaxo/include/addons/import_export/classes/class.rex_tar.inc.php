<?php

/**
 * REDAXO Tar Klasse
 *
 * Diese Subklasse fixed ein paar Bugs gegenüber der
 * original Implementierung und erhoeht die Performanz
 *
 * @author	Markus Staab
 *
 * @package     kernel
 * @subpackage  core
 */

class rex_tar extends tar
{
  /**
   * Add a file to the tar archive
   *
   * @param   string  $filename
   * @param   boolean $binary     Binary file?
   * @return  bool
   **/
  function addFile($filename, $binary = false)
  {
    // Make sure the file we are adding exists!
    if (!file_exists($filename))
    {
      return false;
    }

    // Make sure there are no other files in the archive that have this same filename
    if ($this->containsFile($filename))
    {
      return false;
    }

    // Get file information
    $file_information = stat($filename);
    // STM
    // hier mit get_file_contents, ist viel schneller als fopen/fread/fclose
    $file_contents = rex_get_file_contents($filename);

    // Add file to processed data
    $this->numFiles++;
    $activeFile = & $this->files[];
    $activeFile["name"] = $filename;
    $activeFile["mode"] = $file_information["mode"];
    $activeFile["user_id"] = $file_information["uid"];
    $activeFile["group_id"] = $file_information["gid"];
    $activeFile["size"] = $file_information["size"];
    $activeFile["time"] = $file_information["mtime"];
    $activeFile["checksum"] = isset ($checksum) ? $checksum : '';
    $activeFile["user_name"] = "";
    $activeFile["group_name"] = "";
    // STM
    // trim entfernt, da manche Dateien leere Header haben und
    // diese benoetigen (z.b. TTF)
    // $activeFile["file"] = trim($file_contents);
    $activeFile["file"] = $file_contents;

    return true;
  }
}
?>