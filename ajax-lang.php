<?php 
require_once '_config.php'; 

if(!isset($_POST) || empty($_POST['langkey']))	die;

$_SESSION['lang'] = $_POST['langkey'];
  
?>
