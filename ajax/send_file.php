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
   send_file.php
   Outputs the contents of files on the server, used for Report iframes
*/

require_once('../conf/config.php');

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