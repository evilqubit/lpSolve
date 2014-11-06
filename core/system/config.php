<?php
define('DB_TYPE', 'mysql');
if( $_SERVER['HTTP_HOST'] == 'localhost')
{
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'scheduler');
  define('DB_USER', 'root');
  define('DB_PASS', '');

  define('URL', 'http://localhost/scheduler/');
}
else
{
  define('DB_HOST', '');
  define('DB_NAME', '');
  define('DB_USER', '');
  define('DB_PASS', '');
  define('URL', 'http://'.$_SERVER['HTTP_HOST'].'/');	
}

define ('WEBSITE_TITLE','Scheduler');
define('NO_ROBOTS', 0);

error_reporting( E_ALL ^ E_NOTICE );
date_default_timezone_set('Asia/Beirut');
// error_reporting(0);
//ini_set('display_errors', 0);
include ('functions.general.php');
include ('functions.custom.php');
?>