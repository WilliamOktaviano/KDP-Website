<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

$_SESSION[$class->loginSession]['simulator'] = array();
header("location: /simulator");
die;

?>