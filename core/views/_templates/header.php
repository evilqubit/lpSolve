<?php $_Settings['menu_selected'] = array("$WRAPPER" => 'active'); ?>

<!DOCTYPE html>
<html lang="en">
	<head>

	<base href="<?php echo URL;?>" />

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php $htmlTitle = ( isset ($_Title) ) ? $_Title : WEBSITE_TITLE;?>
	<title><?php echo $htmlTitle;?></title>

	<meta name="keywords" content="" />
	
	<link rel="shortcut icon" type="image/png" href="public/img/favicon.png?v=1.05" />
	<link rel="apple-touch-icon" href="public/img/favicon-touch.png?v=1.05" />
	
	<link rel="stylesheet" href="public/css/bootstrap.min.css">
	<link rel="stylesheet" href="public/css/jquery.datetimepicker.css"/ >
	<link rel="stylesheet" href="public/css/style.css" >

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
	<script src="public/js/jquery.datetimepicker.js"></script>
	
</head>

<body id="body-scroll" class="page-<?php echo $WRAPPER;?>">

<nav id="navbar" class="navbar navbar-default" role="navigation">
  <div class="container">
    <div class="row">
      <div class="collapse navbar-collapse navbar-left menu-navbar-collapse">
        <ul class="nav navbar-nav">
          <li class="<?php echo $_Settings['menu_selected']['home'];?>"><a href="./">Home</a></li>
				<!--<li class="<?php echo $_Settings['menu_selected']['settings'];?>"><a href="settings">Settings</a></li>-->
        </ul>
      </div>
    </div>
  </div>
</nav>