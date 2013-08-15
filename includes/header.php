
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
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/responsive.css" rel="stylesheet">
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
				</ul>

				<p class="navbar-text pull-right" style="margin-right:30px;">
				<?php if(isset($_SESSION['email'])) { echo $_SESSION['email']; } else { echo '<a href="#">Log In</a>'; } ?>
				</p>
				
			</div>
		</div>
	</div>

    <div class="container-fluid">
