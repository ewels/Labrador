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


/*
Script to write out the cluster flow filenames file to the cluster
*/

require_once('../includes/start.php');

if(isset($_POST['contents']) && isset($_POST['project_id']) && is_numeric($_POST['project_id'])){

	$project_q = mysqli_query($dblink, "SELECT * FROM `projects` WHERE `id` = '".$_POST['project_id']."'");
	$project = mysqli_fetch_array($project_q);
	
	$filename = $project['name'].'_labrador_downloads_'.date('d_m_Y').'.txt';
	$dir = $data_root.$project['name'].'/';
	$fn = $dir.$filename;

	// Save history message
	$query = sprintf("INSERT INTO `history` (`project_id`, `user_id`, `note`, `time`) VALUES ('%d', '%d', '%s', '%d')",
		$project['id'], $user['id'], mysqli_real_escape_string($dblink, "Saved file names file '$filename'"), time());
	mysqli_query($dblink, $query);

	// Write file contents
	$output = $_POST['contents'];
	$output = str_replace("\r", "", $output);

	// Check the directory exists
	if(!is_dir($dir)){
		if(!mkdir($dir, 0775)){
			die("can't create directory $dir");
		}
	}

	// Write file
	$fh = fopen($fn, 'w') or die("can't open file $fn");
	fwrite($fh, $output);
	fclose($fh);

	echo 'File saved.';

} else {
	echo 'Missing vars to save file';
}

?>
