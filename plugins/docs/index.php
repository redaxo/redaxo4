<?php
$forward = strrchr($_SERVER[REQUEST_URI],"?");
header("location: online/index.php".$forward);
?>