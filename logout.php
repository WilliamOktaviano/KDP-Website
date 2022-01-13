<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
if (!empty($_SESSION[$class->loginSession]))
	unset($_SESSION[$class->loginSession]); 

header('location: /');
?>