
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Labrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic' rel='stylesheet' type='text/css'>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/responsive.css" rel="stylesheet">
	<link href="includes/chosen/chosen.css" rel="stylesheet">
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
			
				<a class="brand" href="index.php">Labrador<sub style="font-size:10px;">BETA</sub></a>
				
				<ul class="nav">
					<li><a href="create_paper.php" class="request-dataset-nav-link">Fields</a></li>
					<li><a href="create_paper.php" class="request-dataset-nav-link">Filters</a></li>
				</ul>
				
				<form class="navbar-search form-search pull-left">
					<div class="input-append">
						
						<input type="text" class="search-query" placeholder="Search">
						<button class="btn">Search</button>
					</div>
				</form>
				
				<ul class="nav">
					<li><a href="create_paper.php" class="request-dataset-nav-link">Create New Project</a></li>
				</ul>

				<p class="navbar-text pull-right" style="margin-right:30px;">
				<?php if(isset($_SESSION['email'])) { echo $_SESSION['email']; } else { echo '<a href="#">Log In</a>'; } ?>
				</p>
				
			</div>
		</div>
	</div>
	
	<?php if(!empty($msg)): ?>
	<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
		<?php foreach($msg as $var)	echo $var.'<br>'; ?>
	</div>
	<?php endif; ?>

    <div class="container-fluid">
