
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Labrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic' rel='stylesheet' type='text/css'>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


  </head>

	<body <?php if(basename($_SERVER['PHP_SELF']) == 'project.php') { ?> style="background-color: #FAFAFA;"<?php } ?>>
	
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
			
				<a class="brand" href="index.php">Labrador<sub style="font-size:10px; color:#777; margin-right:30px;">BETA</sub></a>
				
				<form class="navbar-search form-search pull-left" action="index.php" method="get">
					<div class="input-append">
						<input type="text" name="s" id="s" class="search-query" placeholder="Search" <?php if(isset($_GET['s'])){ echo 'value="'.$_GET['s'].'"'; } ?>>
						<button class="btn">Search</button>
					</div>
				</form>
				
				<ul class="nav" style="margin-left:20px;">
					<li><a href="project.php" class="request-dataset-nav-link">Create New Project</a></li>
					<?php labrador_login_link(); ?>
				</ul>

			</div>
		</div>
	</div>

    <div class="container-fluid">
		<noscript>
			<div id="no_javascript" class="container alert alert-error">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<h4>Javascript is not enabled!</h4>
				Warning - Labrador uses Javascript which is currently disabled in your browser.
				Many parts of Labrador will not work without Javascript.
				You can find instructions on how to enable Javascript <a href="http://www.enable-javascript.com/" target="_blank">here</a>.
			</div>
		</noscript>
