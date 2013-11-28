<?php

##########################################################################
# Copyright 2013, Philip Ewels (phil.ewels@babraham.ac.uk)               #
#                                                                        #
# This file is part of Labrador.                                         #
#                                                                        #
# Labrador is free software: you can redistribute it and/or modify       #
# it under the terms of the GNU General Public License as published by   #
# the Free Software Foundation, either version 3 of the License, or      #
# (at your option) any later version.                                    #
#                                                                        #
# Labrador is distributed in the hope that it will be useful,            #
# but WITHOUT ANY WARRANTY; without even the implied warranty of         #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
# GNU General Public License for more details.                           #
#                                                                        #
# You should have received a copy of the GNU General Public License      #
# along with Labrador.  If not, see <http://www.gnu.org/licenses/>.      #
##########################################################################

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