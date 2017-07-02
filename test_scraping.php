<?php
require("scraping.php");

$scr=new Scraping_pmagicos();
$data=$scr->execute(False,True);

$myfile = fopen("files_scraping.json", "w") or die("Unable to open file!");
fwrite($myfile, $data);
fclose($myfile);

?>
