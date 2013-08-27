<?php

/*
   send_file.php
   Outputs the contents of files on the server, used for Report iframes
*/

require_once('../includes/config.php');

$path = $data_root.$_GET['path'];
$dir = dirname($_GET['path']).'/';

$fileinfo = pathinfo($path);

$allowed_extensions = array(
	'html',
	'htm',
	'jpeg',
	'jpg',
	'png',
	'gif'
);

if(in_array($fileinfo['extension'], $allowed_extensions)){

	$file = file_get_contents($path);
	
	$file = preg_replace('/src=\"((?!data:))/', 'src="send_file.php?path='.$dir, $file);
	
	echo $file;
	
}