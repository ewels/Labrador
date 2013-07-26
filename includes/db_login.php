<?php

$dblink = mysql_connect('localhost', 'labrador');
if (!$dblink) die('Could not connect: ' . mysql_error());
$db_selected = mysql_select_db('labrador_dev', $dblink);
if (!$db_selected) die ('Can\'t use database : ' . mysql_error());

?>