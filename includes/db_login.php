<?php

$dblink = mysql_connect('localhost', 'tallphil_reik','LqfH5UXOfQ3I');
if (!$dblink) die('Could not connect: ' . mysql_error());
$db_selected = mysql_select_db('dataset_browser', $dblink);
if (!$db_selected) die ('Can\'t use database : ' . mysql_error());

?>