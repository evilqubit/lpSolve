<?php
$_Settings['menu_selected'] = array("$WRAPPER" => 'active');
?>

<!DOCTYPE html>
<html lang="en">
	<head>

	<base href="<?php echo URL;?>" />

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php $htmlTitle = ( isset ($_Title) ) ? $_Title : WEBSITE_TITLE;?>
	<title><?php echo $htmlTitle;?></title>

	<link rel="stylesheet" href="public/css/bootstrap.min.css">
	<link rel="stylesheet" href="public/css/jquery.datetimepicker.css" />
	<link rel="stylesheet" href="public/css/style.css" >

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" type="text/javascript"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js" type="text/javascript"></script>
	<script src="public/js/jquery.datetimepicker.js"></script>
	
</head>

<body id="body-scroll" class="page-<?php echo $WRAPPER;?>">

<nav id="navbar" class="navbar navbar-default" role="navigation">
	<div class="container">
		<div class="row">
			<div class="collapse navbar-collapse navbar-left menu-navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="<?php echo $_Settings['menu_selected']['home'];?>"><a href="./">Home</a></li>
					<li class="<?php echo $_Settings['menu_selected']['settings'];?>"><a href="settings">Settings</a></li>
					<li class="<?php echo $_Settings['menu_selected']['students'];?>"><a href="students">Students</a></li>
					<li class="<?php echo $_Settings['menu_selected']['teachers'];?>"><a href="teachers">Teachers</a></li>
				</ul>
			</div>
		</div>
	</div>
</nav>