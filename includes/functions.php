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
* functions.php
* ---------
* Helper PHP functions for Labrador
*
*/

function accession_badges ($string, $type){
	$return = '';
	switch ($type){
		case 'geo':
			$class = '';
			$url = 'http://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=';
			break;
		case 'sra':
			$class = 'label-info';
			$url = 'http://www.ncbi.nlm.nih.gov/sra/';
			break;
		case 'ena':
			$class = 'label-success';
			$url = 'http://www.ebi.ac.uk/ena/data/view/';
			break;
		case 'ddjb':
			$class = 'label-warning';
			$url = 'http://trace.ddbj.nig.ac.jp/DRASearch/run?acc=';
			break;
		default:
			$class = 'label-inverse';
			$url = '#';
			break;
	}
	
	$accessions = preg_split('/\s+/', $string);
	if(count($accessions) > 0){
		foreach($accessions as $accession){
			if(strlen($accession) > 0){
				$return .= '<a class="label '.$class.'" href="'.$url.$accession.'" target="_blank">'.$accession.'</a> ';
			}
		}
	}
	
	return $return;
}

function project_header($project) {
	global $data_root;
	
	echo '<h1>';
	echo $project['name'].' ';
	echo accession_badges ($project['accession_geo'], 'geo').' ';
	echo accession_badges ($project['accession_sra'], 'sra').' ';
	echo accession_badges ($project['accession_ena'], 'ena').' ';
	echo accession_badges ($project['accession_ddjb'], 'ddjb');
	echo '</h1>';
	
	echo '<p class="project_header_tag"><span class="label ';
	if($project['status'] == 'Processing Complete'){ 
		echo 'label-success'; 
	} else if($project['status'] == 'Currently Processing'){
		echo 'label-warning'; 
	} else if($project['status'] == 'Not Started'){
		echo 'label-important';
	}
	echo '">'.$project['status'].'</span>';	
	
	$p_users = mysql_query("SELECT `users`.`email`, `users`.`firstname`, `users`.`surname` FROM `users` LEFT JOIN `project_contacts` on `users`.`id` = `project_contacts`.`user_id` WHERE `project_contacts`.`project_id` = '".$project['id']."'");
	if(mysql_num_rows($p_users) > 0){
		echo '<small>Contact';
		if(mysql_num_rows($p_users) > 1) echo 's';
		echo ': ';
		$first = true;
		while($p_user = mysql_fetch_array($p_users)){
			if(!$first){ echo ', '; } $first = false;
			echo '<a href="mailto:'.$p_user['email'].'">'.$p_user['firstname'].' '.$p_user['surname'].'</a>';
		}
		echo '</small>';
	}
	
	if($project['assigned_to'] != ''){
		echo '<small>Assigned to: <a href="mailto:'.$project['assigned_to'].'">'.$project['assigned_to'].'</a></small>';
	}
	
	echo '<small>Location: <code>'.$data_root.$project['name'].'/</code></small>';
	
	echo '</p>';
}


function human_filesize($bytes, $decimals = 2) {
	$sz = ' KMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	if(@$sz[$factor] == ' '){
		$decimals = 0;
	}
	return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$sz[$factor].'B';
}

?>