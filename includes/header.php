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
Template file - page header
*/

if(!isset($root)){
	$root = "";
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel='shortcut icon' type='image/x-icon' href="<?php echo $root; ?>img/ctr_600_0F7_icon.ico" />
    <title>CTR-BFX Labrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $homepage_title.' - '.$homepage_subtitle; ?>">
    <meta name="author" content="Philip Ewels, Babraham Institute, Cambridge, UK">

    <!-- Le styles -->
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic' rel='stylesheet' type='text/css'>
	<link href="<?php echo $root; ?>css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo $root; ?>css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="<?php echo $root; ?>includes/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
	<link href="<?php echo $root; ?>includes/datatables/css/dataTables.tableTools.min.css" rel="stylesheet">
	<link href="<?php echo $root; ?>css/styles.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


  </head>

	<body <?php if(basename($_SERVER['PHP_SELF']) == 'project.php') { ?> style="background-color: #FAFAFA;"<?php } ?>>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">

				<a class="brand" href="<?php echo $root; ?>index.php">Labrador <img src="<?php echo $root; ?>img/labrador_logo_tiny.png"></a>

				<form class="navbar-search form-search pull-left" action="<?php echo $root; ?>search.php" method="get">
					<div class="input-append">
						<input type="text" name="s" id="s" class="search-query" placeholder="Search" autocomplete="off" <?php if(isset($_GET['s'])){ echo 'value="'.htmlentities($_GET['s']).'"'; } ?>>
						<button class="btn">Search</button>
					</div>
				</form>

				<ul class="nav" style="margin-left:20px;">
					<li><a href="<?php echo $root; ?>project.php" class="request-dataset-nav-link">Create New Project</a></li>
					<?php labrador_login_link(); ?>
				</ul>

			</div>
		</div>
	</div>

    <div class="container-fluid">
		<noscript>
			<div id="no_javascript" class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<h4>Javascript is not enabled!</h4>
				Warning - Labrador uses Javascript which is currently disabled in your browser.
				Many parts of Labrador will not work without Javascript.
				You can find instructions on how to enable Javascript <a href="http://www.enable-javascript.com/" target="_blank">here</a>.
			</div>
		</noscript>
