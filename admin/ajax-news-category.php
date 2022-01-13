<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $newsCategory;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>