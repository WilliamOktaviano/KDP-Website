<?php 
require_once '_config.php'; 
require_once '_include-min.php';  
require_once '_global.php';  


$obj = $city;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>
