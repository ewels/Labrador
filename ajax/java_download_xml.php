<?php
session_start();

require_once('../includes/config.php');

$file_list = $_SESSION['files'];

function parse_paths_of_files($array){
    $result = array();
    foreach ($array as $item)    {
        $parts = explode('/', $item);
        $current = &$result;
        for ($i = 1, $max = count($parts); $i < $max; $i++) {
            if (!isset($current[$parts[$i-1]])) {
                 $current[$parts[$i-1]] = array();
            }
            $current = &$current[$parts[$i-1]];
        }
        $current[] = $parts[$i-1];
    }
    return $result;
}

$files = parse_paths_of_files($file_list);

function print_files_array($files, &$result, $dir){
	global $labrador_url;
	foreach($files as $name => $file){
		if(is_array($file)) {
				$dir .= $name.'/';
				$result .= '<folder name="'.$name.'">';
				print_files_array($file, $result, $dir);
				$result .= '</folder>';
				$dir_a = explode('/', $dir);
				$trash = array_pop($dir_a);
				$trash = array_pop($dir_a);
				$dir = implode('/', $dir_a).'/';
		} else {
			$result .= '<file name="'.$file.'"><url>'.$labrador_url.'download_file.php?fn='.$dir.$file.'</url></file>';
		}
	}
	return $result;
}

$empty = '';
echo '<?xml version="1.0" encoding="UTF-8"?><download>'.print_files_array($files, $empty, $empty).'</download>';
?>